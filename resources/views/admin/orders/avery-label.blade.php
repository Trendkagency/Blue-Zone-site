<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Avery Shipping Label - {{ $orderNumber }} | BLUE ZONE™</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,600,700,800,900|inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Cairo', sans-serif;
            background-color: #E2E8F0;
            color: #0F172A;
            padding: 2rem 1rem;
        }

        .toolbar {
            max-width: 650px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
            border: 1px solid transparent;
        }

        .btn-primary {
            background-color: #031827;
            color: #ffffff;
        }

        .btn-secondary {
            background-color: #F1F5F9;
            color: #334155;
            border-color: #CBD5E1;
        }

        /* Avery 4x6 / 5163 Shipping Label */
        .avery-label-sheet {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #0F172A;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .label-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0F172A;
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: #031827;
        }

        .brand-sub {
            font-size: 0.75rem;
            color: #64748B;
        }

        .postage-badge {
            border: 2px solid #0F172A;
            padding: 0.4rem 0.8rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            text-align: center;
            border-radius: 6px;
            background: #F8FAFC;
        }

        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .from-box {
            font-size: 0.75rem;
            color: #475569;
            line-height: 1.4;
            border-right: 1px dashed #CBD5E1;
            padding-right: 1rem;
        }

        .to-box {
            font-size: 0.875rem;
            color: #0F172A;
            line-height: 1.5;
        }

        .to-name {
            font-size: 1.15rem;
            font-weight: 900;
            color: #031827;
            margin-bottom: 0.25rem;
        }

        .qr-barcode-grid {
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 1.25rem;
            align-items: center;
            background: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .qr-box {
            width: 90px;
            height: 90px;
            background: #ffffff;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            padding: 4px;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .barcode-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .barcode-svg {
            width: 100%;
            max-height: 50px;
        }

        .barcode-num {
            font-family: monospace;
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: 0.15em;
            margin-top: 0.25rem;
        }

        .handling-notes {
            border-top: 1px solid #E2E8F0;
            padding-top: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: #64748B;
        }

        .caution-tag {
            background: #FEF3C7;
            color: #92400E;
            font-weight: 800;
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            border: 1px solid #FDE68A;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .toolbar {
                display: none !important;
            }
            .avery-label-sheet {
                border: 2px solid #000000 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: 4in 6in;
                margin: 0.2in;
            }
        }
    </style>
