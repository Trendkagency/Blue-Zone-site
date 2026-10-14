<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PurchaseOrderStatus;
use App\Enums\QcVerdict;
use App\Enums\RawMaterialBatchStatus;
use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\Location;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\QcInspection;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\Supplier;
use App\Models\WorkOrder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplyChainReportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Report 1: Vendor Quality Scorecard & Supplier Performance Report
     */
    public function vendorScorecard(Request $request): View|StreamedResponse
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = Supplier::with(['country', 'purchaseOrders', 'batches'])
            ->withCount(['purchaseOrders', 'batches'])
            ->get()
            ->each(function ($supplier) {
                $totalBatches = $supplier->batches->count();
                $approvedBatches = $supplier->batches->whereIn('status', [RawMaterialBatchStatus::Approved, RawMaterialBatchStatus::Exhausted])->count();
                $supplier->computed_qc_pass_rate = $totalBatches > 0 ? round(($approvedBatches / $totalBatches) * 100, 1) : 100.0;

                $supplier->total_spend = (float) $supplier->purchaseOrders->where('status', PurchaseOrderStatus::Received)->sum('grand_total');

                $grns = GoodsReceipt::with('purchaseOrder')->where('supplier_id', $supplier->id)->get();
                $onTimeCount = 0;
                foreach ($grns as $grn) {
                    if ($grn->purchaseOrder && $grn->purchaseOrder->expected_date) {
                        if ($grn->received_date <= $grn->purchaseOrder->expected_date) {
                            $onTimeCount++;
                        }
                    } else {
                        $onTimeCount++;
                    }
                }
                $supplier->computed_on_time_rate = $grns->count() > 0 ? round(($onTimeCount / $grns->count()) * 100, 1) : 100.0;
            });

        if ($request->query('export') === 'xlsx') {
            return $this->exportSpreadsheet(
                'vendor_scorecard_report',
                ['Code', 'Name (EN)', 'Name (AR)', 'Country', 'Rating', 'Total POs', 'Total Spend (AED)', 'Lead Time (Days)', 'QC Pass %', 'On-Time %', 'Active'],
                $suppliers->map(fn ($s) => [
                    $s->code, $s->name_en, $s->name_ar, $s->country?->name_en ?? 'N/A', $s->rating, $s->purchase_orders_count, $s->total_spend, $s->lead_time_days, $s->computed_qc_pass_rate.'%', $s->computed_on_time_rate.'%', $s->is_active ? 'Yes' : 'No',
                ])->toArray()
            );
        }

        return view('admin.supply_chain.reports.vendor_scorecard', compact('suppliers'));
    }

    /**
     * Report 2: Raw Material Stock Valuation & FEFO Expiry Risk Report
     */
    public function valuationExpiry(Request $request): View|StreamedResponse
    {
        $this->authorize('viewAny', RawMaterialBatch::class);

        $query = RawMaterialBatch::with(['rawMaterial', 'supplier', 'location'])
            ->where('current_qty', '>', 0);

        if ($locationId = $request->query('location_id')) {
            $query->where('location_id', $locationId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $batches = $query->orderBy('expiry_date', 'asc')->get()->map(function ($batch) {
            $daysLeft = now()->diffInDays($batch->expiry_date, false);
            $totalVal = round((float) $batch->current_qty * (float) $batch->unit_cost, 2);

            $risk = 'Safe';
            if ($daysLeft < 0) {
                $risk = 'Expired';
            } elseif ($daysLeft <= 30) {
                $risk = 'Critical (<= 30d)';
            } elseif ($daysLeft <= 60) {
                $risk = 'Warning (<= 60d)';
            } elseif ($daysLeft <= 90) {
                $risk = 'Notice (<= 90d)';
            }

            return [
                'batch_number' => $batch->batch_number,
                'supplier_lot' => $batch->supplier_lot,
                'material_code' => $batch->rawMaterial->code,
                'material_name' => $batch->rawMaterial->name_en,
                'location' => $batch->location->name_en ?? $batch->location_id,
                'storage_bin' => $batch->storage_bin ?? 'N/A',
                'current_qty' => $batch->current_qty,
                'uom' => $batch->rawMaterial->unit_of_measure->value,
                'unit_cost' => $batch->unit_cost,
                'total_value' => $totalVal,
                'expiry_date' => $batch->expiry_date->toDateString(),
                'days_left' => $daysLeft,
                'status' => $batch->status->value,
                'risk_status' => $risk,
            ];
        });

        // Summary KPIs
        $totalCapital = $batches->sum('total_value');
        $risk30 = $batches->where('days_left', '<=', 30)->where('days_left', '>=', 0)->sum('total_value');
        $risk60 = $batches->where('days_left', '<=', 60)->where('days_left', '>', 30)->sum('total_value');
        $risk90 = $batches->where('days_left', '<=', 90)->where('days_left', '>', 60)->sum('total_value');
        $expiredVal = $batches->where('days_left', '<', 0)->sum('total_value');

        if ($request->query('export') === 'xlsx') {
            return $this->exportSpreadsheet(
                'raw_material_valuation_expiry',
                ['Batch #', 'Supplier Lot', 'Material Code', 'Material Name', 'Location', 'Bin', 'Qty', 'UOM', 'Unit Cost', 'Total Value (AED)', 'Expiry Date', 'Days Left', 'Status', 'Risk Level'],
                $batches->map(fn ($b) => array_values($b))->toArray()
            );
        }

        $totalValuation = $totalCapital;
        $locations = Location::where('is_active', true)->get();

        return view('admin.supply_chain.reports.valuation_expiry', compact('batches', 'totalCapital', 'totalValuation', 'risk30', 'risk60', 'risk90', 'expiredVal', 'locations'));
    }

    /**
     * Report 3: Procurement & Open Purchase Order Aging Report
     */
    public function poAging(Request $request): View|StreamedResponse
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        $orders = PurchaseOrder::with(['supplier', 'destinationLocation', 'items'])
            ->whereNotIn('status', [PurchaseOrderStatus::Received, PurchaseOrderStatus::Cancelled])
            ->orderBy('order_date', 'asc')
            ->get();

        $bucket0_15 = $orders->filter(fn ($po) => $po->order_date && $po->order_date->diffInDays(now()) <= 15);
        $bucket16_30 = $orders->filter(fn ($po) => $po->order_date && $po->order_date->diffInDays(now()) > 15 && $po->order_date->diffInDays(now()) <= 30);
        $bucket31_60 = $orders->filter(fn ($po) => $po->order_date && $po->order_date->diffInDays(now()) > 30 && $po->order_date->diffInDays(now()) <= 60);
        $bucket60_plus = $orders->filter(fn ($po) => $po->order_date && $po->order_date->diffInDays(now()) > 60);

        if ($request->query('export') === 'xlsx') {
            return $this->exportSpreadsheet(
                'purchase_order_aging_report',
                ['PO #', 'Supplier', 'Order Date', 'Expected Date', 'Age (Days)', 'Items', 'Total Cost', 'Status'],
                $orders->map(fn ($po) => [
                    $po->po_number,
                    $po->supplier?->name_en ?? 'N/A',
                    $po->order_date?->toDateString(),
                    $po->expected_date?->toDateString() ?? 'N/A',
                    $po->order_date ? (int) $po->order_date->diffInDays(now()) : 0,
                    $po->items->count(),
                    $po->grand_total,
                    $po->status->label(),
                ])->toArray()
            );
        }

        return view('admin.supply_chain.reports.po_aging', compact('orders', 'bucket0_15', 'bucket16_30', 'bucket31_60', 'bucket60_plus'));
    }

    /**
     * Report 4: Quality Control & Quarantine Clearance Log
     */
    public function qcLog(Request $request): View|StreamedResponse
    {
        $this->authorize('viewAny', QcInspection::class);

        $inspections = QcInspection::with(['batch.rawMaterial', 'batch.supplier', 'inspector', 'verifier', 'parameters'])
            ->orderBy('tested_at', 'desc')
            ->get();

        $totalTests = $inspections->count();
        $passedTests = $inspections->where('verdict', QcVerdict::Passed)->count();
        $passRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 100.0;

        if ($request->query('export') === 'xlsx') {
            return $this->exportSpreadsheet(
                'qc_inspection_log',
                ['QC Code', 'Batch #', 'Material', 'Supplier', 'Inspector', 'Verifier', 'Verdict', 'Tested At', 'Verified At'],
                $inspections->map(fn ($i) => [
                    $i->inspection_code, $i->batch->batch_number, $i->batch->rawMaterial->name_en, $i->batch->supplier->name_en, $i->inspector->name, $i->verifier?->name ?? 'Pending', $i->verdict->value, $i->tested_at?->toDateTimeString(), $i->verified_at?->toDateTimeString(),
                ])->toArray()
            );
        }

        return view('admin.supply_chain.reports.qc_log', compact('inspections', 'totalTests', 'passedTests', 'passRate'));
    }

    /**
     * Report 5: Material Requirements Planning (MRP) & Stockout Forecast
     */
    public function mrpForecast(Request $request): View|StreamedResponse
    {
        $this->authorize('viewAny', RawMaterial::class);

        $materials = RawMaterial::with(['defaultSupplier', 'batches'])
            ->where('is_active', true)
            ->get();

        if ($request->query('export') === 'xlsx') {
            $exportRows = $materials->map(function ($m) {
                $available = (float) $m->batches->where('status', RawMaterialBatchStatus::Approved)->sum('available_qty');
                $reorderPoint = (float) $m->min_reorder_qty;
                $urgent = $available <= $reorderPoint;
                $suggested = $urgent ? max(0.0, round(($reorderPoint * 2) - $available, 4)) : 0.0;

                return [
                    $m->code,
                    $m->name_en,
                    $m->category->value,
                    (float) $m->batches->sum('current_qty'),
                    (float) $m->batches->sum('reserved_qty'),
                    $available,
                    $m->unit_of_measure->value,
                    $reorderPoint,
                    $urgent ? 'YES' : 'NO',
                    $suggested,
                    $m->defaultSupplier?->name_en ?? 'N/A',
                ];
            });

            return $this->exportSpreadsheet(
                'mrp_stockout_forecast',
                ['Code', 'Name', 'Category', 'On-Hand', 'Reserved', 'Available', 'UOM', 'Reorder Point', 'Urgent Reorder?', 'Suggested Reorder Qty', 'Supplier'],
                $exportRows->toArray()
            );
        }

        return view('admin.supply_chain.reports.mrp_forecast', compact('materials'));
    }

    /**
     * Report 6: Production Consumption & Scrap Variance Report
     */
    public function scrapVariance(Request $request): View|StreamedResponse
    {
        $this->authorize('viewAny', WorkOrder::class);

        $workOrders = WorkOrder::with(['product.recipeItems', 'consumptions.batch.rawMaterial', 'consumptions.operator'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();

        $varianceRows = [];
        foreach ($workOrders as $wo) {
            foreach ($wo->product->recipeItems as $recipeItem) {
                $targetQty = $recipeItem->calculateRequiredQuantity($wo->produced_qty);
                $actualConsumed = (float) $wo->consumptions
                    ->filter(fn ($c) => $c->batch->raw_material_id === $recipeItem->raw_material_id)
                    ->sum('quantity');

                $variance = round($actualConsumed - $targetQty, 4);
                $variancePct = $targetQty > 0 ? round(($variance / $targetQty) * 100, 2) : 0.0;
                $unitCost = (float) $recipeItem->rawMaterial->standard_cost;
                $varianceCost = round($variance * $unitCost, 2);

                $varianceRows[] = [
                    'order_number' => $wo->order_number,
                    'finished_lot' => $wo->target_finished_lot,
                    'product_name' => $wo->product->name_en,
                    'material_code' => $recipeItem->rawMaterial->code,
                    'material_name' => $recipeItem->rawMaterial->name_en,
                    'target_qty' => $targetQty,
                    'actual_consumed' => $actualConsumed,
                    'variance_qty' => $variance,
                    'variance_pct' => $variancePct,
                    'variance_cost' => $varianceCost,
                    'uom' => $recipeItem->rawMaterial->unit_of_measure->value,
                ];
            }
        }

        if ($request->query('export') === 'xlsx') {
            return $this->exportSpreadsheet(
                'production_scrap_variance',
                ['Work Order', 'Finished Lot', 'Product', 'Material Code', 'Material Name', 'Target Qty', 'Actual Consumed', 'Variance Qty', 'Variance %', 'Variance Cost (AED)', 'UOM'],
                $varianceRows
            );
        }

        return view('admin.supply_chain.reports.scrap_variance', compact('varianceRows', 'workOrders'));
    }

    /**
     * Report 7: Complete Lot Traceability & Regulatory Recall Audit Report (Under 60s)
     */
    public function lotTraceability(Request $request): View|StreamedResponse
    {
        $this->authorize('viewAny', RawMaterialBatch::class);

        $startTime = microtime(true);
        $searchQuery = trim($request->query('lot_number', $request->query('query', '')));
        $searchType = $request->query('direction', $request->query('search_type', 'forward'));

        $backwardResults = null;
        $forwardResults = null;

        // Sample chips for 1-click test & interactive exploring
        $sampleRawBatches = RawMaterialBatch::with(['rawMaterial', 'supplier'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $sampleWorkOrders = WorkOrder::with(['product'])
            ->whereNotNull('target_finished_lot')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        if (! empty($searchQuery)) {
            // 1. Try finding finished work order
            $workOrder = WorkOrder::with([
                'product',
                'location',
                'creator',
                'consumptions.batch.rawMaterial',
                'consumptions.batch.supplier',
                'consumptions.batch.inspections.inspector',
                'consumptions.operator',
            ])->where('target_finished_lot', $searchQuery)
                ->orWhere('order_number', $searchQuery)
                ->first();

            // 2. Try finding raw material batch
            $batch = RawMaterialBatch::with([
                'rawMaterial',
                'supplier',
                'location',
                'goodsReceipt.purchaseOrder',
                'inspections.inspector',
                'inspections.verifier',
                'inspections.parameters',
                'consumptions.workOrder.product',
                'consumptions.operator',
            ])->where('batch_number', $searchQuery)
                ->orWhere('supplier_lot', $searchQuery)
                ->first();

            // Intelligent auto-detection of direction if the user selected one direction but typed the other
            if ($searchType === 'backward' && ! $workOrder && $batch) {
                $searchType = 'forward';
            } elseif ($searchType === 'forward' && ! $batch && $workOrder) {
                $searchType = 'backward';
            }

            if ($workOrder) {
                $backwardResults = [
                    'work_order' => $workOrder,
                    'consumptions' => $workOrder->consumptions,
                    'ingredients' => $workOrder->consumptions->map(fn ($c) => [
                        'batch_number' => $c->batch?->batch_number,
                        'supplier_lot' => $c->batch?->supplier_lot,
                        'material' => $c->batch?->rawMaterial?->name,
                        'supplier' => $c->batch?->supplier?->name,
                        'consumed_qty' => $c->quantity,
                        'uom' => $c->batch?->rawMaterial?->uom?->value ?? $c->batch?->rawMaterial?->unit_of_measure?->value,
                        'operator' => $c->operator?->name,
                        'scanned_barcode' => $c->scanned_barcode,
                        'dispensed_at' => $c->created_at?->toDateTimeString(),
                        'latest_qc_verdict' => $c->batch?->inspections->last()?->verdict?->value ?? 'Passed',
                        'qc_code' => $c->batch?->inspections->last()?->inspection_code ?? 'QC-AUTO',
                    ]),
                ];
            }

            if ($batch) {
                $workOrders = $batch->consumptions->map(fn ($c) => $c->workOrder)->filter()->unique('id')->values();
                $finishedLots = $workOrders->pluck('target_finished_lot')->filter()->values();

                $productIds = $workOrders->pluck('product_id')->unique();
                $affectedOrders = Order::with('customer')
                    ->whereHas('items', fn ($q) => $q->whereIn('product_id', $productIds))
                    ->orderBy('created_at', 'desc')
                    ->take(50)
                    ->get();

                $forwardResults = [
                    'batch' => $batch,
                    'work_orders' => $workOrders,
                    'finished_lots' => $finishedLots,
                    'affected_orders' => $affectedOrders,
                ];
            }
        }

        $executionTime = max(0.012, round(microtime(true) - $startTime, 3));
        $results = ($searchType === 'forward') ? $forwardResults : $backwardResults;

        if ($request->query('export') === 'xlsx' && $results) {
            if ($searchType === 'forward') {
                return $this->exportSpreadsheet(
                    'forward_recall_dossier_'.$searchQuery,
                    ['Type', 'Identifier / Number', 'Item / Title', 'Entity / Partner', 'Qty / Status', 'Timestamp / Date'],
                    collect([
                        ['Raw Batch Origin', $results['batch']->batch_number, $results['batch']->rawMaterial?->name, $results['batch']->supplier?->name, $results['batch']->current_qty.' '.$results['batch']->rawMaterial?->uom?->value, $results['batch']->created_at?->toDateTimeString()],
                    ])->concat($results['work_orders']->map(fn ($wo) => [
                        'Impacted Work Order', $wo->order_number, $wo->product?->name_en, 'Finished Lot: '.$wo->target_finished_lot, $wo->produced_qty.' units', $wo->scheduled_start?->toDateTimeString(),
                    ]))->concat($results['affected_orders']->map(fn ($ord) => [
                        'Exposed Customer Order', $ord->order_number ?? '#'.$ord->id, 'Online Order', $ord->customer?->name ?? 'Guest', $ord->total_amount ?? $ord->grand_total, $ord->created_at?->toDateTimeString(),
                    ]))->toArray()
                );
            }

            return $this->exportSpreadsheet(
                'backward_genealogy_dossier_'.$searchQuery,
                ['Raw Batch #', 'Supplier LOT', 'Ingredient', 'Supplier', 'Consumed Qty', 'UOM', 'Operator', 'QC Verdict', 'Dispensed At'],
                $results['ingredients']->map(fn ($i) => [
                    $i['batch_number'], $i['supplier_lot'], $i['material'], $i['supplier'], $i['consumed_qty'], $i['uom'], $i['operator'], $i['latest_qc_verdict'], $i['dispensed_at'],
                ])->toArray()
            );
        }

        return view('admin.supply_chain.reports.lot_traceability', compact(
            'searchQuery',
            'searchType',
            'backwardResults',
            'forwardResults',
            'results',
            'sampleRawBatches',
            'sampleWorkOrders',
            'executionTime'
        ));
    }

    private function exportSpreadsheet(string $filename, array $headers, array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([$headers], null, 'A1');
        $sheet->fromArray($data, null, 'A2');

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'_'.date('Ymd_His').'.xlsx"',
        ]);
    }
}
