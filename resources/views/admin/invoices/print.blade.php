<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $tpl = \App\Services\InvoiceTemplateService::getTemplate();
        
        $isModel = $order instanceof \Illuminate\Database\Eloquent\Model;
        $ordNum = $isModel ? $order->order_number : ($order['order_number'] ?? 'ORD-000');
        $invNum = $isModel ? ($order->invoice_number ?? $ordNum) : ($order['invoice_number'] ?? $ordNum);
        
        $rawDate = $isModel ? ($order->invoice_date ?? $order->date) : ($order['invoice_date'] ?? ($order['date'] ?? now()));
        $invDate = $rawDate instanceof \Carbon\Carbon ? $rawDate->format('Y-m-d') : (is_string($rawDate) ? substr($rawDate, 0, 10) : date('Y-m-d'));

        $dueDate = $isModel && !empty($order->due_date) 
            ? ($order->due_date instanceof \Carbon\Carbon ? $order->due_date->format('Y-m-d') : substr($order->due_date, 0, 10))
            : \Carbon\Carbon::parse($invDate)->addDays(30)->format('Y-m-d');
        
        $custName = $isModel 
            ? ($order->customer_name ?? ($order->customer?->name ?? 'عميل معتمد')) 
            : ($order['customer_name'] ?? 'عميل معتمد');
            
        $custPhone = $isModel 
            ? ($order->customer_phone ?? ($order->customer?->phone ?? '—')) 
            : ($order['customer_phone'] ?? '—');
            
        $custEmail = $isModel 
            ? ($order->customer_email ?? ($order->customer?->email ?? '—')) 
            : ($order['customer_email'] ?? '—');
            
        $rawAddr = $isModel ? $order->shipping_address : ($order['shipping_address'] ?? null);
        if (is_array($rawAddr)) {
            $custAddress = implode(', ', array_filter([$rawAddr['street'] ?? '', $rawAddr['city'] ?? '', $rawAddr['country'] ?? '']));
        } elseif (is_string($rawAddr) && !empty($rawAddr)) {
            $custAddress = $rawAddr;
        } else {
            $custAddress = 'المملكة العربية السعودية';
        }

        $items = $isModel ? $order->items : ($order['items'] ?? []);
        
        // Financials
        $subtotal = (float) ($isModel ? ($order->subtotal ?? 0) : ($order['subtotal'] ?? 0));
        $discount = (float) ($isModel ? ($order->discount ?? 0) : ($order['discount'] ?? 0));
        $tax = (float) ($isModel ? ($order->tax ?? 0) : ($order['tax'] ?? 0));
        $total = (float) ($isModel ? ($order->total ?? 0) : ($order['total'] ?? 0));

        $calcSubtotal = 0;
        $calcTax = 0;
        $calcTotal = 0;

        if ($subtotal <= 0 && count($items) > 0) {
            foreach ($items as $it) {
                $q = (float) ($isModel ? $it->quantity : ($it['quantity'] ?? 1));
                $p = (float) ($isModel ? ($it->unit_price ?? $it->price) : ($it['unit_price'] ?? ($it['price'] ?? 0)));
                $subtotal += ($q * $p);
            }
        }
        if ($total <= 0) {
            $total = max(0, $subtotal - $discount + $tax);
        }

        // Payments
        $payMethod = strtolower($isModel ? ($order->payment_method ?? '') : ($order['payment_method'] ?? ''));
        $payStatus = strtolower($isModel ? ($order->payment_status ?? 'unpaid') : ($order['payment_status'] ?? 'unpaid'));
        
        $paidAmount = $isModel 
            ? (float) ($order->paid_amount ?? ($payStatus === 'paid' ? $total : 0)) 
            : (float) ($order['paid_amount'] ?? ($payStatus === 'paid' ? $total : 0));
            
        $dueBalance = $isModel 
            ? (float) ($order->due_balance ?? max(0, $total - $paidAmount)) 
            : (float) ($order['due_balance'] ?? max(0, $total - $paidAmount));

        $bankRef = $isModel 
            ? ($order->payment_transaction_id ?? ($order->payment_payload['reference'] ?? '—')) 
            : ($order['payment_transaction_id'] ?? ($order['bank_reference'] ?? '—'));

        // Public verification URL encoded in QR Code
        $verificationUrl = route('orders.verify', ['order' => $ordNum]);
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
            max-width: 840px;
            margin: 0 auto 1rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 0.6rem 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .no-print-bar button, .no-print-bar a {
            font-family: var(--inv-font);
            font-size: 0.82rem;
            font-weight: 800;
            padding: 0.45rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
        }

        .btn-print {
            background: #0284c7;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #0369a1;
        }
        .btn-close {
            background: #e2e8f0;
            color: #334155;
        }

        /* Screen Responsive Viewport */
        .invoice-page-viewport {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            display: flex;
            justify-content: center;
            padding: 0.25rem 0 1.5rem 0;
        }

        /* Main Printable Sheet (Calibrated for A4 Single Page Fit & Exact Spreadsheet Design) */
        .invoice-canvas {
            max-width: 840px;
            width: 100%;
            background: #ffffff;
            padding: 0.85rem 1.15rem;
            border: 2px solid var(--inv-border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            position: relative;
            page-break-inside: avoid;
        }

        /* Responsive Breakpoint for Mobile/Tablet Screens */
        @media screen and (max-width: 860px) {
            body {
                padding: 0.6rem 0.35rem;
            }
            .invoice-canvas {
                min-width: 780px; /* guarantees table geometry and no clipping */
            }
        }

        /* Header Grid: Right (Company Info) | Center (Name+Logo) | Left (Customer+QR) in RTL */
        .inv-header-grid {
            display: grid;
            grid-template-columns: 36% 22% 42%;
            gap: 0.6rem;
            align-items: start;
            padding-bottom: 0.25rem;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        /* Right Column (Company Details in RTL) */
        .company-meta-box {
            font-size: 0.74rem;
            line-height: 1.45;
            min-width: 0;
            overflow: hidden;
        }
        .meta-line {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 0.12rem;
            white-space: nowrap;
        }
        .meta-line.meta-wrap {
            white-space: normal;
            line-height: 1.3;
            word-break: break-word;
        }
        .meta-label {
            font-weight: 800;
            color: #1e293b;
            flex-shrink: 0;
            font-size: 0.74rem;
        }
        .meta-dots {
            flex: 1;
            border-bottom: 1px dotted #94a3b8;
            height: 0.85em;
            margin: 0 0.15rem;
        }
        .meta-value {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.74rem;
            min-width: 0;
        }

        /* Center Column (Name & Logo) */
        .company-center-col {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 0;
        }
        .company-center-col h1 {
            font-size: 1.08rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin-bottom: 0.2rem;
            line-height: 1.2;
            word-break: break-word;
        }
        .company-logo-img {
            max-height: 46px;
            max-width: 130px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        /* Left Column (Customer Details & Scannable QR Code in RTL) - Fully Responsive */
        .client-qr-box {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
            justify-content: space-between;
            direction: rtl;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
            overflow: hidden;
        }
        .qr-wrapper {
            flex-shrink: 0;
            width: 68px;
            height: 68px;
            max-width: 68px;
            max-height: 68px;
            aspect-ratio: 1 / 1;
            border: 1px solid #cbd5e1;
            padding: 2px;
            background: #ffffff;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }
        .qr-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .client-info-fields {
            flex: 1 1 0%;
            min-width: 0; /* CRITICAL: allows flex child to shrink properly */
            font-size: 0.74rem;
            line-height: 1.35;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .client-meta-line {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 0.12rem;
            font-size: 0.74rem;
            min-width: 0;
        }
        .client-meta-line .meta-label {
            font-weight: 800;
            color: #1e293b;
            flex-shrink: 0;
            font-size: 0.73rem;
            white-space: nowrap;
        }
        .client-meta-line .meta-value {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.73rem;
            word-break: break-word;
            overflow-wrap: break-word;
            min-width: 0;
        }
            font-size: 0.73rem;
            word-break: break-word;
        }

        /* Centered Big Invoice Title */
        .invoice-main-title {
            text-align: center;
            font-size: 1.38rem;
            font-weight: 900;
            color: #0f172a;
            margin: 0.35rem 0 0.35rem 0;
            letter-spacing: -0.01em;
            line-height: 1.15;
        }

        /* Spreadsheet-Styled Line Items Table */
        .sheet-table-wrap {
            width: 100%;
            margin-bottom: 0.45rem;
        }
        .sheet-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.72rem;
            border: 1.5px solid var(--inv-border);
            table-layout: fixed;
        }
        .sheet-table th, .sheet-table td {
            border: 1px solid var(--inv-border);
            padding: 0.28rem 0.25rem;
            vertical-align: middle;
            overflow: hidden;
        }
        .sheet-table th {
            background-color: var(--inv-th-bg);
            color: #0f172a;
            font-weight: 900;
            text-align: center;
            line-height: 1.15;
            padding: 0.32rem 0.2rem;
        }
        .th-ar {
            display: block;
            font-size: 0.70rem;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .th-en {
            display: block;
            font-size: 0.58rem;
            font-weight: 700;
            color: #334155;
            direction: ltr;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sheet-table tbody tr {
            min-height: 22px;
        }
        .text-center { text-align: center; }
        .text-start { text-align: right; }
        .text-end { text-align: left; }
        .font-mono { font-family: monospace, monospace; }
        .font-bold { font-weight: 800; }
        .sheet-table td.cell-code {
            white-space: nowrap;
            text-align: center;
            font-family: monospace, monospace;
            font-weight: 700;
            font-size: 0.72rem;
            letter-spacing: -0.01em;
            padding: 0.28rem 0.2rem;
        }
        .sheet-table td.cell-num {
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
            text-align: center;
            font-size: 0.72rem;
            padding: 0.28rem 0.2rem;
        }

        /* Bottom Section: Right (Totals) | Left (Payment & Signatures) */
        .inv-bottom-section {
            display: grid;
            grid-template-columns: 50% 50%;
            gap: 0.75rem;
            align-items: start;
            margin-top: 0.35rem;
            width: 100%;
            page-break-inside: avoid;
        }

        /* Right Side Totals Table */
        .totals-box-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.74rem;
            border: 1px solid var(--inv-border);
            table-layout: fixed;
        }
        .totals-box-table th, .totals-box-table td {
            border: 1px solid var(--inv-border);
            padding: 0.28rem 0.45rem;
            vertical-align: middle;
        }
        .totals-box-table th {
            background-color: var(--inv-th-bg);
            font-weight: 900;
            text-align: center;
            padding: 0.32rem 0.45rem;
        }

        /* Left Side Payment Settlement Table */
        .payment-box-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.74rem;
            border: 1px solid var(--inv-border);
            table-layout: fixed;
        }
        .payment-box-table th, .payment-box-table td {
            border: 1px solid var(--inv-border);
            padding: 0.28rem 0.45rem;
            vertical-align: middle;
        }
        .payment-box-table th {
            background-color: var(--inv-th-bg);
            font-weight: 900;
            text-align: center;
            padding: 0.32rem 0.45rem;
        }
        .payment-methods-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.3rem 0.5rem;
            align-items: center;
            font-size: 0.68rem;
            font-weight: 800;
        }
        .chk-item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            white-space: nowrap;
            font-size: 0.68rem;
        }
        .chk-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 13px;
            height: 13px;
            border: 1.5px solid #0f172a;
            border-radius: 2px;
            font-size: 9px;
            font-weight: 900;
            line-height: 1;
            background: #fff;
            flex-shrink: 0;
        }

        /* Signatures Section */
        .signatures-grid {
            margin-top: 0.5rem;
            padding-top: 0.25rem;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.65rem;
            text-align: center;
            font-size: 0.73rem;
        }
        .sig-item {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .sig-line {
            border-bottom: 1.5px dashed #64748b;
            width: 85%;
            margin: 0 auto;
        }
        .stamp-box {
            width: 54px;
            height: 54px;
            border: 1.5px dashed #94a3b8;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Footer Terms */
        .inv-footer-notes {
            margin-top: 0.45rem;
            padding-top: 0.35rem;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 0.67rem;
            color: #64748b;
            line-height: 1.35;
            page-break-inside: avoid;
        }

        /* Strict Single-Page A4 Print Configuration */
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
            .no-print-bar {
                display: none !important;
            }
            .invoice-page-viewport {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                overflow: visible !important;
            }
            .invoice-canvas {
                border: 1.5px solid #000000 !important;
                box-shadow: none !important;
                max-width: 100% !important;
                min-width: 0 !important;
                width: 100% !important;
                margin: 0 auto !important;
                padding: 0.35rem 0.45rem !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
            }
            .sheet-table th {
                background-color: #d4dfe8 !important;
            }
            .totals-box-table th, .payment-box-table th {
                background-color: #d4dfe8 !important;
            }
            .inv-header-grid {
                grid-template-columns: 36% 22% 42% !important;
                gap: 0.35rem !important;
                width: 100% !important;
            }
            .client-qr-box {
                display: flex !important;
                flex-direction: row !important;
                align-items: flex-start !important;
                justify-content: space-between !important;
                gap: 0.35rem !important;
                width: 100% !important;
                min-width: 0 !important;
                overflow: hidden !important;
            }
            .client-info-fields {
                flex: 1 1 0% !important;
                min-width: 0 !important;
                word-break: break-word !important;
            }
            .qr-wrapper {
                width: 16mm !important;
                height: 16mm !important;
                min-width: 16mm !important;
                min-height: 16mm !important;
                max-width: 16mm !important;
                max-height: 16mm !important;
                flex-shrink: 0 !important;
                page-break-inside: avoid !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                border: 1px solid #000000 !important;
                padding: 1px !important;
                background: #ffffff !important;
            }
            .qr-wrapper img {
                width: 100% !important;
                height: 100% !important;
                object-fit: contain !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Toolbar for Screen/Print Action -->
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-file-invoice text-sky-600 text-lg"></i>
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

    <!-- Responsive Viewport Wrapper -->
    <div class="invoice-page-viewport">
        <!-- Official Invoice Canvas -->
        <div class="invoice-canvas">

        <!-- 1. Top Header Grid: RIGHT (Company Info) | CENTER (Logo & Name) | LEFT (Client Info & QR) in RTL -->
        <div class="inv-header-grid">
            <!-- Right: Company Corporate & Tax Details -->
            <div class="company-meta-box">
                <div class="meta-line meta-wrap">
                    <span class="meta-label">العنوان:</span>
                    <span class="meta-value">{{ $tpl['address_ar'] }}</span>
                </div>
                <div class="meta-line">
                    <span class="meta-label">هاتف:</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value font-mono"><bdi dir="ltr">{{ $tpl['phone'] }}</bdi></span>
                </div>
                <div class="meta-line">
                    <span class="meta-label">البريد الإلكتروني:</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value font-mono"><bdi dir="ltr">{{ $tpl['email'] }}</bdi></span>
                </div>
                <div class="meta-line">
                    <span class="meta-label">الرقم الضريبي (.VAT No):</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value font-mono font-bold"><bdi dir="ltr">{{ $siteInfo['tax_number'] ?? ($tpl['vat_number'] ?? '31004829100003') }}</bdi></span>
                </div>
                <div class="meta-line">
                    <span class="meta-label">السجل التجاري:</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value font-mono font-bold"><bdi dir="ltr">{{ $siteInfo['commercial_record'] ?? ($tpl['cr_number'] ?? 'CR-1010842910') }}</bdi></span>
                </div>
                @if(!empty($siteInfo['clinical_license']))
                    <div class="meta-line">
                        <span class="meta-label">الترخيص الطبي:</span>
                        <span class="meta-dots"></span>
                        <span class="meta-value font-mono font-bold"><bdi dir="ltr">{{ $siteInfo['clinical_license'] }}</bdi></span>
                    </div>
                @endif
            </div>

            <!-- Center: Company Name & Logo -->
            <div class="company-center-col">
                <h1>{{ $tpl['company_name_en'] }}</h1>
                <img src="{{ asset('assets/logo/logo-main.png') }}" 
                     alt="Blue Zone Logo" 
                     class="company-logo-img" 
                     onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
            </div>

            <!-- Left: Client Info & QR Code -->
            <div class="client-qr-box">
                <div class="client-info-fields">
                    <div class="client-meta-line">
                        <span class="meta-label">اسم العميل:</span>
                        <span class="meta-value font-bold" style="white-space: normal; line-height: 1.3;">{{ $custName }}</span>
                    </div>
                    <div class="client-meta-line">
                        <span class="meta-label">رقم العميل:</span>
                        <span class="meta-value font-mono"><bdi dir="ltr">{{ $custPhone }}</bdi></span>
                    </div>
                    <div class="client-meta-line">
                        <span class="meta-label">العنوان:</span>
                        <span class="meta-value" style="white-space: normal; line-height: 1.25; font-size: 0.72rem;">{{ $custAddress }}</span>
                    </div>
                    <div class="client-meta-line" style="margin-top: 0.15rem;">
                        <span class="meta-label">تاريخ الفاتورة:</span>
                        <span class="meta-value font-mono"><bdi dir="ltr">{{ $invDate }}</bdi></span>
                    </div>
                    <div class="client-meta-line">
                        <span class="meta-label">رقم الفاتورة:</span>
                        <span class="meta-value font-mono font-bold"><bdi dir="ltr">{{ $invNum }}</bdi></span>
                    </div>
                </div>
                <div class="qr-wrapper" title="Scan for Official Verification">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($verificationUrl) }}" alt="QR Code">
                </div>
            </div>
        </div>

        <!-- 2. Centered Big Invoice Title -->
        <div class="invoice-main-title">
            {{ $tpl['title_ar'] ?? 'فاتورة مبيعات' }}
            <span style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569; letter-spacing: 0.04em; margin-top: 0.1rem;">
                فاتورة ضريبية رسمية &bull; TAX INVOICE
            </span>
        </div>

        <!-- 3. Line Items Table (Exact 11 Columns matching screen) -->
        <div class="sheet-table-wrap">
            <table class="sheet-table">
                <thead>
                    <tr>
                        <th style="width: 3.5%;">
                            <span class="th-ar">م</span>
                            <span class="th-en">N</span>
                        </th>
                        <th style="width: 11%;">
                            <span class="th-ar">كود الصنف</span>
                            <span class="th-en">Item Code</span>
                        </th>
                        <th style="width: 23%;">
                            <span class="th-ar">اسم الصنف</span>
                            <span class="th-en">Item Description</span>
                        </th>
                        <th style="width: 6%;">
                            <span class="th-ar">الكمية</span>
                            <span class="th-en">Qty</span>
                        </th>
                        <th style="width: 13%;">
                            <span class="th-ar">تاريخ الصلاحية / التشغيلة</span>
                            <span class="th-en">Expiry Date / Batch No.</span>
                        </th>
                        <th style="width: 7.5%;">
                            <span class="th-ar">سعر الوحدة</span>
                            <span class="th-en">Price</span>
                        </th>
                        <th style="width: 5%;">
                            <span class="th-ar">خصم 1</span>
                            <span class="th-en">Disc. 1</span>
                        </th>
                        <th style="width: 5%;">
                            <span class="th-ar">خصم 2</span>
                            <span class="th-en">Disc. 2</span>
                        </th>
                        <th style="width: 9.5%;">
                            <span class="th-ar">بدون الضريبة</span>
                            <span class="th-en">Total Exc. VAT</span>
                        </th>
                        <th style="width: 7.5%;">
                            <span class="th-ar">الضريبة</span>
                            <span class="th-en">VAT</span>
                        </th>
                        <th style="width: 9%;">
                            <span class="th-ar">شامل الضريبة</span>
                            <span class="th-en">Total Inc.</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $rowCount = 0; 
                        $calcSubtotal = 0;
                        $calcTax = 0;
                        $calcTotal = 0;
                    @endphp
                    @forelse($items as $idx => $item)
                        @php
                            $rowCount++;
                            $isItemObj = is_object($item);
                            $sku = $isItemObj ? ($item->sku ?? ($item->product?->sku ?? 'BZ-' . $rowCount)) : ($item['sku'] ?? 'BZ-' . $rowCount);
                            $name = $isItemObj 
                                ? ($item->product_name_ar ?? ($item->product?->name_ar ?? ($item->product_name_en ?? ($item->product?->name_en ?? 'صنف حيوي'))))
                                : ($item['name_ar'] ?? ($item['name_en'] ?? ($item['product_name_ar'] ?? ($item['product_name_en'] ?? 'صنف حيوي'))));
                            
                            $qty = (float) ($isItemObj ? $item->quantity : ($item['quantity'] ?? 1));
                            $unitPrice = (float) ($isItemObj ? ($item->unit_price ?? $item->price) : ($item['unit_price'] ?? ($item['price'] ?? 0)));
                            
                            $disc1 = (float) ($isItemObj ? ($item->disc_1 ?? ($item->discount ?? 0)) : ($item['disc_1'] ?? ($item['discount'] ?? 0)));
                            $disc2 = (float) ($isItemObj ? ($item->disc_2 ?? 0) : ($item['disc_2'] ?? 0));
                            
                            $batch = $isItemObj ? ($item->batch_number ?? ($item->batch?->batch_number ?? '—')) : ($item['batch_number'] ?? '—');
                            $expiry = $isItemObj ? ($item->expiry_date ?? ($item->batch?->expiry_date ?? '—')) : ($item['expiry_date'] ?? '—');
                            if ($expiry instanceof \Carbon\Carbon) {
                                $expiry = $expiry->format('Y-m-d');
                            }
                            $batchExpiryText = ($batch !== '—' || $expiry !== '—') ? ($expiry . ' / ' . $batch) : '—';

                            // Calculations
                            $gross = $qty * $unitPrice;
                            $discAmount = $gross * ($disc1 / 100) + ($gross * (1 - $disc1 / 100)) * ($disc2 / 100);
                            $lineExcVat = max(0, $gross - $discAmount);
                            $lineVat = $lineExcVat * 0.15;
                            $lineIncVat = $lineExcVat + $lineVat;

                            $calcSubtotal += $lineExcVat;
                            $calcTax += $lineVat;
                            $calcTotal += $lineIncVat;
                        @endphp
                        <tr>
                            <td class="text-center font-mono">{{ $rowCount }}</td>
                            <td class="cell-code">{{ $sku }}</td>
                            <td class="text-start font-bold" style="font-size: 0.72rem; line-height: 1.35; padding: 0.28rem 0.35rem;">{{ $name }}</td>
                            <td class="text-center font-mono font-bold cell-num">{{ $qty }}</td>
                            <td class="text-center font-mono" style="font-size: 0.67rem; line-height: 1.2;">{{ $batchExpiryText }}</td>
                            <td class="text-center font-mono cell-num">{{ number_format($unitPrice, 2) }}</td>
                            <td class="text-center font-mono cell-num">{{ $disc1 > 0 ? $disc1 . '%' : '0%' }}</td>
                            <td class="text-center font-mono cell-num">{{ $disc2 > 0 ? $disc2 . '%' : '0%' }}</td>
                            <td class="text-center font-mono cell-num">{{ number_format($lineExcVat, 2) }}</td>
                            <td class="text-center font-mono cell-num">{{ number_format($lineVat, 2) }}</td>
                            <td class="text-center font-mono font-bold cell-num">{{ number_format($lineIncVat, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center" style="padding: 1rem; color: #64748b;">لا توجد أصناف مدرجة في الفاتورة.</td>
                        </tr>
                    @endforelse

                    <!-- Controlled empty filler rows for authentic spreadsheet look (fits within 1 page) -->
                    @for($i = $rowCount; $i < 4; $i++)
                        <tr>
                            <td class="text-center font-mono text-slate-300">{{ $i + 1 }}</td>
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
        </div>

        @php
            $finalSubtotal = $calcSubtotal > 0 ? $calcSubtotal : $subtotal;
            $finalTax = $calcTax > 0 ? $calcTax : $tax;
            $finalTotal = $calcTotal > 0 ? $calcTotal : $total;
            $finalDue = max(0, $finalTotal - $paidAmount);

            $isCash = in_array($payMethod, ['cash', 'نقداً'], true);
            $isBank = in_array($payMethod, ['bank_transfer', 'wire', 'تحويل بنكي'], true);
            $isCheque = in_array($payMethod, ['cheque', 'check', 'شيك'], true);
            $isPartial = $payStatus === 'partially_paid' || ($paidAmount > 0 && $finalDue > 0);
        @endphp

        <!-- 4. Bottom Section: RIGHT (Financial Totals) | LEFT (Payment Settlement & Signatures) in RTL -->
        <div class="inv-bottom-section">
            <!-- Right Column in RTL: Financial Summary Totals Table -->
            <div>
                <table class="totals-box-table">
                    <thead>
                        <tr>
                            <th style="width: 55%;">البيان</th>
                            <th style="width: 45%;">القيمة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-bold">{{ $tpl['totals_box']['label_subtotal_ar'] }}</td>
                            <td class="text-center font-mono font-bold" style="white-space: nowrap;">SAR {{ number_format($finalSubtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>{{ $tpl['totals_box']['label_extra_disc_pct_ar'] }}</td>
                            <td class="text-center font-mono">0%</td>
                        </tr>
                        <tr>
                            <td>{{ $tpl['totals_box']['label_extra_disc_val_ar'] }}</td>
                            <td class="text-center font-mono" style="white-space: nowrap;">SAR {{ number_format($discount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{ $tpl['totals_box']['label_net_before_vat_ar'] }}</td>
                            <td class="text-center font-mono font-bold" style="white-space: nowrap;">SAR {{ number_format(max(0, $finalSubtotal - $discount), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{ $tpl['totals_box']['label_vat_ar'] }}</td>
                            <td class="text-center font-mono font-bold" style="white-space: nowrap;">SAR {{ number_format($finalTax, 2) }}</td>
                        </tr>
                        <tr style="background-color: var(--inv-th-bg);">
                            <td class="font-bold" style="font-size: 0.82rem;">{{ $tpl['totals_box']['label_grand_total_ar'] }}</td>
                            <td class="text-center font-mono font-bold" style="font-size: 0.88rem; white-space: nowrap;">
                                SAR {{ number_format($finalTotal, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Left Column in RTL: Payment Settlement & Signatures -->
            <div>
                <table class="payment-box-table">
                    <thead>
                        <tr>
                            <th style="width: 38%;">البيان</th>
                            <th style="width: 62%;">القيمة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-bold" style="vertical-align: middle;">طريقة الدفع:</td>
                            <td style="padding: 0.35rem 0.45rem;">
                                <div class="payment-methods-grid">
                                    <span class="chk-item">
                                        <span class="chk-box">{{ $isCash ? '✓' : '' }}</span>
                                        <span>نقداً Cash</span>
                                    </span>
                                    <span class="chk-item">
                                        <span class="chk-box">{{ $isBank ? '✓' : '' }}</span>
                                        <span>تحويل بنكي Bank</span>
                                    </span>
                                    <span class="chk-item">
                                        <span class="chk-box">{{ $isPartial ? '✓' : '' }}</span>
                                        <span>دفع جزئي Partial</span>
                                    </span>
                                    <span class="chk-item">
                                        <span class="chk-box">{{ $isCheque ? '✓' : '' }}</span>
                                        <span>شيك Cheque</span>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold">المبلغ المدفوع:</td>
                            <td class="font-mono font-bold" style="white-space: nowrap;">SAR {{ number_format($paidAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">المبلغ المتبقي:</td>
                            <td class="font-mono font-bold" style="white-space: nowrap; color: {{ $finalDue > 0 ? '#b91c1c' : '#047857' }};">
                                SAR {{ number_format($finalDue, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold">تاريخ الاستحقاق:</td>
                            <td class="font-mono"><bdi dir="ltr">{{ $finalDue > 0 ? $dueDate : 'مسدد بالكامل' }}</bdi></td>
                        </tr>
                        <tr>
                            <td class="font-bold">مرجع التحويل:</td>
                            <td class="font-mono"><bdi dir="ltr">{{ $bankRef ?: '—' }}</bdi></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Signatures & Stamp -->
                <div class="signatures-grid">
                    <div class="sig-item">
                        <span class="font-bold">المستلم:</span>
                        <div class="sig-line"></div>
                    </div>
                    <div class="sig-item">
                        <span class="font-bold">التوقيع:</span>
                        <div class="sig-line"></div>
                    </div>
                    <div class="sig-item">
                        <span class="font-bold">الختم:</span>
                        <div class="stamp-box">
                            Official Seal
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Footer Notes & Regulatory Compliance -->
        @if(!empty($tpl['footer']['enabled']))
            <div class="inv-footer-notes">
                <p>{{ $tpl['footer']['terms_ar'] }}</p>
                <p style="font-size: 0.65rem; margin-top: 0.15rem; font-family: 'Outfit', sans-serif; direction: ltr;">
                    {{ $tpl['footer']['terms_en'] }} &bull; ZATCA Tax Compliance &bull; SFDA Serialized
                </p>
            </div>
        @endif
        </div><!-- /.invoice-canvas -->
    </div><!-- /.invoice-page-viewport -->

</body>
</html>
