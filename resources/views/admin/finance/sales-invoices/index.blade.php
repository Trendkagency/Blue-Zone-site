<x-layouts.admin 
    :pageTitle="__('finance.sales_invoices')" 
    :pageSubtitle="__('finance.sales_invoices_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.sales_invoices') => route('admin.finance.sales-invoices.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.finance.customer-payments.index') }}" class="btn btn-outline font-bold shadow-sm">
                <i class="fa-solid fa-receipt mr-1.5 ml-1.5 text-teal-500"></i>
                {{ __('finance.customer_payments') }}
            </a>
            <a href="{{ route('admin.sales.orders.index') }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-cart-shopping mr-1.5 ml-1.5 text-sky-500"></i>
                {{ __('admin.menu.online_orders') }}
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
                {{ __('finance.total_sales_invoiced') }}
            </span>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                {{ number_format($totalInvoiced, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
            </div>
        </div>

        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ __('finance.status_paid') }}
            </span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                {{ $paidOrdersCount }}
            </div>
        </div>

        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ __('finance.unpaid_customer_invoices') }}
            </span>
            <div class="text-2xl font-black text-amber-500 font-mono">
                {{ $unpaidCount }}
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card p-4 sm:p-5 border border-slate-200 dark:border-slate-800 rounded-2xl mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.finance.sales-invoices.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative flex-1 sm:max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute top-3 rtl:right-3 ltr:left-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" 
                       placeholder="{{ __('finance.search') }}" 
                       class="form-control text-xs rtl:pr-8 ltr:pl-8 py-2 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 w-full">
            </div>

            <div class="flex items-center gap-2.5">
                <select name="payment_status" onchange="this.form.submit()" class="form-select text-xs py-2 rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900">
                    <option value="all">{{ __('finance.all') }}</option>
                    <option value="Paid" {{ $selectedPayment === 'Paid' ? 'selected' : '' }}>{{ __('finance.status_paid') }}</option>
                    <option value="Partially Paid" {{ $selectedPayment === 'Partially Paid' ? 'selected' : '' }}>{{ __('finance.status_partially_paid') }}</option>
                    <option value="Pending" {{ $selectedPayment === 'Pending' ? 'selected' : '' }}>{{ __('finance.status_unpaid') }}</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold rounded-xl py-2 px-3">
                    <i class="fa-solid fa-filter mr-1 ml-1 text-slate-500"></i> {{ __('finance.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Sales Invoices Ledger Table -->
    <div class="card overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49]">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/80 text-xs uppercase text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4">{{ __('finance.invoice_number') }}</th>
                        <th class="p-4">{{ __('finance.order_number') }}</th>
                        <th class="p-4">{{ __('finance.customer') }}</th>
                        <th class="p-4">{{ __('finance.invoice_date') }}</th>
                        <th class="p-4 text-center">{{ __('finance.payment_status') }}</th>
                        <th class="p-4 text-right rtl:text-left">{{ __('finance.total') }}</th>
                        <th class="p-4 text-right rtl:text-left">{{ __('finance.due_amount') }}</th>
                        <th class="p-4 text-center w-32">{{ __('finance.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-sm">
                    @forelse($invoices as $inv)
                        @php
                            $invNum = $inv->invoice_number ?? ('INV-' . str_replace(['BZ-', 'ORD-'], '', $inv->order_number));
                            $isPaid = strtolower($inv->payment_status ?? '') === 'paid';
                            $dueBal = $inv->due_balance;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <!-- Invoice # -->
                            <td class="p-4 font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.invoices.show', $inv->id) }}" class="hover:underline">
                                    {{ $invNum }}
                                </a>
                            </td>

                            <!-- Order # -->
                            <td class="p-4 font-mono text-xs text-slate-600 dark:text-slate-400">
                                <a href="{{ route('admin.orders.show', $inv->id) }}" class="hover:underline">
                                    {{ $inv->order_number }}
                                </a>
                            </td>

                            <!-- Customer -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white text-xs">
                                    {{ $inv->customer_name ?? 'Client' }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $inv->customer_phone ?? $inv->customer_email ?? '—' }}
                                </div>
                            </td>

                            <!-- Date -->
                            <td class="p-4 text-xs font-mono text-slate-500 dark:text-slate-400">
                                {{ $inv->date ? $inv->date->format('Y-m-d') : ($inv->created_at ? $inv->created_at->format('Y-m-d') : '—') }}
                            </td>

                            <!-- Payment Status -->
                            <td class="p-4 text-center">
                                <span class="badge {{ $isPaid ? 'badge-success' : ($dueBal < (float)$inv->total ? 'badge-warning' : 'badge-danger') }} text-xs font-bold">
                                    {{ $inv->payment_status ?? 'Pending' }}
                                </span>
                            </td>

                            <!-- Total -->
                            <td class="p-4 text-right rtl:text-left font-mono font-black text-slate-900 dark:text-white">
                                {{ number_format($inv->total, 2) }} SAR
                            </td>

                            <!-- Due Balance -->
                            <td class="p-4 text-right rtl:text-left font-mono font-bold {{ $dueBal > 0 ? 'text-amber-500' : 'text-slate-400' }}">
                                {{ number_format($dueBal, 2) }} SAR
                            </td>

                            <!-- Actions -->
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($dueBal > 0)
                                        <button type="button" 
                                                onclick="openPaymentModal({{ $inv->id }}, '{{ addslashes($invNum) }}', {{ $dueBal }})" 
                                                class="btn btn-primary btn-sm p-1.5 rounded-lg text-xs" 
                                                title="{{ __('finance.record_payment') }}">
                                            <i class="fa-solid fa-hand-holding-dollar"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.invoices.show', $inv->id) }}" class="btn btn-secondary btn-sm p-1.5 rounded-lg" title="{{ __('finance.view_invoice') }}">
                                        <i class="fa-solid fa-eye text-xs text-sky-500"></i>
                                    </a>
                                    <a href="{{ route('admin.invoices.print', $inv->id) }}" target="_blank" class="btn btn-secondary btn-sm p-1.5 rounded-lg" title="{{ __('finance.print_invoice') }}">
                                        <i class="fa-solid fa-print text-xs text-emerald-500"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                                No sales invoices found.
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

    <!-- Receive Payment Modal -->
    <dialog id="modal-receive-payment" class="modal">
        <div class="modal-box rounded-2xl max-w-md bg-white dark:bg-[#062B49] text-slate-900 dark:text-white p-6">
            <h3 class="font-black text-base flex items-center gap-2 mb-4">
                <i class="fa-solid fa-hand-holding-dollar text-emerald-500"></i>
                {{ __('finance.record_payment') }} &bull; <span id="modal-pay-inv-title" class="text-sky-500 font-mono"></span>
            </h3>
            <form id="receivePaymentForm" method="POST" action="" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.paid_from') }} / Deposit Into *</label>
                    <select name="financial_account_id" required class="form-select text-xs w-full rounded-xl">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.amount') }} (SAR) *</label>
                    <input type="number" step="0.01" name="amount" id="modal-pay-amount" required min="0.01" class="form-control text-xs w-full rounded-xl font-bold font-mono">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.payment_method') }} *</label>
                        <select name="payment_method" class="form-select text-xs w-full rounded-xl">
                            <option value="bank_transfer">Bank Transfer / حوالة</option>
                            <option value="mada">Mada / مدى</option>
                            <option value="credit_card">Credit Card / فيزا</option>
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
                    <input type="text" name="reference_number" placeholder="Bank receipt / auth code" class="form-control text-xs w-full rounded-xl">
                </div>
                <div class="modal-action flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-receive-payment').close()" class="btn btn-secondary btn-sm rounded-xl">
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
        function openPaymentModal(orderId, invNumber, dueAmount) {
            document.getElementById('modal-pay-inv-title').innerText = invNumber;
            document.getElementById('modal-pay-amount').value = dueAmount.toFixed(2);
            document.getElementById('modal-pay-amount').max = dueAmount.toFixed(2);
            document.getElementById('receivePaymentForm').action = "/admin/finance/sales-invoices/" + orderId + "/receive-payment";
            document.getElementById('modal-receive-payment').showModal();
        }
    </script>
</x-layouts.admin>
