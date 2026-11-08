<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\SupplierQuotation;
use App\Models\SupplierQuotationItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplierQuotationController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupplierQuotation::with(['supplier', 'creator', 'convertedPurchaseOrder'])
            ->withCount('items');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhere('rfq_reference', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($supplierId = $request->query('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = min(100, max(10, (int) $request->query('per_page', 15)));
        $quotations = $query->orderBy('quote_date', 'desc')->paginate($perPage)->withQueryString();

        $kpis = [
            'total_quotes' => SupplierQuotation::count(),
            'pending' => SupplierQuotation::where('status', 'pending')->count(),
            'accepted' => SupplierQuotation::where('status', 'accepted')->count(),
            'converted' => SupplierQuotation::where('status', 'converted')->count(),
        ];

        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.supply_chain.supplier_quotations.index', compact('quotations', 'kpis', 'suppliers'));
    }

    public function create(Request $request): View
    {
        $suppliers = Supplier::where('is_active', true)->with('country')->orderBy('name_en')->get();
        $rawMaterials = RawMaterial::orderBy('name_en')->get(['id', 'name_en', 'name_ar', 'code', 'standard_cost', 'unit_of_measure']);
        $products = Product::orderBy('name_en')->get(['id', 'name_en', 'name_ar', 'sku', 'cost_price']);
        $supportedCurrencies = \App\Services\CurrencyService::supportedCurrencies();
        $currencies = array_keys($supportedCurrencies);
        $baseCurrency = \App\Services\CurrencyService::code();
        $baseCurrencySymbol = \App\Services\CurrencyService::symbol($baseCurrency);
        $defaultQuoteNumber = SupplierQuotation::generateQuoteNumber();
        $selectedSupplierId = $request->query('supplier_id');

        $defaultCurrency = $baseCurrency;
        if ($selectedSupplierId) {
            $selectedSupplier = $suppliers->firstWhere('id', (int) $selectedSupplierId);
            if ($selectedSupplier && !empty($selectedSupplier->currency)) {
                $defaultCurrency = $selectedSupplier->currency;
            }
        }

        return view('admin.supply_chain.supplier_quotations.create', compact(
            'suppliers',
            'rawMaterials',
            'products',
            'currencies',
            'supportedCurrencies',
            'baseCurrency',
            'baseCurrencySymbol',
            'defaultCurrency',
            'defaultQuoteNumber',
            'selectedSupplierId'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quote_number' => ['required', 'string', 'max:50', 'unique:supplier_quotations,quote_number'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'rfq_reference' => ['nullable', 'string', 'max:100'],
            'quote_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quote_date'],
            'status' => ['required', 'in:draft,pending,accepted,rejected'],
            'currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['required', 'numeric', 'min:0.0001'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'lead_time_days' => ['required', 'integer', 'min:1', 'max:365'],
            'estimated_freight' => ['nullable', 'numeric', 'min:0'],
            'estimated_customs' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', 'in:raw_material,packaging,product'],
            'items.*.raw_material_id' => ['nullable', 'exists:raw_materials,id'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        $quotation = DB::transaction(function () use ($validated, $request) {
            $itemsData = $validated['items'];
            unset($validated['items']);

            $subtotal = 0.0;
            $taxAmount = 0.0;

            foreach ($itemsData as &$item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $taxRate = (float) $item['tax_rate'];

                $lineSub = round($qty * $price, 2);
                $lineTax = round($lineSub * ($taxRate / 100), 2);
                $lineTotal = $lineSub + $lineTax;

                $item['tax_amount'] = $lineTax;
                $item['total_price'] = $lineTotal;

                $subtotal += $lineSub;
                $taxAmount += $lineTax;
            }

            $freight = (float) ($validated['estimated_freight'] ?? 0.0);
            $customs = (float) ($validated['estimated_customs'] ?? 0.0);
            $grandTotal = $subtotal + $taxAmount + $freight + $customs;

            $validated['subtotal'] = $subtotal;
            $validated['tax_amount'] = $taxAmount;
            $validated['grand_total'] = $grandTotal;
            $validated['created_by'] = auth()->id();

            $quote = SupplierQuotation::create($validated);

            foreach ($itemsData as $itemRow) {
                $quote->items()->create($itemRow);
            }

            return $quote;
        });

        return redirect()->route('admin.supplier-quotations.show', $quotation)
            ->with('success', __('supplier_quotations.messages.created'));
    }

    public function show(SupplierQuotation $supplierQuotation): View
    {
        $supplierQuotation->load(['supplier.country', 'items.rawMaterial', 'items.product', 'creator', 'convertedPurchaseOrder']);

        return view('admin.supply_chain.supplier_quotations.show', compact('supplierQuotation'));
    }

    public function edit(SupplierQuotation $supplierQuotation): View|RedirectResponse
    {
        if ($supplierQuotation->status === 'converted') {
            return redirect()->route('admin.supplier-quotations.show', $supplierQuotation)
                ->with('error', __('supplier_quotations.messages.already_converted', ['po_number' => $supplierQuotation->convertedPurchaseOrder?->po_number ?? 'PO']));
        }

        $supplierQuotation->load(['items.rawMaterial', 'items.product', 'supplier.country']);
        $suppliers = Supplier::where('is_active', true)->with('country')->orderBy('name_en')->get();
        $rawMaterials = RawMaterial::orderBy('name_en')->get(['id', 'name_en', 'name_ar', 'code', 'standard_cost', 'unit_of_measure']);
        $products = Product::orderBy('name_en')->get(['id', 'name_en', 'name_ar', 'sku', 'cost_price']);
        $supportedCurrencies = \App\Services\CurrencyService::supportedCurrencies();
        $currencies = array_keys($supportedCurrencies);
        $baseCurrency = \App\Services\CurrencyService::code();
        $baseCurrencySymbol = \App\Services\CurrencyService::symbol($baseCurrency);
        $defaultCurrency = $supplierQuotation->currency ?: $baseCurrency;

        return view('admin.supply_chain.supplier_quotations.edit', compact(
            'supplierQuotation',
            'suppliers',
            'rawMaterials',
            'products',
            'currencies',
            'supportedCurrencies',
            'baseCurrency',
            'baseCurrencySymbol',
            'defaultCurrency'
        ));
    }

    public function update(Request $request, SupplierQuotation $supplierQuotation): RedirectResponse
    {
        if ($supplierQuotation->status === 'converted') {
            return redirect()->route('admin.supplier-quotations.show', $supplierQuotation)
                ->with('error', __('supplier_quotations.messages.already_converted', ['po_number' => $supplierQuotation->convertedPurchaseOrder?->po_number ?? 'PO']));
        }

        $validated = $request->validate([
            'quote_number' => ['required', 'string', 'max:50', 'unique:supplier_quotations,quote_number,' . $supplierQuotation->id],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'rfq_reference' => ['nullable', 'string', 'max:100'],
            'quote_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:quote_date'],
            'status' => ['required', 'in:draft,pending,accepted,rejected'],
            'currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['required', 'numeric', 'min:0.0001'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'lead_time_days' => ['required', 'integer', 'min:1', 'max:365'],
            'estimated_freight' => ['nullable', 'numeric', 'min:0'],
            'estimated_customs' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', 'in:raw_material,packaging,product'],
            'items.*.raw_material_id' => ['nullable', 'exists:raw_materials,id'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $supplierQuotation) {
            $itemsData = $validated['items'];
            unset($validated['items']);

            $subtotal = 0.0;
            $taxAmount = 0.0;

            foreach ($itemsData as &$item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $taxRate = (float) $item['tax_rate'];

                $lineSub = round($qty * $price, 2);
                $lineTax = round($lineSub * ($taxRate / 100), 2);
                $lineTotal = $lineSub + $lineTax;

                $item['tax_amount'] = $lineTax;
                $item['total_price'] = $lineTotal;

                $subtotal += $lineSub;
                $taxAmount += $lineTax;
            }

            $freight = (float) ($validated['estimated_freight'] ?? 0.0);
            $customs = (float) ($validated['estimated_customs'] ?? 0.0);
            $grandTotal = $subtotal + $taxAmount + $freight + $customs;

            $validated['subtotal'] = $subtotal;
            $validated['tax_amount'] = $taxAmount;
            $validated['grand_total'] = $grandTotal;

            $supplierQuotation->update($validated);

            $supplierQuotation->items()->delete();
            foreach ($itemsData as $itemRow) {
                $supplierQuotation->items()->create($itemRow);
            }
        });

        return redirect()->route('admin.supplier-quotations.show', $supplierQuotation)
            ->with('success', __('supplier_quotations.messages.updated'));
    }

    public function destroy(SupplierQuotation $supplierQuotation): RedirectResponse
    {
        if ($supplierQuotation->status === 'converted') {
            return back()->with('error', 'Cannot delete quotation after it has been converted to an official PO.');
        }

        $supplierQuotation->delete();

        return redirect()->route('admin.supplier-quotations.index')
            ->with('success', __('supplier_quotations.messages.deleted'));
    }

    /**
     * 1-Click Convert Supplier Quotation directly into an official Purchase Order.
     */
    public function convert(SupplierQuotation $supplierQuotation): RedirectResponse
    {
        if ($supplierQuotation->status === 'converted' && $supplierQuotation->converted_po_id) {
            return redirect()->route('admin.purchase-orders.show', $supplierQuotation->converted_po_id)
                ->with('info', __('supplier_quotations.messages.already_converted', [
                    'po_number' => $supplierQuotation->convertedPurchaseOrder?->po_number ?? 'PO',
                ]));
        }

        $supplierQuotation->load(['items', 'supplier']);

        if ($supplierQuotation->items->isEmpty()) {
            return back()->with('error', 'Cannot convert an empty quotation with no line items.');
        }

        if (! $supplierQuotation->supplier?->is_active) {
            return back()->with('error', app()->getLocale() === 'ar'
                ? 'لا يمكن تحويل العرض إلى أمر شراء رسمي لأن المورد ما زال بانتظار الاعتماد المزدوج وتأهيل cGMP.'
                : 'Cannot convert quotation to Purchase Order because the vendor is pending dual-approval qualification.');
        }

        $po = $supplierQuotation->convertToPurchaseOrder(auth()->user());

        return redirect()->route('admin.purchase-orders.show', $po)
            ->with('success', __('supplier_quotations.messages.converted', [
                'po_number' => $po->po_number,
            ]));
    }

    public function updateStatus(Request $request, SupplierQuotation $supplierQuotation): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:draft,pending,accepted,rejected'],
        ]);

        if ($supplierQuotation->status === 'converted') {
            return back()->with('error', 'Cannot alter status after quotation has been converted to PO.');
        }

        $supplierQuotation->update(['status' => $request->input('status')]);

        return back()->with('success', __('supplier_quotations.messages.status_updated'));
    }

    public function print(SupplierQuotation $supplierQuotation): View
    {
        $supplierQuotation->load(['supplier.country', 'items.rawMaterial', 'items.product', 'creator']);

        return view('admin.supply_chain.supplier_quotations.print', compact('supplierQuotation'));
    }
}
