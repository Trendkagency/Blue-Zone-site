<<<<<<< HEAD
<x-layouts.admin 
    :pageTitle="__('admin.inventory.transfer_title')" 
    pageSubtitle="Execute auditable stock relocations between fulfillment hubs, flagship boutiques, and quarantine warehouses."
    :breadcrumbs="['Inventory' => route('admin.inventory.index'), 'Transfers' => route('admin.inventory.transfers')]"
>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
        <!-- Transfer Form Card -->
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Initiate Transfer Request
            </h3>

            <form action="#" method="GET">
                <x-forms.select 
                    name="product_id" 
                    label="Target Formulation" 
                    :options="[
                        '1' => 'BLUE MIND (BZ-MND-001) — 60 Veg Capsules',
                        '2' => 'BLUE CELL (BZ-CEL-002) — 60 Veg Capsules',
                        '3' => 'BLUE DEFENSE (BZ-DEF-003) — 60 Veg Capsules',
                        '4' => 'BLUE METABOLIC (BZ-MET-004) — 60 Capsules',
                        '5' => 'BLUE SLEEP (BZ-SLP-005) — 60 Capsules',
                        '6' => 'BLUE VITALITY (BZ-VIT-006) — 60 Capsules',
                    ]" 
                    required 
                />

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <x-forms.select 
                        name="from_location" 
                        :label="__('admin.inventory.from_location')" 
                        :options="[
                            'central_wh' => 'Central Quarantine Warehouse (Available: 500 units)',
                            'online' => 'Online Fulfillment Hub (Available: 124 units)',
                            'offline' => 'Flagship Boutique POS (Available: 38 units)',
                        ]" 
                        required 
                    />

                    <x-forms.select 
                        name="to_location" 
                        :label="__('admin.inventory.to_location')" 
                        :options="[
                            'offline' => 'Flagship Boutique POS (Current: 38 units)',
                            'online' => 'Online Fulfillment Hub (Current: 124 units)',
                            'central_wh' => 'Central Quarantine Warehouse',
                        ]" 
                        required 
                    />
                </div>

                <x-forms.input 
                    name="quantity" 
                    type="number" 
                    :label="__('admin.inventory.transfer_qty')" 
                    value="25" 
                    min="1" 
                    required 
                />

                <x-forms.textarea 
                    name="reason" 
                    :label="__('admin.inventory.transfer_reason')" 
                    rows="2" 
                    placeholder="e.g. Replenishing weekend walk-in stock buffer for VIP launch event." 
                    required 
                />

                <button type="button" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;" onclick="alert('Stock Transfer verified & executed! Stock movement log #MOV-2026-0892 generated.')">
                    ⚡ {{ __('admin.inventory.confirm_transfer') }}
=======
@php
    $prodOptions = [];
    foreach($products as $p) {
        $pId = is_array($p) ? $p['id'] : $p->id;
        $pName = is_array($p) ? ($p['name_en'] ?? $p['sku']) : ($p->name_en ?? $p->sku);
        $pSku = is_array($p) ? ($p['sku'] ?? '') : ($p->sku ?? '');
        $prodOptions[$pId] = "{$pName} ({$pSku})";
    }

    $selectedProductId = request('product_id', $products[0]->id ?? 1);
    $selectedFromLoc = request('from_location', 'online');
    $selectedToLoc = $selectedFromLoc === 'online' ? 'offline' : 'online';
@endphp

<x-layouts.admin 
    :pageTitle="__('admin.inventory.transfer_title')" 
    :pageSubtitle="app()->getLocale() == 'ar' ? 'تنفيذ تحويلات المخزون الموثقة بين مستودع الأونلاين ومعرض البوتيك والمستودع المركزي.' : 'Execute auditable stock relocations between fulfillment hubs, flagship boutiques, and quarantine warehouses.'"
    :breadcrumbs="[__('admin.menu.inventory') => route('admin.inventory.index'), __('admin.inventory.transfer_title') => route('admin.inventory.transfers')]"
