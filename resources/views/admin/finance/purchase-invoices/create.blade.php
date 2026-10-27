<x-layouts.admin 
    :pageTitle="__('finance.create_purchase_invoice')" 
    :pageSubtitle="__('finance.purchase_invoices_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.purchase_invoices') => route('admin.finance.purchase-invoices.index'),
        __('finance.create_purchase_invoice') => route('admin.finance.purchase-invoices.create')
    ]"
>
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.finance.purchase-invoices.store') }}" class="space-y-6">
            @csrf

            <!-- Header Info Card -->
            <div class="card p-6 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
                <h3 class="text-base font-black text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-amber-500"></i>
                    Vendor Bill Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.supplier') }} *</label>
                        <select name="supplier_id" required class="form-select text-xs w-full rounded-xl">
                            <option value="">{{ __('finance.select_supplier') }}</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ $po && $po->supplier_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->name_en }} ({{ $s->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.linked_po') }}</label>
                        <select name="purchase_order_id" class="form-select text-xs w-full rounded-xl">
                            <option value="">{{ __('finance.select_po') }}</option>
                            @foreach($purchaseOrders as $order)
                                <option value="{{ $order->id }}" {{ $po && $po->id == $order->id ? 'selected' : '' }}>
                                    {{ $order->po_number }} — {{ number_format($order->grand_total, 2) }} SAR
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.supplier_reference') }}</label>
                        <input type="text" name="reference_number" placeholder="e.g. SUP-INV-9921" class="form-control text-xs w-full rounded-xl">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.invoice_date') }} *</label>
                        <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="form-control text-xs w-full rounded-xl">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.due_date') }}</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="form-control text-xs w-full rounded-xl">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.notes') }}</label>
                        <input type="text" name="notes" placeholder="Batch / PO notes" class="form-control text-xs w-full rounded-xl">
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="card p-6 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                        <i class="fa-solid fa-list text-sky-500"></i>
                        {{ __('finance.items_table') }}
                    </h3>
                    <button type="button" onclick="addItemRow()" class="btn btn-outline btn-sm font-bold rounded-xl">
                        <i class="fa-solid fa-plus mr-1 ml-1 text-emerald-500"></i> Add Item Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table" id="itemsTable">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900 text-xs uppercase text-slate-400 font-bold">
                                <th>{{ __('finance.item_name') }}</th>
                                <th class="w-28 text-center">{{ __('finance.quantity') }}</th>
                                <th class="w-36 text-right">{{ __('finance.unit_price') }}</th>
                                <th class="w-36 text-right">{{ __('finance.tax') }} (15%)</th>
                                <th class="w-36 text-right">{{ __('finance.amount') }}</th>
                                <th class="w-12 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @if($po && $po->items->isNotEmpty())
                                @foreach($po->items as $idx => $pItem)
                                    <tr>
                                        <td>
                                            <input type="text" name="items[{{ $idx }}][item_name]" value="{{ $pItem->rawMaterial?->name_en ?? 'Raw Material' }}" required class="form-control text-xs w-full rounded-xl">
                                            <input type="hidden" name="items[{{ $idx }}][raw_material_id]" value="{{ $pItem->raw_material_id }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $idx }}][quantity]" value="{{ $pItem->quantity }}" required oninput="calculateTotals()" class="item-qty form-control text-xs text-center rounded-xl">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $idx }}][unit_price]" value="{{ $pItem->unit_price }}" required oninput="calculateTotals()" class="item-price form-control text-xs text-right rounded-xl">
                                        </td>
                                        <td class="text-right font-mono text-xs text-slate-500 item-tax">0.00</td>
                                        <td class="text-right font-mono font-bold text-xs text-slate-900 dark:text-white item-row-total">0.00</td>
                                        <td class="text-center">
                                            <button type="button" onclick="removeItemRow(this)" class="text-rose-500 hover:text-rose-700 text-sm">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>
                                        <input type="text" name="items[0][item_name]" placeholder="e.g. Pharmaceutical Trans-Resveratrol 99%" required class="form-control text-xs w-full rounded-xl">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[0][quantity]" value="1" required oninput="calculateTotals()" class="item-qty form-control text-xs text-center rounded-xl">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[0][unit_price]" value="1000.00" required oninput="calculateTotals()" class="item-price form-control text-xs text-right rounded-xl">
                                    </td>
                                    <td class="text-right font-mono text-xs text-slate-500 item-tax">150.00</td>
                                    <td class="text-right font-mono font-bold text-xs text-slate-900 dark:text-white item-row-total">1150.00</td>
                                    <td class="text-center">
                                        <button type="button" onclick="removeItemRow(this)" class="text-rose-500 hover:text-rose-700 text-sm">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Totals Breakdown -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <div class="w-72 space-y-2 text-sm">
                        <div class="flex justify-between text-slate-500">
                            <span>{{ __('finance.subtotal') }}:</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white" id="displaySubtotal">0.00 SAR</span>
                            <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>{{ __('finance.tax') }} (15%):</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white" id="displayTax">0.00 SAR</span>
                            <input type="hidden" name="tax_amount" id="inputTax" value="0">
                        </div>
                        <div class="flex justify-between text-base font-black text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-700">
                            <span>{{ __('finance.total') }}:</span>
                            <span class="font-mono text-amber-500 text-lg" id="displayGrandTotal">0.00 SAR</span>
                            <input type="hidden" name="grand_total" id="inputGrandTotal" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.finance.purchase-invoices.index') }}" class="btn btn-secondary font-bold rounded-xl px-5">
                    {{ __('finance.cancel') }}
                </a>
                <button type="submit" class="btn btn-primary font-bold rounded-xl px-6 shadow-md">
                    <i class="fa-solid fa-check mr-1.5 ml-1.5"></i> {{ __('finance.save') }}
                </button>
            </div>
        </form>
    </div>

    <script>
        let rowCount = {{ $po && $po->items->isNotEmpty() ? $po->items->count() : 1 }};

        function addItemRow() {
            const tbody = document.getElementById('itemsBody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" name="items[${rowCount}][item_name]" placeholder="Item Description" required class="form-control text-xs w-full rounded-xl">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${rowCount}][quantity]" value="1" required oninput="calculateTotals()" class="item-qty form-control text-xs text-center rounded-xl">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${rowCount}][unit_price]" value="0.00" required oninput="calculateTotals()" class="item-price form-control text-xs text-right rounded-xl">
                </td>
                <td class="text-right font-mono text-xs text-slate-500 item-tax">0.00</td>
                <td class="text-right font-mono font-bold text-xs text-slate-900 dark:text-white item-row-total">0.00</td>
                <td class="text-center">
                    <button type="button" onclick="removeItemRow(this)" class="text-rose-500 hover:text-rose-700 text-sm">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            rowCount++;
            calculateTotals();
        }

        function removeItemRow(btn) {
            const rows = document.querySelectorAll('#itemsBody tr');
            if (rows.length > 1) {
                btn.closest('tr').remove();
                calculateTotals();
            }
        }

        function calculateTotals() {
            let subtotal = 0;
            let tax = 0;

            const rows = document.querySelectorAll('#itemsBody tr');
            rows.forEach(tr => {
                const qty = parseFloat(tr.querySelector('.item-qty').value) || 0;
                const price = parseFloat(tr.querySelector('.item-price').value) || 0;
                const rowSub = qty * price;
                const rowTax = Math.round(rowSub * 0.15 * 100) / 100;
                const rowTotal = rowSub + rowTax;

                tr.querySelector('.item-tax').innerText = rowTax.toFixed(2);
                tr.querySelector('.item-row-total').innerText = rowTotal.toFixed(2);

                subtotal += rowSub;
                tax += rowTax;
            });

            const grand = subtotal + tax;

            document.getElementById('displaySubtotal').innerText = subtotal.toFixed(2) + ' SAR';
            document.getElementById('inputSubtotal').value = subtotal.toFixed(2);

            document.getElementById('displayTax').innerText = tax.toFixed(2) + ' SAR';
            document.getElementById('inputTax').value = tax.toFixed(2);

            document.getElementById('displayGrandTotal').innerText = grand.toFixed(2) + ' SAR';
            document.getElementById('inputGrandTotal').value = grand.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', calculateTotals);
    </script>
</x-layouts.admin>
