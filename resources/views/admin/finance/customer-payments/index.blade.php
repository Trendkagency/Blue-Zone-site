<x-layouts.admin 
    :pageTitle="__('finance.customer_payments')" 
    :pageSubtitle="__('finance.customer_payments_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.customer_payments') => route('admin.finance.customer-payments.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="document.getElementById('modal-new-payment').showModal()" class="btn btn-primary font-bold shadow-md">
                <i class="fa-solid fa-hand-holding-dollar mr-1.5 ml-1.5"></i>
                {{ __('finance.receive_customer_payment') }}
            </button>
            <a href="{{ route('admin.finance.sales-invoices.index') }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-file-invoice-dollar mr-1.5 ml-1.5 text-sky-500"></i>
                {{ __('finance.sales_invoices') }}
            </a>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Total Collected Metric -->
    <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm mb-6 max-w-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
            Total Customer Payments Collected
        </span>
        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
            {{ number_format($totalCollected, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
        </div>
    </div>

    <!-- Payments Ledger Table -->
    <div class="card overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49]">
        <div class="card-header p-5 bg-slate-50/70 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                <i class="fa-solid fa-receipt text-teal-600 dark:text-teal-400"></i>
                Customer Collections Ledger
            </h3>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/80 text-xs uppercase text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4">{{ __('finance.voucher_number') }}</th>
                        <th class="p-4">{{ __('finance.order_number') }}</th>
                        <th class="p-4">{{ __('finance.customer') }}</th>
                        <th class="p-4">{{ __('finance.payment_date') }}</th>
                        <th class="p-4">{{ __('finance.paid_from') }}</th>
                        <th class="p-4 text-center">{{ __('finance.payment_method') }}</th>
                        <th class="p-4 text-right rtl:text-left">{{ __('finance.amount') }}</th>
                        <th class="p-4 text-center w-28">{{ __('finance.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-sm">
                    @forelse($payments as $pay)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <!-- Receipt / Voucher # -->
                            <td class="p-4 font-mono font-bold text-teal-600 dark:text-teal-400">
                                {{ $pay->receipt_voucher_number ?? $pay->transaction_number }}
                                <div class="text-[10px] text-slate-400 font-mono">{{ $pay->transaction_number }}</div>
                            </td>

                            <!-- Order / Invoice # -->
                            <td class="p-4 font-mono text-xs text-sky-600">
                                @if($pay->order)
                                    <a href="{{ route('admin.orders.show', $pay->order->id) }}" class="hover:underline">
                                        {{ $pay->order->order_number }}
                                    </a>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $pay->order->invoice_number }}</div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Customer -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white text-xs">
                                    {{ $pay->customer?->name ?? ($pay->order?->customer_name ?? 'Client') }}
                                </div>
                            </td>

                            <!-- Payment Date -->
                            <td class="p-4 text-xs font-mono text-slate-500 dark:text-slate-400">
                                {{ $pay->transaction_date ? $pay->transaction_date->format('Y-m-d') : '—' }}
                            </td>

                            <!-- Account Credited -->
                            <td class="p-4 text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ $pay->account?->localized_name }}
                            </td>

                            <!-- Payment Method -->
                            <td class="p-4 text-center">
                                <span class="badge badge-neutral text-xs uppercase font-mono">
                                    {{ $pay->payment_method }}
                                </span>
                            </td>

                            <!-- Amount -->
                            <td class="p-4 text-right rtl:text-left font-mono font-black text-emerald-600 dark:text-emerald-400">
                                +{{ number_format($pay->amount, 2) }} SAR
                            </td>

                            <!-- Print Official Voucher -->
                            <td class="p-4 text-center">
                                <a href="{{ route('admin.finance.customer-payments.voucher', $pay->id) }}" target="_blank" class="btn btn-secondary btn-sm p-1.5 rounded-lg text-xs" title="{{ __('finance.print_voucher') }}">
                                    <i class="fa-solid fa-print text-emerald-500"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                                No customer payments recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- New Customer Payment Modal -->
    <dialog id="modal-new-payment" class="modal">
        <div class="modal-box rounded-2xl max-w-md bg-white dark:bg-[#062B49] text-slate-900 dark:text-white p-6">
            <h3 class="font-black text-base flex items-center gap-2 mb-4">
                <i class="fa-solid fa-hand-holding-dollar text-emerald-500"></i>
                {{ __('finance.receive_customer_payment') }}
            </h3>
            <form method="POST" action="{{ route('admin.finance.customer-payments.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.order_number') }} *</label>
                    <select name="order_id" required class="form-select text-xs w-full rounded-xl">
                        <option value="">Select Customer Order</option>
                        @foreach($unpaidOrders as $ord)
                            <option value="{{ $ord->id }}">
                                {{ $ord->order_number }} — {{ $ord->customer_name }} (Due: {{ number_format($ord->due_balance, 2) }} SAR)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Deposit Into Account *</label>
                    <select name="financial_account_id" required class="form-select text-xs w-full rounded-xl">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.amount') }} (SAR) *</label>
                    <input type="number" step="0.01" name="amount" required min="0.01" class="form-control text-xs w-full rounded-xl font-bold font-mono">
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
                    <input type="text" name="reference_number" placeholder="Bank auth code / transaction reference" class="form-control text-xs w-full rounded-xl">
                </div>
                <div class="modal-action flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-new-payment').close()" class="btn btn-secondary btn-sm rounded-xl">
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
