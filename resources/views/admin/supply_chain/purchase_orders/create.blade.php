<x-layouts.admin 
    :pageTitle="__('purchase_orders.new_po')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إنشاء مسودة أمر شراء جديد وإضافة بنود المواد الأولية وتكلفة الشحن' : 'Draft a new cGMP purchase order with raw material line items and landed cost estimates'"
    :breadcrumbs="[__('purchase_orders.title') => route('admin.purchase-orders.index'), __('purchase_orders.new_po') => '#']"
>
    <div class="max-w-5xl mx-auto" x-data="purchaseOrderForm()">
        <form method="POST" action="{{ route('admin.purchase-orders.store') }}" class="space-y-6">
            @csrf

            <!-- Header Information -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-file-invoice text-sky-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'بيانات أمر الشراء والمورد' : 'PO Header & Supplier Details' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('purchase_orders.supplier') }} <span class="text-rose-500">*</span></label>
                        <select name="supplier_id" required class="form-select text-sm" x-model="supplierId" @change="onSupplierChange()">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المورد المعتمد --' : '-- Select Qualified Supplier --' }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} ({{ $supplier->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('purchase_orders.order_date') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('purchase_orders.expected_delivery_date') }}</label>
                        <input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('purchase_orders.currency') }} <span class="text-rose-500">*</span></label>
                        <select name="currency" required class="form-select text-sm" x-model="currency">
                            <option value="AED">AED (UAE Dirham)</option>
                            <option value="USD">USD (US Dollar)</option>
                            <option value="EUR">EUR (Euro)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('purchase_orders.exchange_rate') }} <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.0001" name="exchange_rate" value="{{ old('exchange_rate', 1.0000) }}" required class="form-control text-sm font-mono">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('purchase_orders.payment_terms') }}</label>
                        <input type="text" name="payment_terms" value="{{ old('payment_terms', 'Net 30') }}" class="form-control text-sm">
                    </div>
                </div>

                <div>
                    <label class="form-label font-bold text-xs">{{ __('purchase_orders.shipping_address') }}</label>
                    <textarea name="shipping_address" rows="2" class="form-control text-sm" placeholder="Central Warehouse Receiving Dock...">{{ old('shipping_address', 'Blue Zone Pharma Central Hub, Dubai Science Park, UAE') }}</textarea>
                </div>
            </div>

            <!-- Line Items Repeater -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-emerald-600"></i>
                        {{ app()->getLocale() === 'ar' ? 'بنود المواد الأولية المطلوبة' : 'Raw Material Line Items' }}
                    </h3>
                    <button type="button" class="btn btn-secondary btn-sm font-bold" @click="addItem()">
                        <i class="fa-solid fa-plus mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إضافة بند' : 'Add Item' }}
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table text-xs">
                        <thead>
                            <tr>
                                <th style="width: 40%;">{{ __('purchase_orders.raw_material') }}</th>
                                <th style="width: 20%;">{{ __('purchase_orders.quantity') }}</th>
                                <th style="width: 20%;">{{ __('purchase_orders.unit_price') }}</th>
                                <th style="width: 15%;">{{ __('purchase_orders.line_total') }}</th>
                                <th style="width: 5%; text-align: center;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td>
                                        <select :name="`items[${index}][raw_material_id]`" required class="form-select text-xs" x-model="item.raw_material_id">
                                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المادة --' : '-- Select Material --' }}</option>
                                            @foreach($materials as $material)
                                                <option value="{{ $material->id }}">
                                                    {{ $material->name }} ({{ $material->sku }}) - {{ $material->uom?->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" :name="`items[${index}][quantity_ordered]`" required class="form-control text-xs font-mono" x-model.number="item.quantity_ordered" @input="calculateTotal()">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" :name="`items[${index}][unit_price]`" required class="form-control text-xs font-mono" x-model.number="item.unit_price" @input="calculateTotal()">
                                    </td>
                                    <td class="font-mono font-bold text-slate-800 dark:text-white">
                                        <span x-text="(item.quantity_ordered * item.unit_price).toFixed(2)"></span>
                                        <span x-text="currency"></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" class="btn btn-ghost btn-xs text-rose-500" @click="removeItem(index)" x-show="items.length > 1">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Landed Cost & Totals Ribbon -->
                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="form-label font-bold text-[11px]">{{ __('purchase_orders.shipping_cost') }}</label>
                            <input type="number" step="0.01" name="shipping_cost" value="0.00" class="form-control text-xs font-mono" x-model.number="shippingCost" @input="calculateTotal()">
                        </div>
                        <div>
                            <label class="form-label font-bold text-[11px]">{{ __('purchase_orders.customs_duty') }}</label>
                            <input type="number" step="0.01" name="customs_duty" value="0.00" class="form-control text-xs font-mono" x-model.number="customsDuty" @input="calculateTotal()">
                        </div>
                        <div>
                            <label class="form-label font-bold text-[11px]">{{ __('purchase_orders.tax_amount') }}</label>
                            <input type="number" step="0.01" name="tax_amount" value="0.00" class="form-control text-xs font-mono" x-model.number="taxAmount" @input="calculateTotal()">
                        </div>
                    </div>

                    <div class="text-end space-y-1">
                        <div class="text-xs text-gray-500">
                            {{ __('purchase_orders.subtotal') }}: <span class="font-mono font-bold text-slate-800 dark:text-white" x-text="subtotal.toFixed(2)"></span> <span x-text="currency"></span>
                        </div>
                        <div class="text-base font-black text-slate-800 dark:text-white">
                            {{ __('purchase_orders.total_amount') }}: <span class="font-mono text-sky-600" x-text="total.toFixed(2)"></span> <span x-text="currency"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm">
                    <i class="fa-solid fa-save mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ كمسودة أمر شراء' : 'Save as Draft PO' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        function purchaseOrderForm() {
            return {
                supplierId: '{{ old("supplier_id") }}',
                currency: 'AED',
                shippingCost: 0,
                customsDuty: 0,
                taxAmount: 0,
                subtotal: 0,
                total: 0,
                items: [
                    { raw_material_id: '', quantity_ordered: 100, unit_price: 10 }
                ],
                addItem() {
                    this.items.push({ raw_material_id: '', quantity_ordered: 1, unit_price: 0 });
                    this.calculateTotal();
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotal();
                    }
                },
                calculateTotal() {
                    let sub = 0;
                    this.items.forEach(i => {
                        sub += (Number(i.quantity_ordered) || 0) * (Number(i.unit_price) || 0);
                    });
                    this.subtotal = sub;
                    this.total = sub + (Number(this.shippingCost) || 0) + (Number(this.customsDuty) || 0) + (Number(this.taxAmount) || 0);
                },
                onSupplierChange() {
                    // Reactive hook
                },
                init() {
                    this.calculateTotal();
                }
            }
        }
    </script>
</x-layouts.admin>
