<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $tpl = \App\Services\InvoiceTemplateService::getPurchaseTemplate();
        
        $isModel = $invoice instanceof \Illuminate\Database\Eloquent\Model;
        $invNum = $isModel ? ($invoice->invoice_number ?? 'PINV-000') : ($invoice['invoice_number'] ?? 'PINV-000');
        $refNum = $isModel ? ($invoice->reference_number ?? '—') : ($invoice['reference_number'] ?? '—');
        
        $rawDate = $isModel ? $invoice->invoice_date : ($invoice['invoice_date'] ?? now());
        $invDate = $rawDate instanceof \Carbon\Carbon ? $rawDate->format('Y-m-d') : (is_string($rawDate) ? substr($rawDate, 0, 10) : date('Y-m-d'));

        $rawDueDate = $isModel ? ($invoice->due_date ?? null) : ($invoice['due_date'] ?? null);
        $dueDate = $rawDueDate instanceof \Carbon\Carbon 
            ? $rawDueDate->format('Y-m-d') 
            : (is_string($rawDueDate) && !empty($rawDueDate) ? substr($rawDueDate, 0, 10) : \Carbon\Carbon::parse($invDate)->addDays(30)->format('Y-m-d'));
        
        $supplierName = $isModel 
            ? ($invoice->supplier?->name ?? ($invoice->supplier?->name_ar ?? 'المورد المعتمد')) 
            : ($invoice['supplier_name'] ?? 'المورد المعتمد');
            
        $supplierCode = $isModel 
            ? ($invoice->supplier?->code ?? ('VND-' . $invoice->supplier_id)) 
            : ($invoice['supplier_code'] ?? 'VND-001');
            
        $supplierAddress = $isModel 
            ? ($invoice->supplier?->address ?? ($invoice->supplier?->country ?? 'المملكة العربية السعودية')) 
            : ($invoice['supplier_address'] ?? 'المملكة العربية السعودية');

        $warehouseName = $isModel 
            ? ($invoice->purchaseOrder?->destinationLocation?->name_ar ?? ($invoice->purchaseOrder?->destinationLocation?->name_en ?? 'المستودع الرئيسي - رصيف أ')) 
            : ($invoice['warehouse'] ?? 'المستودع الرئيسي - رصيف أ');

        $items = $isModel ? $invoice->items : ($invoice['items'] ?? []);
        
        // Financials
        $subtotal = (float) ($isModel ? ($invoice->subtotal ?? 0) : ($invoice['subtotal'] ?? 0));
        $tax = (float) ($isModel ? ($invoice->tax_amount ?? 0) : ($invoice['tax_amount'] ?? 0));
        $total = (float) ($isModel ? ($invoice->grand_total ?? 0) : ($invoice['grand_total'] ?? 0));
        $extraDiscPct = (float) ($isModel ? ($invoice->discount_percent ?? 0) : ($invoice['discount_percent'] ?? 0));
        $extraDiscVal = (float) ($isModel ? ($invoice->discount_amount ?? 0) : ($invoice['discount_amount'] ?? 0));

        if ($subtotal <= 0 && count($items) > 0) {
            foreach ($items as $it) {
                $q = (float) ($isModel ? $it->quantity : ($it['quantity'] ?? 1));
                $p = (float) ($isModel ? $it->unit_price : ($it['unit_price'] ?? 0));
                $subtotal += ($q * $p);
            }
        }
        if ($total <= 0) {
            $total = max(0, $subtotal - $extraDiscVal + $tax);
        }

        // Payments
        $payMethod = strtolower($isModel ? ($invoice->payment_method ?? 'bank_transfer') : ($invoice['payment_method'] ?? 'bank_transfer'));
        $payStatus = strtolower($isModel ? ($invoice->payment_status ?? 'unpaid') : ($invoice['payment_status'] ?? 'unpaid'));
        
        $paidAmount = (float) ($isModel ? ($invoice->paid_amount ?? 0) : ($invoice['paid_amount'] ?? 0));
        $dueBalance = (float) ($isModel ? ($invoice->due_balance ?? max(0, $total - $paidAmount)) : ($invoice['due_balance'] ?? max(0, $total - $paidAmount)));

        $bankRef = $isModel 
            ? ($invoice->payments->last()?->reference_number ?? ($invoice->reference_number ?? '—')) 
            : ($invoice['bank_reference'] ?? '—');

        $verificationUrl = url("/admin/finance/purchase-invoices/{$invNum}");
    @endphp

    <title>{{ $tpl['title_ar'] }} - {{ $invNum }} | {{ $tpl['company_name_en'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --inv-border: {{ $tpl['styling']['border_color'] ?? '#0f172a' }};
            --inv-th-bg: {{ $tpl['styling']['header_bg'] ?? '#d4dfe8' }};
            --inv-font: {{ $tpl['styling']['font_family'] ?? "'Tajawal', 'Outfit', sans-serif" }};
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--inv-font);
            background: #f1f5f9;
            color: #0f172a;
            direction: rtl;
            text-align: right;
            padding: 1.25rem 1rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 1rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 0.65rem 1rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .btn-print {
            background: #0284c7;
            color: #ffffff;
            border: none;
            padding: 0.45rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.15s ease;
        }
        .btn-print:hover { background: #0369a1; }

        .btn-close {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 0.45rem 0.85rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
        }

        /* Viewport & Spreadsheet Canvas */
        .invoice-page-viewport {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .invoice-canvas {
            width: 100%;
            max-width: 900px;
            background: #ffffff;
            border: 1.5px solid var(--inv-border);
            padding: 0.85rem 1rem 1rem 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* 1. Header Grid: RIGHT (Company) | CENTER (Brand & Logo) | LEFT (Supplier & QR) */
        .inv-header-grid {
            display: grid;
            grid-template-columns: 36% 26% 38%;
            gap: 0.5rem;
            align-items: stretch;
            padding-bottom: 0.4rem;
            border-bottom: 1.5px solid var(--inv-border);
            margin-bottom: 0.4rem;
        }

        .company-meta-box {
            font-size: 0.72rem;
            line-height: 1.4;
            color: #1e293b;
        }
        .company-meta-box .meta-line {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 0.1rem;
        }
        .company-meta-box .meta-label {
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            min-width: 65px;
        }
        .company-meta-box .meta-value {
            font-weight: 700;
            color: #334155;
            word-break: break-word;
            flex: 1;
            border-bottom: 1px dotted #94a3b8;
            padding-bottom: 1px;
        }

        .company-center-col {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .company-center-col h1 {
            font-size: 0.88rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.2rem;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }
        .company-logo-img {
            max-height: 44px;
            max-width: 125px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .supplier-qr-box {
            display: flex;
            gap: 0.45rem;
            align-items: flex-start;
            justify-content: space-between;
            font-size: 0.72rem;
            line-height: 1.35;
        }
        .supplier-info-fields {
            flex: 1 1 0%;
            min-width: 0;
        }
        .supplier-meta-line {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 0.1rem;
        }
        .supplier-meta-line .meta-label {
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            min-width: 65px;
        }
        .supplier-meta-line .meta-value {
            font-weight: 700;
            color: #1e293b;
            flex: 1;
            border-bottom: 1px dotted #94a3b8;
            padding-bottom: 1px;
        }

        .qr-wrapper {
            flex-shrink: 0;
            width: 68px;
            height: 68px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            background: #ffffff;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* 2. Main Title Banner */
        .invoice-main-title {
            text-align: center;
            font-size: 1.45rem;
            font-weight: 900;
            color: #0f172a;
            margin: 0.35rem 0 0.45rem 0;
            letter-spacing: -0.01em;
            line-height: 1.15;
            position: relative;
        }
        .invoice-main-title span {
            display: block;
            font-size: 0.72rem;
            font-weight: 800;
            color: #64748b;
            letter-spacing: 0.05em;
            margin-top: 0.1rem;
        }

        /* 3. 10-Column Spreadsheet Table */
        .sheet-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.71rem;
            border: 1.5px solid var(--inv-border);
            margin-bottom: 0.4rem;
        }
        .sheet-table th {
            background-color: var(--inv-th-bg);
            color: #0f172a;
            border: 1px solid var(--inv-border);
            padding: 5px 3px;
            font-weight: 800;
            text-align: center;
            line-height: 1.25;
            vertical-align: middle;
        }
        .sheet-table th .en-sub {
            display: block;
            font-size: 0.62rem;
            font-weight: 600;
            color: #334155;
        }
        .sheet-table td {
            border: 1px solid var(--inv-border);
            padding: 4px 4px;
            color: #0f172a;
            vertical-align: middle;
            height: 24px;
        }
        .sheet-table tr.empty-row td {
            height: 22px;
            border-color: #cbd5e1;
            background: #fafafa;
        }

        /* 4. Bottom Section: Left (Settlement & Signatures) | Right (Totals) */
        .inv-bottom-section {
            display: grid;
            grid-template-columns: 55% 45%;
            gap: 0.75rem;
            margin-top: 0.35rem;
            align-items: start;
        }

        /* Settlement Box */
        .settlement-card {
            border: 1.5px solid var(--inv-border);
            padding: 0.4rem 0.55rem;
            border-radius: 2px;
            font-size: 0.7rem;
            margin-bottom: 0.5rem;
            background: #ffffff;
        }
        .settlement-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 0.2rem;
            padding-bottom: 0.15rem;
            border-bottom: 1px dotted #cbd5e1;
        }
        .settlement-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .settlement-label {
            font-weight: 800;
            color: #0f172a;
            min-width: 90px;
        }
        .settlement-value {
            font-weight: 700;
            font-family: monospace;
            color: #0284c7;
        }

        .payment-methods-ribbon {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            font-size: 0.68rem;
            font-weight: 800;
            color: #1e293b;
        }
        .method-check {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .check-box-square {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1.5px solid #0f172a;
            border-radius: 2px;
            line-height: 10px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
        }

        /* Signatures Grid */
        .signatures-box {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.5rem;
            border: 1.5px solid var(--inv-border);
            padding: 0.4rem;
            text-align: center;
            font-size: 0.68rem;
        }
        .sig-col {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .sig-title {
            font-weight: 800;
            color: #0f172a;
        }
        .sig-line {
            border-bottom: 1.5px dashed #94a3b8;
            margin-top: 0.8rem;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .stamp-circle {
            width: 44px;
            height: 44px;
            border: 1.5px dashed #94a3b8;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.55rem;
            color: #94a3b8;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Totals Box */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.72rem;
            border: 1.5px solid var(--inv-border);
        }
        .totals-table td {
            border: 1px solid var(--inv-border);
            padding: 4px 6px;
        }
        .totals-table .total-label {
            font-weight: 800;
            color: #0f172a;
            background: #f8fafc;
        }
        .totals-table .total-val {
            text-align: left;
            font-family: monospace;
            font-weight: 800;
            color: #0f172a;
        }
        .totals-table .grand-total-row td {
            background-color: var(--inv-th-bg);
            font-size: 0.8rem;
            font-weight: 900;
            color: #0284c7;
            border-top: 2px solid var(--inv-border);
        }

        /* 5. A4 Print Specifications */
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm 7mm;
            }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                height: 100% !important;
            }
            .no-print-bar { display: none !important; }
            .invoice-canvas {
                border: 1.5px solid #000000 !important;
                box-shadow: none !important;
                max-width: 100% !important;
                padding: 4mm 5mm !important;
            }
            .sheet-table th {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                border-color: #000000 !important;
            }
            .sheet-table td, .totals-table td, .inv-header-grid {
                border-color: #000000 !important;
            }
            .grand-total-row td {
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Action Bar -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-file-invoice-dollar text-emerald-600 text-lg"></i>
            <span style="font-weight: 800; color: #0f172a;">{{ $tpl['title_ar'] }} — {{ $invNum }}</span>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button onclick="window.print()" class="btn-print">
                <i class="fa-solid fa-print"></i>
                <span>طباعة الفاتورة الرسمية / Print</span>
            </button>
            <button onclick="window.close()" class="btn-close">
                <span>إغلاق / Close</span>
            </button>
        </div>
    </div>

    <div class="invoice-page-viewport">
        <div class="invoice-canvas">
            
            <!-- 1. Header Grid -->
            <div class="inv-header-grid">
                <!-- Right: Corporate & Contact Details -->
                <div class="company-meta-box">
                    <div class="meta-line">
                        <span class="meta-label">العنوان:</span>
                        <span class="meta-value">{{ $tpl['address_ar'] }}</span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-label">هاتف:</span>
                        <span class="meta-value font-mono"><bdi dir="ltr">{{ $tpl['phone'] }}</bdi></span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-label">البريد الإلكتروني:</span>
                        <span class="meta-value font-mono"><bdi dir="ltr">{{ $tpl['email'] }}</bdi></span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-label">الرقم الضريبي:</span>
                        <span class="meta-value font-mono font-bold"><bdi dir="ltr">{{ $tpl['vat_number'] }}</bdi></span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-label">السجل التجاري:</span>
                        <span class="meta-value font-mono font-bold"><bdi dir="ltr">{{ $tpl['cr_number'] }}</bdi></span>
                    </div>
                </div>

                <!-- Center: Brand Logo & Title -->
                <div class="company-center-col">
                    <h1>{{ $tpl['company_name_en'] }}</h1>
                    <img src="{{ asset('assets/logo/logo-main.png') }}" 
                         alt="Blue Zone Logo" 
                         class="company-logo-img" 
                         onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
                </div>

                <!-- Left: Supplier Info & QR Code -->
                <div class="supplier-qr-box">
                    <div class="supplier-info-fields">
                        <div class="supplier-meta-line">
                            <span class="meta-label">اسم المورد:</span>
                            <span class="meta-value font-bold">{{ $supplierName }}</span>
                        </div>
                        <div class="supplier-meta-line">
                            <span class="meta-label">رقم المورد:</span>
                            <span class="meta-value font-mono">{{ $supplierCode }}</span>
                        </div>
                        <div class="supplier-meta-line">
                            <span class="meta-label">العنوان:</span>
                            <span class="meta-value">{{ $supplierAddress }}</span>
                        </div>
                        <div class="supplier-meta-line" style="margin-top: 0.15rem;">
                            <span class="meta-label">تاريخ الفاتورة:</span>
                            <span class="meta-value font-mono">{{ $invDate }}</span>
                        </div>
                        <div class="supplier-meta-line">
                            <span class="meta-label">رقم الفاتورة:</span>
                            <span class="meta-value font-mono font-bold">{{ $invNum }}</span>
                        </div>
                        <div class="supplier-meta-line">
                            <span class="meta-label">المخزن:</span>
                            <span class="meta-value">{{ $warehouseName }}</span>
                        </div>
                    </div>

                    <div class="qr-wrapper" title="Scan for Verification">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($verificationUrl) }}" alt="QR Code">
                    </div>
                </div>
            </div>

            <!-- 2. Invoice Main Title Banner -->
            <div class="invoice-main-title">
                {{ $tpl['title_ar'] ?? 'فاتورة مشتريات' }}
                <span>فاتورة مشتريات ضريبية معتمدة &bull; PURCHASE TAX INVOICE</span>
            </div>

            <!-- 3. Spreadsheet 10-Column Line Items Table -->
            <table class="sheet-table">
                <thead>
                    <tr>
                        <th style="width: 3.5%;">
                            م
                            <span class="en-sub">N</span>
                        </th>
                        <th style="width: 11%;">
                            كود الصنف
                            <span class="en-sub">Item Code</span>
                        </th>
                        <th style="width: 25%;">
                            اسم الصنف
                            <span class="en-sub">Item Description</span>
                        </th>
                        <th style="width: 6%;">
                            الكمية
                            <span class="en-sub">Quantity</span>
                        </th>
                        <th style="width: 14%;">
                            تاريخ الصلاحية / التشغيله
                            <span class="en-sub">Expiry Date / Batch No.</span>
                        </th>
                        <th style="width: 8%;">
                            سعر الوحدة
                            <span class="en-sub">Price</span>
                        </th>
                        <th style="width: 5.5%;">
                            نسبة الخصم 1
                            <span class="en-sub">Disc. 1</span>
                        </th>
                        <th style="width: 11%;">
                            إجمالي بدون الضريبة
                            <span class="en-sub">Total Exc. VAT</span>
                        </th>
                        <th style="width: 8%;">
                            الضريبة
                            <span class="en-sub">VAT</span>
                        </th>
                        <th style="width: 12%;">
                            إجمالي الصنف شامل الضريبة
                            <span class="en-sub">Total Inc. VAT</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rowCount = 0;
                    @endphp
                    @foreach($items as $index => $item)
                        @php
                            $rowCount++;
                            $itCode = $isModel 
                                ? ($item->rawMaterial?->material_code ?? ($item->product?->sku ?? ($item->code ?? 'RM-'.$item->id))) 
                                : ($item['sku'] ?? ($item['item_code'] ?? '—'));
                                
                            $itName = $isModel ? $item->item_name : ($item['item_name'] ?? ($item['name_en'] ?? 'Item'));
                            $itQty = (float) ($isModel ? $item->quantity : ($item['quantity'] ?? 1));
                            $itPrice = (float) ($isModel ? $item->unit_price : ($item['unit_price'] ?? ($item['price'] ?? 0)));
                            $itDisc = (float) ($isModel ? ($item->discount_percent ?? ($item->disc_1 ?? 0)) : ($item['disc_1'] ?? 0));
                            
                            $itSubtotal = (float) ($isModel ? $item->subtotal : ($item['total_exc_vat'] ?? ($item['subtotal'] ?? ($itQty * $itPrice))));
                            $itTax = (float) ($isModel ? $item->tax_amount : ($item['tax_amount'] ?? ($item['tax'] ?? round($itSubtotal * 0.05, 2))));
                            $itTotal = (float) ($isModel ? $item->total : ($item['total'] ?? ($itSubtotal + $itTax)));

                            $batchExp = $isModel 
                                ? ($item->batch_number ?? ($item->expiry_date ? $item->expiry_date->format('Y-m-d') : 'LOT-2026 / 2028-12')) 
                                : ($item['batch_number'] ?? 'LOT-2026 / 2028-12');
                        @endphp
                        <tr>
                            <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $index + 1 }}</td>
                            <td style="text-align: center; font-family: monospace; font-size: 0.68rem;">{{ $itCode }}</td>
                            <td style="text-align: right; font-weight: bold;">{{ $itName }}</td>
                            <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ number_format($itQty, 2) }}</td>
                            <td style="text-align: center; font-family: monospace; font-size: 0.68rem;">{{ $batchExp }}</td>
                            <td style="text-align: center; font-family: monospace;">{{ number_format($itPrice, 2) }}</td>
                            <td style="text-align: center; font-family: monospace;">{{ $itDisc > 0 ? $itDisc . '%' : '0%' }}</td>
                            <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ number_format($itSubtotal, 2) }}</td>
                            <td style="text-align: center; font-family: monospace;">{{ number_format($itTax, 2) }}</td>
                            <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ number_format($itTotal, 2) }}</td>
                        </tr>
                    @endforeach

                    <!-- Filler Spreadsheet Grid Lines for authentic invoice look -->
                    @for($i = $rowCount; $i < 6; $i++)
                        <tr class="empty-row">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <!-- 4. Bottom Section: Left (Settlement & Signatures) | Right (Totals) -->
            <div class="inv-bottom-section">
                <!-- Left: Settlement Details & Signatures -->
                <div>
                    <!-- Settlement Box -->
                    <div class="settlement-card">
                        <div class="settlement-row">
                            <span class="settlement-label">طريقة الدفع:</span>
                            <div class="payment-methods-ribbon">
                                <span class="method-check">
                                    <span class="check-box-square">{{ $payMethod === 'cash' ? '✓' : '' }}</span> Cash نقداً
                                </span>
                                <span class="method-check">
                                    <span class="check-box-square">{{ $payMethod === 'bank_transfer' ? '✓' : '' }}</span> Bank Transfer تحويل بنكي
                                </span>
                                <span class="method-check">
                                    <span class="check-box-square">{{ $payMethod === 'cheque' ? '✓' : '' }}</span> Cheque شيك
                                </span>
                                <span class="method-check">
                                    <span class="check-box-square">{{ $payStatus === 'partially_paid' || $payStatus === 'partial' ? '✓' : '' }}</span> Partial Payment دفع جزئي
                                </span>
                            </div>
                        </div>
                        <div class="settlement-row">
                            <span class="settlement-label">المبلغ المدفوع:</span>
                            <span class="settlement-value">{{ number_format($paidAmount, 2) }} AED/SAR</span>
                        </div>
                        <div class="settlement-row">
                            <span class="settlement-label">المبلغ المتبقي:</span>
                            <span class="settlement-value" style="color: {{ $dueBalance > 0 ? '#e11d48' : '#059669' }};">{{ number_format($dueBalance, 2) }} AED/SAR</span>
                        </div>
                        <div class="settlement-row">
                            <span class="settlement-label">تاريخ الاستحقاق:</span>
                            <span class="settlement-value">{{ $dueDate }}</span>
                        </div>
                        <div class="settlement-row">
                            <span class="settlement-label">مرجع التحويل البنكي:</span>
                            <span class="settlement-value font-mono">{{ $bankRef }}</span>
                        </div>
                    </div>

                    <!-- Signatures Box -->
                    <div class="signatures-box">
                        <div class="sig-col">
                            <span class="sig-title">المستلم</span>
                            <div class="sig-line"></div>
                        </div>
                        <div class="sig-col">
                            <span class="sig-title">التوقيع</span>
                            <div class="sig-line"></div>
                        </div>
                        <div class="sig-col">
                            <span class="sig-title">الختم</span>
                            <div class="stamp-circle">STAMP</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Financial Totals Table (Matching user spreadsheet) -->
                <div>
                    <table class="totals-table">
                        <tr>
                            <td class="total-label">إجمالي الفاتورة بعد خصم الأصناف</td>
                            <td class="total-val">{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">خصم إضافي (%)</td>
                            <td class="total-val">{{ $extraDiscPct > 0 ? $extraDiscPct . '%' : '0.00%' }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">قيمة الخصم الإضافي</td>
                            <td class="total-val">{{ number_format($extraDiscVal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">صافي الفاتورة قبل الضريبة VAT</td>
                            <td class="total-val">{{ number_format(max(0, $subtotal - $extraDiscVal), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="total-label">قيمة الضريبة VAT</td>
                            <td class="total-val">{{ number_format($tax, 2) }}</td>
                        </tr>
                        <tr class="grand-total-row">
                            <td class="total-label" style="background: transparent;">إجمالي الفاتورة شامل الضريبة</td>
                            <td class="total-val" style="color: #0284c7; font-size: 0.88rem;">{{ number_format($total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Footer Policy Note -->
            <div style="margin-top: 0.5rem; text-align: center; font-size: 0.64rem; color: #64748b; border-top: 1px dotted #cbd5e1; padding-top: 0.3rem;">
                {{ $tpl['footer']['terms_ar'] ?? 'تخضع المواد والمنتجات الواردة لشروط الفحص المخبري وسلسلة التبريد المعتمدة لدى بلو زون للأدوية.' }}
            </div>
        </div>
    </div>
</body>
</html>
