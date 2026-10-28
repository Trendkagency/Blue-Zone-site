<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Services\FinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseInvoiceController extends Controller
{
    public function __construct(protected FinanceService $financeService)
    {
    }

    /**
     * List all vendor bills and purchase invoices.
     */
    public function index(Request $request): View
    {
        $status = $request->query('payment_status', 'all');
        $supplierId = $request->query('supplier_id', 'all');
        $search = $request->query('search');

        $query = PurchaseInvoice::with(['supplier', 'purchaseOrder', 'items', 'payments'])->latest('id');

        if ($status && $status !== 'all') {
            $query->where('payment_status', $status);
        }

        if ($supplierId && $supplierId !== 'all') {
            $query->where('supplier_id', $supplierId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();
        $accounts = FinancialAccount::where('is_active', true)->get();

        $allBills = PurchaseInvoice::all();
        $totalBilled = (float) $allBills->sum('grand_total');
        $totalPaid = (float) $allBills->sum('paid_amount');
        $totalDue = max(0, $totalBilled - $totalPaid);

        return view('admin.finance.purchase-invoices.index', [
            'invoices' => $invoices,
            'suppliers' => $suppliers,
            'accounts' => $accounts,
            'totalBilled' => $totalBilled,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
            'selectedPayment' => $status,
            'selectedSupplier' => $supplierId,
            'search' => $search,
        ]);
    }

    /**
     * Show form to create a new purchase invoice.
     */
    public function create(Request $request): View
    {
        $poId = $request->query('purchase_order_id');
        $po = $poId ? PurchaseOrder::with(['supplier', 'items.rawMaterial'])->find($poId) : null;

        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();
        $purchaseOrders = PurchaseOrder::latest('id')->limit(50)->get();
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.finance.purchase-invoices.create', [
            'po' => $po,
            'suppliers' => $suppliers,
            'purchaseOrders' => $purchaseOrders,
            'rawMaterials' => $rawMaterials,
        ]);
    }

    /**
     * Store new purchase invoice.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'invoice_number' => 'nullable|string|max:60|unique:purchase_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric|min:0.01',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'items.*.raw_material_id' => 'nullable|exists:raw_materials,id',
        ]);

        $po = !empty($validated['purchase_order_id']) ? PurchaseOrder::find($validated['purchase_order_id']) : null;

        $invoice = $this->financeService->createPurchaseInvoice($validated, $po);

        return redirect()->route('admin.finance.purchase-invoices.show', $invoice->id)
            ->with('status', __('finance.success_created'));
    }

    /**
     * Show vendor bill dossier.
     */
    public function show(int $id): View
    {
        $invoice = PurchaseInvoice::with(['supplier', 'purchaseOrder', 'items.rawMaterial', 'payments.account'])
            ->findOrFail($id);

        $accounts = FinancialAccount::where('is_active', true)->get();

        return view('admin.finance.purchase-invoices.show', [
            'invoice' => $invoice,
            'accounts' => $accounts,
        ]);
    }

    /**
     * Print vendor purchase invoice.
     */
    public function print(int $id): View
    {
        $invoice = PurchaseInvoice::with(['supplier', 'purchaseOrder', 'items.rawMaterial', 'payments.account'])
            ->findOrFail($id);

        $siteInfo = [
            'brand_name' => 'Blue Zone Pharmaceuticals',
            'tax_number' => '100293847500003',
            'commercial_record' => 'CR-1010884920',
            'phone' => '+971 4 800 2583',
            'email' => 'procurement@bluezonepharma.com',
            'website' => 'www.bluezonepharma.com',
        ];

        return view('admin.finance.purchase-invoices.print', [
            'invoice' => $invoice,
            'siteInfo' => $siteInfo,
        ]);
    }

    /**
     * Record supplier payment against bill.
     */
    public function pay(Request $request, int $id): RedirectResponse
    {
        $invoice = PurchaseInvoice::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($invoice->due_balance + 0.01),
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'payment_method' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $account = FinancialAccount::findOrFail($validated['financial_account_id']);

        $this->financeService->recordSupplierPayment($invoice, (float) $validated['amount'], $account, $validated);

        return redirect()->back()->with('status', __('finance.success_payment'));
    }
}
