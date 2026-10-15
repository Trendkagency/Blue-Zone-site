<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller
{
    /**
     * Display listing of commercial quotations.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = Quotation::with(['customer', 'creator'])->latest('id');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $quotations = $query->paginate(15)->withQueryString();

        // Quotations KPIs
        $allQuotes = Quotation::all();
        $totalQuotes = $allQuotes->count();
        $pipelineValue = (float) $allQuotes->whereIn('status', ['Draft', 'Sent', 'Approved'])->sum('total');
        $approvedCount = $allQuotes->where('status', 'Approved')->count();
        $convertedCount = $allQuotes->where('status', 'Converted')->count();
        $conversionRate = $totalQuotes > 0 ? round(($convertedCount / $totalQuotes) * 100, 1) : 0;

        return view('admin.sales.quotations.index', [
            'quotations' => $quotations,
            'totalQuotes' => $totalQuotes,
            'pipelineValue' => $pipelineValue,
            'approvedCount' => $approvedCount,
            'convertedCount' => $convertedCount,
            'conversionRate' => $conversionRate,
            'selectedStatus' => $status,
            'search' => $search,
            'currentPage' => $quotations->currentPage(),
            'totalPages' => $quotations->lastPage(),
        ]);
    }

    /**
     * Show form to create a new quotation.
     */
    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name_en')->get();
        $nextQuoteNumber = Quotation::generateNumber();

        return view('admin.sales.quotations.create', [
            'customers' => $customers,
            'products' => $products,
            'nextQuoteNumber' => $nextQuoteNumber,
        ]);
    }

    /**
     * Store new quotation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:date',
            'terms_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_type' => 'nullable|string|in:product,service',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0.0;
            $discountTotal = 0.0;
            $itemsData = [];

            foreach ($validated['items'] as $raw) {
                $qty = (int) $raw['quantity'];
                $price = (float) $raw['unit_price'];
                $itemDiscount = (float) ($raw['discount'] ?? 0);
                $lineSub = ($qty * $price) - $itemDiscount;
                if ($lineSub < 0) $lineSub = 0;

                $subtotal += ($qty * $price);
                $discountTotal += $itemDiscount;

                $product = !empty($raw['product_id']) ? Product::find($raw['product_id']) : null;

                $itemsData[] = [
                    'product_id' => $product?->id,
                    'item_type' => $raw['item_type'] ?? ($product?->type ?? 'product'),
                    'item_name' => $raw['item_name'],
                    'sku' => $product?->sku ?? 'CUSTOM-SKU',
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'discount' => $itemDiscount,
                    'tax_rate' => 15.00,
                    'total' => $lineSub,
                ];
            }

            $taxable = max(0, $subtotal - $discountTotal);
            $tax = $taxable * 0.15;
            $grandTotal = $taxable + $tax;

            $quotation = Quotation::create([
                'quotation_number' => Quotation::generateNumber(),
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
                'date' => $validated['date'],
                'valid_until' => $validated['valid_until'] ?? now()->addDays(30)->toDateString(),
                'status' => 'Draft',
                'subtotal' => $subtotal,
                'discount' => $discountTotal,
                'tax' => $tax,
                'total' => $grandTotal,
                'currency' => 'SAR',
                'terms_conditions' => $validated['terms_conditions'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($itemsData as $item) {
                $quotation->items()->create($item);
            }

            DB::commit();

            return redirect()->route('admin.sales.quotations.show', $quotation->id)
                ->with('status', app()->getLocale() === 'ar'
                    ? "تم إنشاء عرض السعر رقم {$quotation->quotation_number} بنجاح."
                    : "Quotation {$quotation->quotation_number} generated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['quote_error' => $e->getMessage()]);
        }
    }

    /**
     * Show quotation detail sheet.
     */
    public function show(int $id): View
    {
        $quotation = Quotation::with(['items.product', 'customer', 'creator', 'convertedOrder'])->findOrFail($id);

        return view('admin.sales.quotations.show', [
            'quotation' => $quotation,
        ]);
    }

    /**
     * Branded printable quotation document.
     */
    public function print(int $id): View
    {
        $quotation = Quotation::with(['items.product', 'customer'])->findOrFail($id);

        return view('admin.sales.quotations.print', [
            'quotation' => $quotation,
        ]);
    }

    /**
     * Convert Quotation into a confirmed Sales Order (One-Click).
     */
    public function convert(int $id): RedirectResponse
    {
        $quotation = Quotation::with(['items.product', 'customer'])->findOrFail($id);

        if ($quotation->status === 'Converted' && $quotation->converted_order_id) {
            return redirect()->route('admin.orders.show', $quotation->converted_order_id)
                ->with('status', app()->getLocale() === 'ar' ? 'عرض السعر تم تحويله مسبقاً لهذا الطلب.' : 'This quotation was already converted.');
        }

        DB::beginTransaction();
        try {
            $orderYear = now()->format('Y');
            $orderCount = Order::whereYear('created_at', $orderYear)->count() + 1;
            $orderNumber = sprintf('ORD-%s-%05d', $orderYear, $orderCount);

            $order = Order::create([
                'order_number' => $orderNumber,
                'invoice_number' => 'INV-' . strtoupper(bin2hex(random_bytes(3))),
                'channel' => 'quotation',
                'customer_name' => $quotation->customer_name,
                'customer_email' => $quotation->customer_email,
                'customer_phone' => $quotation->customer_phone,
                'customer_id' => $quotation->customer_id,
                'date' => now()->toDateString(),
                'status' => 'Confirmed',
                'payment_method' => 'Bank Transfer / Invoice',
                'payment_status' => 'Pending',
                'subtotal' => $quotation->subtotal,
                'discount' => $quotation->discount,
                'shipping' => 0.00,
                'tax' => $quotation->tax,
                'total' => $quotation->total,
                'notes' => "Converted from Quotation #{$quotation->quotation_number}. Notes: " . ($quotation->notes ?? 'None'),
            ]);

            foreach ($quotation->items as $qItem) {
                $product = $qItem->product;
                $nameEn = $product?->name_en ?? $qItem->item_name;
                $nameAr = $product?->name_ar ?? $qItem->item_name;
                $sku = $qItem->sku ?? ($product?->sku ?? 'CUSTOM-SKU');
                $unitPrice = (float) $qItem->unit_price;
                $total = (float) $qItem->total;
                $image = $product?->primary_image_url ?? $product?->image ?? 'assets/products/blue-mind.jpg';

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'product_name_en' => $nameEn,
                    'product_name_ar' => $nameAr,
                    'variant_en' => 'Standard Pack',
                    'variant_ar' => 'العبوة القياسية',
                    'sku' => $sku,
                    'unit_price' => $unitPrice,
                    'quantity' => (int) $qItem->quantity,
                    'total' => $total,
                    'image' => $image,
                ]);
            }

            $quotation->status = 'Converted';
            $quotation->converted_order_id = $order->id;
            $quotation->save();

            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)
                ->with('status', app()->getLocale() === 'ar'
                    ? "تم تحويل عرض السعر ({$quotation->quotation_number}) بنجاح إلى أمر بيع رسمي رقم ({$order->order_number})!"
                    : "Quotation #{$quotation->quotation_number} converted successfully into Sales Order #{$order->order_number}!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['convert_error' => $e->getMessage()]);
        }
    }

    /**
     * Update quotation status.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Draft,Sent,Approved,Rejected,Expired',
        ]);

        $quotation = Quotation::findOrFail($id);
        $quotation->status = $validated['status'];
        $quotation->save();

        return back()->with('status', app()->getLocale() === 'ar'
            ? "تم تحديث حالة عرض السعر إلى ({$validated['status']}) بنجاح."
            : "Quotation status updated to ({$validated['status']}) successfully.");
    }
}