>
    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-circle-check mr-1.5 ml-1.5 text-success"></i> {{ session('status') }}
        </div>
    @endif

    @if($errors->has('transfer_error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-triangle-exclamation mr-1.5 ml-1.5 text-danger"></i> {{ $errors->first('transfer_error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 2rem; margin-bottom: 3rem;">
        <!-- Transfer Form Card -->
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                <i class="fa-solid fa-arrow-right-arrow-left text-primary mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() == 'ar' ? 'طلب تحويل مخزون بين المواقع' : 'Initiate Stock Transfer Request' }}
            </h3>

            <form action="{{ route('admin.inventory.transfers.store') }}" method="POST" id="transferForm">
                @csrf
                
                <div style="margin-bottom: 1.25rem;">
                    <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() == 'ar' ? 'التركيبة المستهدفة' : 'Target Formulation' }}
                    </label>
                    <select name="product_id" id="transferProductId" class="form-select text-sm" style="width: 100%;" onchange="updateProjection()" required>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ $selectedProductId == $prod->id ? 'selected' : '' }}>
                                {{ app()->getLocale() == 'ar' ? ($prod->name_ar ?? $prod->name_en) : $prod->name_en }} ({{ $prod->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                            {{ __('admin.inventory.from_location') }}
                        </label>
                        <select name="from_location" id="fromLocation" class="form-select text-sm" style="width: 100%;" onchange="updateProjection()" required>
                            @foreach($locations as $loc)
                                @php
                                    $locId = is_array($loc) ? $loc['id'] : $loc->id;
                                    $locDisplay = app()->getLocale() === 'ar' ? (is_array($loc) ? ($loc['name_ar'] ?? $loc['name_en']) : ($loc->name_ar ?? $loc->name_en)) : (is_array($loc) ? $loc['name_en'] : $loc->name_en);
                                @endphp
                                <option value="{{ $locId }}" {{ $selectedFromLoc === $locId ? 'selected' : '' }}>
                                    {{ $locDisplay }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                            {{ __('admin.inventory.to_location') }}
                        </label>
                        <select name="to_location" id="toLocation" class="form-select text-sm" style="width: 100%;" onchange="updateProjection()" required>
                            @foreach($locations as $loc)
                                @php
                                    $locId = is_array($loc) ? $loc['id'] : $loc->id;
                                    $locDisplay = app()->getLocale() === 'ar' ? (is_array($loc) ? ($loc['name_ar'] ?? $loc['name_en']) : ($loc->name_ar ?? $loc->name_en)) : (is_array($loc) ? $loc['name_en'] : $loc->name_en);
                                @endphp
                                <option value="{{ $locId }}" {{ $selectedToLoc === $locId ? 'selected' : '' }}>
                                    {{ $locDisplay }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                        {{ __('admin.inventory.transfer_qty') }}
                    </label>
                    <input type="number" name="quantity" id="transferQty" class="form-control text-sm" value="20" min="1" oninput="updateProjection()" required>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                        {{ __('admin.inventory.transfer_reason') }}
                    </label>
                    <textarea name="reason" class="form-control text-sm" rows="2" placeholder="{{ app()->getLocale() == 'ar' ? 'مثال: تعزيز رصيد المعرض لعطلة نهاية الأسبوع، أو تزويد مستودع الأونلاين للطلبات المتزايدة...' : 'e.g. Replenishing weekend walk-in stock buffer for VIP launch event.' }}" required></textarea>
                </div>

                <button type="submit" id="submitTransferBtn" class="btn btn-primary btn-lg" style="width: 100%;">
                    <i class="fa-solid fa-bolt mr-1.5 ml-1.5"></i> {{ __('admin.inventory.confirm_transfer') }}
>>>>>>> origin/main
                </button>
            </form>
        </div>

        <!-- Live Projection Preview Card -->
        <div class="card" style="padding: 2rem; background: var(--color-bg-subtle);">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
<<<<<<< HEAD
                Real-Time Stock Projection
=======
                <i class="fa-solid fa-chart-line text-primary mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() == 'ar' ? 'المعاينة الحية لتأثير النقل' : 'Real-Time Stock Projection' }}
>>>>>>> origin/main
            </h3>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Source Projection -->
                <div style="background: var(--color-bg-surface); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
<<<<<<< HEAD
                    <span class="badge badge-warning" style="margin-bottom: 0.5rem;">Source Location (Central Warehouse)</span>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span class="text-sm">Current On-Hand:</span>
                        <span class="font-bold">500 units</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--color-danger);">
                        <span class="text-sm">Deduction Quantity:</span>
                        <span class="font-bold">-25 units</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--color-border); padding-top: 0.5rem;" class="font-bold">
                        <span>{{ __('admin.inventory.preview_source_rem') }}:</span>
                        <span class="text-primary font-black">475 units</span>
=======
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span class="badge badge-warning">
                            {{ app()->getLocale() == 'ar' ? 'موقع المصدر (خصم)' : 'Source Location (Outbound)' }}
                        </span>
                        <span id="sourceLocName" class="text-xs font-bold text-muted"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span class="text-sm text-muted">{{ app()->getLocale() == 'ar' ? 'الرصيد المتوفر الحالي:' : 'Current Available Stock:' }}</span>
                        <span class="font-bold" id="sourceCurrentStock">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--color-danger);">
                        <span class="text-sm">{{ app()->getLocale() == 'ar' ? 'كمية الخصم:' : 'Deduction Quantity:' }}</span>
                        <span class="font-bold" id="sourceDeduction">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--color-border); padding-top: 0.5rem;" class="font-bold">
                        <span>{{ __('admin.inventory.preview_source_rem') }}:</span>
                        <span class="text-primary font-black" id="sourceRemaining">0</span>
                    </div>
                    <div id="insufficientAlert" style="display: none; margin-top: 0.75rem;" class="text-xs text-danger font-bold">
                        <i class="fa-solid fa-triangle-exclamation mr-1 ml-1"></i>
                        {{ app()->getLocale() == 'ar' ? 'تنبيه: الكمية المطلوبة تتجاوز الرصيد المتوفر في هذا الموقع!' : 'Alert: Transfer quantity exceeds available stock!' }}
>>>>>>> origin/main
                    </div>
                </div>

                <!-- Destination Projection -->
                <div style="background: var(--color-bg-surface); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
<<<<<<< HEAD
                    <span class="badge badge-success" style="margin-bottom: 0.5rem;">Destination Location (Flagship POS)</span>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span class="text-sm">Current On-Hand:</span>
                        <span class="font-bold">38 units</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--color-success);">
                        <span class="text-sm">Inbound Addition:</span>
                        <span class="font-bold">+25 units</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--color-border); padding-top: 0.5rem;" class="font-bold">
                        <span>{{ __('admin.inventory.preview_dest_new') }}:</span>
                        <span class="text-success font-black">63 units</span>
