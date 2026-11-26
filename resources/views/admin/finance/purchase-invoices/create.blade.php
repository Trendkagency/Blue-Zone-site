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
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('finance.supplier') }} <span class="text-rose-500">*</span></label>
                        <select name="supplier_id" id="supplier_select" onchange="onSupplierChange(this)" required class="form-select text-xs w-full rounded-xl">
                            <option value="">{{ __('finance.select_supplier') }}</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" data-terms="{{ $s->payment_terms }}" {{ $po && $po->supplier_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->name_en }} ({{ $s->code }}) @if($s->payment_terms) — [{{ $s->payment_terms }}] @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('finance.linked_po') }}</label>
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
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('finance.supplier_reference') }}</label>
                        <input type="text" name="reference_number" placeholder="e.g. SUP-INV-9921" class="form-control text-xs w-full rounded-xl">
                    </div>

                    <!-- 1. Invoice Date (Issue Date: Strictly Automatic Today & Locked/Disabled) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ __('finance.invoice_date') }} <span class="text-rose-500">*</span>
                            </label>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                <i class="fa-solid fa-lock text-[9px] text-slate-400"></i>
                                {{ app()->getLocale() === 'ar' ? 'تلقائي (اليوم)' : 'Automatic Today' }}
                            </span>
                        </div>
                        <div class="relative">
                            <input type="date" 
                                   id="invoice_date" 
                                   name="invoice_date" 
                                   value="{{ date('Y-m-d') }}" 
                                   readonly 
                                   tabindex="-1"
                                   required 
                                   class="form-control text-xs w-full rounded-xl bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 cursor-not-allowed select-none font-mono font-bold pl-9 rtl:pr-9 rtl:pl-3"
                                   title="{{ app()->getLocale() === 'ar' ? 'تاريخ الإصدار محدد تلقائياً باليوم الحالي لأغراض التدقيق والمطابقة المحاسبية' : 'Issue date is locked to today for audit & accounting compliance' }}">
                            <div class="absolute inset-y-0 left-0 rtl:right-0 rtl:left-auto flex items-center pl-3 rtl:pr-3 pointer-events-none text-slate-400 dark:text-slate-500 text-xs">
                                <i class="fa-solid fa-calendar-day"></i>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-[9px]"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'تاريخ التقييد المحاسبي المعتمد رسمياً لليوم الحالي' : 'Official ledger posting date fixed to today.' }}</span>
                        </p>
                    </div>

                    <!-- 2. Due Date (Calculated based on Logic with Small Responsive Customize Button) -->
                    <div>
                        <div class="flex items-center justify-between mb-1 gap-1 flex-wrap">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ __('finance.due_date') }}
                            </label>
                            <button type="button" 
                                    id="btnCustomizeDueDate" 
                                    onclick="toggleCustomizeDueDate()" 
                                    class="btn btn-ghost btn-xs text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/40 rounded-lg inline-flex items-center gap-1 px-2 py-0.5 border border-sky-200 dark:border-sky-800/60 shadow-xs transition"
                                    title="{{ app()->getLocale() === 'ar' ? 'تخصيص تاريخ الاستحقاق يدوياً' : 'Manually override due date' }}">
                                <i class="fa-solid fa-pen-to-square text-[10px]" id="customizeIcon"></i>
                                <span id="customizeBtnText">{{ app()->getLocale() === 'ar' ? 'تخصيص التاريخ' : 'Customize Date' }}</span>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="date" 
                                   id="due_date" 
                                   name="due_date" 
                                   value="{{ date('Y-m-d', strtotime('+30 days')) }}" 
                                   readonly
                                   class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 font-mono font-bold pl-9 rtl:pr-9 rtl:pl-3 transition"
                                   onchange="onManualDueDateChange()">
                            <div class="absolute inset-y-0 left-0 rtl:right-0 rtl:left-auto flex items-center pl-3 rtl:pr-3 pointer-events-none text-sky-500 text-xs" id="dueDateIcon">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                        </div>

                        <!-- Dynamic Logic Preset Pills & Status -->
                        <div class="mt-1.5 flex items-center justify-between gap-1 flex-wrap text-[10px]">
                            <div class="flex items-center gap-1 flex-wrap" id="dueTermsPills">
                                <button type="button" onclick="setDueDatePreset(0, 'Immediate (0d)')" class="term-pill px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition">
                                    {{ app()->getLocale() === 'ar' ? 'فوري (0 يوم)' : '0d (Due)' }}
                                </button>
                                <button type="button" onclick="setDueDatePreset(15, 'Net 15 (+15d)')" class="term-pill px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition">
                                    Net 15
                                </button>
                                <button type="button" onclick="setDueDatePreset(30, 'Net 30 (+30d)')" class="term-pill active-pill px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-300 dark:border-sky-800 transition">
                                    Net 30
                                </button>
                                <button type="button" onclick="setDueDatePreset(60, 'Net 60 (+60d)')" class="term-pill px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition">
                                    Net 60
                                </button>
                                <button type="button" onclick="setDueDatePreset(90, 'Net 90 (+90d)')" class="term-pill px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition">
                                    Net 90
                                </button>
                            </div>
                            <span id="dueDateLogicBadge" class="font-mono text-[10px] text-sky-600 dark:text-sky-400 font-bold truncate max-w-[120px]">
                                Net 30 (Default)
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">{{ __('finance.notes') }}</label>
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
                                                    <option value="{{ $rm->id }}" data-cost="{{ $rm->standard_cost }}" data-name="{{ $rm->name_en }}" data-shelf-life="{{ (int) ($rm->shelf_life_days ?? 730) }}" {{ $pItem->raw_material_id == $rm->id ? 'selected' : '' }}>
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
                                            <div class="flex items-center gap-1">
                                                <input type="date" name="items[{{ $idx }}][expiry_date]" value="{{ $pItem->expiry_date?->format('Y-m-d') ?? '' }}" required min="{{ now()->addDay()->toDateString() }}" class="item-expiry form-control text-xs rounded-xl font-mono">
                                                <button type="button" onclick="autoFillExpiry(this)" title="Auto-calculate from shelf life" class="px-2 py-1.5 text-[10px] font-bold rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0">Auto</button>
                                            </div>
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
                                                    <option value="{{ $rm->id }}" data-cost="{{ $rm->standard_cost }}" data-name="{{ $rm->name_en }}" data-shelf-life="{{ (int) ($rm->shelf_life_days ?? 730) }}">
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
                                        <div class="flex items-center gap-1">
                                            <input type="date" name="items[0][expiry_date]" required min="{{ now()->addDay()->toDateString() }}" class="item-expiry form-control text-xs rounded-xl font-mono">
                                            <button type="button" onclick="autoFillExpiry(this)" title="Auto-calculate from shelf life" class="px-2 py-1.5 text-[10px] font-bold rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0">Auto</button>
                                        </div>
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
        const rawMaterialsList = @json($rawMaterials->map(fn($r) => ['id' => $r->id, 'name' => $r->name_en . ' (' . $r->material_code . ')', 'cost' => (float)$r->standard_cost, 'shelf_life' => (int)($r->shelf_life_days ?? 730)]));
        const productsList = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name_en . ' (' . $p->sku . ')', 'cost' => (float)$p->cost_price, 'shelf_life' => (int)(($p->shelf_life_months ?? 24) * 30)]));

        let rowCount = {{ $po && $po->items->isNotEmpty() ? $po->items->count() : 1 }};

        function getMinExpiryDate() {
            const d = new Date();
            d.setDate(d.getDate() + 1);
            return d.toISOString().split('T')[0];
        }

        function addItemRow(defaultType = 'raw_material') {
            const tbody = document.getElementById('itemsBody');
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.setAttribute('data-row', rowCount);

            const isRm = defaultType === 'raw_material';
            const minDate = getMinExpiryDate();

            let optionsHtml = isRm
                ? rawMaterialsList.map(item => `<option value="${item.id}" data-cost="${item.cost}" data-name="${item.name}" data-shelf-life="${item.shelf_life}">${item.name}</option>`).join('')
                : productsList.map(item => `<option value="${item.id}" data-cost="${item.cost}" data-name="${item.name}" data-shelf-life="${item.shelf_life}">${item.name}</option>`).join('');

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
                    <div class="flex items-center gap-1">
                        <input type="date" name="items[${rowCount}][expiry_date]" required min="${minDate}" class="item-expiry form-control text-xs rounded-xl font-mono">
                        <button type="button" onclick="autoFillExpiry(this)" title="Auto-calculate from shelf life" class="px-2 py-1.5 text-[10px] font-bold rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 shrink-0">Auto</button>
                    </div>
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

            let optionsHtml = list.map(item => `<option value="${item.id}" data-cost="${item.cost}" data-name="${item.name}" data-shelf-life="${item.shelf_life}">${item.name}</option>`).join('');

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

        function autoFillExpiry(btn) {
            const tr = btn.closest('tr');
            if (!tr) return;
            const expiryInput = tr.querySelector('.item-expiry');
            if (!expiryInput) return;

            const itemSelect = tr.querySelector('select[name*="[raw_material_id]"], select[name*="[product_id]"]');
            const selectedOpt = itemSelect && itemSelect.selectedIndex >= 0 ? itemSelect.options[itemSelect.selectedIndex] : null;
            const shelfDays = parseInt(selectedOpt?.getAttribute('data-shelf-life') || '730', 10) || 730;

            const invoiceDateInput = document.querySelector('input[name="invoice_date"]');
            const baseDate = invoiceDateInput && invoiceDateInput.value ? new Date(invoiceDateInput.value) : new Date();
            baseDate.setDate(baseDate.getDate() + shelfDays);
            expiryInput.value = baseDate.toISOString().split('T')[0];
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

        document.addEventListener('DOMContentLoaded', function() {
            calculateTotals();

            // Enforce automatic today on invoice_date
            const invDateInput = document.getElementById('invoice_date');
            if (invDateInput) {
                invDateInput.value = getTodayDateStr();
            }

            // Initialize Due Date based on current supplier terms or default Net 30
            const supplierSelect = document.getElementById('supplier_select');
            if (supplierSelect && supplierSelect.value) {
                onSupplierChange(supplierSelect);
            } else {
                updateDueDateByDays(30, 'Net 30 (Default)');
            }
        });

        // ==========================================
        // SMART DATE & PAYMENT TERMS LOGIC
        // ==========================================
        const isArabic = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
        let isCustomDueDate = false;
        let currentLogicDays = 30;

        function getTodayDateStr() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function calculateDatePlusDays(days) {
            const d = new Date();
            d.setDate(d.getDate() + parseInt(days));
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        function parsePaymentTermsDays(termsStr) {
            if (!termsStr) return 30;
            const lower = termsStr.toLowerCase().trim();
            if (lower.includes('advance') || lower.includes('immediate') || lower.includes('cash') || lower.includes('receipt') || lower.includes('0')) {
                return 0;
            }
            const match = lower.match(/\d+/);
            if (match) {
                return parseInt(match[0]);
            }
            return 30;
        }

        function updateDueDateByDays(days, label = null) {
            currentLogicDays = days;
            const dateStr = calculateDatePlusDays(days);
            const dueDateInput = document.getElementById('due_date');
            if (dueDateInput) {
                dueDateInput.value = dateStr;
            }

            const badge = document.getElementById('dueDateLogicBadge');
            if (badge) {
                badge.innerText = label || (days === 0 ? (isArabic ? 'استحقاق فوري (0d)' : 'Due on Receipt (0d)') : `Net ${days} (+${days}d)`);
                badge.className = 'font-mono text-[10px] text-sky-600 dark:text-sky-400 font-bold truncate max-w-[120px]';
            }

            // Highlight matching preset button
            document.querySelectorAll('#dueTermsPills .term-pill').forEach(btn => {
                btn.classList.remove('active-pill', 'bg-sky-100', 'dark:bg-sky-950/60', 'text-sky-700', 'dark:text-sky-300', 'border', 'border-sky-300', 'dark:border-sky-800', 'font-bold');
                btn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'font-semibold');
                const btnText = btn.innerText.toLowerCase();
                if ((days === 0 && (btnText.includes('0') || btnText.includes('فوري'))) ||
                    (days > 0 && btnText.includes(String(days)))) {
                    btn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'font-semibold');
                    btn.classList.add('active-pill', 'bg-sky-100', 'dark:bg-sky-950/60', 'text-sky-700', 'dark:text-sky-300', 'border', 'border-sky-300', 'dark:border-sky-800', 'font-bold');
                }
            });
        }

        function setDueDatePreset(days, label = null) {
            isCustomDueDate = false;
            const dueDateInput = document.getElementById('due_date');
            if (dueDateInput) {
                dueDateInput.readOnly = true;
                dueDateInput.classList.remove('bg-white', 'dark:bg-slate-900', 'border-amber-400', 'dark:border-amber-500', 'ring-2', 'ring-amber-100', 'dark:ring-amber-950/40');
                dueDateInput.classList.add('bg-slate-50', 'dark:bg-slate-800/60', 'border-slate-200', 'dark:border-slate-700');
            }

            const btn = document.getElementById('btnCustomizeDueDate');
            if (btn) {
                btn.className = 'btn btn-ghost btn-xs text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/40 rounded-lg inline-flex items-center gap-1 px-2 py-0.5 border border-sky-200 dark:border-sky-800/60 shadow-xs transition';
                document.getElementById('customizeBtnText').innerText = isArabic ? 'تخصيص التاريخ' : 'Customize Date';
                document.getElementById('customizeIcon').className = 'fa-solid fa-pen-to-square text-[10px]';
            }

            const icon = document.getElementById('dueDateIcon');
            if (icon) {
                icon.className = 'absolute inset-y-0 left-0 rtl:right-0 rtl:left-auto flex items-center pl-3 rtl:pr-3 pointer-events-none text-sky-500 text-xs';
                icon.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i>';
            }

            updateDueDateByDays(days, label);
        }

        function toggleCustomizeDueDate() {
            isCustomDueDate = !isCustomDueDate;
            const dueDateInput = document.getElementById('due_date');
            const btn = document.getElementById('btnCustomizeDueDate');
            const badge = document.getElementById('dueDateLogicBadge');
            const icon = document.getElementById('dueDateIcon');

            if (isCustomDueDate) {
                // Unlock input
                dueDateInput.readOnly = false;
                dueDateInput.classList.remove('bg-slate-50', 'dark:bg-slate-800/60', 'border-slate-200', 'dark:border-slate-700');
                dueDateInput.classList.add('bg-white', 'dark:bg-slate-900', 'border-amber-400', 'dark:border-amber-500', 'ring-2', 'ring-amber-100', 'dark:ring-amber-950/40');
                dueDateInput.focus();
                if (typeof dueDateInput.showPicker === 'function') {
                    try { dueDateInput.showPicker(); } catch (_) {}
                }

                btn.className = 'btn btn-xs text-[11px] font-bold text-amber-700 dark:text-amber-300 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/60 rounded-lg inline-flex items-center gap-1 px-2 py-0.5 border border-amber-300 dark:border-amber-700 shadow-xs transition';
                document.getElementById('customizeBtnText').innerText = isArabic ? 'إعادة للقفل التلقائي' : 'Lock to Logic';
                document.getElementById('customizeIcon').className = 'fa-solid fa-rotate-left text-[10px]';

                if (icon) {
                    icon.className = 'absolute inset-y-0 left-0 rtl:right-0 rtl:left-auto flex items-center pl-3 rtl:pr-3 pointer-events-none text-amber-500 text-xs';
                    icon.innerHTML = '<i class="fa-solid fa-pen"></i>';
                }

                if (badge) {
                    badge.innerText = isArabic ? 'تاريخ مخصص يدوي' : 'Custom Override';
                    badge.className = 'font-mono text-[10px] text-amber-600 dark:text-amber-400 font-bold truncate max-w-[120px]';
                }

                // Clear pill highlights in custom mode
                document.querySelectorAll('#dueTermsPills .term-pill').forEach(pill => {
                    pill.classList.remove('active-pill', 'bg-sky-100', 'dark:bg-sky-950/60', 'text-sky-700', 'dark:text-sky-300', 'border', 'border-sky-300', 'dark:border-sky-800', 'font-bold');
                    pill.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'font-semibold');
                });
            } else {
                // Re-lock to logic
                setDueDatePreset(currentLogicDays);
            }
        }

        function onManualDueDateChange() {
            if (!isCustomDueDate) return;
            const badge = document.getElementById('dueDateLogicBadge');
            if (badge) {
                badge.innerText = isArabic ? 'تاريخ مخصص يدوي' : 'Custom Override';
                badge.className = 'font-mono text-[10px] text-amber-600 dark:text-amber-400 font-bold truncate max-w-[120px]';
            }
        }

        function onSupplierChange(selectElem) {
            const selectedOpt = selectElem.options[selectElem.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) return;

            const termsStr = selectedOpt.getAttribute('data-terms');
            if (!isCustomDueDate) {
                const days = parsePaymentTermsDays(termsStr);
                const label = termsStr ? `${termsStr} (${days}d)` : `Net ${days}`;
                updateDueDateByDays(days, label);
            }
        }
    </script>
</x-layouts.admin>
