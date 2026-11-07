<x-layouts.admin 
    :pageTitle="__('finance.create_purchase_invoice')" 
    :pageSubtitle="__('finance.purchase_invoices_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.purchase_invoices') => route('admin.finance.purchase-invoices.index'),
        __('finance.create_purchase_invoice') => route('admin.finance.purchase-invoices.create')
    ]"
>
    <div class="max-w-5xl mx-auto space-y-6">
        @if ($errors->any())
            <div id="bzInvoiceValidationCard" class="card p-5 border border-rose-400 bg-rose-50/50 dark:bg-rose-950/20 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-3 border-b border-rose-200 dark:border-rose-900/40 pb-3">
                    <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400 font-black text-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'أخطاء التحقق في بيانات فاتورة المشتريات' : 'Validation Errors in Purchase Invoice' }}</span>
                        <span class="badge bg-rose-500 text-white font-mono text-xs px-2 py-0.5 rounded-full">{{ $errors->count() }}</span>
                    </div>
                    <span class="text-xs text-rose-500 font-semibold">
                        {{ app()->getLocale() === 'ar' ? 'اضغط على أي حقل للتصحيح المباشر' : 'Click any item to jump directly to the field' }}
                    </span>
                </div>
                <div class="space-y-2">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center justify-between gap-3 p-2.5 bg-white dark:bg-[#062B49] border border-rose-200 dark:border-rose-900/30 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-300">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-xmark text-rose-500"></i>
                                {{ $error }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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

            <!-- Items Table Card: Control between Raw Materials and Finished Products -->
            <div class="card p-6 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                            <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
                            Invoice Procurement Items (Raw Materials & Finished Products)
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Dynamically add raw materials or finished products with batch numbers and expiry dates
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="addItemRow('raw_material')" class="btn btn-sm btn-outline border-emerald-500 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 font-bold rounded-xl">
                            <i class="fa-solid fa-flask mr-1 ml-1"></i> + Add Raw Material
                        </button>
                        <button type="button" onclick="addItemRow('product')" class="btn btn-sm btn-outline border-sky-500 text-sky-600 dark:text-sky-400 hover:bg-sky-50 font-bold rounded-xl">
                            <i class="fa-solid fa-prescription-bottle mr-1 ml-1"></i> + Add Finished Product
                        </button>
                    </div>
                </div>

                <div class="table-responsive overflow-x-auto">
                    <table class="table w-full" id="itemsTable">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900 text-xs uppercase text-slate-400 font-bold">
                                <th class="w-32">Type</th>
                                <th class="min-w-[200px]">Item & Description</th>
                                <th class="w-32">Batch #</th>
                                <th class="w-36">Expiry Date</th>
                                <th class="w-24 text-center">Qty</th>
                                <th class="w-28 text-right">Unit Price</th>
                                <th class="w-24 text-right">Tax (15%)</th>
                                <th class="w-28 text-right">Total</th>
                                <th class="w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @if($po && $po->items->isNotEmpty())
                                @foreach($po->items as $idx => $pItem)
                                    <tr class="item-row" data-row="{{ $idx }}">
                                        <td>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400">
                                                <i class="fa-solid fa-flask text-[10px]"></i> Raw Mat.
                                            </span>
                                            <input type="hidden" name="items[{{ $idx }}][item_type]" value="raw_material">
                                        </td>
                                        <td>
                                            <select name="items[{{ $idx }}][raw_material_id]" onchange="onSelectItem(this, {{ $idx }}, 'raw_material')" class="form-select text-xs w-full rounded-xl mb-1">
                                                <option value="">-- Choose Raw Material --</option>
                                                @foreach($rawMaterials as $rm)
                                                    <option value="{{ $rm->id }}" data-cost="{{ $rm->standard_cost }}" data-name="{{ $rm->name_en }}" {{ $pItem->raw_material_id == $rm->id ? 'selected' : '' }}>
                                                        {{ $rm->name_en }} ({{ $rm->material_code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="items[{{ $idx }}][item_name]" value="{{ $pItem->rawMaterial?->name_en ?? 'Raw Material' }}" required class="item-name form-control text-xs w-full rounded-xl">
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $idx }}][batch_number]" placeholder="LOT-RM-..." value="{{ $pItem->batch_number ?? '' }}" class="form-control text-xs rounded-xl font-mono">
                                        </td>
                                        <td>
                                            <input type="date" name="items[{{ $idx }}][expiry_date]" value="{{ $pItem->expiry_date?->format('Y-m-d') ?? '' }}" class="form-control text-xs rounded-xl font-mono">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $idx }}][quantity]" value="{{ $pItem->quantity }}" required oninput="calculateTotals()" class="item-qty form-control text-xs text-center rounded-xl font-mono font-bold">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{ $idx }}][unit_price]" value="{{ $pItem->unit_price }}" required oninput="calculateTotals()" class="item-price form-control text-xs text-right rounded-xl font-mono font-bold">
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
                                <tr class="item-row" data-row="0">
                                    <td>
                                        <select name="items[0][item_type]" onchange="switchRowType(this, 0)" class="form-select text-[11px] font-bold rounded-xl text-emerald-600 bg-emerald-50 dark:bg-emerald-950/30">
                                            <option value="raw_material" selected>🧪 Raw Material</option>
                                            <option value="product">💊 Finished Product</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div id="selector-wrapper-0">
                                            <select name="items[0][raw_material_id]" onchange="onSelectItem(this, 0, 'raw_material')" class="form-select text-xs w-full rounded-xl mb-1">
                                                <option value="">-- Choose Raw Material --</option>
                                                @foreach($rawMaterials as $rm)
                                                    <option value="{{ $rm->id }}" data-cost="{{ $rm->standard_cost }}" data-name="{{ $rm->name_en }}">
                                                        {{ $rm->name_en }} ({{ $rm->material_code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="text" name="items[0][item_name]" id="item-name-0" placeholder="Item description" required class="item-name form-control text-xs w-full rounded-xl">
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][batch_number]" placeholder="LOT-RM-2026-001" class="form-control text-xs rounded-xl font-mono">
                                    </td>
                                    <td>
                                        <input type="date" name="items[0][expiry_date]" value="{{ date('Y-m-d', strtotime('+24 months')) }}" class="form-control text-xs rounded-xl font-mono">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[0][quantity]" value="10" required oninput="calculateTotals()" class="item-qty form-control text-xs text-center rounded-xl font-mono font-bold">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[0][unit_price]" value="100.00" required oninput="calculateTotals()" class="item-price form-control text-xs text-right rounded-xl font-mono font-bold">
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
        const rawMaterialsList = @json($rawMaterials->map(fn($r) => ['id' => $r->id, 'name' => $r->name_en . ' (' . $r->material_code . ')', 'cost' => (float)$r->standard_cost]));
        const productsList = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name_en . ' (' . $p->sku . ')', 'cost' => (float)$p->cost_price]));

        let rowCount = {{ $po && $po->items->isNotEmpty() ? $po->items->count() : 1 }};

        function addItemRow(defaultType = 'raw_material') {
            const tbody = document.getElementById('itemsBody');
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.setAttribute('data-row', rowCount);

            const isRm = defaultType === 'raw_material';
            const defaultDate = new Date();
            defaultDate.setMonth(defaultDate.getMonth() + 24);
            const dateStr = defaultDate.toISOString().split('T')[0];

            let optionsHtml = isRm
                ? rawMaterialsList.map(item => `<option value="${item.id}" data-cost="${item.cost}" data-name="${item.name}">${item.name}</option>`).join('')
                : productsList.map(item => `<option value="${item.id}" data-cost="${item.cost}" data-name="${item.name}">${item.name}</option>`).join('');

            const selectName = isRm ? `items[${rowCount}][raw_material_id]` : `items[${rowCount}][product_id]`;
            const placeholder = isRm ? '-- Choose Raw Material --' : '-- Choose Finished Product --';
            const batchPrefix = isRm ? 'LOT-RM-' : 'LOT-FG-';

            tr.innerHTML = `
                <td>
                    <select name="items[${rowCount}][item_type]" onchange="switchRowType(this, ${rowCount})" class="form-select text-[11px] font-bold rounded-xl ${isRm ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/30' : 'text-sky-600 bg-sky-50 dark:bg-sky-950/30'}">
                        <option value="raw_material" ${isRm ? 'selected' : ''}>🧪 Raw Material</option>
                        <option value="product" ${!isRm ? 'selected' : ''}>💊 Finished Product</option>
                    </select>
                </td>
                <td>
                    <div id="selector-wrapper-${rowCount}">
                        <select name="${selectName}" onchange="onSelectItem(this, ${rowCount}, '${defaultType}')" class="form-select text-xs w-full rounded-xl mb-1">
                            <option value="">${placeholder}</option>
                            ${optionsHtml}
                        </select>
                    </div>
                    <input type="text" name="items[${rowCount}][item_name]" id="item-name-${rowCount}" placeholder="Item description" required class="item-name form-control text-xs w-full rounded-xl">
                </td>
                <td>
                    <input type="text" name="items[${rowCount}][batch_number]" placeholder="${batchPrefix}${new Date().getFullYear()}-001" class="form-control text-xs rounded-xl font-mono">
                </td>
                <td>
                    <input type="date" name="items[${rowCount}][expiry_date]" value="${dateStr}" class="form-control text-xs rounded-xl font-mono">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${rowCount}][quantity]" value="1" required oninput="calculateTotals()" class="item-qty form-control text-xs text-center rounded-xl font-mono font-bold">
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${rowCount}][unit_price]" value="0.00" required oninput="calculateTotals()" class="item-price form-control text-xs text-right rounded-xl font-mono font-bold">
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

        function switchRowType(selectElem, rowIdx) {
            const type = selectElem.value;
            const isRm = type === 'raw_material';
            selectElem.className = `form-select text-[11px] font-bold rounded-xl ${isRm ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-950/30' : 'text-sky-600 bg-sky-50 dark:bg-sky-950/30'}`;

            const wrapper = document.getElementById(`selector-wrapper-${rowIdx}`);
            const selectName = isRm ? `items[${rowIdx}][raw_material_id]` : `items[${rowIdx}][product_id]`;
            const placeholder = isRm ? '-- Choose Raw Material --' : '-- Choose Finished Product --';
            const list = isRm ? rawMaterialsList : productsList;

            let optionsHtml = list.map(item => `<option value="${item.id}" data-cost="${item.cost}" data-name="${item.name}">${item.name}</option>`).join('');

            wrapper.innerHTML = `
                <select name="${selectName}" onchange="onSelectItem(this, ${rowIdx}, '${type}')" class="form-select text-xs w-full rounded-xl mb-1">
                    <option value="">${placeholder}</option>
                    ${optionsHtml}
                </select>
            `;
        }

        function onSelectItem(selectElem, rowIdx, type) {
            const selectedOpt = selectElem.options[selectElem.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) return;

            const nameInput = document.getElementById(`item-name-${rowIdx}`);
            if (nameInput) {
                nameInput.value = selectedOpt.getAttribute('data-name') || selectedOpt.text;
            }

            const tr = selectElem.closest('tr');
            const cost = parseFloat(selectedOpt.getAttribute('data-cost')) || 0;
            if (cost > 0 && tr) {
                const priceInput = tr.querySelector('.item-price');
                if (priceInput) {
                    priceInput.value = cost.toFixed(2);
                }
            }

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
                const qtyInput = tr.querySelector('.item-qty');
                const priceInput = tr.querySelector('.item-price');
                if (!qtyInput || !priceInput) return;

                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const rowSub = qty * price;
                const rowTax = Math.round(rowSub * 0.15 * 100) / 100;
                const rowTotal = rowSub + rowTax;

                const taxEl = tr.querySelector('.item-tax');
                const totalEl = tr.querySelector('.item-row-total');
                if (taxEl) taxEl.innerText = rowTax.toFixed(2);
                if (totalEl) totalEl.innerText = rowTotal.toFixed(2);

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
