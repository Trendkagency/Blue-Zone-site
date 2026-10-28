<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\CollectionEntry;
use App\Models\Customer;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Services\CollectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CollectionEntryController extends Controller
{
    public function __construct(protected CollectionService $collectionService) {}

    /**
     * Display the Collection Entry spreadsheet table and dashboard.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $accountId = $request->query('account_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $sortBy = $request->query('sort_by', 'entry_date');
        $sortDir = strtolower((string) $request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 20);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 20;
        }

        $query = CollectionEntry::with(['financialAccount', 'customer', 'supplier', 'order']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('collection_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('customer_source', 'like', "%{$search}%")
                    ->orWhere('cheque_number', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($accountId) {
            $query->where('financial_account_id', $accountId);
        }

        if ($dateFrom) {
            $query->whereDate('entry_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('entry_date', '<=', $dateTo);
        }

        // Supported sort columns
        $sortableColumns = [
            'entry_date' => 'entry_date',
            'date' => 'entry_date',
            'collection_number' => 'collection_number',
            'category' => 'category',
            'description' => 'description',
            'customer_code' => 'customer_code',
            'customer_source' => 'customer_source',
            'amount_excl_vat' => 'amount_excl_vat',
            'vat_amount' => 'vat_amount',
            'gross_amount' => 'gross_amount',
            'payment_method' => 'payment_method',
            'id' => 'id',
        ];

        $orderColumn = $sortableColumns[$sortBy] ?? 'entry_date';
        $query->orderBy($orderColumn, $sortDir);
        if ($orderColumn !== 'id') {
            $query->orderBy('id', $sortDir);
        }

        $collections = $query->paginate($perPage)->withQueryString();

        // If AJAX / JSON format requested for lazy loading
        if ($request->ajax() || $request->wantsJson() || $request->query('format') === 'json') {
            $rowsHtml = view('admin.finance.collections._rows', [
                'collections' => $collections,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $rowsHtml,
                'count' => $collections->count(),
                'total' => $collections->total(),
                'currentPage' => $collections->currentPage(),
                'lastPage' => $collections->lastPage(),
                'hasMorePages' => $collections->hasMorePages(),
                'perPage' => $collections->perPage(),
                'firstItem' => $collections->firstItem() ?? 0,
                'lastItem' => $collections->lastItem() ?? 0,
                'pageTotals' => [
                    'amount_excl_vat' => (float) $collections->sum('amount_excl_vat'),
                    'vat_amount' => (float) $collections->sum('vat_amount'),
                    'gross_amount' => (float) $collections->sum('gross_amount'),
                ],
            ]);
        }

        // Calculate KPI summary
        $totalGross = (float) CollectionEntry::sum('gross_amount');
        $totalVat = (float) CollectionEntry::sum('vat_amount');
        $totalNet = (float) CollectionEntry::sum('amount_excl_vat');
        $totalAutoSynced = CollectionEntry::where('is_auto_synced', true)->count();
        $totalEntries = CollectionEntry::count();

        $accounts = FinancialAccount::where('is_active', true)->get();
        $categories = CollectionEntry::getCategories();

        // Load customers, suppliers, and recent/open invoices for quick selection & auto-fill
        $customers = Customer::orderBy('name')->get(['id', 'name', 'email', 'phone', 'city']);
        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get(['id', 'name_en', 'name_ar', 'code', 'phone']);

        // Orders / Sales Invoices with financial breakdown
        $invoices = Order::latest('id')
            ->take(50)
            ->get(['id', 'order_number', 'invoice_number', 'customer_id', 'customer_name', 'subtotal', 'tax', 'total', 'payment_status', 'date', 'created_at']);

        // Purchase Invoices for supplier refunds or returns
        $purchaseInvoices = PurchaseInvoice::with('supplier:id,name_en,name_ar,code')
            ->latest('id')
            ->take(30)
            ->get(['id', 'invoice_number', 'supplier_id', 'subtotal', 'tax_amount', 'grand_total', 'payment_status', 'invoice_date']);

        return view('admin.finance.collections.index', [
            'collections' => $collections,
            'totalGross' => $totalGross,
            'totalVat' => $totalVat,
            'totalNet' => $totalNet,
            'totalAutoSynced' => $totalAutoSynced,
            'totalEntries' => $totalEntries,
            'accounts' => $accounts,
            'categories' => $categories,
            'customers' => $customers,
            'suppliers' => $suppliers,
            'invoices' => $invoices,
            'purchaseInvoices' => $purchaseInvoices,
            'search' => $search,
            'category' => $category,
            'accountId' => $accountId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Store a new collection entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'category' => 'required|string',
            'source_type' => 'nullable|string|in:customer,supplier,asset,other',
            'customer_id' => 'nullable|exists:customers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_id' => 'nullable|exists:orders,id',
            'description' => 'nullable|string',
            'customer_code' => 'nullable|string|max:50',
            'customer_source' => 'nullable|string|max:255',
            'amount_excl_vat' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'gross_amount' => 'nullable|numeric|min:0',
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'payment_method' => 'required|string|max:50',
            'cheque_number' => 'nullable|string|max:100',
            'cheque_bank' => 'nullable|string|max:100',
            'cheque_due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Auto-fill metadata if customer or supplier selected
        if (! empty($validated['customer_id'])) {
            $customer = Customer::find($validated['customer_id']);
            if ($customer) {
                $validated['customer_source'] = ($validated['customer_source'] ?? null) ?: $customer->name;
                $validated['customer_code'] = ($validated['customer_code'] ?? null) ?: ('CUST-'.str_pad((string) $customer->id, 4, '0', STR_PAD_LEFT));
            }
        } elseif (! empty($validated['supplier_id'])) {
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $validated['customer_source'] = ($validated['customer_source'] ?? null) ?: ($supplier->name_en ?: $supplier->name_ar);
                $validated['customer_code'] = ($validated['customer_code'] ?? null) ?: ($supplier->code ?: ('SUP-'.$supplier->id));
            }
        }

        // If order_id is linked and description is empty, auto-generate standard invoice ref
        if (! empty($validated['order_id']) && empty($validated['description'])) {
            $order = Order::find($validated['order_id']);
            if ($order) {
                $ref = $order->invoice_number ?: $order->order_number;
                $validated['description'] = 'Invoice #'.$ref;
            }
        }

        $this->collectionService->recordEntry($validated);

        return redirect()->route('admin.finance.collections.index')
            ->with('status', app()->getLocale() === 'ar' ? 'تم تسجيل وترحيل قيد التحصيل بنجاح وتحديث أرصدة الحسابات.' : 'Collection Entry posted successfully and account balances updated.');
    }

    /**
     * Trigger synchronization with online sales orders.
     */
    public function syncOnlineSales(): RedirectResponse
    {
        $count = $this->collectionService->syncAllOnlineSales();

        $msg = app()->getLocale() === 'ar'
            ? "تمت المزامنة الآلية بنجاح: تم إنشاء {$count} سند تحصيل جديد لطلبات المبيعات الأونلاين."
            : "Online sales synchronization complete: {$count} collection entries generated.";

        return redirect()->route('admin.finance.collections.index')->with('status', $msg);
    }

    /**
     * Display printable official receipt voucher for collection.
     */
    public function voucher(int $id): View
    {
        $entry = CollectionEntry::with(['financialAccount', 'customer', 'order'])
            ->findOrFail($id);

        return view('admin.finance.collections.voucher', [
            'entry' => $entry,
        ]);
    }

    /**
     * Export collection entries as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = CollectionEntry::with(['financialAccount', 'customer', 'order'])
            ->latest('entry_date')
            ->latest('id');

        if ($request->query('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->query('account_id')) {
            $query->where('financial_account_id', $request->query('account_id'));
        }

        if ($request->query('date_from')) {
            $query->whereDate('entry_date', '>=', $request->query('date_from'));
        }

        if ($request->query('date_to')) {
            $query->whereDate('entry_date', '<=', $request->query('date_to'));
        }

        $records = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="collection_entries_'.date('Y_m_d_His').'.csv"',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Arabic support in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Date',
                'Collection No.',
                'Category',
                'Description',
                'Customer Code',
                'Customer / Source',
                'Amount Excl. VAT',
                'VAT',
                'Gross Amount',
                'Payment Method / Account',
                'Status',
            ]);

            foreach ($records as $r) {
                fputcsv($file, [
                    $r->entry_date ? $r->entry_date->format('Y-m-d') : '',
                    $r->collection_number,
                    $r->category_meta['name_en'] ?? $r->category,
                    $r->description,
                    $r->customer_code,
                    $r->customer_source,
                    number_format($r->amount_excl_vat, 2, '.', ''),
                    number_format($r->vat_amount, 2, '.', ''),
                    number_format($r->gross_amount, 2, '.', ''),
                    ($r->financialAccount ? $r->financialAccount->name_en : $r->payment_method),
                    $r->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