=======
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span class="badge badge-success">
                            {{ app()->getLocale() == 'ar' ? 'موقع الوجهة (إضافة)' : 'Destination Location (Inbound)' }}
                        </span>
                        <span id="destLocName" class="text-xs font-bold text-muted"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span class="text-sm text-muted">{{ app()->getLocale() == 'ar' ? 'الرصيد المتوفر الحالي:' : 'Current Stock:' }}</span>
                        <span class="font-bold" id="destCurrentStock">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--color-success);">
                        <span class="text-sm">{{ app()->getLocale() == 'ar' ? 'الكمية المضافة:' : 'Inbound Addition:' }}</span>
                        <span class="font-bold" id="destAddition">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--color-border); padding-top: 0.5rem;" class="font-bold">
                        <span>{{ __('admin.inventory.preview_dest_new') }}:</span>
                        <span class="text-success font-black" id="destNewStock">0</span>
>>>>>>> origin/main
                    </div>
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
=======

    <!-- Recent Transfer Audit Log Table -->
    <div class="card">
        <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border);">
            <h3 class="card-title" style="font-size: 1.1rem; font-weight: 800; margin: 0;">
                <i class="fa-solid fa-clock-rotate-left mr-1.5 ml-1.5 text-primary"></i>
                {{ app()->getLocale() == 'ar' ? 'أحدث عمليات النقل المسجلة في النظام' : 'Recent Stock Transfers Audit' }}
            </h3>
        </div>
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.inventory.movement_id') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التركيبة' : 'Product' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'مسار التحويل (من ← إلى)' : 'Routing (From → To)' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الكمية المحولة' : 'Transferred Qty' }}</th>
                        <th>{{ __('admin.inventory.logged_user') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التاريخ والسبب' : 'Date & Note' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $t)
                        @php
                            $tId = $t->movement_number ?? ('MOV-' . $t->id);
                            $tName = app()->getLocale() == 'ar' ? ($t->product_name_ar ?? $t->product_name_en) : $t->product_name_en;
                        @endphp
                        <tr>
                            <td class="font-mono text-xs font-bold text-primary">{{ $tId }}</td>
                            <td>
                                <div class="font-bold text-sm">{{ $tName }}</div>
                                <div class="text-xs text-muted font-mono">{{ $t->sku }}</div>
                            </td>
                            <td class="text-xs">
                                <div><strong>{{ app()->getLocale() == 'ar' ? 'من:' : 'From:' }}</strong> {{ $t->from_location }}</div>
                                <div><strong>{{ app()->getLocale() == 'ar' ? 'إلى:' : 'To:' }}</strong> {{ $t->to_location }}</div>
                            </td>
                            <td class="font-bold text-primary">
                                {{ $t->quantity }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}
                            </td>
                            <td class="text-xs">
                                <i class="fa-solid fa-user-shield mr-1 ml-1 text-muted"></i> {{ $t->user }}
                            </td>
                            <td class="text-xs text-muted">
                                <div>{{ $t->date?->format('Y-m-d') }} {{ $t->time }}</div>
                                @if($t->note)
                                    <div class="text-secondary" style="font-style: italic;">"{{ $t->note }}"</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-muted">
                                {{ app()->getLocale() == 'ar' ? 'لا توجد حركات نقل مسجلة حتى الآن.' : 'No transfer movements recorded yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const inventoryData = @json($inventoryMap);

        function updateProjection() {
            const pId = document.getElementById('transferProductId').value;
            const fromLoc = document.getElementById('fromLocation').value;
            const toLoc = document.getElementById('toLocation').value;
            const qty = parseInt(document.getElementById('transferQty').value) || 0;

            const fromSelect = document.getElementById('fromLocation');
            const toSelect = document.getElementById('toLocation');
            document.getElementById('sourceLocName').innerText = fromSelect.options[fromSelect.selectedIndex].text;
            document.getElementById('destLocName').innerText = toSelect.options[toSelect.selectedIndex].text;

            const sourceStock = (inventoryData[pId] && inventoryData[pId][fromLoc]) ? inventoryData[pId][fromLoc].available : 0;
            const destStock = (inventoryData[pId] && inventoryData[pId][toLoc]) ? inventoryData[pId][toLoc].current : 0;

            document.getElementById('sourceCurrentStock').innerText = sourceStock + ' units';
            document.getElementById('sourceDeduction').innerText = '-' + qty + ' units';
            
            const remaining = sourceStock - qty;
            document.getElementById('sourceRemaining').innerText = remaining + ' units';

            document.getElementById('destCurrentStock').innerText = destStock + ' units';
            document.getElementById('destAddition').innerText = '+' + qty + ' units';
            document.getElementById('destNewStock').innerText = (destStock + qty) + ' units';

            const alertBox = document.getElementById('insufficientAlert');
            const submitBtn = document.getElementById('submitTransferBtn');

            if (fromLoc === toLoc) {
                alertBox.style.display = 'block';
                alertBox.innerText = "{{ app()->getLocale() == 'ar' ? 'تنبيه: لا يمكن اختيار نفس الموقع كمصدر ووجهة!' : 'Alert: Source and destination cannot be identical!' }}";
                submitBtn.disabled = true;
            } else if (qty > sourceStock) {
                alertBox.style.display = 'block';
                alertBox.innerText = "{{ app()->getLocale() == 'ar' ? 'تنبيه: الكمية المطلوبة تتجاوز الرصيد المتوفر في موقع المصدر!' : 'Alert: Transfer quantity exceeds available stock!' }}";
                submitBtn.disabled = true;
            } else {
                alertBox.style.display = 'none';
                submitBtn.disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', updateProjection);
    </script>
>>>>>>> origin/main
</x-layouts.admin>
