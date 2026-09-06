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
                </button>
            </form>
        </div>

        <!-- Live Projection Preview Card -->
        <div class="card" style="padding: 2rem; background: var(--color-bg-subtle);">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Real-Time Stock Projection
            </h3>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Source Projection -->
                <div style="background: var(--color-bg-surface); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
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
                    </div>
                </div>

                <!-- Destination Projection -->
                <div style="background: var(--color-bg-surface); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
