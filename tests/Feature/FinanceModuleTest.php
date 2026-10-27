<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected FinancialAccount $bankAccount;
    protected FinancialAccount $cashVault;
    protected ExpenseCategory $category;

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

        $this->bankAccount = FinancialAccount::create([
            'code' => 'ACC-RAJHI-TEST',
            'name_en' => 'Al Rajhi Corporate Bank',
            'name_ar' => 'مصرف الراجحي - الحساب الرئيسي',
            'type' => 'bank',
            'bank_name' => 'Al Rajhi Bank',
            'account_number' => '4455667788',
            'iban' => 'SA4480000445566778899001',
            'currency' => 'SAR',
            'initial_balance' => 50000.00,
            'current_balance' => 50000.00,
            'is_active' => true,
        ]);

        $this->cashVault = FinancialAccount::create([
            'code' => 'ACC-VAULT-TEST',
            'name_en' => 'Petty Cash Treasury Vault',
            'name_ar' => 'خزينة العهدة النقدية',
            'type' => 'cash',
            'bank_name' => 'HQ Internal Vault',
            'currency' => 'SAR',
            'initial_balance' => 5000.00,
            'current_balance' => 5000.00,
            'is_active' => true,
        ]);

        $this->category = ExpenseCategory::create([
            'code' => 'EXP-CLINICAL',
            'name_en' => 'Clinical Research & Trials',
            'name_ar' => 'الأبحاث السريرية والتجارب المخبرية',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_finance_dashboard(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.finance.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(__('finance.dashboard'));
        $response->assertSee(__('finance.total_treasury_balance'));
        $response->assertSee('55,000.00'); // Sum of bank + cash
    }

    public function test_sales_invoices_ledger_and_customer_payment_reconciliation(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-FIN-1001',
            'invoice_number' => 'INV-FIN-1001',
            'date' => now()->toDateString(),
            'invoice_date' => now(),
            'channel' => 'b2b',
            'status' => 'processing',
            'payment_status' => 'unpaid',
            'subtotal' => 1000.00,
            'tax' => 150.00,
            'total' => 1150.00,
            'payment_method' => 'bank_transfer',
            'customer_name' => 'Dr. Khalid Longevity Center',
        ]);

        $this->assertEquals(1150.00, $order->fresh()->due_balance);

        // Access sales invoices index
        $indexResponse = $this->actingAs($this->admin, 'web')->get(route('admin.finance.sales-invoices.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('INV-FIN-1001');

        // Receive partial customer payment of 500 SAR
        $paymentResponse = $this->actingAs($this->admin, 'web')->post(route('admin.finance.customer-payments.store'), [
            'order_id' => $order->id,
            'financial_account_id' => $this->bankAccount->id,
            'amount' => 500.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'TXN-CLINIC-500',
            'notes' => 'Advance retainer payment',
        ]);

        $paymentResponse->assertRedirect();
        $this->assertEquals(50500.00, (float) $this->bankAccount->fresh()->current_balance);
        $this->assertEquals(500.00, (float) $order->fresh()->paid_amount);
        $this->assertEquals(650.00, (float) $order->fresh()->due_balance);
        $this->assertEquals('Partially Paid', $order->fresh()->payment_status);

        // Receive remaining payment of 650 SAR
        $this->actingAs($this->admin, 'web')->post(route('admin.finance.customer-payments.store'), [
            'order_id' => $order->id,
            'financial_account_id' => $this->bankAccount->id,
            'amount' => 650.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'TXN-CLINIC-650',
            'notes' => 'Settlement in full',
        ]);

        $this->assertEquals(51150.00, (float) $this->bankAccount->fresh()->current_balance);
        $this->assertEquals(1150.00, (float) $order->fresh()->paid_amount);
        $this->assertEquals(0.00, (float) $order->fresh()->due_balance);
        $this->assertEquals('Paid', $order->fresh()->payment_status);
    }

    public function test_purchase_invoice_and_supplier_payment_disbursement(): void
    {
        $supplier = Supplier::create([
            'code' => 'SUP-BIOGEN-01',
            'name_en' => 'BioGen Laboratory Reagents',
            'name_ar' => 'مختبرات بايو جين للمحاليل',
            'email' => 'sales@biogen-reagents.com',
            'phone' => '+966551122334',
            'status' => 'active',
            'is_active' => true,
        ]);

        // Access Purchase Invoices create page
        $createPageResponse = $this->actingAs($this->admin, 'web')->get(route('admin.finance.purchase-invoices.create'));
        $createPageResponse->assertStatus(200);

        // Store Purchase Invoice
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.purchase-invoices.store'), [
            'supplier_id' => $supplier->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 2000.00,
            'tax_amount' => 300.00,
            'grand_total' => 2300.00,
            'reference_number' => 'BILL-BIOGEN-9921',
            'items' => [
                [
                    'item_name' => 'Cellular Growth Medium 500ml',
                    'quantity' => 10,
                    'unit_price' => 200.00,
                    'tax_rate' => 15.00,
                ],
            ],
            'notes' => 'Direct procurement for clinical lot',
        ]);

        $response->assertRedirect();

        $invoice = PurchaseInvoice::where('reference_number', 'BILL-BIOGEN-9921')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(2000.00, (float) $invoice->subtotal);
        $this->assertEquals(300.00, (float) $invoice->tax_amount);
        $this->assertEquals(2300.00, (float) $invoice->grand_total);
        $this->assertEquals('unpaid', $invoice->payment_status);

        // View invoice show page
        $showResponse = $this->actingAs($this->admin, 'web')->get(route('admin.finance.purchase-invoices.show', $invoice->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('BILL-BIOGEN-9921');

        // Pay supplier bill from bank account
        $payResponse = $this->actingAs($this->admin, 'web')->post(route('admin.finance.supplier-payments.store'), [
            'purchase_invoice_id' => $invoice->id,
            'financial_account_id' => $this->bankAccount->id,
            'amount' => 2300.00,
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'WIRE-PO-2300',
            'notes' => 'Settlement for reagent delivery',
        ]);

        $payResponse->assertRedirect();
        $this->assertEquals(47700.00, (float) $this->bankAccount->fresh()->current_balance);
        $this->assertEquals('paid', $invoice->fresh()->payment_status);
        $this->assertEquals(2300.00, (float) $invoice->fresh()->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->fresh()->due_balance);
    }

    public function test_operational_expense_creation_deducts_account_balance(): void
    {
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.expenses.store'), [
            'expense_category_id' => $this->category->id,
            'financial_account_id' => $this->cashVault->id,
            'title_en' => 'Clinical Cleanroom Filters Replacement',
            'title_ar' => 'استبدال فلاتر الغرف المعقمة',
            'amount' => 800.00,
            'tax_amount' => 120.00,
            'total_amount' => 920.00,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'petty_cash',
            'beneficiary_name' => 'CleanAir Technologies Co.',
            'reference_number' => 'EXP-AIR-01',
            'notes' => 'Monthly preventative maintenance',
        ]);

        $response->assertRedirect();

        $expense = Expense::where('reference_number', 'EXP-AIR-01')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(920.00, (float) $expense->total_amount);

        // Cash vault initial was 5000, now 5000 - 920 = 4080
        $this->assertEquals(4080.00, (float) $this->cashVault->fresh()->current_balance);

        // Financial transaction created
        $transaction = FinancialTransaction::where('expense_id', $expense->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('debit', $transaction->direction);
        $this->assertEquals(920.00, (float) $transaction->amount);
    }

    public function test_funds_transfer_between_accounts(): void
    {
        // Transfer 2,000 SAR from Bank to Cash Vault
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.accounts.transfer'), [
            'from_account_id' => $this->bankAccount->id,
            'to_account_id' => $this->cashVault->id,
            'amount' => 2000.00,
            'date' => now()->toDateString(),
            'reference_number' => 'TRF-TREASURY-01',
            'notes' => 'Petty cash replenishment',
        ]);

        $response->assertRedirect();

        // Bank was 50,000 -> now 48,000
        $this->assertEquals(48000.00, (float) $this->bankAccount->fresh()->current_balance);
        // Vault was 5,000 -> now 7,000
        $this->assertEquals(7000.00, (float) $this->cashVault->fresh()->current_balance);

        // Account statement show page
        $showBank = $this->actingAs($this->admin, 'web')->get(route('admin.finance.accounts.show', $this->bankAccount->id));
        $showBank->assertStatus(200);
        $showBank->assertSee('TRF-TREASURY-01');
    }

    public function test_voucher_print_views_render(): void
    {
        $supplier = Supplier::create([
            'code' => 'SUP-VCH-TEST',
            'name_en' => 'Test Supplier',
            'name_ar' => 'مورد تجريبي',
            'email' => 'supplier@test.com',
            'status' => 'active',
            'is_active' => true,
        ]);

        $invoice = PurchaseInvoice::create([
            'invoice_number' => 'PINV-TEST-001',
            'supplier_id' => $supplier->id,
            'invoice_date' => now(),
            'subtotal' => 100.00,
            'tax_amount' => 15.00,
            'grand_total' => 115.00,
            'paid_amount' => 115.00,
            'payment_status' => 'paid',
        ]);

        $supplierPayment = FinancialTransaction::create([
            'transaction_number' => 'TRX-SUP-001',
            'receipt_voucher_number' => 'VCH-SUP-001',
            'financial_account_id' => $this->bankAccount->id,
            'supplier_id' => $supplier->id,
            'purchase_invoice_id' => $invoice->id,
            'type' => 'supplier_payment',
            'direction' => 'debit',
            'amount' => 115.00,
            'balance_after' => 49885.00,
            'payment_method' => 'bank_transfer',
            'transaction_date' => now(),
        ]);

        $responseSup = $this->actingAs($this->admin, 'web')->get(route('admin.finance.supplier-payments.voucher', $supplierPayment->id));
        $responseSup->assertStatus(200);
        $responseSup->assertSee('VCH-SUP-001');

        $order = Order::create([
            'order_number' => 'ORD-VCH-001',
            'invoice_number' => 'INV-VCH-001',
            'date' => now()->toDateString(),
            'channel' => 'web',
            'status' => 'completed',
            'payment_status' => 'paid',
            'subtotal' => 200.00,
            'tax' => 30.00,
            'total' => 230.00,
            'currency' => 'SAR',
            'customer_name' => 'Sara Al-Otaibi',
        ]);

        $customerPayment = FinancialTransaction::create([
            'transaction_number' => 'TRX-CUST-001',
            'receipt_voucher_number' => 'VCH-CUST-001',
            'financial_account_id' => $this->bankAccount->id,
            'order_id' => $order->id,
            'type' => 'customer_payment',
            'direction' => 'credit',
            'amount' => 230.00,
            'balance_after' => 50230.00,
            'payment_method' => 'mada',
            'transaction_date' => now(),
        ]);

        $responseCust = $this->actingAs($this->admin, 'web')->get(route('admin.finance.customer-payments.voucher', $customerPayment->id));
        $responseCust->assertStatus(200);
        $responseCust->assertSee('VCH-CUST-001');
    }
}
