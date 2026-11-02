<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\PaymentEntry;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentEntryController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    /**
     * Display the enterprise spreadsheet ledger of all payment entries.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $accountId = $request->query('account_id');
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $sortBy = $request->query('sort_by', 'entry_date');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage < 5 || $perPage > 200) {
            $perPage = 15;
        }

        $query = PaymentEntry::with(['financialAccount', 'supplier', 'purchaseInvoice', 'creator'])
            ->category($category)
            ->account($accountId)
            ->status($status)
            ->dateBetween($dateFrom, $dateTo);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('payee_name', 'like', "%{$search}%")
                  ->orWhere('supplier_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('cheque_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Apply interactive column sort
        $allowedSorts = [
            'entry_date', 'payment_number', 'category', 'description',
            'supplier_code', 'payee_name', 'amount_excl_vat',
            'vat_amount', 'gross_amount', 'payment_method', 'status'
        ];
        if (in_array($sortBy, $allowedSorts, true)) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('entry_date', 'desc')->orderBy('id', 'desc');
        }

        $payments = $query->paginate($perPage)->withQueryString();

        // If AJAX infinite scroll/lazy batch request, return partial rows view with metadata
        if ($request->ajax()) {
            $html = view('admin.finance.payments._rows', compact('payments'))->render();
            return response()->json([
                'html' => $html,
                'current_count' => $payments->count(),
                'total_count' => $payments->total(),
                'has_more' => $payments->hasMorePages(),
                'next_page' => $payments->currentPage() + 1,
                'excl_sum' => number_format($payments->sum('amount_excl_vat'), 2),
                'vat_sum' => number_format($payments->sum('vat_amount'), 2),
                'gross_sum' => number_format($payments->sum('gross_amount'), 2),
            ]);
        }

        // Executive KPI Metrics across all records
        $totalGross = (float) PaymentEntry::sum('gross_amount');
        $totalNet = (float) PaymentEntry::sum('amount_excl_vat');
        $totalVat = (float) PaymentEntry::sum('vat_amount');
        $totalPendingCheques = (float) PaymentEntry::where('status', 'pending_clearance')->sum('gross_amount');
        $totalEntries = PaymentEntry::count();

        // Data for modal & filter controls
        $categories = PaymentEntry::getCategories();
        $accounts = FinancialAccount::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name_en')->get();
        $unpaidInvoices = PurchaseInvoice::with('supplier')
            ->whereIn('payment_status', ['unpaid', 'partially_paid'])
            ->latest('id')
            ->get();

        return view('admin.finance.payments.index', [
            'payments' => $payments,
            'totalGross' => $totalGross,
            'totalNet' => $totalNet,
            'totalVat' => $totalVat,
            'totalPendingCheques' => $totalPendingCheques,
            'totalEntries' => $totalEntries,
            'categories' => $categories,
            'accounts' => $accounts,
            'suppliers' => $suppliers,
            'unpaidInvoices' => $unpaidInvoices,
            'search' => $search,
            'category' => $category,
            'accountId' => $accountId,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Store a newly created payment entry in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_number' => 'nullable|string|max:50|unique:payment_entries,payment_number',
            'entry_date' => 'required|date',
            'category' => 'required|string|max:60',
            'description' => 'nullable|string|max:500',
            'payee_type' => 'required|string|in:supplier,general_payee,employee',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_invoice_id' => 'nullable|exists:purchase_invoices,id',
            'supplier_code' => 'nullable|string|max:50',
            'payee_name' => 'nullable|string|max:255',
            'amount_excl_vat' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'gross_amount' => 'nullable|numeric|min:0',
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'payment_method' => 'required|string|max:50',
            'cheque_number' => 'nullable|string|max:100',
            'cheque_bank' => 'nullable|string|max:100',
            'cheque_due_date' => 'nullable|date',
            'status' => 'nullable|string|in:posted,pending_clearance',
            'notes' => 'nullable|string',
        ]);

        // Auto-fill payee metadata if supplier is selected
        if (!empty($validated['supplier_id'])) {
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $validated['payee_name'] = ($validated['payee_name'] ?? null) ?: ($supplier->name_en ?: $supplier->name_ar);
                $validated['supplier_code'] = ($validated['supplier_code'] ?? null) ?: ($supplier->code ?: ('SUP-' . $supplier->id));
            }
        } elseif (empty($validated['payee_name'])) {
            $validated['payee_name'] = 'General Payee';
        }

        // If purchase_invoice_id is selected and description is empty, auto-populate reference
        if (!empty($validated['purchase_invoice_id']) && empty($validated['description'])) {
            $inv = PurchaseInvoice::find($validated['purchase_invoice_id']);
            if ($inv) {
                $validated['description'] = 'Settlement for Bill #' . $inv->invoice_number;
            }
        }

        $this->paymentService->recordEntry($validated);

        return redirect()->route('admin.finance.payments.index')
            ->with('status', app()->getLocale() === 'ar' ? 'تم تسجيل وترحيل سند وقيد الصرف بنجاح وتحديث أرصدة الحسابات.' : 'Payment Entry recorded successfully and ledger balances updated.');
    }

    /**
     * Mark a pending cheque as cleared.
     */
    public function clearCheque(Request $request, PaymentEntry $paymentEntry): RedirectResponse|JsonResponse
    {
        $this->paymentService->clearCheque($paymentEntry);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => app()->getLocale() === 'ar' ? 'تم صرف وتسوية الشيك البنكي بنجاح' : 'Cheque cleared successfully',
            ]);
        }

        return redirect()->back()
            ->with('status', app()->getLocale() === 'ar' ? 'تم صرف الشيك بنجاح وتحديث رصيد البنك.' : 'Cheque cleared successfully and bank balance updated.');
    }

    /**
     * Official Printable Corporate Payment Voucher (سند صرف رسمي).
     */
    public function voucher(int $id): View
    {
        $entry = PaymentEntry::with(['financialAccount', 'supplier', 'purchaseInvoice', 'creator'])
            ->findOrFail($id);

        return view('admin.finance.payments.voucher', compact('entry'));
    }

    /**
     * Export payment entries to CSV/Excel.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = PaymentEntry::with(['financialAccount', 'supplier', 'purchaseInvoice'])
            ->category($request->query('category'))
            ->account($request->query('account_id'))
            ->status($request->query('status'))
            ->dateBetween($request->query('date_from'), $request->query('date_to'));

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhere('payee_name', 'like', "%{$search}%")
                  ->orWhere('supplier_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $filename = 'payment_entries_' . date('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for proper Arabic rendering in Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Payment Number',
                'Date',
                'Category',
                'Supplier Code',
                'Payee Name',
                'Description',
                'Amount Excl. VAT (SAR)',
                'VAT Rate (%)',
                'VAT Amount (SAR)',
                'Gross Total (SAR)',
                'Payment Method',
                'Account / Bank',
                'Cheque Number',
                'Status',
                'Created At',
            ]);

            $query->chunk(250, function ($records) use ($handle) {
                foreach ($records as $item) {
                    fputcsv($handle, [
                        $item->payment_number,
                        $item->entry_date ? $item->entry_date->format('Y-m-d') : '',
                        $item->category,
                        $item->supplier_code ?: '',
                        $item->payee_name,
                        $item->description ?: '',
                        number_format($item->amount_excl_vat, 2, '.', ''),
                        number_format($item->vat_rate, 2, '.', ''),
                        number_format($item->vat_amount, 2, '.', ''),
                        number_format($item->gross_amount, 2, '.', ''),
                        $item->payment_method,
                        $item->financialAccount ? ($item->financialAccount->name_en . ' / ' . $item->financialAccount->name_ar) : '',
                        $item->cheque_number ?: '',
                        $item->status,
                        $item->created_at ? $item->created_at->format('Y-m-d H:i') : '',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
