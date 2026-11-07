<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $supplierQuotation->quote_number }} - {{ __('supplier_quotations.title') }}</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 24px; }
        .header { display: flex; justify-content: space-between; align-items: start; border-bottom: 2px solid #0284c7; padding-bottom: 12px; margin-bottom: 20px; }
        .logo-title { font-size: 20px; font-weight: 900; color: #0284c7; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 9999px; font-weight: bold; font-size: 10px; background: #f1f5f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; margin-bottom: 16px; }
        th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px; font-weight: bold; text-align: start; }
        td { border: 1px solid #e2e8f0; padding: 8px; }
        .text-end { text-align: end; }
        .totals-box { width: 320px; margin-inline-start: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; background: #f8fafc; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 6px; }
        .grand-total { font-size: 14px; font-weight: 900; color: #0284c7; border-top: 1px solid #cbd5e1; padding-top: 6px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; display: flex; justify-content: space-between;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0284c7; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            {{ app()->getLocale() === 'ar' ? 'طباعة المستند' : 'Print Document' }}
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer;">
            {{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}
        </button>
    </div>

    <div class="header">
        <div>
            <div class="logo-title">BLUE ZONE BIOCEUTICALS</div>
            <div style="font-size: 11px; color: #64748b;">cGMP Compliant Sourcing &amp; Procurement System</div>
            <div style="font-size: 13px; font-weight: bold; margin-top: 8px;">
                {{ __('supplier_quotations.title') }}
            </div>
        </div>
        <div style="text-align: end;">
            <div style="font-size: 16px; font-weight: 900; font-family: monospace;">{{ $supplierQuotation->quote_number }}</div>
            <div style="font-size: 11px; color: #64748b;">{{ __('supplier_quotations.quote_date') }}: {{ $supplierQuotation->quote_date?->format('Y-m-d') }}</div>
            @if($supplierQuotation->valid_until)
                <div style="font-size: 11px; color: #64748b;">{{ __('supplier_quotations.valid_until') }}: {{ $supplierQuotation->valid_until->format('Y-m-d') }}</div>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px;">
        <div style="border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px;">
            <div style="font-weight: bold; color: #64748b; margin-bottom: 4px;">{{ __('supplier_quotations.supplier') }}:</div>
            <div style="font-size: 14px; font-weight: bold;">{{ $supplierQuotation->supplier?->name_en }}</div>
            <div>{{ $supplierQuotation->supplier?->name_ar }}</div>
            <div style="font-family: monospace; font-size: 11px; color: #64748b;">Code: {{ $supplierQuotation->supplier?->code }}</div>
            <div>{{ $supplierQuotation->supplier?->email }} | {{ $supplierQuotation->supplier?->phone }}</div>
            <div>{{ $supplierQuotation->supplier?->country?->name_en }}</div>
        </div>
        <div style="border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px;">
            <div style="font-weight: bold; color: #64748b; margin-bottom: 4px;">{{ app()->getLocale() === 'ar' ? 'الشروط والمواصفات' : 'Commercial Parameters' }}:</div>
            <div><strong>{{ __('supplier_quotations.currency') }}:</strong> {{ $supplierQuotation->currency }}</div>
            <div><strong>{{ __('supplier_quotations.payment_terms') }}:</strong> {{ $supplierQuotation->payment_terms ?? 'Net 30' }}</div>
            <div><strong>{{ __('supplier_quotations.lead_time_days') }}:</strong> {{ $supplierQuotation->lead_time_days }} {{ app()->getLocale() === 'ar' ? 'أيام' : 'Days' }}</div>
            @if($supplierQuotation->rfq_reference)
                <div><strong>Ref:</strong> {{ $supplierQuotation->rfq_reference }}</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">{{ app()->getLocale() === 'ar' ? 'المادة / البند' : 'Material / Item Description' }}</th>
                <th style="width: 15%; text-align: end;">{{ __('supplier_quotations.quantity') }}</th>
                <th style="width: 15%; text-align: end;">{{ __('supplier_quotations.unit_price') }}</th>
                <th style="width: 20%; text-align: end;">{{ __('supplier_quotations.total_price') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supplierQuotation->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $item->item_name }}</div>
                        @if($item->rawMaterial)
                            <div style="font-size: 10px; color: #64748b; font-family: monospace;">{{ $item->rawMaterial->material_code }}</div>
                        @endif
                    </td>
                    <td class="text-end" style="font-family: monospace;">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-end" style="font-family: monospace;">{{ number_format($item->unit_price, 4) }} {{ $supplierQuotation->currency }}</td>
                    <td class="text-end" style="font-family: monospace; font-weight: bold;">{{ number_format($item->total_price, 2) }} {{ $supplierQuotation->currency }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-box">
        <div class="totals-row">
            <span>{{ __('supplier_quotations.subtotal') }}:</span>
            <span style="font-family: monospace; font-weight: bold;">{{ number_format($supplierQuotation->subtotal, 2) }} {{ $supplierQuotation->currency }}</span>
        </div>
        <div class="totals-row">
            <span>{{ __('supplier_quotations.tax_amount') }} (VAT):</span>
            <span style="font-family: monospace;">{{ number_format($supplierQuotation->tax_amount, 2) }} {{ $supplierQuotation->currency }}</span>
        </div>
        @if($supplierQuotation->estimated_freight > 0)
            <div class="totals-row">
                <span>{{ __('supplier_quotations.freight_estimate') }}:</span>
                <span style="font-family: monospace;">{{ number_format($supplierQuotation->estimated_freight, 2) }} {{ $supplierQuotation->currency }}</span>
            </div>
        @endif
        @if($supplierQuotation->estimated_customs > 0)
            <div class="totals-row">
                <span>{{ __('supplier_quotations.customs_estimate') }}:</span>
                <span style="font-family: monospace;">{{ number_format($supplierQuotation->estimated_customs, 2) }} {{ $supplierQuotation->currency }}</span>
            </div>
        @endif
        <div class="totals-row grand-total">
            <span>{{ __('supplier_quotations.grand_total') }}:</span>
            <span style="font-family: monospace;">{{ number_format($supplierQuotation->grand_total, 2) }} {{ $supplierQuotation->currency }}</span>
        </div>
    </div>

    @if($supplierQuotation->notes)
        <div style="margin-top: 24px; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px;">
            <div style="font-weight: bold; color: #64748b; margin-bottom: 4px;">{{ __('supplier_quotations.notes') }}:</div>
            <div>{{ $supplierQuotation->notes }}</div>
        </div>
    @endif
</body>
</html>
