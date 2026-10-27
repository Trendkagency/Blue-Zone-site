<x-layouts.admin 
    :pageTitle="'Bill: ' . $invoice->invoice_number" 
    :pageSubtitle="__('finance.purchase_invoices_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.purchase_invoices') => route('admin.finance.purchase-invoices.index'),
        $invoice->invoice_number => route('admin.finance.purchase-invoices.show', $invoice->id)
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap">
            @if($invoice->due_balance > 0)
                <button type="button" onclick="document.getElementById('modal-pay-supplier').showModal()" class="btn btn-primary font-bold shadow-md">
                    <i class="fa-solid fa-money-bill-transfer mr-1.5 ml-1.5"></i>
                    {{ __('finance.pay_supplier') }} ({{ number_format($invoice->due_balance, 2) }} SAR)
                </button>
            @endif
            <a href="{{ route('admin.finance.purchase-invoices.index') }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i> Back to Bills
            </a>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Bill Details & Line Items -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Items Card -->
            <div class="card p-6 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
                <h3 class="text-base font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-amber-500"></i>
                    {{ __('finance.items_table') }}
                </h3>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900 text-xs uppercase text-slate-400 font-bold">
                                <th>{{ __('finance.item_name') }}</th>
                                <th class="text-center">{{ __('finance.quantity') }}</th>
                                <th class="text-right">{{ __('finance.unit_price') }}</th>
                                <th class="text-right">{{ __('finance.tax') }}</th>
                                <th class="text-right">{{ __('finance.total') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            @forelse($invoice->items as $item)
                                <tr>
                                    <td class="font-bold text-slate-900 dark:text-white">
                                        {{ $item->item_name }}
                                        @if($item->rawMaterial)
                                            <div class="text-[10px] text-slate-400 font-mono">{{ $item->rawMaterial->code }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center font-mono">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-right font-mono">{{ number_format($item->unit_price, 2) }} SAR</td>
                                    <td class="text-right font-mono text-slate-500">{{ number_format($item->tax_amount, 2) }} SAR</td>
                                    <td class="text-right font-mono font-black text-slate-900 dark:text-white">{{ number_format($item->total, 2) }} SAR</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-slate-400 text-xs">No items recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <div class="w-72 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-500">
                            <span>{{ __('finance.subtotal') }}:</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ number_format($invoice->subtotal, 2) }} SAR</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>{{ __('finance.tax') }} (15%):</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ number_format($invoice->tax_amount, 2) }} SAR</span>
                        </div>
                        <div class="flex justify-between text-base font-black text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700">
                            <span>{{ __('finance.total') }}:</span>
                            <span class="font-mono text-amber-500 text-lg">{{ number_format($invoice->grand_total, 2) }} SAR</span>
                        </div>
                        <div class="flex justify-between text-xs font-bold text-emerald-600">
                            <span>{{ __('finance.paid_amount') }}:</span>
                            <span class="font-mono">{{ number_format($invoice->paid_amount, 2) }} SAR</span>
                        </div>
                        <div class="flex justify-between text-xs font-bold text-rose-500 pt-1 border-t border-slate-100 dark:border-slate-800">
                            <span>{{ __('finance.due_amount') }}:</span>
                            <span class="font-mono">{{ number_format($invoice->due_balance, 2) }} SAR</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Transactions History -->
            <div class="card p-6 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
                <h3 class="text-base font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-500"></i>
                    Disbursement & Payment History
                </h3>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900 text-xs uppercase text-slate-400 font-bold">
                                <th>TXN #</th>
                                <th>{{ __('finance.date') }}</th>
                                <th>{{ __('finance.paid_from') }}</th>
                                <th>{{ __('finance.payment_method') }}</th>
                                <th class="text-right">{{ __('finance.amount') }}</th>
                                <th class="text-center w-20">{{ __('finance.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            @forelse($invoice->payments as $pay)
                                <tr>
                                    <td class="font-mono font-bold text-xs text-slate-900 dark:text-white">{{ $pay->transaction_number }}</td>
                                    <td class="text-xs font-mono text-slate-500">{{ $pay->transaction_date ? $pay->transaction_date->format('Y-m-d') : '—' }}</td>
                                    <td class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $pay->account?->localized_name }}</td>
                                    <td class="text-xs uppercase font-mono">{{ $pay->payment_method }}</td>
                                    <td class="text-right font-mono font-black text-emerald-600">{{ number_format($pay->amount, 2) }} SAR</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.finance.supplier-payments.voucher', $pay->id) }}" target="_blank" class="btn btn-secondary btn-sm p-1 rounded-lg text-xs" title="Print Voucher">
                                            <i class="fa-solid fa-print text-amber-500"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-slate-400 text-xs">No payments recorded yet for this bill.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right: Supplier & Meta Sidebar -->
        <div class="space-y-6">
            
            <!-- Bill Status Card -->
            <div class="card p-6 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status</span>
                    <span class="badge {{ $invoice->payment_status === 'paid' ? 'badge-success' : ($invoice->payment_status === 'partially_paid' ? 'badge-warning' : 'badge-danger') }} text-xs font-bold">
                        {{ __('finance.status_' . $invoice->payment_status) }}
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">{{ __('finance.invoice_number') }}:</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">{{ __('finance.invoice_date') }}:</span>
                        <span class="font-mono text-slate-900 dark:text-white">{{ $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">{{ __('finance.due_date') }}:</span>
                        <span class="font-mono text-amber-500 font-bold">{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '—' }}</span>
                    </div>
                    @if($invoice->reference_number)
                        <div class="flex justify-between">
                            <span class="text-slate-400">{{ __('finance.supplier_reference') }}:</span>
                            <span class="font-mono text-slate-900 dark:text-white">{{ $invoice->reference_number }}</span>
                        </div>
                    @endif
                    @if($invoice->purchaseOrder)
                        <div class="flex justify-between">
                            <span class="text-slate-400">{{ __('finance.linked_po') }}:</span>
                            <a href="{{ route('admin.purchase-orders.show', $invoice->purchaseOrder->id) }}" class="font-mono text-sky-500 hover:underline">
                                {{ $invoice->purchaseOrder->po_number }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Supplier Dossier Card -->
            <div class="card p-6 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-truck-field text-sky-500"></i>
                    {{ __('finance.supplier') }}
                </h4>

                @if($invoice->supplier)
                    <div class="space-y-2 text-xs">
                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $invoice->supplier->name_en }}</div>
                        <div class="text-slate-500">{{ $invoice->supplier->name_ar }}</div>
                        <div class="text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div>Email: {{ $invoice->supplier->email ?? '—' }}</div>
                            <div>Phone: {{ $invoice->supplier->phone ?? '—' }}</div>
                            <div>Tax ID: {{ $invoice->supplier->tax_number ?? '—' }}</div>
                        </div>
                    </div>
                @else
                    <span class="text-slate-400 text-xs">No supplier assigned.</span>
                @endif
            </div>

        </div>

    </div>

    <!-- Pay Supplier Modal -->
    <dialog id="modal-pay-supplier" class="modal">
        <div class="modal-box rounded-2xl max-w-md bg-white dark:bg-[#062B49] text-slate-900 dark:text-white p-6">
            <h3 class="font-black text-base flex items-center gap-2 mb-4">
                <i class="fa-solid fa-money-bill-transfer text-rose-500"></i>
                {{ __('finance.pay_supplier') }}
            </h3>
            <form method="POST" action="{{ route('admin.finance.purchase-invoices.pay', $invoice->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.paid_from') }} *</label>
                    <select name="financial_account_id" required class="form-select text-xs w-full rounded-xl">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.amount') }} (SAR) *</label>
                    <input type="number" step="0.01" name="amount" value="{{ number_format($invoice->due_balance, 2, '.', '') }}" max="{{ number_format($invoice->due_balance, 2, '.', '') }}" required min="0.01" class="form-control text-xs w-full rounded-xl font-bold font-mono">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.payment_method') }} *</label>
                        <select name="payment_method" class="form-select text-xs w-full rounded-xl">
                            <option value="bank_transfer">Bank Transfer / حوالة</option>
                            <option value="cheque">Corporate Cheque / شيك</option>
                            <option value="cash">Cash / نقدي</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.payment_date') }} *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="form-control text-xs w-full rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.reference_number') }}</label>
                    <input type="text" name="reference_number" placeholder="Bank transaction / voucher #" class="form-control text-xs w-full rounded-xl">
                </div>
                <div class="modal-action flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-pay-supplier').close()" class="btn btn-secondary btn-sm rounded-xl">
                        {{ __('finance.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-xl font-bold">
                        <i class="fa-solid fa-check mr-1 ml-1"></i> {{ __('finance.save') }}
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</x-layouts.admin>