</head>
<body>

    @php
        $custName = is_array($order) ? ($order['customer_name'] ?? 'VIP Client') : ($order->customer_name ?? 'VIP Client');
        $custPhone = is_array($order) ? ($order['customer_phone'] ?? '+966 50 000 0000') : ($order->customer_phone ?? '+966 50 000 0000');
        $shippingAddr = is_array($order) ? ($order['shipping_address'] ?? []) : ($order->shipping_address ?? []);
        $itemsCount = is_array($order) ? count($order['items'] ?? []) : ($order->items ? $order->items->count() : 1);
        $total = is_array($order) ? ($order['total'] ?? 0) : ($order->total ?? 0);
        $verificationUrl = route('orders.track', $orderNumber);
    @endphp

    <!-- Screen Toolbar -->
    <div class="toolbar">
        <div>
            <a href="{{ route('admin.invoices.print', $orderNumber) }}" class="btn btn-secondary">
                <i class="fa-solid fa-file-invoice mr-1.5 ml-1.5"></i> View Tax Invoice
            </a>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                Close
            </button>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> Print Avery / Thermal Label
            </button>
        </div>
    </div>

    <!-- Avery / Thermal Shipping Label Card -->
    <div class="avery-label-sheet">
        
        <!-- Header -->
        <div class="label-header">
            <div>
                <div class="brand-title">BLUE ZONE™ Bioceuticals</div>
                <div class="brand-sub">Priority Cold-Chain Express Shipping</div>
            </div>
            <div class="postage-badge">
                PREPAID PRIORITY<br>
                EXP-SA-2026
            </div>
        </div>

        <!-- Addresses Grid -->
        <div class="address-grid">
            <!-- Return Address -->
            <div class="from-box">
                <strong style="color: #031827; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 0.2rem;">SHIP FROM:</strong>
                BLUE ZONE HQ Central Depot<br>
                Al-Olaya Towers, King Fahd Rd<br>
                Riyadh 12213, Saudi Arabia<br>
                Tel: +966 11 482 9100
            </div>

            <!-- Ship To Address -->
            <div class="to-box">
                <strong style="color: #0284C7; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; display: block; margin-bottom: 0.2rem;">DELIVER TO:</strong>
                <div class="to-name">{{ $custName }}</div>
                <div>{{ is_array($shippingAddr) ? ($shippingAddr['street'] ?? 'King Fahd District, Luxury Suites') : 'King Fahd District' }}</div>
                <div>{{ is_array($shippingAddr) ? ($shippingAddr['city'] ?? 'Riyadh') : 'Riyadh' }}, {{ is_array($shippingAddr) ? ($shippingAddr['country'] ?? 'Saudi Arabia') : 'Saudi Arabia' }}</div>
                <div style="font-weight: 700; margin-top: 0.25rem;">Tel: {{ $custPhone }}</div>
            </div>
        </div>

        <!-- Scannable QR & Barcode Section -->
        <div class="qr-barcode-grid">
            <div class="qr-box">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($verificationUrl) }}" alt="Scan for Order Details">
            </div>

            <div class="barcode-box">
                <!-- Barcode SVG Simulation -->
                <svg class="barcode-svg" viewBox="0 0 200 40">
                    <rect x="5" width="3" height="40" fill="#031827"/>
                    <rect x="10" width="1" height="40" fill="#031827"/>
                    <rect x="13" width="4" height="40" fill="#031827"/>
                    <rect x="19" width="2" height="40" fill="#031827"/>
                    <rect x="23" width="5" height="40" fill="#031827"/>
                    <rect x="30" width="2" height="40" fill="#031827"/>
                    <rect x="34" width="3" height="40" fill="#031827"/>
                    <rect x="39" width="1" height="40" fill="#031827"/>
                    <rect x="42" width="6" height="40" fill="#031827"/>
                    <rect x="50" width="2" height="40" fill="#031827"/>
                    <rect x="54" width="4" height="40" fill="#031827"/>
                    <rect x="60" width="2" height="40" fill="#031827"/>
                    <rect x="64" width="5" height="40" fill="#031827"/>
                    <rect x="71" width="1" height="40" fill="#031827"/>
                    <rect x="74" width="4" height="40" fill="#031827"/>
                    <rect x="80" width="3" height="40" fill="#031827"/>
                    <rect x="85" width="2" height="40" fill="#031827"/>
                    <rect x="89" width="5" height="40" fill="#031827"/>
                    <rect x="96" width="2" height="40" fill="#031827"/>
                    <rect x="100" width="4" height="40" fill="#031827"/>
                    <rect x="106" width="1" height="40" fill="#031827"/>
                    <rect x="109" width="6" height="40" fill="#031827"/>
                    <rect x="117" width="2" height="40" fill="#031827"/>
                    <rect x="121" width="3" height="40" fill="#031827"/>
                    <rect x="126" width="4" height="40" fill="#031827"/>
                    <rect x="132" width="2" height="40" fill="#031827"/>
                    <rect x="136" width="5" height="40" fill="#031827"/>
                    <rect x="143" width="1" height="40" fill="#031827"/>
                    <rect x="146" width="4" height="40" fill="#031827"/>
                    <rect x="152" width="3" height="40" fill="#031827"/>
                    <rect x="157" width="2" height="40" fill="#031827"/>
                    <rect x="161" width="5" height="40" fill="#031827"/>
                    <rect x="168" width="2" height="40" fill="#031827"/>
                    <rect x="172" width="4" height="40" fill="#031827"/>
                    <rect x="178" width="1" height="40" fill="#031827"/>
                    <rect x="181" width="6" height="40" fill="#031827"/>
                    <rect x="189" width="3" height="40" fill="#031827"/>
                    <rect x="194" width="2" height="40" fill="#031827"/>
                </svg>
                <div class="barcode-num">{{ $orderNumber }}</div>
                <div style="font-size: 0.7rem; color: #64748B;">Scan QR or Barcode for Full Package Manifest</div>
            </div>
        </div>

        <!-- Package Handling Instructions & Item Count -->
        <div class="handling-notes">
            <div>
                <strong>Items:</strong> {{ $itemsCount }} Units &bull; <strong>Valuation:</strong> @currency((float)$total)
            </div>
            <div class="caution-tag">
                ❄️ COLD CHAIN 15°C - 25°C
            </div>
        </div>
    </div>

</body>
</html>
