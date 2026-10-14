<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>PO_{{ $purchaseOrder->po_number }} - Blue Zone Bioceuticals</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 900;
            color: #0369a1;
            letter-spacing: 0.5px;
        }
        .po-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            text-align: right;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            background: #f8fafc;
        }
        .box-title {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right { text-align: right; }
        .totals-table {
            width: 300px;
            margin-left: auto;
            margin-bottom: 20px;
        }
        .totals-table td { padding: 4px 8px; }
        .grand-total {
            font-size: 14px;
            font-weight: 800;
            color: #0369a1;
            border-top: 2px solid #0284c7;
        }
        .cgmp-notice {
            border: 1px solid #fed7aa;
            background: #fffbeb;
            padding: 10px;
            border-radius: 6px;
            font-size: 11px;
            color: #9a3412;
            margin-bottom: 20px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }
        .sign-box {
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0284c7; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Print / Save as PDF
        </button>
    </div>

    <div class="header">
        <div>
            <div class="brand-title">BLUE ZONE BIOCEUTICALS</div>
            <div style="color: #64748b; font-size: 11px; margin-top: 4px;">
                Dubai Science Park, Clinical Manufacturing Campus<br>
                Dubai, United Arab Emirates | TRN: 100293847500003<br>
                Email: procurement@bluezone.ae | Phone: +971 4 800 2583
            </div>
        </div>
        <div>
            <div class="po-title">PURCHASE ORDER</div>
            <div style="font-family: monospace; font-size: 14px; font-weight: bold; color: #0284c7;">
                {{ $purchaseOrder->po_number }}
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                Status: {{ $purchaseOrder->status->label() }}<br>
                Date: {{ $purchaseOrder->order_date?->format('d M Y') }}
            </div>
        </div>
    </div>

    <div class="meta-grid">
        <div class="box">
            <div class="box-title">QUALIFIED VENDOR (SUPPLIER)</div>
            <strong>{{ $purchaseOrder->supplier?->name }}</strong><br>
            Vendor Code: {{ $purchaseOrder->supplier?->code }}<br>
            {{ $purchaseOrder->supplier?->address }}<br>
            {{ $purchaseOrder->supplier?->country }}<br>
            Attn: {{ $purchaseOrder->supplier?->contact_person }} | {{ $purchaseOrder->supplier?->email }}
        </div>
        <div class="box">
            <div class="box-title">DELIVERY & SHIPPING DESTINATION</div>
            <strong>Blue Zone Bioceuticals Receiving Dock</strong><br>
            {{ $purchaseOrder->shipping_address ?? 'Central Warehouse Receiving Dock, Dubai Science Park, UAE' }}<br>
            Delivery Due: <strong>{{ $purchaseOrder->expected_delivery_date?->format('d M Y') ?? 'Standard Lead Time' }}</strong><br>
            Payment Terms: {{ $purchaseOrder->payment_terms ?? 'Net 30' }} | Currency: {{ $purchaseOrder->currency }}
        </div>
    </div>

    <div class="cgmp-notice">
        <strong>cGMP MANDATORY INBOUND SPECIFICATIONS:</strong> All materials must arrive with tamper-evident container seals intact, temperature data logger reading attached, and manufacturer's Certificate of Analysis (COA) corresponding to each lot. Quarantine inspection will take place immediately at dock receipt.
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Material Description / Specification</th>
                <th style="width: 15%;">SKU / CAS</th>
                <th style="width: 10%; text-align: right;">Qty</th>
                <th style="width: 12%; text-align: right;">Unit Price</th>
                <th style="width: 13%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->rawMaterial?->name_en }}</strong>
                        @if($item->rawMaterial?->storage_condition)
                            <br><span style="font-size: 10px; color: #0284c7;">Storage: {{ $item->rawMaterial->storage_condition->label() }}</span>
                        @endif
                    </td>
                    <td style="font-family: monospace;">
                        {{ $item->rawMaterial?->sku }}
                        @if($item->rawMaterial?->cas_number)
                            <br><span style="font-size: 10px; color: #64748b;">CAS: {{ $item->rawMaterial->cas_number }}</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-family: monospace;">
                        {{ number_format($item->quantity_ordered, 2) }} {{ $item->rawMaterial?->uom?->value }}
                    </td>
                    <td class="text-right" style="font-family: monospace;">
                        {{ number_format($item->unit_price, 2) }}
                    </td>
                    <td class="text-right" style="font-family: monospace; font-weight: bold;">
                        {{ number_format($item->total_price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right font-mono">{{ number_format($purchaseOrder->subtotal, 2) }} {{ $purchaseOrder->currency }}</td>
        </tr>
        @if($purchaseOrder->shipping_cost > 0)
        <tr>
            <td>Estimated Freight / Shipping:</td>
            <td class="text-right font-mono">{{ number_format($purchaseOrder->shipping_cost, 2) }} {{ $purchaseOrder->currency }}</td>
        </tr>
        @endif
        @if($purchaseOrder->customs_duty > 0)
        <tr>
            <td>Customs Duty & Clearance:</td>
            <td class="text-right font-mono">{{ number_format($purchaseOrder->customs_duty, 2) }} {{ $purchaseOrder->currency }}</td>
        </tr>
        @endif
        @if($purchaseOrder->tax_amount > 0)
        <tr>
            <td>VAT / Tax:</td>
            <td class="text-right font-mono">{{ number_format($purchaseOrder->tax_amount, 2) }} {{ $purchaseOrder->currency }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td>TOTAL AMOUNT:</td>
            <td class="text-right font-mono">{{ number_format($purchaseOrder->total_amount, 2) }} {{ $purchaseOrder->currency }}</td>
        </tr>
    </table>

    <div class="signatures">
        <div class="sign-box">
            <strong>{{ $purchaseOrder->creator?->name ?? 'Procurement Officer' }}</strong><br>
            Prepared By (Purchaser)<br>
            <span style="font-size: 10px; color: #64748b;">{{ $purchaseOrder->created_at?->format('d M Y') }}</span>
        </div>
        <div class="sign-box">
            <strong>{{ $purchaseOrder->approver?->name ?? 'Authorized Signatory' }}</strong><br>
            Procurement Head / Director<br>
            <span style="font-size: 10px; color: #64748b;">{{ $purchaseOrder->approved_at?->format('d M Y') ?? 'Pending' }}</span>
        </div>
        <div class="sign-box">
            <strong>Quality Assurance</strong><br>
            cGMP Receiving Clearance<br>
            <span style="font-size: 10px; color: #64748b;">Pre-Notification Logged</span>
        </div>
    </div>
</body>
</html>
