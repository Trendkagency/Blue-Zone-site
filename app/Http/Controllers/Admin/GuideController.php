<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\CrmLead;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\GoodsReceipt;
use App\Models\HeroSlide;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\PurchaseOrder;
use App\Models\QcInspection;
use App\Models\RawMaterial;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuideController extends Controller
{
    /**
     * Display the comprehensive interactive System Guide & Knowledge Hub.
     */
    public function index(Request $request): View
    {
        // Real-time system metrics to enrich the guide across all system domains
        $systemMetrics = [
            'products_count' => Product::count(),
            'batches_count' => ProductBatch::count(),
            'suppliers_count' => Supplier::count(),
            'warehouses_count' => Location::count(),
            'purchase_orders_count' => PurchaseOrder::count(),
            'goods_receipts_count' => GoodsReceipt::count(),
            'work_orders_count' => WorkOrder::count(),
            'raw_materials_count' => RawMaterial::count(),
            'qc_inspections_count' => QcInspection::count(),
            'orders_count' => Order::count(),
            'customers_count' => Customer::count(),
            'financial_accounts_count' => FinancialAccount::count(),
            'financial_transactions_count' => FinancialTransaction::count(),
            'employees_count' => Employee::count(),
            'departments_count' => Department::count(),
            'attendance_count' => AttendanceRecord::count(),
            'crm_leads_count' => CrmLead::count(),
            'roles_count' => Role::count(),
            'users_count' => User::count(),
            'hero_slides_count' => HeroSlide::count(),
        ];

        $guideService = \App\Services\GuideDataService::getInstance();
        $overviewArchitecture = $guideService->getOverviewArchitecture();
        $guideModules = $guideService->getModules($systemMetrics);
        $catalogRows = $guideService->getCatalogRows($systemMetrics);

        return view('admin.guide.index', compact(
            'systemMetrics',
            'overviewArchitecture',
            'guideModules',
            'catalogRows'
        ));
    }
}
