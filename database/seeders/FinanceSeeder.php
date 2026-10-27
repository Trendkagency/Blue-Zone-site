<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Corporate Treasury & Bank Accounts
        $accAlrajhi = FinancialAccount::updateOrCreate(
            ['code' => 'ACC-ALRAJHI'],
            [
                'name_en' => 'Al Rajhi Main Operations Bank',
                'name_ar' => 'مصرف الراجحي - الحساب التشغيلي الرئيسي',
                'type' => 'bank',
                'account_number' => '402819200018',
                'bank_name' => 'Al Rajhi Banking Corp',
                'iban' => 'SA4480000402819200018001',
                'currency' => 'SAR',
                'initial_balance' => 450000.00,
                'current_balance' => 450000.00,
                'is_active' => true,
                'notes' => 'Primary corporate settlement and operational clearing account.',
            ]
        );

        $accSnb = FinancialAccount::updateOrCreate(
            ['code' => 'ACC-SNB'],
            [
                'name_en' => 'SNB Treasury & Investment Reserve',
                'name_ar' => 'البنك الأهلي السعودي - احتياطي الخزينة والاستثمار',
                'type' => 'bank',
                'account_number' => '1029384756',
                'bank_name' => 'Saudi National Bank (SNB)',
                'iban' => 'SA0310000010293847560002',
                'currency' => 'SAR',
                'initial_balance' => 850000.00,
                'current_balance' => 850000.00,
                'is_active' => true,
                'notes' => 'Capital reserve and high-yield operational treasury buffer.',
            ]
        );

        $accMada = FinancialAccount::updateOrCreate(
            ['code' => 'ACC-MADA'],
            [
                'name_en' => 'Corporate Mada & Online Payment Gateway',
                'name_ar' => 'حساب بوابة دفع مدى والبطاقات الإلكترونية',
                'type' => 'gateway',
                'account_number' => 'GW-MADA-8821',
                'bank_name' => 'Geidea / HyperPay Network',
                'iban' => 'SA8880000088219900018821',
                'currency' => 'SAR',
                'initial_balance' => 125400.00,
                'current_balance' => 125400.00,
                'is_active' => true,
                'notes' => 'Automated settlement clearing for online checkout and card terminal receipts.',
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
                'currency' => 'SAR',
                'initial_balance' => 30000.00,
                'current_balance' => 30000.00,
                'is_active' => true,
                'notes' => 'Cash vault for urgent pharmaceutical dispatches and emergency facility purchases.',
            ]
        );

        // 2. Expense Categories
        $categories = [
            [
                'code' => 'EXP-CLINICAL',
                'name_en' => 'Clinical Research & Laboratory Assays',
                'name_ar' => 'الأبحاث السريرية والتحاليل المخبرية',
                'color' => '#0A4F78',
            ],
            [
                'code' => 'EXP-LOGISTICS',
                'name_en' => 'Cold-Chain Logistics & Dispatch',
                'name_ar' => 'الخدمات اللوجستية والشحن المبرد',
                'color' => '#2A8FC2',
            ],
            [
                'code' => 'EXP-MARKETING',
                'name_en' => 'Brand Marketing & Digital Campaigns',
                'name_ar' => 'التسويق الرقمي وحملات التوعية',
                'color' => '#67B34A',
            ],
            [
                'code' => 'EXP-PACKAGING',
                'name_en' => 'Bio-Bottles & Eco-Packaging Materials',
                'name_ar' => 'مواد التعبئة والعبوات الحيوية المعقمة',
                'color' => '#E08E45',
            ],
            [
                'code' => 'EXP-REGULATORY',
                'name_en' => 'SFDA & ZATCA Compliance Licensing',
                'name_ar' => 'تراخيص هيئة الغذاء والدواء ومتطلبات الامتثال',
                'color' => '#7C3AED',
            ],
            [
                'code' => 'EXP-FACILITY',
                'name_en' => 'Facility Utilities & Cleanroom Operations',
                'name_ar' => 'تشغيل المرافق وغرف التعقيم cGMP',
                'color' => '#EF4444',
            ],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['code']] = ExpenseCategory::updateOrCreate(['code' => $cat['code']], $cat);
        }

        // 3. Seed Realistic Expenses
        $expensesData = [
            [
                'expense_number' => 'EXP-2026-0001',
                'category_code' => 'EXP-CLINICAL',
                'account_id' => $accAlrajhi->id,
                'title_en' => 'HPLC Purity Assay for Beta-NMN Batch 2026-01',
                'title_ar' => 'فحص النقاوة HPLC لشحنة بيتا إن إم إن المتقدمة',
                'amount' => 12500.00,
                'tax_amount' => 1875.00,
                'total_amount' => 14375.00,
                'expense_date' => now()->subDays(12)->toDateString(),
                'beneficiary_name' => 'Saudi BioTech Analytical Labs',
                'payment_method' => 'bank_transfer',
                'reference_number' => 'REF-SBT-9821',
            ],
            [
                'expense_number' => 'EXP-2026-0002',
                'category_code' => 'EXP-LOGISTICS',
                'account_id' => $accAlrajhi->id,
                'title_en' => 'Cold-Chain Dedicated Fleet Monthly Dispatch Fee',
                'title_ar' => 'رسوم أسطول النقل المبرد المخصص لشهر سبتمبر',
                'amount' => 8400.00,
                'tax_amount' => 1260.00,
                'total_amount' => 9660.00,
                'expense_date' => now()->subDays(8)->toDateString(),
                'beneficiary_name' => 'DHL Global Forwarding KSA',
                'payment_method' => 'bank_transfer',
                'reference_number' => 'INV-DHL-4402',
            ],
            [
                'expense_number' => 'EXP-2026-0003',
                'category_code' => 'EXP-MARKETING',
                'account_id' => $accMada->id,
                'title_en' => 'Longevity Summit Riyadh Booth & Keynote Sponsorship',
                'title_ar' => 'رعاية جناح قمة طول العمر بالرياض والمحاضرة العلمية',
                'amount' => 22000.00,
                'tax_amount' => 3300.00,
                'total_amount' => 25300.00,
                'expense_date' => now()->subDays(5)->toDateString(),
                'beneficiary_name' => 'Riyadh Medical Exhibitions Co.',
                'payment_method' => 'mada',
                'reference_number' => 'RMEX-88210',
            ],
            [
                'expense_number' => 'EXP-2026-0004',
                'category_code' => 'EXP-FACILITY',
                'account_id' => $accCash->id,
                'title_en' => 'Emergency HEPA Filter Maintenance for Cleanroom 2',
                'title_ar' => 'صيانة طارئة لفلاتر الهواء HEPA لغرفة التعقيم 2',
                'amount' => 3200.00,
                'tax_amount' => 480.00,
                'total_amount' => 3680.00,
                'expense_date' => now()->subDays(2)->toDateString(),
                'beneficiary_name' => 'CleanAir Environmental Services',
                'payment_method' => 'cash',
                'reference_number' => 'CA-VCH-391',
            ],
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

        // 4. Seed Realistic Purchase Invoices (Vendor Bills)
        $supplier = Supplier::first();
        $po = PurchaseOrder::first();

        if ($supplier) {
            $pInv1 = PurchaseInvoice::updateOrCreate(
                ['invoice_number' => 'PINV-2026-0001'],
                [
                    'supplier_id' => $supplier->id,
                    'purchase_order_id' => $po?->id,
                    'invoice_date' => now()->subDays(15)->toDateString(),
                    'due_date' => now()->addDays(15)->toDateString(),
                    'subtotal' => 45000.00,
                    'tax_amount' => 6750.00,
                    'grand_total' => 51750.00,
                    'paid_amount' => 25000.00,
                    'payment_status' => 'partially_paid',
                    'status' => 'approved',
                    'reference_number' => 'SUP-INV-88912',
                    'notes' => 'Bulk pharmaceutical active compounds procurement.',
                ]
            );

            PurchaseInvoiceItem::updateOrCreate(
                ['purchase_invoice_id' => $pInv1->id, 'item_name' => 'USP-Grade Pure Trans-Resveratrol 99%'],
                [
                    'quantity' => 10.0,
                    'unit_price' => 4500.00,
                    'subtotal' => 45000.00,
                    'tax_rate' => 15.00,
                    'tax_amount' => 6750.00,
                    'total' => 51750.00,
                ]
            );

            // Record Partial Supplier Payment
            FinancialTransaction::updateOrCreate(
                ['transaction_number' => 'TXN-SUP-2026-0001'],
                [
                    'type' => 'supplier_payment',
                    'direction' => 'debit',
                    'financial_account_id' => $accAlrajhi->id,
                    'purchase_invoice_id' => $pInv1->id,
                    'supplier_id' => $supplier->id,
                    'amount' => 25000.00,
                    'balance_after' => 425000.00,
                    'payment_method' => 'bank_transfer',
                    'transaction_date' => now()->subDays(5)->toDateString(),
                    'reference_number' => 'TXN-BANK-44910',
                    'receipt_voucher_number' => 'VCH-SUP-0001',
                    'notes' => 'Partial payment for vendor bill #' . $pInv1->invoice_number,
                ]
            );
        }

        // 5. Connect existing completed orders with Customer Payment transactions
        $paidOrders = Order::where('payment_status', 'Paid')->limit(5)->get();
        foreach ($paidOrders as $idx => $ord) {
            FinancialTransaction::updateOrCreate(
                ['transaction_number' => 'TXN-ORD-' . $ord->id],
                [
                    'type' => 'customer_payment',
                    'direction' => 'credit',
                    'financial_account_id' => $accMada->id,
                    'order_id' => $ord->id,
                    'customer_id' => $ord->customer_id,
                    'amount' => $ord->total,
                    'balance_after' => 125400.00 + $ord->total,
                    'payment_method' => $ord->payment_method ?? 'mada',
                    'transaction_date' => $ord->date ?? now()->toDateString(),
                    'reference_number' => 'ORD-PAY-' . $ord->order_number,
                    'receipt_voucher_number' => 'RCP-ORD-' . $ord->id,
                    'notes' => 'Customer payment for Order #' . $ord->order_number,
                ]
            );
        }
    }
}
