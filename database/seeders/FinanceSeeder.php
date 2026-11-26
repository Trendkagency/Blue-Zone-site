<?php

namespace Database\Seeders;

use App\Models\CollectionEntry;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\PaymentEntry;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $sysCurrency = Setting::get('currency', 'EGP');
        $adminUser = User::where('email', 'admin@bluezone.com')->first() ?? User::first();

        // 1. Corporate Treasury & Bank Accounts
        $accAlrajhi = FinancialAccount::updateOrCreate(
            ['code' => 'ACC-ALRAJHI'],
            [
                'name_en' => 'CIB / Al Rajhi Main Operations Bank',
                'name_ar' => 'الحساب البنكي التشغيلي الرئيسي',
                'type' => 'bank',
                'account_number' => '402819200018',
                'bank_name' => 'Commercial Operations Bank',
                'iban' => 'EG440010000402819200018001',
                'currency' => $sysCurrency,
                'initial_balance' => 450000.00,
                'current_balance' => 450000.00,
                'is_active' => true,
                'notes' => 'Primary corporate settlement and operational clearing account.',
            ]
        );

        FinancialAccount::updateOrCreate(
            ['code' => 'ACC-SNB'],
            [
                'name_en' => 'Treasury & Capital Reserve Account',
                'name_ar' => 'حساب احتياطي الخزينة والاستثمار',
                'type' => 'bank',
                'account_number' => '1029384756',
                'bank_name' => 'Corporate Reserve Bank',
                'iban' => 'EG0310000010293847560002',
                'currency' => $sysCurrency,
                'initial_balance' => 850000.00,
                'current_balance' => 850000.00,
                'is_active' => true,
                'notes' => 'Capital reserve and high-yield operational treasury buffer.',
            ]
        );

        $accMada = FinancialAccount::updateOrCreate(
            ['code' => 'ACC-MADA'],
            [
                'name_en' => 'Corporate Online Payment Gateway',
                'name_ar' => 'حساب بوابة الدفع والبطاقات الإلكترونية',
                'type' => 'gateway',
                'account_number' => 'GW-PAY-8821',
                'bank_name' => 'Stripe / Paymob Clearing Network',
                'iban' => 'EG8880000088219900018821',
                'currency' => $sysCurrency,
                'initial_balance' => 125400.00,
                'current_balance' => 125400.00,
                'is_active' => true,
                'notes' => 'Automated settlement clearing for online checkout and card receipts.',
            ]
        );

        $accCash = FinancialAccount::updateOrCreate(
            ['code' => 'ACC-CASH'],
            [
                'name_en' => 'Central HQ Petty Cash Vault',
                'name_ar' => 'خزينة العهد النقدية للمقر الرئيسي',
                'type' => 'cash',
                'account_number' => 'VAULT-01',
                'bank_name' => 'Cash in Vault',
                'iban' => null,
                'currency' => $sysCurrency,
                'initial_balance' => 35000.00,
                'current_balance' => 35000.00,
                'is_active' => true,
                'notes' => 'Cash vault for urgent dispatches and emergency cleanroom purchases.',
            ]
        );

        // 2. Expense Categories
        $categories = [
            ['code' => 'EXP-CLINICAL',   'name_en' => 'Clinical Research & Laboratory Assays',     'name_ar' => 'الأبحاث السريرية والتحاليل المخبرية',        'color' => '#0A4F78'],
            ['code' => 'EXP-LOGISTICS',  'name_en' => 'Cold-Chain Logistics & Dispatch',           'name_ar' => 'الخدمات اللوجستية والشحن المبرد',            'color' => '#2A8FC2'],
            ['code' => 'EXP-MARKETING',  'name_en' => 'Brand Marketing & Digital Campaigns',       'name_ar' => 'التسويق الرقمي وحملات التوعية',              'color' => '#67B34A'],
            ['code' => 'EXP-PACKAGING',  'name_en' => 'Bio-Bottles & Eco-Packaging Materials',     'name_ar' => 'مواد التعبئة والعبوات الحيوية المعقمة',       'color' => '#E08E45'],
            ['code' => 'EXP-REGULATORY', 'name_en' => 'Regulatory & Quality Compliance Licensing', 'name_ar' => 'تراخيص هيئة الدواء ومتطلبات الامتثال',       'color' => '#7C3AED'],
            ['code' => 'EXP-FACILITY',   'name_en' => 'Facility Utilities & Cleanroom Operations', 'name_ar' => 'تشغيل المرافق وغرف التعقيم cGMP',            'color' => '#EF4444'],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['code']] = ExpenseCategory::updateOrCreate(['code' => $cat['code']], $cat);
        }

        // 3. Operating Expenses
        $expensesData = [
            ['expense_number' => 'EXP-2026-0001', 'category_code' => 'EXP-CLINICAL',  'account_id' => $accAlrajhi->id, 'title_en' => 'HPLC Purity Assay for Micronized PEA Batch',   'title_ar' => 'فحص النقاوة HPLC لشحنة PEA الميكرونية',   'amount' => 12500.00, 'tax_amount' => 1875.00, 'total_amount' => 14375.00, 'expense_date' => now()->subDays(12)->toDateString(), 'beneficiary_name' => 'BioTech Analytical Labs',       'payment_method' => 'bank_transfer', 'reference_number' => 'REF-SBT-9821'],
            ['expense_number' => 'EXP-2026-0002', 'category_code' => 'EXP-LOGISTICS', 'account_id' => $accAlrajhi->id, 'title_en' => 'Cold-Chain Dedicated Fleet Monthly Dispatch',   'title_ar' => 'رسوم أسطول النقل المبرد المخصص',           'amount' => 8400.00,  'tax_amount' => 1260.00, 'total_amount' => 9660.00,  'expense_date' => now()->subDays(8)->toDateString(),  'beneficiary_name' => 'DHL Global Cold-Chain',         'payment_method' => 'bank_transfer', 'reference_number' => 'INV-DHL-4402'],
            ['expense_number' => 'EXP-2026-0003', 'category_code' => 'EXP-MARKETING', 'account_id' => $accMada->id,    'title_en' => 'Medical Longevity Symposium Booth & Print',     'title_ar' => 'رعاية مؤتمر الطب التجديدي والمطبوعات',       'amount' => 18000.00, 'tax_amount' => 2700.00, 'total_amount' => 20700.00, 'expense_date' => now()->subDays(5)->toDateString(),  'beneficiary_name' => 'MedExpo Events Co.',            'payment_method' => 'mada',          'reference_number' => 'RMEX-88210'],
            ['expense_number' => 'EXP-2026-0004', 'category_code' => 'EXP-FACILITY',  'account_id' => $accCash->id,    'title_en' => 'Cleanroom 2 HEPA Filter Calibration & Seal',    'title_ar' => 'صيانة ومعايرة فلاتر HEPA لغرفة التعقيم 2', 'amount' => 3200.00,  'tax_amount' => 480.00,  'total_amount' => 3680.00,  'expense_date' => now()->subDays(2)->toDateString(),  'beneficiary_name' => 'CleanAir Environmental Services', 'payment_method' => 'cash',          'reference_number' => 'CA-VCH-391'],
        ];

        foreach ($expensesData as $eData) {
            $cat = $catModels[$eData['category_code']];
            $exp = Expense::updateOrCreate(
                ['expense_number' => $eData['expense_number']],
                [
                    'expense_category_id' => $cat->id,
                    'financial_account_id' => $eData['account_id'],
                    'title_en' => $eData['title_en'],
                    'title_ar' => $eData['title_ar'],
                    'amount' => $eData['amount'],
                    'tax_amount' => $eData['tax_amount'],
                    'total_amount' => $eData['total_amount'],
                    'expense_date' => $eData['expense_date'],
                    'payment_method' => $eData['payment_method'],
                    'beneficiary_name' => $eData['beneficiary_name'],
                    'reference_number' => $eData['reference_number'],
                    'status' => 'approved',
                ]
            );

            FinancialTransaction::updateOrCreate(
                ['transaction_number' => 'TXN-' . str_replace('EXP-', 'E', $exp->expense_number)],
                [
                    'type' => 'expense',
                    'direction' => 'debit',
                    'financial_account_id' => $exp->financial_account_id,
                    'expense_id' => $exp->id,
                    'amount' => $exp->total_amount,
                    'balance_after' => 450000.00 - $exp->total_amount,
                    'payment_method' => $exp->payment_method,
                    'transaction_date' => $exp->expense_date,
                    'reference_number' => $exp->reference_number,
                    'receipt_voucher_number' => 'VCH-' . $exp->expense_number,
                    'notes' => 'Expense: ' . $exp->title_en,
                ]
            );
        }

        // 4. Purchase Invoices Linked to Real Batches & Expiry Dates (Raw Material + Finished Goods)
        $supplier = Supplier::where('code', 'SUP-CHROMA-01')->first() ?? Supplier::first();
        $po = PurchaseOrder::where('po_number', 'PO-2026-0001')->first() ?? PurchaseOrder::first();
        $rmBatch = RawMaterialBatch::first();
        $rm = $rmBatch?->rawMaterial ?? RawMaterial::first();
        $fgBatch = ProductBatch::where('location_id', 'central_wh')->first() ?? ProductBatch::first();
        $fgProd = $fgBatch?->product ?? Product::first();

        if ($supplier) {
            $pInv1 = PurchaseInvoice::updateOrCreate(
                ['invoice_number' => 'PINV-2026-0001'],
                [
                    'supplier_id' => $supplier->id,
                    'purchase_order_id' => $po?->id,
                    'invoice_date' => now()->subDays(30)->toDateString(),
                    'due_date' => now()->addDays(15)->toDateString(),
                    'subtotal' => 127000.00,
                    'tax_amount' => 19050.00,
                    'grand_total' => 146050.00,
                    'paid_amount' => 75000.00,
                    'payment_status' => 'partially_paid',
                    'status' => 'approved',
                    'reference_number' => 'SUP-INV-88912',
                    'notes' => 'Matched PO-2026-0001 procurement invoice for active raw ingredient.',
                ]
            );

            PurchaseInvoiceItem::updateOrCreate(
                ['purchase_invoice_id' => $pInv1->id, 'item_name' => $rm?->name_en ?? 'Micronized PEA 99.5%'],
                [
                    'item_type' => 'raw_material',
                    'raw_material_id' => $rm?->id,
                    'raw_material_batch_id' => $rmBatch?->id,
                    'batch_number' => $rmBatch?->batch_number,
                    'expiry_date' => $rmBatch?->expiry_date,
                    'quantity' => 20.0,
                    'unit_price' => 6350.00,
                    'subtotal' => 127000.00,
                    'tax_rate' => 15.00,
                    'tax_amount' => 19050.00,
                    'total' => 146050.00,
                ]
            );

            FinancialTransaction::updateOrCreate(
                ['transaction_number' => 'TXN-SUP-2026-0001'],
                [
                    'type' => 'supplier_payment',
                    'direction' => 'debit',
                    'financial_account_id' => $accAlrajhi->id,
                    'purchase_invoice_id' => $pInv1->id,
                    'supplier_id' => $supplier->id,
                    'amount' => 75000.00,
                    'balance_after' => 375000.00,
                    'payment_method' => 'bank_transfer',
                    'transaction_date' => now()->subDays(10)->toDateString(),
                    'reference_number' => 'TXN-BANK-44910',
                    'receipt_voucher_number' => 'VCH-SUP-0001',
                    'notes' => 'Partial settlement for vendor bill ' . $pInv1->invoice_number,
                ]
            );

            if ($fgProd && $fgBatch) {
                $pInv2 = PurchaseInvoice::updateOrCreate(
                    ['invoice_number' => 'PINV-2026-0002'],
                    [
                        'supplier_id' => $supplier->id,
                        'invoice_date' => now()->subDays(12)->toDateString(),
                        'due_date' => now()->addDays(18)->toDateString(),
                        'subtotal' => 2500.00,
                        'tax_amount' => 375.00,
                        'grand_total' => 2875.00,
                        'paid_amount' => 2875.00,
                        'payment_status' => 'paid',
                        'status' => 'approved',
                        'reference_number' => 'SUP-FG-1091',
                        'notes' => 'Direct Finished Formulation packaging & fill lot invoice.',
                    ]
                );

                PurchaseInvoiceItem::updateOrCreate(
                    ['purchase_invoice_id' => $pInv2->id, 'item_name' => $fgProd->name_en],
                    [
                        'item_type' => 'product',
                        'product_id' => $fgProd->id,
                        'product_batch_id' => $fgBatch->id,
                        'batch_number' => $fgBatch->batch_number,
                        'expiry_date' => $fgBatch->expiry_date,
                        'quantity' => 100.0,
                        'unit_price' => 25.00,
                        'subtotal' => 2500.00,
                        'tax_rate' => 15.00,
                        'tax_amount' => 375.00,
                        'total' => 2875.00,
                    ]
                );
            }

            PaymentEntry::updateOrCreate(
                ['payment_number' => 'PAY-2026-0001'],
                [
                    'entry_date' => now()->subDays(10)->toDateString(),
                    'category' => 'purchase',
                    'description' => 'Raw material procurement payment against PINV-2026-0001',
                    'payee_type' => 'supplier',
                    'supplier_id' => $supplier->id,
                    'purchase_invoice_id' => $pInv1->id,
                    'supplier_code' => $supplier->code,
                    'payee_name' => $supplier->name_en,
                    'amount_excl_vat' => 65217.39,
                    'vat_rate' => 15.00,
                    'vat_amount' => 9782.61,
                    'gross_amount' => 75000.00,
                    'financial_account_id' => $accAlrajhi->id,
                    'payment_method' => 'bank_transfer',
                    'status' => 'posted',
                    'cleared_at' => now()->subDays(9),
                    'created_by' => $adminUser?->id,
                ]
            );
        }

        // 5. Connect ALL Paid Orders cleanly to FinancialTransactions & CollectionEntries
        $runningGateway = 125400.00;
        $paidOrders = Order::where('payment_status', 'Paid')->orderBy('id')->get();
        foreach ($paidOrders as $idx => $ord) {
            $runningGateway += (float) $ord->total;
            FinancialTransaction::updateOrCreate(
                ['transaction_number' => 'TXN-ORD-' . str_pad((string) $ord->id, 4, '0', STR_PAD_LEFT)],
                [
                    'type' => 'customer_payment',
                    'direction' => 'credit',
                    'financial_account_id' => $accMada->id,
                    'order_id' => $ord->id,
                    'customer_id' => $ord->customer_id,
                    'amount' => $ord->total,
                    'balance_after' => $runningGateway,
                    'payment_method' => $ord->payment_method ?? 'mada',
                    'transaction_date' => $ord->date ?? now()->toDateString(),
                    'reference_number' => $ord->payment_transaction_id ?? ('ORD-PAY-' . $ord->order_number),
                    'receipt_voucher_number' => 'RCP-ORD-' . $ord->id,
                    'notes' => 'Settled receipt for Sales Order #' . $ord->order_number . ' (' . $ord->invoice_number . ')',
                ]
            );

            CollectionEntry::updateOrCreate(
                ['collection_number' => 'COL-ORD-' . str_pad((string) $ord->id, 4, '0', STR_PAD_LEFT)],
                [
                    'entry_date' => $ord->date ?? now()->toDateString(),
                    'category' => 'online_sales_auto',
                    'description' => 'Sales collection for Invoice ' . $ord->invoice_number,
                    'source_type' => 'customer',
                    'customer_id' => $ord->customer_id,
                    'customer_source' => $ord->customer_name,
                    'amount_excl_vat' => max(0, (float) $ord->subtotal - (float) $ord->discount),
                    'vat_rate' => 15.00,
                    'vat_amount' => (float) $ord->tax,
                    'gross_amount' => (float) $ord->total,
                    'financial_account_id' => $accMada->id,
                    'payment_method' => 'online_gateway',
                    'order_id' => $ord->id,
                    'is_auto_synced' => true,
                    'status' => 'posted',
                    'created_by' => $adminUser?->id,
                ]
            );
        }
    }
}
