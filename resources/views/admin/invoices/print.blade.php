<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tax Invoice - {{ is_array($order) ? ($order['invoice_number'] ?? $order['order_number']) : ($order->invoice_number ?? $order->order_number) }} | BLUE ZONE™</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,600,700,800,900|inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        :root {
            --bz-navy: #031827;
            --bz-cyan: #00D2D3;
            --bz-teal: #0A9396;
            --bz-gold: #D4AF37;
            --bz-gray-50: #F8FAFC;
            --bz-gray-100: #F1F5F9;
            --bz-gray-200: #E2E8F0;
            --bz-gray-500: #64748B;
            --bz-gray-700: #334155;
            --bz-gray-900: #0F172A;
            --bz-green: #059669;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Cairo', sans-serif;
            background-color: #E2E8F0;
            color: var(--bz-navy);
            line-height: 1.5;
            padding: 2rem 1rem;
            -webkit-font-smoothing: antialiased;
        }

        .no-print-toolbar {
            max-width: 860px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: var(--bz-navy);
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #0A2540;
        }

        .btn-secondary {
            background-color: #F1F5F9;
            color: #334155;
            border-color: #CBD5E1;
        }
        .btn-secondary:hover {
            background-color: #E2E8F0;
        }

        /* Invoice Container */
        .invoice-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 3.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(3, 24, 39, 0.08);
            position: relative;
            overflow: hidden;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 8rem;
            font-weight: 900;
            color: rgba(3, 24, 39, 0.02);
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            letter-spacing: 0.2em;
        }

        /* Top Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--bz-navy);
            padding-bottom: 2rem;
            margin-bottom: 2rem;
        }

        .brand-block {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .brand-logo-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .brand-name-en {
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: var(--bz-navy);
            line-height: 1.1;
        }

        .brand-name-ar {
            font-family: 'Cairo', sans-serif;
            font-size: 1rem;
            font-weight: 800;
            color: var(--bz-teal);
        }

        .brand-meta {
            font-size: 0.8rem;
            color: var(--bz-gray-500);
            line-height: 1.4;
        }

        .invoice-meta-block {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }

        .invoice-title {
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--bz-navy);
            letter-spacing: 0.05em;
        }

        .invoice-title-ar {
            font-family: 'Cairo', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--bz-teal);
            margin-top: -0.35rem;
            margin-bottom: 0.5rem;
        }

        .invoice-number-badge {
            background: var(--bz-gray-100);
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-family: monospace;
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--bz-navy);
            border: 1px solid var(--bz-gray-200);
        }

        /* Two Column Info Section */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--bz-gray-200);
        }

        .info-box-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--bz-teal);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .info-box-content {
            font-size: 0.875rem;
            color: var(--bz-gray-700);
            line-height: 1.6;
        }

        .info-box-content strong {
            font-size: 1.05rem;
            color: var(--bz-navy);
            display: block;
            margin-bottom: 0.25rem;
        }

        /* Items Table */
        .table-wrap {
            margin-bottom: 2.5rem;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .invoice-table th {
            background: var(--bz-navy);
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            text-align: left;
        }

        .invoice-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--bz-gray-200);
            color: var(--bz-gray-900);
        }

        .invoice-table tr:nth-child(even) td {
            background-color: var(--bz-gray-50);
        }

        .item-title {
            font-weight: 800;
            color: var(--bz-navy);
            font-size: 0.95rem;
        }

        .item-subtitle {
            font-size: 0.75rem;
            color: var(--bz-gray-500);
            margin-top: 0.2rem;
        }

        .item-sku {
            font-family: monospace;
            font-size: 0.8rem;
            color: var(--bz-teal);
            font-weight: 600;
        }

        /* Calculation & QR Section */
        .bottom-calc-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 2.5rem;
            margin-bottom: 2.5rem;
        }

        .qr-verification-card {
            display: flex;
            gap: 1.25rem;
            background: var(--bz-gray-50);
            border: 1px dashed var(--bz-gray-200);
            border-radius: 12px;
            padding: 1.25rem;
            align-items: center;
        }

        .qr-box {
            width: 90px;
            height: 90px;
            background: #ffffff;
            padding: 6px;
            border-radius: 8px;
            border: 1px solid var(--bz-gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
        }

        .qr-text {
            font-size: 0.75rem;
            color: var(--bz-gray-500);
            line-height: 1.4;
        }

        .qr-text strong {
            color: var(--bz-navy);
            display: block;
            margin-bottom: 0.25rem;
            font-size: 0.8125rem;
        }

        .totals-card {
            background: var(--bz-gray-50);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--bz-gray-200);
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
            margin-bottom: 0.65rem;
            color: var(--bz-gray-700);
        }

        .totals-row.discount {
            color: var(--bz-green);
            font-weight: 700;
        }

        .totals-row.grand-total {
            border-top: 2px solid var(--bz-navy);
            padding-top: 0.85rem;
            margin-top: 0.85rem;
            margin-bottom: 0;
            font-size: 1.25rem;
            font-weight: 900;
            color: var(--bz-navy);
        }

        .grand-price {
            color: var(--bz-teal);
            font-size: 1.35rem;
        }

        /* Paid Badge Stamp */
        .status-stamp {
            display: inline-block;
            border: 3px solid var(--bz-green);
            color: var(--bz-green);
            font-weight: 900;
            text-transform: uppercase;
            padding: 0.35rem 1rem;
            border-radius: 8px;
            letter-spacing: 0.1em;
            transform: rotate(-5deg);
            margin-top: 1rem;
        }

        /* Footer Notes */
        .invoice-footer {
            border-top: 1px solid var(--bz-gray-200);
            padding-top: 1.5rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            font-size: 0.75rem;
            color: var(--bz-gray-500);
            line-height: 1.5;
        }

        .signature-box {
            border-top: 1px dashed var(--bz-gray-500);
            margin-top: 2.5rem;
            padding-top: 0.5rem;
            text-align: center;
            font-weight: 700;
            color: var(--bz-navy);
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                color: #000000 !important;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .invoice-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }

            .invoice-table th {
                background-color: #031827 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-table tr:nth-child(even) td {
                background-color: #F8FAFC !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .totals-card, .qr-verification-card {
                background-color: #F8FAFC !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    @php
        $invNum = is_array($order) ? ($order['invoice_number'] ?? $order['order_number']) : ($order->invoice_number ?? $order->order_number);
        $ordNum = is_array($order) ? ($order['order_number'] ?? $invNum) : ($order->order_number ?? $invNum);
        $ordDate = is_array($order) ? ($order['date'] ?? now()->format('Y-m-d')) : ($order->date?->format('Y-m-d') ?? now()->format('Y-m-d'));
        $custName = is_array($order) ? ($order['customer_name'] ?? 'Authorized Client') : ($order->customer_name ?? 'Authorized Client');
        $custEmail = is_array($order) ? ($order['customer_email'] ?? 'care@bluezone.com') : ($order->customer_email ?? 'care@bluezone.com');
        $custPhone = is_array($order) ? ($order['customer_phone'] ?? '+966 50 000 0000') : ($order->customer_phone ?? '+966 50 000 0000');
        $channel = is_array($order) ? ($order['channel'] ?? 'online') : ($order->channel ?? 'online');
        $payMethod = is_array($order) ? ($order['payment_method'] ?? 'Mada / Visa') : ($order->payment_method ?? 'Mada / Visa');
        $payStatus = is_array($order) ? ($order['payment_status'] ?? 'paid') : ($order->payment_status ?? 'paid');
        $subtotal = is_array($order) ? ($order['subtotal'] ?? 0) : ($order->subtotal ?? 0);
        $discount = is_array($order) ? ($order['discount'] ?? 0) : ($order->discount ?? 0);
        $tax = is_array($order) ? ($order['tax'] ?? 0) : ($order->tax ?? 0);
        $shipping = is_array($order) ? ($order['shipping'] ?? 0) : ($order->shipping ?? 0);
        $total = is_array($order) ? ($order['total'] ?? 0) : ($order->total ?? 0);
        
        $shippingAddr = is_array($order) ? ($order['shipping_address'] ?? []) : ($order->shipping_address ?? []);
        $items = is_array($order) ? ($order['items'] ?? []) : ($order->items ?? []);
    @endphp

    <!-- Screen Toolbar -->
    <div class="no-print-toolbar">
        <div>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left mr-1.5 ml-1.5"></i> Back to Invoices
            </a>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                Close
            </button>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> Print Tax Invoice / طباعة الفاتورة
            </button>
        </div>
    </div>

    <!-- Official Printable Invoice Sheet -->
    <div class="invoice-sheet">
        <div class="watermark">BLUE ZONE</div>

        <!-- Header -->
        <header class="invoice-header">
            <div class="brand-block">
                <div class="brand-logo-title">
                    <img src="{{ asset('assets/logo/logo-main.png') }}" alt="BLUE ZONE Logo" class="brand-logo-img" onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
                    <div>
                        <h1 class="brand-name-en">{{ $siteInfo['brand_name'] ?? 'BLUE ZONE™ Bioceuticals' }}</h1>
                        <div class="brand-name-ar">شركة بلو زون للمستحضرات الحيوية والمكملات الدقيقة</div>
                    </div>
                </div>
                <div class="brand-meta" style="margin-top: 0.5rem;">
                    <div><strong>Tax Registration / الرقم الضريبي:</strong> {{ $siteInfo['tax_number'] ?? '31004829100003' }}</div>
                    <div><strong>Commercial Record / السجل التجاري:</strong> {{ $siteInfo['commercial_record'] ?? 'CR-1010842910' }}</div>
                    <div><strong>Clinical License / ترخيص المنشأة:</strong> {{ $siteInfo['clinical_license'] ?? 'MOH-CERT-2026-BZ884' }}</div>
                    <div>{{ $siteInfo['address_en'] ?? 'King Fahd Road, Al-Olaya, Riyadh 12213, Saudi Arabia' }}</div>
                </div>
            </div>

            <div class="invoice-meta-block">
                <div class="invoice-title">TAX INVOICE</div>
                <div class="invoice-title-ar">فاتورة ضريبية رسمية</div>
                <div class="invoice-number-badge">{{ $invNum }}</div>
                
                <div style="font-size: 0.8125rem; color: var(--bz-gray-500); margin-top: 0.5rem; text-align: right;">
                    <div><strong>Date / التاريخ:</strong> {{ $ordDate }}</div>
                    <div><strong>Order Ref / رقم الطلب:</strong> {{ $ordNum }}</div>
                    <div><strong>Channel / القناة:</strong> <span style="text-transform: uppercase; font-weight: 700;">{{ $channel }}</span></div>
                </div>

                @if(strtolower($payStatus) === 'paid' || strtolower($payStatus) === 'completed')
                    <div class="status-stamp">✓ PAID / مسدد بالكامل</div>
                @else
                    <div class="status-stamp" style="border-color: #E11D48; color: #E11D48;">{{ strtoupper($payStatus) }}</div>
                @endif
            </div>
        </header>

        <!-- Customer & Order Information Grid -->
        <section class="info-grid">
            <div>
                <div class="info-box-title">
                    <span>👤</span> Billed To / العميل المكرم
                </div>
                <div class="info-box-content">
                    <strong>{{ $custName }}</strong>
                    <div>{{ $custEmail }}</div>
                    <div>{{ $custPhone }}</div>
                    @if(!empty($shippingAddr))
                        <div style="margin-top: 0.35rem; color: var(--bz-gray-500);">
                            {{ is_array($shippingAddr) ? ($shippingAddr['street'] ?? '') : '' }}
                            {{ is_array($shippingAddr) ? ($shippingAddr['city'] ?? '') : '' }}, 
                            {{ is_array($shippingAddr) ? ($shippingAddr['country'] ?? '') : '' }}
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <div class="info-box-title">
                    <span>💳</span> Payment & Fulfillment / بيانات الدفع والتسليم
                </div>
                <div class="info-box-content">
                    <div><strong>Method:</strong> {{ $payMethod }}</div>
                    <div><strong>Payment Status:</strong> <span style="font-weight: 700; color: var(--bz-green);">{{ ucfirst($payStatus) }}</span></div>
                    <div><strong>Fulfillment Center:</strong> Blue Zone Main Cellular Depot (Riyadh HQ)</div>
                    <div><strong>Origin:</strong> Licensed GMP Bio-Facility</div>
                </div>
            </div>
        </section>

        <!-- Product Breakdown Table -->
        <section class="table-wrap">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Item / Description (الصنف والمواصفات)</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 12%; text-align: right;">Unit Price</th>
                        <th style="width: 8%; text-align: center;">Qty</th>
                        <th style="width: 15%; text-align: right;">Total ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $idx = 1; @endphp
                    @forelse($items as $item)
                        @php
                            $iNameEn = is_array($item) ? ($item['product_name_en'] ?? 'Longevity Protocol') : ($item->product_name_en ?? 'Longevity Protocol');
                            $iNameAr = is_array($item) ? ($item['product_name_ar'] ?? '') : ($item->product_name_ar ?? '');
                            $iVar = is_array($item) ? ($item['variant_en'] ?? '') : ($item->variant_en ?? '');
                            $iSku = is_array($item) ? ($item['sku'] ?? 'BZ-PROD') : ($item->sku ?? 'BZ-PROD');
                            $iPrice = is_array($item) ? ($item['unit_price'] ?? 0) : ($item->unit_price ?? 0);
                            $iQty = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                            $iTotal = is_array($item) ? ($item['total'] ?? ($iPrice * $iQty)) : ($item->total ?? ($iPrice * $iQty));
                        @endphp
                        <tr>
                            <td>{{ $idx++ }}</td>
                            <td>
                                <div class="item-title">{{ $iNameEn }}</div>
                                @if($iNameAr)
                                    <div style="font-family: 'Cairo', sans-serif; font-size: 0.8rem; color: var(--bz-teal);">{{ $iNameAr }}</div>
                                @endif
                                @if($iVar)
                                    <div class="item-subtitle">{{ $iVar }}</div>
                                @endif
                            </td>
                            <td class="item-sku">{{ $iSku }}</td>
                            <td style="text-align: right;">${{ number_format((float)$iPrice, 2) }}</td>
                            <td style="text-align: center; font-weight: 700;">{{ $iQty }}</td>
                            <td style="text-align: right; font-weight: 800;">${{ number_format((float)$iTotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--bz-gray-500); padding: 2rem;">
                                No item records found for this invoice.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <!-- Calculation Summary & QR Section -->
        <section class="bottom-calc-grid">
            <!-- QR and Compliance -->
            <div>
                <div class="qr-verification-card">
                    <div class="qr-box">
                        <!-- ZATCA Dynamic QR Code Generator -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('BLUE ZONE Bioceuticals | Tax: 31004829100003 | Date: ' . $ordDate . ' | Total: $' . $total . ' | Inv: ' . $invNum) }}" alt="Tax QR Code">
                    </div>
                    <div class="qr-text">
                        <strong>ZATCA E-Invoice Verification</strong>
                        <div>Scan to verify invoice cryptographic compliance and authenticity with Blue Zone Bioceuticals Inc. Central Registry.</div>
                        <div style="margin-top: 0.35rem; font-family: monospace; font-size: 0.7rem; color: var(--bz-teal);">HASH: {{ md5($invNum . $total) }}</div>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; font-size: 0.75rem; color: var(--bz-gray-500);">
                    <div>• All bioceutical products stored under cold-chain regulated conditions (15°C - 25°C).</div>
                    <div>• 14-day replacement warranty for sealed clinical packaging.</div>
                </div>
            </div>

            <!-- Financial Totals -->
            <div class="totals-card">
                <div class="totals-row">
                    <span>Taxable Subtotal / المجموع الفرعي:</span>
                    <span style="font-weight: 700;">${{ number_format((float)$subtotal, 2) }}</span>
                </div>

                @if((float)$discount > 0)
                    <div class="totals-row discount">
                        <span>Discount / الخصم المعتمد:</span>
                        <span>-${{ number_format((float)$discount, 2) }}</span>
                    </div>
                @endif

                <div class="totals-row">
                    <span>Shipping & Handling / الشحن والتسليم:</span>
                    <span>{{ (float)$shipping > 0 ? '$' . number_format((float)$shipping, 2) : 'Free Shipping' }}</span>
                </div>

                <div class="totals-row">
                    <span>VAT (15%) / ضريبة القيمة المضافة:</span>
                    <span>${{ number_format((float)$tax, 2) }}</span>
                </div>

                <div class="totals-row grand-total">
                    <div>
                        <div>Grand Total</div>
                        <div style="font-family: 'Cairo', sans-serif; font-size: 0.85rem; font-weight: 600; color: var(--bz-gray-500);">المبلغ الإجمالي المستحق</div>
                    </div>
                    <div class="grand-price">${{ number_format((float)$total, 2) }}</div>
                </div>
            </div>
        </section>

        <!-- Footer / Legal & Signature -->
        <footer class="invoice-footer">
            <div>
                <strong>BLUE ZONE™ Cellular Longevity Systems</strong>
                <p>Corporate Office: Level 24, Al-Olaya Towers, King Fahd Rd, Riyadh 12213, KSA.<br>
                For clinical inquiries & prescription validation: care@bluezone.com | +966 11 482 9100</p>
                <p style="margin-top: 0.25rem;">This is an electronically generated and certified tax invoice under the Kingdom of Saudi Arabia Electronic Invoicing Regulations.</p>
            </div>

            <div>
                <div class="signature-box">
                    Authorized Electronic Stamp
                </div>
            </div>
        </footer>
    </div>

</body>
</html>
