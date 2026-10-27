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
            padding: 1.5rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            max-width: 1040px;
            margin: 0 auto 1rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .no-print-bar button, .no-print-bar a {
            font-family: var(--inv-font);
            font-size: 0.85rem;
            font-weight: 800;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        /* Main Printable Sheet */
        .invoice-canvas {
            max-width: 1040px;
            margin: 0 auto;
            background: #ffffff;
            padding: 1.25rem 1.5rem;
            border: 2px solid var(--inv-border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        /* Header Grid (Top section) */
        .inv-header-grid {
            display: grid;
            grid-template-columns: 310px 1fr 310px;
            gap: 1rem;
            align-items: start;
            padding-bottom: 0.5rem;
        }

        /* Top Center */
        .company-center-col {
            text-align: center;
        }
        .company-center-col h1 {
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 0.35rem;
        }
        .company-logo-img {
            max-height: 52px;
            max-width: 170px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        /* Top Right (Company/Seller Details) */
        .company-meta-box {
            font-size: 0.8rem;
            line-height: 1.6;
        }
        .meta-line {
            display: flex;
            align-items: baseline;
            gap: 0.35rem;
            margin-bottom: 0.2rem;
        }
        .meta-label {
            font-weight: 800;
            color: #1e293b;
            white-space: nowrap;
        }
        .meta-dots {
            flex: 1;
            border-bottom: 1px dotted #94a3b8;
            height: 0.85em;
        }
        .meta-value {
            font-weight: 700;
            color: #0f172a;
        }

        /* Top Left (Customer Details + QR) */
        .client-qr-box {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }
        .qr-wrapper {
            flex-shrink: 0;
            width: 82px;
            height: 82px;
            border: 1px solid #cbd5e1;
            padding: 3px;
            background: #fff;
            border-radius: 6px;
        }
        .qr-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .client-info-fields {
            flex: 1;
            font-size: 0.8rem;
            line-height: 1.5;
        }

        /* Centered Big Invoice Title */
        .invoice-main-title {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 900;
            color: #0f172a;
            margin: 0.75rem 0 0.5rem 0;
            letter-spacing: -0.01em;
        }

        /* Spreadsheet-Styled Line Items Table */
        .sheet-table-wrap {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 0.75rem;
        }
        .sheet-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.78rem;
            border: 1.5px solid var(--inv-border);
        }
        .sheet-table th, .sheet-table td {
            border: 1px solid var(--inv-border);
            padding: 0.35rem 0.4rem;
            vertical-align: middle;
        }
        .sheet-table th {
            background-color: var(--inv-th-bg);
            color: #0f172a;
            font-weight: 900;
            text-align: center;
            line-height: 1.25;
        }
        .th-ar {
            display: block;
            font-size: 0.78rem;
        }
        .th-en {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            color: #334155;
            direction: ltr;
        }
        .sheet-table tbody tr {
            min-height: 28px;
        }
        .sheet-table tbody td {
            color: #0f172a;
        }
        .text-center { text-align: center; }
        .text-start { text-align: right; }
        .text-end { text-align: left; }
        .font-mono { font-family: monospace, monospace; }
        .font-bold { font-weight: 800; }

        /* Bottom Section: Totals & Payments side-by-side */
        .inv-bottom-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            align-items: start;
            margin-top: 0.5rem;
        }

        /* Left Payment Box */
        .payment-box-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            border: 1px solid var(--inv-border);
        }
        .payment-box-table th, .payment-box-table td {
            border: 1px solid var(--inv-border);
            padding: 0.35rem 0.5rem;
        }
        .payment-box-table th {
            background-color: var(--inv-th-bg);
            font-weight: 900;
            text-align: center;
        }
        .payment-methods-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.35rem;
            flex-wrap: wrap;
            font-size: 0.72rem;
            font-weight: 800;
        }
        .chk-item {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            white-space: nowrap;
        }
        .chk-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 13px;
            height: 13px;
            border: 1.5px solid #0f172a;
            font-size: 10px;
            font-weight: 900;
            line-height: 1;
            background: #fff;
        }

        /* Right Totals Box */
        .totals-box-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            border: 1px solid var(--inv-border);
        }
        .totals-box-table th, .totals-box-table td {
            border: 1px solid var(--inv-border);
            padding: 0.35rem 0.5rem;
        }
        .totals-box-table th {
            background-color: var(--inv-th-bg);
            font-weight: 900;
            text-align: center;
        }

        /* Signatures Section */
        .signatures-grid {
            margin-top: 1rem;
            padding-top: 0.5rem;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 2rem;
            text-align: center;
            font-size: 0.8rem;
        }
        .sig-item {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .sig-line {
            border-bottom: 1.5px dashed #64748b;
            width: 80%;
            margin: 0 auto;
        }
        .stamp-box {
            width: 75px;
            height: 75px;
            border: 1.5px dashed #94a3b8;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Footer Terms */
        .inv-footer-notes {
            margin-top: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 0.72rem;
            color: #64748b;
            line-height: 1.5;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .invoice-canvas {
                border: 1.5px solid #000000 !important;
                box-shadow: none !important;
                max-width: 100% !important;
                padding: 0.75rem 1rem !important;
            }
            @page {
                size: A4 portrait;
                margin: 0.6cm;
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

    <!-- Official Invoice Canvas -->
    <div class="invoice-canvas">

        <!-- 1. Top Header Grid -->
        <div class="inv-header-grid">
            <!-- Left: Client Info & QR Code -->
            <div class="client-qr-box">
                <div class="qr-wrapper" title="Scan for Official Verification">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($verificationUrl) }}" alt="QR Code">
                </div>
                <div class="client-info-fields">
                    <div class="meta-line">
                        <span class="meta-label">اسم العميل:</span>
                        <span class="meta-value">{{ $custName }}</span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-label">رقم العميل:</span>
                        <span class="meta-value font-mono">{{ $custPhone }}</span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-label">العنوان:</span>
                        <span class="meta-value" style="font-size: 0.75rem;">{{ $custAddress }}</span>
                    </div>
                    <div class="meta-line" style="margin-top: 0.35rem;">
                        <span class="meta-label">تاريخ الفاتورة:</span>
                        <span class="meta-value font-mono">{{ $invDate }}</span>
                    </div>
                    <div class="meta-line">
                        <span class="meta-label">رقم الفاتورة:</span>
                        <span class="meta-value font-mono font-bold">{{ $invNum }}</span>
                    </div>
                </div>
            </div>

            <!-- Center: Company Name & Logo -->
            <div class="company-center-col">
                <h1>{{ $tpl['company_name_en'] }}</h1>
                <img src="{{ asset('assets/logo/logo-main.png') }}" 
                     alt="Blue Zone Logo" 
                     class="company-logo-img" 
                     onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
            </div>

            <!-- Right: Company Corporate & Tax Details -->
            <div class="company-meta-box">
                <div class="meta-line">
                    <span class="meta-label">العنوان:</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value">{{ $tpl['address_ar'] }}</span>
                </div>
                <div class="meta-line">
                    <span class="meta-label">هاتف:</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value font-mono">{{ $tpl['phone'] }}</span>
                </div>
                <div class="meta-line">
                    <span class="meta-label">البريد الإلكتروني:</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value font-mono">{{ $tpl['email'] }}</span>
                </div>
                <div class="meta-line">
                    <span class="meta-label">الرقم الضريبي (.VAT No):</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value font-mono font-bold">{{ $siteInfo['tax_number'] ?? ($tpl['vat_number'] ?? '31004829100003') }}</span>
                </div>
                <div class="meta-line">
                    <span class="meta-label">السجل التجاري:</span>
                    <span class="meta-dots"></span>
                    <span class="meta-value font-mono font-bold">{{ $siteInfo['commercial_record'] ?? ($tpl['cr_number'] ?? 'CR-1010842910') }}</span>
                </div>
                @if(!empty($siteInfo['clinical_license']))
                    <div class="meta-line">
                        <span class="meta-label">الترخيص الطبي:</span>
                        <span class="meta-dots"></span>
                        <span class="meta-value font-mono font-bold">{{ $siteInfo['clinical_license'] }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Centered Big Invoice Title -->
        <div class="invoice-main-title">
            {{ $tpl['title_ar'] ?? 'فاتورة مبيعات' }}
            <span style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; letter-spacing: 0.05em; margin-top: 0.15rem;">
                فاتورة ضريبية رسمية &bull; TAX INVOICE
            </span>
        </div>

        <!-- 3. Line Items Table (11 Columns) -->
        <div class="sheet-table-wrap">
            <table class="sheet-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">
                            <span class="th-ar">م</span>
                            <span class="th-en">N</span>
                        </th>
                        <th style="width: 10%;">
                            <span class="th-ar">كود الصنف</span>
                            <span class="th-en">Item Code</span>
                        </th>
                        <th style="width: 25%;">
                            <span class="th-ar">اسم الصنف</span>
                            <span class="th-en">Item Descreption</span>
                        </th>
                        <th style="width: 7%;">
                            <span class="th-ar">الكمية</span>
                            <span class="th-en">Quantity</span>
                        </th>
                        <th style="width: 13%;">
                            <span class="th-ar">تاريخ الصلاحية / التشغيلة</span>
                            <span class="th-en">Expiry Date / Batch No.</span>
                        </th>
                        <th style="width: 8%;">
                            <span class="th-ar">سعر الوحدة</span>
                            <span class="th-en">Price</span>
                        </th>
                        <th style="width: 6%;">
                            <span class="th-ar">نسبة الخصم 1</span>
                            <span class="th-en">Disc. 1</span>
                        </th>
                        <th style="width: 6%;">
                            <span class="th-ar">نسبة الخصم 2</span>
                            <span class="th-en">Disc. 2</span>
                        </th>
                        <th style="width: 9%;">
                            <span class="th-ar">إجمالي بدون الضريبة</span>
                            <span class="th-en">Totat Exc. VAT</span>
                        </th>
                        <th style="width: 6%;">
                            <span class="th-ar">الضريبة</span>
                            <span class="th-en">VAT</span>
                        </th>
                        <th style="width: 10%;">
                            <span class="th-ar">إجمالي الصنف شامل الضريبة</span>
                            <span class="th-en">Total Inc. VAT</span>
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
                            <td class="text-center font-mono font-bold">{{ $sku }}</td>
                            <td class="text-start font-bold">{{ $name }}</td>
                            <td class="text-center font-mono font-bold">{{ $qty }}</td>
                            <td class="text-center font-mono" style="font-size: 0.72rem;">{{ $batchExpiryText }}</td>
                            <td class="text-center font-mono">{{ number_format($unitPrice, 2) }}</td>
                            <td class="text-center font-mono">{{ $disc1 > 0 ? $disc1 . '%' : '0%' }}</td>
                            <td class="text-center font-mono">{{ $disc2 > 0 ? $disc2 . '%' : '0%' }}</td>
                            <td class="text-center font-mono">{{ number_format($lineExcVat, 2) }}</td>
                            <td class="text-center font-mono">{{ number_format($lineVat, 2) }}</td>
                            <td class="text-center font-mono font-bold">{{ number_format($lineIncVat, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center" style="padding: 1.5rem; color: #64748b;">لا توجد أصناف مدرجة في الفاتورة.</td>
                        </tr>
                    @endforelse

                    <!-- Empty filler rows for authentic spreadsheet look (minimum 6 rows) -->
                    @for($i = $rowCount; $i < 6; $i++)
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

        <!-- 4. Bottom Section: Left (Payment) & Right (Totals) -->
        <div class="inv-bottom-section">
            <!-- Left: Payment Settlement & Details Table -->
            <div>
                <table class="payment-box-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">البيان</th>
                            <th style="width: 55%;">القيمة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-bold">طريقة الدفع:</td>
                            <td>
                                <div class="payment-methods-row">
                                    <span class="chk-item">
                                        <span class="chk-box">{{ $isPartial ? '✓' : '' }}</span>
                                        <span>دفع جزئي Partial</span>
                                    </span>
                                    <span class="chk-item">
                                        <span class="chk-box">{{ $isCheque ? '✓' : '' }}</span>
                                        <span>شيك Cheque</span>
                                    </span>
                                    <span class="chk-item">
                                        <span class="chk-box">{{ $isBank ? '✓' : '' }}</span>
                                        <span>تحويل بنكي Bank</span>
                                    </span>
                                    <span class="chk-item">
                                        <span class="chk-box">{{ $isCash ? '✓' : '' }}</span>
                                        <span>نقداً Cash</span>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold">المبلغ المدفوع:</td>
                            <td class="font-mono font-bold">{{ number_format($paidAmount, 2) }} SAR</td>
                        </tr>
                        <tr>
                            <td class="font-bold">المبلغ المتبقي:</td>
                            <td class="font-mono font-bold" style="color: {{ $finalDue > 0 ? '#b91c1c' : '#047857' }};">
                                {{ number_format($finalDue, 2) }} SAR
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold">تاريخ الاستحقاق:</td>
                            <td class="font-mono">{{ $finalDue > 0 ? $dueDate : 'مسدد بالكامل' }}</td>
                        </tr>
                        <tr>
                            <td class="font-bold">مرجع التحويل البنكي:</td>
                            <td class="font-mono">{{ $bankRef ?: '—' }}</td>
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

            <!-- Right: Financial Summary Totals Table -->
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
                            <td class="text-center font-mono font-bold">{{ number_format($finalSubtotal, 2) }} SAR</td>
                        </tr>
                        <tr>
                            <td>{{ $tpl['totals_box']['label_extra_disc_pct_ar'] }}</td>
                            <td class="text-center font-mono">0%</td>
                        </tr>
                        <tr>
                            <td>{{ $tpl['totals_box']['label_extra_disc_val_ar'] }}</td>
                            <td class="text-center font-mono">{{ number_format($discount, 2) }} SAR</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{ $tpl['totals_box']['label_net_before_vat_ar'] }}</td>
                            <td class="text-center font-mono font-bold">{{ number_format(max(0, $finalSubtotal - $discount), 2) }} SAR</td>
                        </tr>
                        <tr>
                            <td class="font-bold">{{ $tpl['totals_box']['label_vat_ar'] }}</td>
                            <td class="text-center font-mono font-bold">{{ number_format($finalTax, 2) }} SAR</td>
                        </tr>
                        <tr style="background-color: var(--inv-th-bg);">
                            <td class="font-bold" style="font-size: 0.88rem;">{{ $tpl['totals_box']['label_grand_total_ar'] }}</td>
                            <td class="text-center font-mono font-bold" style="font-size: 0.95rem;">
                                {{ number_format($finalTotal, 2) }} SAR
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Footer Notes & Regulatory Compliance -->
        @if(!empty($tpl['footer']['enabled']))
            <div class="inv-footer-notes">
                <p>{{ $tpl['footer']['terms_ar'] }}</p>
                <p style="font-size: 0.68rem; margin-top: 0.2rem; font-family: 'Outfit', sans-serif; direction: ltr;">
                    {{ $tpl['footer']['terms_en'] }} &bull; ZATCA Tax Compliance &bull; SFDA Serialized
                </p>
            </div>
        @endif
    </div>

</body>
</html>
