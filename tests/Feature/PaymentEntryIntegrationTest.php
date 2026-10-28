<?php

namespace Tests\Feature;

use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\PaymentEntry;
use App\Models\PurchaseInvoice;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentEntryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected FinancialAccount $bankAccount;
    protected Supplier $supplier;

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
            'name_en' => 'Al Rajhi Corporate Operating Account',
            'name_ar' => 'حساب مصرف الراجحي الرئيسي',
            'account_number' => 'ACC-RAJHI-001',
            'type' => 'bank',
            'currency' => 'SAR',
            'current_balance' => 100000.00,
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-TEST-101',
            'name_en' => 'Sigma Aldrich Chemical Co.',
            'name_ar' => 'شركة سيجما ألدريتش للكيماويات',
            'email' => 'sigma@example.com',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_payment_entry_index_and_see_spreadsheet_table(): void
    {
        PaymentEntry::create([
            'payment_number' => 'PAY-2026-0001',
            'entry_date' => now()->toDateString(),
            'category' => PaymentEntry::CATEGORY_PURCHASE,
            'description' => 'Raw materials purchase consignment',
            'payee_type' => 'supplier',
            'supplier_id' => $this->supplier->id,
            'supplier_code' => $this->supplier->code,
            'payee_name' => $this->supplier->name_en,
            'amount_excl_vat' => 2000.00,
            'vat_rate' => 15.00,
            'vat_amount' => 300.00,
            'gross_amount' => 2300.00,
            'financial_account_id' => $this->bankAccount->id,
            'payment_method' => 'bank_transfer',
            'status' => 'posted',
        ]);

        $response = $this->actingAs($this->admin, 'web')->get(route('admin.finance.payments.index'));

        $response->assertStatus(200);
        $response->assertSee('Payment Entry');
        $response->assertSee('PAY-2026-0001');
        $response->assertSee('Sigma Aldrich Chemical Co.');
        $response->assertSee('2,300.00');
    }

    public function test_admin_can_create_manual_payment_entry_with_dynamic_vat_and_balance_deduction(): void
    {
        $initialBalance = $this->bankAccount->current_balance;

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.payments.store'), [
            'entry_date' => now()->toDateString(),
            'category' => PaymentEntry::CATEGORY_TRANSPORTATION,
            'payee_type' => 'general_payee',
            'payee_name' => 'FastTrack Logistics Cargo LLC',
            'amount_excl_vat' => 1000.00,
            'vat_rate' => 15.00,
            'gross_amount' => 1150.00,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
            'description' => 'Cold chain sea freight shipment',
        ]);

        $response->assertRedirect(route('admin.finance.payments.index'));

        $this->assertDatabaseHas('payment_entries', [
            'payee_name' => 'FastTrack Logistics Cargo LLC',
            'category' => PaymentEntry::CATEGORY_TRANSPORTATION,
            'amount_excl_vat' => 1000.00,
            'vat_amount' => 150.00,
            'gross_amount' => 1150.00,
            'status' => 'posted',
        ]);

        // Verifies financial account balance was decremented
        $this->bankAccount->refresh();
        $this->assertEquals($initialBalance - 1150.00, $this->bankAccount->current_balance);

        // Verifies audit financial transaction logged
        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'supplier_payment',
            'direction' => 'debit',
            'amount' => 1150.00,
            'financial_account_id' => $this->bankAccount->id,
        ]);
    }

    public function test_admin_can_settle_purchase_invoice_via_payment_entry(): void
    {
        $invoice = PurchaseInvoice::create([
            'invoice_number' => 'PINV-2026-901',
            'supplier_id' => $this->supplier->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 1739.13,
            'tax_amount' => 260.87,
            'grand_total' => 2000.00,
            'paid_amount' => 0.00,
            'payment_status' => 'unpaid',
            'status' => 'approved',
        ]);

        // Partial payment 1: 800 SAR
        $this->actingAs($this->admin, 'web')->post(route('admin.finance.payments.store'), [
            'entry_date' => now()->toDateString(),
            'category' => PaymentEntry::CATEGORY_PURCHASE,
            'payee_type' => 'supplier',
            'supplier_id' => $this->supplier->id,
            'purchase_invoice_id' => $invoice->id,
            'gross_amount' => 800.00,
            'amount_excl_vat' => 695.65,
            'vat_rate' => 15.00,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
        ]);

        $invoice->refresh();
        $this->assertEquals(800.00, (float) $invoice->paid_amount);
        $this->assertEquals(1200.00, (float) $invoice->due_balance);
        $this->assertEquals('partially_paid', $invoice->payment_status);

        // Settlement payment 2: 1200 SAR
        $this->actingAs($this->admin, 'web')->post(route('admin.finance.payments.store'), [
            'entry_date' => now()->toDateString(),
            'category' => PaymentEntry::CATEGORY_PURCHASE,
            'payee_type' => 'supplier',
            'supplier_id' => $this->supplier->id,
            'purchase_invoice_id' => $invoice->id,
            'gross_amount' => 1200.00,
            'amount_excl_vat' => 1043.48,
            'vat_rate' => 15.00,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
        ]);

        $invoice->refresh();
        $this->assertEquals(2000.00, (float) $invoice->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->due_balance);
        $this->assertEquals('paid', $invoice->payment_status);
    }

    public function test_cheque_payment_requires_clearance_before_account_deduction(): void
    {
        $initialBalance = $this->bankAccount->current_balance;

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.payments.store'), [
            'entry_date' => now()->toDateString(),
            'category' => PaymentEntry::CATEGORY_OPERATING_EXPENSES,
            'payee_type' => 'general_payee',
            'payee_name' => 'Pharma Tech Facilities Management',
            'gross_amount' => 5000.00,
            'amount_excl_vat' => 4347.83,
            'vat_rate' => 15.00,
            'payment_method' => 'cheque',
            'cheque_number' => 'CHQ-88201',
            'cheque_bank' => 'Banque Saudi Fransi',
            'cheque_due_date' => now()->addDays(15)->toDateString(),
            'financial_account_id' => $this->bankAccount->id,
        ]);

        $response->assertRedirect(route('admin.finance.payments.index'));

        $entry = PaymentEntry::where('cheque_number', 'CHQ-88201')->firstOrFail();
        $this->assertEquals('pending_clearance', $entry->status);

        // Financial account balance should NOT be deducted yet
        $this->bankAccount->refresh();
        $this->assertEquals($initialBalance, $this->bankAccount->current_balance);

        // Now clear the cheque
        $clearResponse = $this->actingAs($this->admin, 'web')
            ->post(route('admin.finance.payments.clear-cheque', $entry->id));

        $clearResponse->assertRedirect();

        $entry->refresh();
        $this->assertEquals('posted', $entry->status);
        $this->assertNotNull($entry->cleared_at);

        // Now balance should be deducted
        $this->bankAccount->refresh();
        $this->assertEquals($initialBalance - 5000.00, $this->bankAccount->current_balance);
    }

    public function test_admin_can_export_payments_to_csv(): void
    {
        PaymentEntry::create([
            'payment_number' => 'PAY-2026-0002',
            'entry_date' => now()->toDateString(),
            'category' => PaymentEntry::CATEGORY_PURCHASE,
            'payee_name' => 'Saudi German Packaging Materials',
            'amount_excl_vat' => 1500.00,
            'vat_rate' => 15.00,
            'vat_amount' => 225.00,
            'gross_amount' => 1725.00,
            'payment_method' => 'bank_transfer',
            'status' => 'posted',
        ]);

        $response = $this->actingAs($this->admin, 'web')->get(route('admin.finance.payments.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContainsString('PAY-2026-0002', $response->streamedContent());
    }

    public function test_admin_can_view_official_payment_voucher(): void
    {
        $entry = PaymentEntry::create([
            'payment_number' => 'PAY-2026-0003',
            'entry_date' => now()->toDateString(),
            'category' => PaymentEntry::CATEGORY_ASSETS,
            'payee_name' => 'Sterile Solutions Lab Equipment Corp.',
            'amount_excl_vat' => 10000.00,
            'vat_rate' => 15.00,
            'vat_amount' => 1500.00,
            'gross_amount' => 11500.00,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
            'status' => 'posted',
        ]);

        $response = $this->actingAs($this->admin, 'web')->get(route('admin.finance.payments.voucher', $entry->id));

        $response->assertStatus(200);
        $response->assertSee('PAY-2026-0003');
        $response->assertSee('Sterile Solutions Lab Equipment Corp.');
        $response->assertSee('11,500.00');
        $response->assertSee('BLUE ZONE PHARMACEUTICALS');
    }
}
