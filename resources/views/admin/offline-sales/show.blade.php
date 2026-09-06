<x-layouts.admin 
    :pageTitle="'POS Sale #' . $sale['sale_number']" 
    pageSubtitle="Physical store counter receipt and register tender summary."
    :breadcrumbs="['Offline Sales' => route('admin.offline-sales.index'), $sale['sale_number'] => route('admin.offline-sales.show', $sale['id'])]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary" onclick="window.print()">
<<<<<<< HEAD
            🖨️ Print Receipt
=======
            <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'طباعة الإيصال' : 'Print Receipt' }}
>>>>>>> origin/main
        </button>
    </x-slot>

    <div class="card" style="padding: 2.5rem; max-width: 600px; margin: 0 auto; font-family: monospace;">
        <div style="text-align: center; border-bottom: 2px dashed var(--color-border); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0 0 0.25rem 0;">BLUE ZONE BOUTIQUE</h2>
            <div class="text-xs text-muted">Flagship Wellness Center • POS Register #01</div>
            <div class="text-xs text-muted">{{ $sale['date'] }} {{ $sale['time'] }}</div>
            <div class="text-xs font-bold" style="margin-top: 0.5rem;">SALE #{{ $sale['sale_number'] }} • Cashier: {{ $sale['cashier'] }}</div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
            @foreach($sale['items'] as $it)
                <div style="display: flex; justify-content: space-between; font-size: 0.9375rem;">
                    <div>
                        <div class="font-bold">{{ $it['product_name_en'] }}</div>
                        <div class="text-xs text-muted">{{ $it['quantity'] }} × ${{ number_format($it['unit_price'], 2) }}</div>
                    </div>
                    <div class="font-bold">${{ number_format($it['total'], 2) }}</div>
                </div>
            @endforeach
        </div>

        <div style="border-top: 2px dashed var(--color-border); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9375rem;">
            <div class="summary-row">
                <span>SUBTOTAL:</span>
                <span>${{ number_format($sale['subtotal'], 2) }}</span>
            </div>
            <div class="summary-row">
                <span>VAT (15%):</span>
                <span>${{ number_format($sale['tax'], 2) }}</span>
            </div>
            <div class="summary-row total" style="font-size: 1.25rem;">
                <span>TOTAL:</span>
                <span>${{ number_format($sale['total'], 2) }}</span>
            </div>
            <div class="summary-row text-xs text-muted" style="margin-top: 0.5rem;">
                <span>TENDER TYPE:</span>
                <span>{{ $sale['payment_method'] }}</span>
            </div>
        </div>
    </div>
</x-layouts.admin>
