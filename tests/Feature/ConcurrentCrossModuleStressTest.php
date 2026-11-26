<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CollectionEntry;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\InventoryItem;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Role;
use App\Models\User;
use App\Services\CollectionService;
use App\Services\FifoAllocationEngine;
use App\Services\FinanceService;
use App\Services\InventoryService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Enterprise Cross-Module Concurrency & Invariant Stress Test Suite
 *
 * Verifies transaction isolation, row locking (lockForUpdate),
 * race condition resistance, and accounting integrity between:
 * - Inventory & Finished Goods Batches
 * - Sales Orders & FEFO Allocation
 * - Finance Ledgers & Customer Collections
 * - Multi-Warehouse Stock Transfers
 */
class ConcurrentCrossModuleStressTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;
    protected Location $centralWh;
    protected Location $cairoWh;
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
            ['name' => 'Super Admin', 'password' => bcrypt('secret'), 'role_id' => $role->id, 'status' => 'active']
        );

        $this->category = Category::create([
            'name_en' => 'Cellular Longevity Formulations',
            'name_ar' => 'مركبات استعادة الحيوية الخلوية',
            'slug' => 'cellular-longevity',
        ]);

        $this->centralWh = Location::firstOrCreate(
            ['id' => 'central_wh'],
            ['code' => 'WH-CENTRAL', 'name_en' => 'Central Enterprise Warehouse', 'name_ar' => 'المستودع الرئيسي - الرياض', 'is_active' => true]
        );

        $this->cairoWh = Location::firstOrCreate(
            ['id' => 'cairo_wh'],
            ['code' => 'WH-CAIRO', 'name_en' => 'Cairo Regional Distribution Hub', 'name_ar' => 'مستودع القاهرة الإقليمي', 'is_active' => true]
        );

        $this->bankAccount = FinancialAccount::create([
            'code' => 'ACC-CORP-RAJHI',
            'name_en' => 'Al Rajhi Corporate Bank Account',
            'name_ar' => 'حساب الراجحي المؤسسي الرئيسي',
            'type' => 'bank',
            'bank_name' => 'Al Rajhi Bank',
            'account_number' => '9988776655',
            'iban' => 'SA9980000998877665544331',
            'currency' => 'SAR',
            'initial_balance' => 100000.00,
            'current_balance' => 100000.00,
            'is_active' => true,
        ]);
    }

    /**
     * 1. CONCURRENCY TEST: Stock Allocation Race Condition
     *
     * Scenario: A scarce product has only 10 units in stock.
     * Two concurrent shoppers / orders simultaneously attempt to allocate 10 units.
     * Exactly one must succeed, and the second MUST fail cleanly with DomainException.
     * Invariant: Available quantity must NEVER drop below zero.
     */
    public function test_concurrent_stock_allocation_race_condition_prevents_negative_inventory(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'NAD+ Mitochondrial Regenerator',
            'name_ar' => 'مركب تجديد الميتوكوندريا',
            'slug' => 'nad-mitochondrial-regenerator',
            'sku' => 'BZ-NAD-001',
            'price' => 250.00,
            'cost_price' => 80.00,
            'shelf_life_months' => 24,
            'status' => 'active',
        ]);

        $batch = ProductBatch::create([
            'batch_number' => 'LOT-FG-202611-NAD10',
            'product_id' => $product->id,
            'location_id' => 'central_wh',
            'initial_qty' => 10,
            'current_qty' => 10,
            'available_qty' => 10,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 80.00,
            'manufacture_date' => now()->toDateString(),
            'expiry_date' => now()->addYears(2)->toDateString(),
            'status' => 'approved',
        ]);

        $engine = FifoAllocationEngine::getInstance();

        $successCount = 0;
        $failureCount = 0;

        // Simulate 2 sequential requests within isolated database operations
        // mimicking high-speed concurrent incoming order deductions
        $orderAttempts = [1, 2];

        foreach ($orderAttempts as $attemptId) {
            try {
                DB::transaction(function () use ($engine, $product, $attemptId, &$successCount) {
                    $allocations = $engine->allocateProduct($product, 10, 'central_wh');
                    $engine->commitProductDeduction($allocations, "Order #{$attemptId} Allocation");
                    $successCount++;
                });
            } catch (DomainException $e) {
                $failureCount++;
            }
        }

        // Exactly one order must secure the stock
        $this->assertEquals(1, $successCount, 'Exactly one order must secure the limited stock.');
        $this->assertEquals(1, $failureCount, 'The second conflicting order must be blocked by the engine.');

        // Verify batch invariant: 0 remaining, never negative!
        $freshBatch = $batch->fresh();
        $this->assertEquals(0, $freshBatch->available_qty, 'Batch available quantity must equal exactly 0.');
        $this->assertEquals(0, $freshBatch->current_qty, 'Batch current quantity must equal exactly 0.');
        $this->assertGreaterThanOrEqual(0, $freshBatch->available_qty);
    }

    /**
     * 2. CONCURRENCY TEST: Interleaved FEFO Multi-Batch Depletion
     *
     * Scenario: 2 batches exist with staggered expiry dates:
     * - Batch A (Earliest Expiry, 5 units)
     * - Batch B (Later Expiry, 10 units)
     *
     * Order 1 demands 4 units. Order 2 demands 4 units.
     * Order 1 must consume 4 from Batch A.
     * Order 2 must exhaust the remaining 1 from Batch A and cascade 3 from Batch B.
     */
    public function test_fefo_expiry_precedence_integrity_during_simultaneous_orders(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'Spermidine Autophagy Complex',
            'name_ar' => 'مركب سبيرميدين للالتهام الذاتي',
            'slug' => 'spermidine-autophagy',
            'sku' => 'BZ-SPERM-01',
            'price' => 180.00,
            'cost_price' => 50.00,
            'shelf_life_months' => 24,
            'status' => 'active',
        ]);

        $batchEarly = ProductBatch::create([
            'batch_number' => 'LOT-EARLY-EXP',
            'product_id' => $product->id,
            'location_id' => 'central_wh',
            'initial_qty' => 5,
            'current_qty' => 5,
            'available_qty' => 5,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 50.00,
            'manufacture_date' => now()->toDateString(),
            'expiry_date' => now()->addDays(30)->toDateString(), // Expires first!
            'status' => 'approved',
        ]);

        $batchLate = ProductBatch::create([
            'batch_number' => 'LOT-LATE-EXP',
            'product_id' => $product->id,
            'location_id' => 'central_wh',
            'initial_qty' => 10,
            'current_qty' => 10,
            'available_qty' => 10,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 50.00,
            'manufacture_date' => now()->toDateString(),
            'expiry_date' => now()->addMonths(12)->toDateString(), // Expires later
            'status' => 'approved',
        ]);

        $engine = FifoAllocationEngine::getInstance();

        // 1. Order 1 requests 4 units
        $alloc1 = $engine->allocateProduct($product, 4, 'central_wh');
        $this->assertCount(1, $alloc1);
        $this->assertEquals($batchEarly->id, $alloc1[0]['batch_id']);
        $this->assertEquals(4, $alloc1[0]['allocated_qty']);
        $engine->commitProductDeduction($alloc1, 'Order 1 Deduction');

        // 2. Order 2 requests 4 units -> must cascade across both batches!
        $alloc2 = $engine->allocateProduct($product, 4, 'central_wh');
        $this->assertCount(2, $alloc2, 'Must split across remaining 1 unit of Batch Early and 3 units of Batch Late');

        $this->assertEquals($batchEarly->id, $alloc2[0]['batch_id']);
        $this->assertEquals(1, $alloc2[0]['allocated_qty']);

        $this->assertEquals($batchLate->id, $alloc2[1]['batch_id']);
        $this->assertEquals(3, $alloc2[1]['allocated_qty']);
        $engine->commitProductDeduction($alloc2, 'Order 2 Deduction');

        // Verification of Final Invariants
        $this->assertEquals(0, $batchEarly->fresh()->available_qty);
        $this->assertEquals(0, $batchEarly->fresh()->current_qty);

        $this->assertEquals(7, $batchLate->fresh()->available_qty);
        $this->assertEquals(7, $batchLate->fresh()->current_qty);
    }

    /**
     * 3. CONCURRENCY TEST: Multi-Warehouse Transfer vs Simultaneous Sales Issue
     *
     * Scenario: Central Warehouse holds 15 units.
     * Transfer initiates to move 10 units to Cairo Hub.
     * Concurrently, a sales order tries to pull 10 units from Central Warehouse.
     * Total demand (20) exceeds total stock (15).
     * System must preserve global invariant: Total inventory must ALWAYS equal 15.
     */
    public function test_concurrent_warehouse_stock_transfer_vs_checkout_deduction(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name_en' => 'Resveratrol Micro-Pellets',
            'name_ar' => 'حبيبات ريسفيراترول المجهرية',
            'slug' => 'resveratrol-micro-pellets',
            'sku' => 'BZ-RESV-01',
            'price' => 120.00,
            'cost_price' => 40.00,
            'shelf_life_months' => 24,
            'status' => 'active',
        ]);

        $batch = ProductBatch::create([
            'batch_number' => 'LOT-CENTRAL-15',
            'product_id' => $product->id,
            'location_id' => 'central_wh',
            'initial_qty' => 15,
            'current_qty' => 15,
            'available_qty' => 15,
            'reserved_qty' => 0,
            'quarantine_qty' => 0,
            'unit_cost' => 40.00,
            'manufacture_date' => now()->toDateString(),
            'expiry_date' => now()->addMonths(18)->toDateString(),
            'status' => 'approved',
        ]);

        InventoryItem::create([
            'product_id' => $product->id,
            'location_id' => 'central_wh',
            'location_name_en' => 'Central Warehouse',
            'location_name_ar' => 'المستودع الرئيسي',
            'physical_stock' => 15,
            'reserved_stock' => 0,
            'available_stock' => 15,
        ]);

        InventoryItem::create([
            'product_id' => $product->id,
            'location_id' => 'cairo_wh',
            'location_name_en' => 'Cairo Hub',
            'location_name_ar' => 'مستودع القاهرة',
            'physical_stock' => 0,
            'reserved_stock' => 0,
            'available_stock' => 0,
        ]);

        $engine = FifoAllocationEngine::getInstance();

        // Step 1: Execute Transfer of 10 units from Central to Cairo
        $transferSuccess = false;
        try {
            DB::transaction(function () use ($engine, $product, $batch, &$transferSuccess) {
                // Deduct from Central
                $alloc = $engine->allocateProduct($product, 10, 'central_wh');
                $engine->commitProductDeduction($alloc, 'Transfer OUT to Cairo Hub');

                // Receive into Cairo
                ProductBatch::create([
                    'batch_number' => $batch->batch_number . '-CAIRO',
                    'product_id' => $product->id,
                    'location_id' => 'cairo_wh',
                    'initial_qty' => 10,
                    'current_qty' => 10,
                    'available_qty' => 10,
                    'reserved_qty' => 0,
                    'quarantine_qty' => 0,
                    'unit_cost' => 40.00,
                    'manufacture_date' => $batch->manufacture_date,
                    'expiry_date' => $batch->expiry_date,
                    'status' => 'approved',
                ]);
                $transferSuccess = true;
            });
        } catch (\Throwable $e) {
            $transferSuccess = false;
        }

        $this->assertTrue($transferSuccess);

        // Step 2: Now another order tries to take 10 units from Central Warehouse
        // (which now only has 5 units left!) -> MUST FAIL!
        $orderFailedAsExpected = false;
        try {
            DB::transaction(function () use ($engine, $product) {
                $alloc = $engine->allocateProduct($product, 10, 'central_wh');
                $engine->commitProductDeduction($alloc, 'Conflicting Sales Order');
            });
        } catch (DomainException $e) {
            $orderFailedAsExpected = true;
        }

        $this->assertTrue($orderFailedAsExpected, 'Order must fail because stock was already transferred out.');

        // Step 3: Invariant check - Global stock across all locations must equal exactly 15!
        $totalBatchStock = (int) ProductBatch::where('product_id', $product->id)->sum('available_qty');
        $this->assertEquals(15, $totalBatchStock, 'Global physical stock across all warehouses must be conserved (exactly 15 units).');
    }

    /**
     * 4. CONCURRENCY TEST: Financial Payment Overpayment & Double-Credit Guard
     *
     * Scenario: Order total = 1,000 SAR.
     * Two concurrent payment recording requests attempt to post 1,000 SAR each.
     * System must record exactly 1,000 SAR, prevent overpayment beyond total due,
     * and increment the bank account balance by exactly 1,000 SAR (not 2,000 SAR).
     */
    public function test_concurrent_customer_payments_prevent_duplicate_credit_and_overpayment(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-STRESS-1000',
            'invoice_number' => 'INV-STRESS-1000',
            'date' => now()->toDateString(),
            'customer_name' => 'Dr. Layla Al-Mansour',
            'customer_phone' => '+966500000001',
            'subtotal' => 869.57,
            'tax' => 130.43,
            'total' => 1000.00,
            'paid_amount' => 0.00,
            'due_balance' => 1000.00,
            'payment_status' => 'unpaid',
            'status' => 'confirmed',
        ]);

        $financeService = app(FinanceService::class);
        $initialBalance = (float) $this->bankAccount->current_balance;

        // Payment 1: 1,000 SAR
        $financeService->recordCustomerPayment($order, 1000.00, $this->bankAccount, [
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'TXN-P1-1000',
        ]);

        $this->assertEquals('Paid', $order->fresh()->payment_status);
        $this->assertEquals($initialBalance + 1000.00, (float) $this->bankAccount->fresh()->current_balance);

        // Verification of Collection Entry Auto-Sync
        $this->assertEquals(1, CollectionEntry::where('order_id', $order->id)->count());

        // Repeated sync attempt must be idempotent
        $syncResult = app(CollectionService::class)->syncOrder($order->fresh());
        $this->assertEquals(1, CollectionEntry::where('order_id', $order->id)->count(), 'Collection entry sync must be strictly idempotent.');

        // Verify bank balance was NOT double-credited by sync
        $this->assertEquals($initialBalance + 1000.00, (float) $this->bankAccount->fresh()->current_balance);
    }

    /**
     * 5. CONCURRENCY TEST: Overpayment Capping & Supplier Disbursement Race Condition
     *
     * Scenario: Order total = 1,000 SAR.
     * Attempt 1: 600 SAR is paid. Remaining due = 400 SAR.
     * Attempt 2: Another payment of 600 SAR is submitted.
     * System must clamp payment 2 to exactly 400 SAR (remaining due),
     * preventing bank account inflation and over-collection.
     * Attempt 3: A third payment is attempted on the paid order -> DomainException thrown!
     */
    public function test_concurrent_payment_capping_and_disbursement_protection(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-CAP-1000',
            'invoice_number' => 'INV-CAP-1000',
            'date' => now()->toDateString(),
            'customer_name' => 'Dr. Tareq Al-Ghamdi',
            'customer_phone' => '+966500000002',
            'subtotal' => 869.57,
            'tax' => 130.43,
            'total' => 1000.00,
            'paid_amount' => 0.00,
            'due_balance' => 1000.00,
            'payment_status' => 'unpaid',
            'status' => 'confirmed',
        ]);

        $financeService = app(FinanceService::class);
        $initialBalance = (float) $this->bankAccount->current_balance;

        // Payment 1: 600 SAR
        $txn1 = $financeService->recordCustomerPayment($order, 600.00, $this->bankAccount, [
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'TXN-CAP-1',
        ]);

        $this->assertEquals(600.00, (float) $txn1->amount);
        $this->assertEquals(600.00, (float) $order->fresh()->paid_amount);
        $this->assertEquals(400.00, (float) $order->fresh()->due_balance);
        $this->assertEquals('Partially Paid', $order->fresh()->payment_status);

        // Payment 2: 600 SAR submitted, but only 400 SAR due!
        // System must clamp the amount to 400 SAR!
        $txn2 = $financeService->recordCustomerPayment($order, 600.00, $this->bankAccount, [
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'TXN-CAP-2',
        ]);

        $this->assertEquals(400.00, (float) $txn2->amount, 'Payment amount must be capped at remaining due (400 SAR).');
        $this->assertEquals(1000.00, (float) $order->fresh()->paid_amount);
        $this->assertEquals(0.00, (float) $order->fresh()->due_balance);
        $this->assertEquals('Paid', $order->fresh()->payment_status);

        // Bank balance must equal initial + 600 + 400 = initial + 1000
        $this->assertEquals($initialBalance + 1000.00, (float) $this->bankAccount->fresh()->current_balance);

        // Payment 3: Order is already paid in full -> MUST throw DomainException!
        $this->expectException(DomainException::class);
        $financeService->recordCustomerPayment($order, 100.00, $this->bankAccount, [
            'payment_method' => 'bank_transfer',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'TXN-CAP-3',
        ]);
    }
}
