<x-layouts.admin 
    :pageTitle="__('finance.purchase_invoices')" 
    :pageSubtitle="__('finance.purchase_invoices_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.purchase_invoices') => route('admin.finance.purchase-invoices.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.finance.supplier-payments.index') }}" class="btn btn-outline font-bold shadow-sm">
                <i class="fa-solid fa-money-bill-transfer mr-1.5 ml-1.5 text-rose-500"></i>
                {{ __('finance.supplier_payments') }}
            </a>
            <a href="{{ route('admin.finance.purchase-invoices.create') }}" class="btn btn-primary font-bold shadow-md">
                <i class="fa-solid fa-file-circle-plus mr-1.5 ml-1.5"></i>
                {{ __('finance.create_purchase_invoice') }}
            </a>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Metrics Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ __('finance.total_purchases_invoiced') }}
            </span>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                {{ number_format($totalBilled, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
            </div>
        </div>

        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ __('finance.paid_amount') }}
            </span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                {{ number_format($totalPaid, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
            </div>
        </div>

        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ __('finance.supplier_payables') }} ({{ __('finance.due_amount') }})
            </span>
            <div class="text-2xl font-black text-amber-500 font-mono">
                {{ number_format($totalDue, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card p-4 sm:p-5 border border-slate-200 dark:border-slate-800 rounded-2xl mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.finance.purchase-invoices.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative flex-1 sm:max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute top-3 rtl:right-3 ltr:left-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="{{ __('finance.search') }}" 
                       class="form-control text-xs rtl:pr-8 ltr:pl-8 py-2 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 w-full">
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <select name="supplier_id" onchange="this.form.submit()" class="form-select text-xs py-2 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900">
                    <option value="all">{{ __('finance.all') }} {{ __('finance.supplier') }}</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ (string)$selectedSupplier === (string)$s->id ? 'selected' : '' }}>
                            {{ $s->name_en }}
                        </option>
                    @endforeach
                </select>

                <select name="payment_status" onchange="this.form.submit()" class="form-select text-xs py-2 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900">
                    <option value="all">{{ __('finance.all') }}</option>
                    <option value="paid" {{ $selectedPayment === 'paid' ? 'selected' : '' }}>{{ __('finance.status_paid') }}</option>
                    <option value="partially_paid" {{ $selectedPayment === 'partially_paid' ? 'selected' : '' }}>{{ __('finance.status_partially_paid') }}</option>
                    <option value="unpaid" {{ $selectedPayment === 'unpaid' ? 'selected' : '' }}>{{ __('finance.status_unpaid') }}</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold rounded-xl py-2 px-3">
                    <i class="fa-solid fa-filter mr-1 ml-1 text-slate-500"></i> {{ __('finance.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Purchase Invoices Ledger Table -->
    <div class="card overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49]">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/80 text-xs uppercase text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4">{{ __('finance.invoice_number') }}</th>
                        <th class="p-4">{{ __('finance.supplier') }}</th>
                        <th class="p-4">{{ __('finance.invoice_date') }}</th>
                        <th class="p-4">{{ __('finance.due_date') }}</th>
                        <th class="p-4 text-center">{{ __('finance.payment_status') }}</th>
                        <th class="p-4 text-right rtl:text-left">{{ __('finance.total') }}</th>
                        <th class="p-4 text-right rtl:text-left">{{ __('finance.due_amount') }}</th>
                        <th class="p-4 text-center w-32">{{ __('finance.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-sm">
                    @forelse($invoices as $inv)
                        @php
                            $isPaid = $inv->payment_status === 'paid';
                            $dueBal = $inv->due_balance;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <!-- Invoice # -->
                            <td class="p-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                <a href="{{ route('admin.finance.purchase-invoices.show', $inv->id) }}" class="hover:underline">
                                    {{ $inv->invoice_number }}
                                </a>
                                @if($inv->reference_number)
                                    <div class="text-[10px] text-slate-400">Ref: {{ $inv->reference_number }}</div>
                                @endif
                            </td>

                            <!-- Supplier -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white text-xs">
                                    {{ $inv->supplier?->name_en ?? 'Supplier' }}
                                </div>
                                @if($inv->purchaseOrder)
                                    <span class="text-[10px] text-slate-400 font-mono">PO: {{ $inv->purchaseOrder->po_number }}</span>
                                @endif
                            </td>

                            <!-- Invoice Date -->
                            <td class="p-4 text-xs font-mono text-slate-500 dark:text-slate-400">
                                {{ $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : '—' }}
                            </td>

                            <!-- Due Date -->
                            <td class="p-4 text-xs font-mono text-slate-500 dark:text-slate-400">
                                {{ $inv->due_date ? $inv->due_date->format('Y-m-d') : '—' }}
                            </td>

                            <!-- Payment Status -->
                            <td class="p-4 text-center">
                                <span class="badge {{ $isPaid ? 'badge-success' : ($inv->payment_status === 'partially_paid' ? 'badge-warning' : 'badge-danger') }} text-xs font-bold">
                                    {{ __('finance.status_' . $inv->payment_status) }}
                                </span>
                            </td>

                            <!-- Total -->
                            <td class="p-4 text-right rtl:text-left font-mono font-black text-slate-900 dark:text-white">
                                {{ number_format($inv->grand_total, 2) }} SAR
                            </td>

                            <!-- Due Amount -->
                            <td class="p-4 text-right rtl:text-left font-mono font-bold {{ $dueBal > 0 ? 'text-amber-500' : 'text-slate-400' }}">
                                {{ number_format($dueBal, 2) }} SAR
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($dueBal > 0)
                                        <button type="button" 
                                                onclick="openSupplierPayModal({{ $inv->id }}, '{{ addslashes($inv->invoice_number) }}', {{ $dueBal }})" 
                                                class="btn btn-primary btn-sm p-1.5 rounded-lg text-xs" 
                                                title="{{ __('finance.pay_supplier') }}">
                                            <i class="fa-solid fa-money-bill-transfer"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.finance.purchase-invoices.show', $inv->id) }}" class="btn btn-secondary btn-sm p-1.5 rounded-lg" title="View Dossier">
                                        <i class="fa-solid fa-eye text-xs text-sky-500"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                                No purchase invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Pay Supplier Modal -->
    <dialog id="modal-pay-supplier" class="modal">
        <div class="modal-box rounded-2xl max-w-md bg-white dark:bg-[#062B49] text-slate-900 dark:text-white p-6">
            <h3 class="font-black text-base flex items-center gap-2 mb-4">
                <i class="fa-solid fa-money-bill-transfer text-rose-500"></i>
                {{ __('finance.pay_supplier') }} &bull; <span id="modal-pay-supp-title" class="text-amber-500 font-mono"></span>
            </h3>
            <form id="paySupplierForm" method="POST" action="" class="space-y-4">
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
                    <input type="number" step="0.01" name="amount" id="modal-pay-supp-amount" required min="0.01" class="form-control text-xs w-full rounded-xl font-bold font-mono">
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

    <script>
        function openSupplierPayModal(invoiceId, invNumber, dueAmount) {
            document.getElementById('modal-pay-supp-title').innerText = invNumber;
            document.getElementById('modal-pay-supp-amount').value = dueAmount.toFixed(2);
            document.getElementById('modal-pay-supp-amount').max = dueAmount.toFixed(2);
            document.getElementById('paySupplierForm').action = "/admin/finance/purchase-invoices/" + invoiceId + "/pay";
            document.getElementById('modal-pay-supplier').showModal();
        }
    </script>
</x-layouts.admin>
