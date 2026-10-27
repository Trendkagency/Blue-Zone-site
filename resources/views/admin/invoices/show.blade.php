@php
    $tpl = $invoiceTemplate ?? \App\Services\InvoiceTemplateService::getTemplate();
    
    $isModel = $order instanceof \Illuminate\Database\Eloquent\Model;
    $ordNum = $isModel ? $order->order_number : ($order['order_number'] ?? 'ORD-000');
    $invNum = $isModel ? ($order->invoice_number ?? $ordNum) : ($order['invoice_number'] ?? $ordNum);
    $orderKey = $isModel ? ($order->id ?? $ordNum) : ($order['order_number'] ?? 1);

    $rawDate = $isModel ? ($order->invoice_date ?? $order->date) : ($order['invoice_date'] ?? ($order['date'] ?? now()));
    $invDate = $rawDate instanceof \Carbon\Carbon ? $rawDate->format('Y-m-d') : (is_string($rawDate) ? substr($rawDate, 0, 10) : date('Y-m-d'));

    $dueDate = $isModel && !empty($order->due_date) 
        ? ($order->due_date instanceof \Carbon\Carbon ? $order->due_date->format('Y-m-d') : substr($order->due_date, 0, 10))
        : \Carbon\Carbon::parse($invDate)->addDays(30)->format('Y-m-d');
    
    $custName = $isModel 
        ? ($order->customer_name ?? ($order->customer?->name ?? 'Dr. Khalid Al-Mansoor Medical Complex')) 
        : ($order['customer_name'] ?? 'Dr. Khalid Al-Mansoor Medical Complex');
        
    $custPhone = $isModel 
        ? ($order->customer_phone ?? ($order->customer?->phone ?? '+966 50 123 4567')) 
        : ($order['customer_phone'] ?? '+966 50 123 4567');
        
    $custEmail = $isModel 
        ? ($order->customer_email ?? ($order->customer?->email ?? 'procurement@almansoor-clinic.com')) 
        : ($order['customer_email'] ?? 'procurement@almansoor-clinic.com');
        
    $rawAddr = $isModel ? $order->shipping_address : ($order['shipping_address'] ?? null);
    if (is_array($rawAddr)) {
        $custAddress = implode(', ', array_filter([$rawAddr['street'] ?? '', $rawAddr['city'] ?? '', $rawAddr['country'] ?? '']));
    } elseif (is_string($rawAddr) && !empty($rawAddr)) {
        $custAddress = $rawAddr;
    } else {
        $custAddress = 'King Fahd Road, Al Olaya District, Riyadh, KSA';
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
    $payMethod = strtolower($isModel ? ($order->payment_method ?? 'bank_transfer') : ($order['payment_method'] ?? 'bank_transfer'));
    $payStatus = strtolower($isModel ? ($order->payment_status ?? 'partially_paid') : ($order['payment_status'] ?? 'partially_paid'));
    
    $paidAmount = $isModel 
        ? (float) ($order->paid_amount ?? ($payStatus === 'paid' ? $total : 3000.00)) 
        : (float) ($order['paid_amount'] ?? ($payStatus === 'paid' ? $total : 3000.00));
        
    $dueBalance = $isModel 
        ? (float) ($order->due_balance ?? max(0, $total - $paidAmount)) 
        : (float) ($order['due_balance'] ?? max(0, $total - $paidAmount));

    $bankRef = $isModel 
        ? ($order->payment_transaction_id ?? ($order->payment_payload['reference'] ?? 'WIRE-SNB-9940128')) 
        : ($order['payment_transaction_id'] ?? ($order['bank_reference'] ?? 'WIRE-SNB-9940128'));

    $verificationUrl = route('orders.verify', ['order' => $ordNum]);

    $sectionsOrder = $tpl['sections_order'] ?? [
        'header_company_client',
        'title_banner',
        'items_table',
        'totals_and_payments',
        'signatures_stamp',
        'footer_notes',
    ];
@endphp

<x-layouts.admin 
    :pageTitle="(app()->getLocale() === 'ar' ? 'الفاتورة الضريبية الرسمية: ' : 'Official Tax Invoice: ') . $invNum" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'معاينة النموذج المعتمد للفاتورة مع إمكانية التحرير اللحظي وإعادة الترتيب التفاعلي ثلاثي الأبعاد.' : 'Official certified tax invoice layout with real-time 3D interactive studio and drag-and-drop customization.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'الفواتير' : 'Invoices') => route('admin.invoices.index'), 
        $invNum => route('admin.invoices.show', $orderKey)
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            <!-- 3D Studio Mode Toggle -->
            <button type="button" id="btn-toggle-studio" class="btn btn-secondary font-bold" onclick="toggleStudioMode()">
                <i class="fa-solid fa-cube text-purple-500 mr-1.5 ml-1.5"></i>
                <span id="txt-studio-toggle">{{ app()->getLocale() === 'ar' ? 'استوديو التحرير 3D' : '3D Live Studio' }}</span>
            </button>

            <!-- 3D Tilt Effect Toggle -->
            <button type="button" id="btn-toggle-tilt" class="btn btn-secondary font-bold" onclick="toggleTiltEffect()" title="Toggle Realistic 3D Tilt Physics">
                <i class="fa-solid fa-layer-group text-sky-500 mr-1.5 ml-1.5"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تأثير 3D' : '3D Tilt' }}</span>
            </button>

            <!-- Save Real-time Changes -->
            <button type="button" id="btn-save-invoice" class="btn btn-primary font-bold shadow-md" onclick="saveRealtimeInvoice()">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Save Changes' }}</span>
            </button>

            <!-- Print Actions -->
            <a href="{{ route('admin.invoices.print', $orderKey) }}" target="_blank" class="btn btn-secondary font-bold" title="Open Clean Print in New Tab">
                <i class="fa-solid fa-arrow-up-right-from-square mr-1.5 ml-1.5 text-sky-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'صفحة الطباعة' : 'Print Window' }}</span>
            </a>
            <button type="button" class="btn btn-secondary font-bold" onclick="window.print()">
                <i class="fa-solid fa-print mr-1.5 ml-1.5"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'طباعة فورية' : 'Quick Print' }}</span>
            </button>
        </div>
    </x-slot>

    <!-- Embedded High-End Styles for Current Invoice Format & 3D Interactive Studio -->
    <style>
        :root {
            --inv-border: {{ $tpl['styling']['border_color'] ?? '#0f172a' }};
            --inv-th-bg: {{ $tpl['styling']['header_bg'] ?? '#d4dfe8' }};
            --inv-font: {{ $tpl['styling']['font_family'] ?? "'Tajawal', 'Outfit', sans-serif" }};
        }

        /* 3D Scene Wrapper */
        .invoice-3d-scene {
            perspective: 1600px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 0.5rem 0 2rem 0;
            position: relative;
        }

        /* Realistic Printable Canvas */
        .invoice-3d-canvas {
            max-width: 840px;
            width: 100%;
            background: #ffffff;
            color: #0f172a;
            direction: rtl;
            text-align: right;
            padding: 0.85rem 1.15rem;
            border: 2px solid var(--inv-border);
            border-radius: 4px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.05);
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1), box-shadow 0.3s ease;
            font-family: var(--inv-font);
        }

        .invoice-3d-canvas.is-tilting {
            box-shadow: 0 35px 70px -15px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(0, 0, 0, 0.08);
        }

        /* 3D Specular Sheen Overlay */
        .canvas-sheen {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0) 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: inherit;
        }
        .invoice-3d-canvas.is-tilting .canvas-sheen {
            opacity: 1;
        }

        /* Draggable Section Item */
        .inv-drag-block {
            position: relative;
            transition: all 0.25s ease;
            border-radius: 4px;
        }
        .studio-active .inv-drag-block {
            margin-bottom: 0.65rem;
            border: 1.5px dashed rgba(99, 102, 241, 0.4);
            padding: 0.4rem;
            background: rgba(248, 250, 252, 0.6);
        }
        .studio-active .inv-drag-block:hover {
            border-color: #6366f1;
            background: rgba(238, 242, 255, 0.7);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.12);
        }
        .inv-drag-handle {
            display: none;
            position: absolute;
            top: -12px;
            right: 12px;
            background: #6366f1;
            color: #ffffff;
            font-size: 0.68rem;
            font-weight: 800;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            cursor: grab;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.35);
            z-index: 50;
            user-select: none;
        }
        .studio-active .inv-drag-handle {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .inv-drag-block.dragging {
            opacity: 0.4;
            transform: scale(0.98);
        }
        .inv-drag-block.drag-over {
            border: 2px solid #10b981 !important;
            background: rgba(16, 185, 129, 0.08) !important;
        }

        /* Real-Time In-Place Editable Fields */
        .live-edit-field {
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
            outline: none;
        }
        .studio-active .live-edit-field {
            background: rgba(14, 165, 233, 0.08);
            border-bottom: 1.5px dashed #0284c7;
            padding: 0 0.15rem;
            border-radius: 2px;
        }
        .studio-active .live-edit-field:hover, .studio-active .live-edit-field:focus {
            background: rgba(14, 165, 233, 0.18);
            border-bottom-color: #0369a1;
            box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
        }

        /* Header Grid: Right (Company Info) | Center (Name+Logo) | Left (Customer+QR) */
        .inv-header-grid {
            display: grid;
            grid-template-columns: 37% 23% 40%;
            gap: 0.6rem;
            align-items: start;
            padding-bottom: 0.25rem;
            width: 100%;
        }
        .company-meta-box {
            font-size: 0.74rem;
            line-height: 1.45;
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
        }

        /* Center Column (Logo & Name) */
        .company-center-col {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .company-center-col h1 {
            font-size: 1.08rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin-bottom: 0.2rem;
            line-height: 1.2;
        }
        .company-logo-img {
            max-height: 46px;
            max-width: 130px;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        /* Left Column (Customer & QR) */
        .client-qr-box {
            display: flex;
            gap: 0.5rem;
            align-items: flex-start;
            justify-content: flex-end;
            direction: rtl;
        }
        .qr-wrapper {
            flex-shrink: 0;
            width: 70px;
            height: 70px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            background: #fff;
            border-radius: 4px;
        }
        .qr-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .client-info-fields {
            flex: 1;
            font-size: 0.74rem;
            line-height: 1.35;
        }
        .client-meta-line {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
            margin-bottom: 0.12rem;
            font-size: 0.74rem;
        }
        .client-meta-line .meta-label {
            font-weight: 800;
            color: #1e293b;
            flex-shrink: 0;
            min-width: 62px;
            font-size: 0.73rem;
        }
        .client-meta-line .meta-value {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.73rem;
            word-break: break-word;
        }

        /* Big Title Banner */
        .invoice-main-title {
            text-align: center;
            font-size: 1.38rem;
            font-weight: 900;
            color: #0f172a;
            margin: 0.35rem 0 0.35rem 0;
            letter-spacing: -0.01em;
            line-height: 1.15;
        }

        /* Spreadsheet-Styled 11-Columns Line Items Table */
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
            padding: 0.22rem 0.2rem;
            vertical-align: middle;
            overflow: hidden;
            word-wrap: break-word;
        }
        .sheet-table th {
            background-color: var(--inv-th-bg);
            color: #0f172a;
            font-weight: 900;
            text-align: center;
            line-height: 1.15;
            padding: 0.28rem 0.15rem;
        }
        .th-ar { display: block; font-size: 0.71rem; }
        .th-en { display: block; font-size: 0.61rem; font-weight: 700; color: #334155; direction: ltr; }
        .sheet-table td.cell-num {
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
            text-align: center;
            padding: 0.22rem 0.15rem;
        }

        /* Bottom Section: Totals & Payments */
        .inv-bottom-section {
            display: grid;
            grid-template-columns: 50% 50%;
            gap: 0.75rem;
            align-items: start;
            margin-top: 0.35rem;
            width: 100%;
        }
        .totals-box-table, .payment-box-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.74rem;
            border: 1px solid var(--inv-border);
        }
        .totals-box-table th, .totals-box-table td,
        .payment-box-table th, .payment-box-table td {
            border: 1px solid var(--inv-border);
            padding: 0.24rem 0.4rem;
        }
        .totals-box-table th, .payment-box-table th {
            background-color: var(--inv-th-bg);
            font-weight: 900;
            text-align: center;
        }
        .payment-methods-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.2rem 0.35rem;
            font-size: 0.68rem;
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
            width: 12px;
            height: 12px;
            border: 1.5px solid #0f172a;
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

        /* Footer Notes */
        .inv-footer-notes {
            margin-top: 0.45rem;
            padding-top: 0.35rem;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 0.67rem;
            color: #64748b;
            line-height: 1.35;
        }

        /* Floating 3D Interactive Control Sidebar */
        .studio-floating-panel {
            display: none;
            width: 320px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0,0,0,0.05);
            border-radius: 14px;
            padding: 1.25rem;
            position: sticky;
            top: 1rem;
            font-size: 0.82rem;
            z-index: 40;
            direction: rtl;
        }
        .studio-active .studio-floating-panel {
            display: block;
            animation: fadeInRight 0.3s ease forwards;
        }
        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Clean Print Mode */
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm 7mm;
            }
            body, html {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .sidebar, .top-header, .no-print-bar, .studio-floating-panel, .inv-drag-handle, .admin-page-header, .btn {
                display: none !important;
            }
            .invoice-3d-scene {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
                perspective: none !important;
            }
            .invoice-3d-canvas {
                border: 1.5px solid #000000 !important;
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 0.35rem 0.45rem !important;
                transform: none !important;
                page-break-inside: avoid !important;
            }
            .studio-active .inv-drag-block {
                border: none !important;
                padding: 0 !important;
                margin-bottom: 0 !important;
                background: transparent !important;
            }
        }
    </style>

    <!-- 3D Scene Viewport: Invoice Canvas + Floating Real-Time Studio -->
    <div class="invoice-3d-scene" id="invoiceScene">

        <!-- Official High-End Invoice Canvas -->
        <div class="invoice-3d-canvas" id="invoiceCanvas">
            <div class="canvas-sheen" id="canvasSheen"></div>

            <div id="invoiceBlocksContainer">
                @foreach($sectionsOrder as $secKey)
                    @if($secKey === 'header_company_client' || $secKey === 'header_company')
                        <!-- SECTION: Top Header Grid -->
                        <div class="inv-drag-block" draggable="false" data-section-id="header_company_client">
                            <span class="inv-drag-handle">
                                <i class="fa-solid fa-grip-vertical"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'رأس الفاتورة' : 'Header' }}</span>
                            </span>

                            <div class="inv-header-grid">
                                <!-- Right: Company Corporate & Tax Details -->
                                <div class="company-meta-box">
                                    <div class="meta-line meta-wrap">
                                        <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'العنوان:' : 'Address:' }}</span>
                                        <span class="meta-value live-edit-field" contenteditable="false" data-field="address_ar">{{ $tpl['address_ar'] }}</span>
                                    </div>
                                    <div class="meta-line">
                                        <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'هاتف:' : 'Phone:' }}</span>
                                        <span class="meta-dots"></span>
                                        <span class="meta-value font-mono"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="phone">{{ $tpl['phone'] }}</span></bdi></span>
                                    </div>
                                    <div class="meta-line">
                                        <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني:' : 'Email:' }}</span>
                                        <span class="meta-dots"></span>
                                        <span class="meta-value font-mono"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="email">{{ $tpl['email'] }}</span></bdi></span>
                                    </div>
                                    <div class="meta-line">
                                        <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'الرقم الضريبي (.VAT No):' : 'VAT No:' }}</span>
                                        <span class="meta-dots"></span>
                                        <span class="meta-value font-mono font-bold"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="vat_number">{{ $siteInfo['tax_number'] ?? ($tpl['vat_number'] ?? '31004829100003') }}</span></bdi></span>
                                    </div>
                                    <div class="meta-line">
                                        <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'السجل التجاري:' : 'CR No:' }}</span>
                                        <span class="meta-dots"></span>
                                        <span class="meta-value font-mono font-bold"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="cr_number">{{ $siteInfo['commercial_record'] ?? ($tpl['cr_number'] ?? 'CR-1010842910') }}</span></bdi></span>
                                    </div>
                                </div>

                                <!-- Center: Company Name & Logo -->
                                <div class="company-center-col">
                                    <h1 class="live-edit-field" contenteditable="false" data-field="company_name_en">{{ $tpl['company_name_en'] }}</h1>
                                    <img src="{{ asset('assets/logo/logo-main.png') }}" 
                                         alt="Blue Zone Logo" 
                                         class="company-logo-img" 
                                         onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
                                </div>

                                <!-- Left: Client Info & QR Code -->
                                <div class="client-qr-box">
                                    <div class="client-info-fields">
                                        <div class="client-meta-line">
                                            <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'اسم العميل:' : 'Client:' }}</span>
                                            <span class="meta-value font-bold live-edit-field" contenteditable="false" data-field="customer_name" id="disp-cust-name">{{ $custName }}</span>
                                        </div>
                                        <div class="client-meta-line">
                                            <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'رقم العميل:' : 'Phone:' }}</span>
                                            <span class="meta-value font-mono"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="customer_phone" id="disp-cust-phone">{{ $custPhone }}</span></bdi></span>
                                        </div>
                                        <div class="client-meta-line">
                                            <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'العنوان:' : 'Address:' }}</span>
                                            <span class="meta-value live-edit-field" contenteditable="false" data-field="shipping_address" id="disp-cust-addr" style="font-size: 0.72rem;">{{ $custAddress }}</span>
                                        </div>
                                        <div class="client-meta-line" style="margin-top: 0.15rem;">
                                            <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'تاريخ الفاتورة:' : 'Date:' }}</span>
                                            <span class="meta-value font-mono"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="date" id="disp-inv-date">{{ $invDate }}</span></bdi></span>
                                        </div>
                                        <div class="client-meta-line">
                                            <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'رقم الفاتورة:' : 'Inv No:' }}</span>
                                            <span class="meta-value font-mono font-bold"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="invoice_number" id="disp-inv-num">{{ $invNum }}</span></bdi></span>
                                        </div>
                                    </div>
                                    <div class="qr-wrapper" title="Scan for Official Verification">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($verificationUrl) }}" alt="QR Code">
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif($secKey === 'title_banner')
                        <!-- SECTION: Big Invoice Title -->
                        <div class="inv-drag-block" draggable="false" data-section-id="title_banner">
                            <span class="inv-drag-handle">
                                <i class="fa-solid fa-grip-vertical"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'عنوان الفاتورة' : 'Title Banner' }}</span>
                            </span>

                            <div class="invoice-main-title">
                                <span class="live-edit-field" contenteditable="false" data-field="title_ar">{{ $tpl['title_ar'] ?? 'فاتورة مبيعات' }}</span>
                                <span style="display: block; font-size: 0.74rem; font-weight: 700; color: #475569; letter-spacing: 0.04em; margin-top: 0.1rem;">
                                    فاتورة ضريبية رسمية &bull; TAX INVOICE
                                </span>
                            </div>
                        </div>

                    @elseif($secKey === 'items_table')
                        <!-- SECTION: 11-Columns Line Items Table -->
                        <div class="inv-drag-block" draggable="false" data-section-id="items_table">
                            <span class="inv-drag-handle">
                                <i class="fa-solid fa-grip-vertical"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'جدول الأصناف (11 عمود)' : 'Line Items (11 Cols)' }}</span>
                            </span>

                            <div class="sheet-table-wrap">
                                <table class="sheet-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%;">
                                                <span class="th-ar">م</span>
                                                <span class="th-en">N</span>
                                            </th>
                                            <th style="width: 8.5%;">
                                                <span class="th-ar">كود الصنف</span>
                                                <span class="th-en">Item Code</span>
                                            </th>
                                            <th style="width: 22%;">
                                                <span class="th-ar">اسم الصنف</span>
                                                <span class="th-en">Item Description</span>
                                            </th>
                                            <th style="width: 5%;">
                                                <span class="th-ar">الكمية</span>
                                                <span class="th-en">Qty</span>
                                            </th>
                                            <th style="width: 14%;">
                                                <span class="th-ar">تاريخ الصلاحية / التشغيلة</span>
                                                <span class="th-en">Expiry Date / Batch No.</span>
                                            </th>
                                            <th style="width: 7.5%;">
                                                <span class="th-ar">سعر الوحدة</span>
                                                <span class="th-en">Price</span>
                                            </th>
                                            <th style="width: 4.5%;">
                                                <span class="th-ar">نسبة الخصم 1</span>
                                                <span class="th-en">Disc. 1</span>
                                            </th>
                                            <th style="width: 4.5%;">
                                                <span class="th-ar">نسبة الخصم 2</span>
                                                <span class="th-en">Disc. 2</span>
                                            </th>
                                            <th style="width: 10%;">
                                                <span class="th-ar">إجمالي بدون الضريبة</span>
                                                <span class="th-en">Total Exc. VAT</span>
                                            </th>
                                            <th style="width: 8.5%;">
                                                <span class="th-ar">الضريبة</span>
                                                <span class="th-en">VAT</span>
                                            </th>
                                            <th style="width: 12.5%;">
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
                                                <td class="text-start font-bold" style="font-size: 0.7rem; line-height: 1.25;">{{ $name }}</td>
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
                        </div>

                    @elseif($secKey === 'totals_and_payments')
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

                        <!-- SECTION: Financial Totals & Payments Settlement -->
                        <div class="inv-drag-block" draggable="false" data-section-id="totals_and_payments">
                            <span class="inv-drag-handle">
                                <i class="fa-solid fa-grip-vertical"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'الملخص المالي والسداد' : 'Totals & Payments' }}</span>
                            </span>

                            <div class="inv-bottom-section">
                                <!-- Right: Totals Box Table -->
                                <div>
                                    <table class="totals-box-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 58%;">البيان</th>
                                                <th style="width: 42%;">القيمة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font-bold">{{ $tpl['totals_box']['label_subtotal_ar'] }}</td>
                                                <td class="text-center font-mono font-bold" id="disp-calc-subtotal" style="white-space: nowrap;">SAR {{ number_format($finalSubtotal, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ $tpl['totals_box']['label_extra_disc_pct_ar'] }}</td>
                                                <td class="text-center font-mono">0%</td>
                                            </tr>
                                            <tr>
                                                <td>{{ $tpl['totals_box']['label_extra_disc_val_ar'] }}</td>
                                                <td class="text-center font-mono" id="disp-calc-discount" style="white-space: nowrap;">SAR {{ number_format($discount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">{{ $tpl['totals_box']['label_net_before_vat_ar'] }}</td>
                                                <td class="text-center font-mono font-bold" id="disp-calc-net" style="white-space: nowrap;">SAR {{ number_format(max(0, $finalSubtotal - $discount), 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">{{ $tpl['totals_box']['label_vat_ar'] }}</td>
                                                <td class="text-center font-mono font-bold" id="disp-calc-tax" style="white-space: nowrap;">SAR {{ number_format($finalTax, 2) }}</td>
                                            </tr>
                                            <tr style="background-color: var(--inv-th-bg);">
                                                <td class="font-bold" style="font-size: 0.82rem;">{{ $tpl['totals_box']['label_grand_total_ar'] }}</td>
                                                <td class="text-center font-mono font-bold" id="disp-calc-grand-total" style="font-size: 0.88rem; white-space: nowrap;">
                                                    SAR {{ number_format($finalTotal, 2) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Left: Payment Settlement Table -->
                                <div>
                                    <table class="payment-box-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 42%;">البيان</th>
                                                <th style="width: 58%;">القيمة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font-bold">طريقة الدفع:</td>
                                                <td>
                                                    <div class="payment-methods-grid">
                                                        <span class="chk-item">
                                                            <span class="chk-box" id="chk-partial">{{ $isPartial ? '✓' : '' }}</span>
                                                            <span>دفع جزئي Partial</span>
                                                        </span>
                                                        <span class="chk-item">
                                                            <span class="chk-box" id="chk-cheque">{{ $isCheque ? '✓' : '' }}</span>
                                                            <span>شيك Cheque</span>
                                                        </span>
                                                        <span class="chk-item">
                                                            <span class="chk-box" id="chk-bank">{{ $isBank ? '✓' : '' }}</span>
                                                            <span>تحويل بنكي Bank</span>
                                                        </span>
                                                        <span class="chk-item">
                                                            <span class="chk-box" id="chk-cash">{{ $isCash ? '✓' : '' }}</span>
                                                            <span>نقداً Cash</span>
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">المبلغ المدفوع:</td>
                                                <td class="font-mono font-bold" id="disp-paid-amount" style="white-space: nowrap;">SAR {{ number_format($paidAmount, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">المبلغ المتبقي:</td>
                                                <td class="font-mono font-bold" id="disp-due-balance" style="white-space: nowrap; color: {{ $finalDue > 0 ? '#b91c1c' : '#047857' }};">
                                                    SAR {{ number_format($finalDue, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">تاريخ الاستحقاق:</td>
                                                <td class="font-mono"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="due_date" id="disp-due-date">{{ $finalDue > 0 ? $dueDate : 'مسدد بالكامل' }}</span></bdi></td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">مرجع التحويل البنكي:</td>
                                                <td class="font-mono"><bdi dir="ltr"><span class="live-edit-field" contenteditable="false" data-field="payment_transaction_id" id="disp-bank-ref">{{ $bankRef ?: '—' }}</span></bdi></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    @elseif($secKey === 'signatures_stamp')
                        <!-- SECTION: Signatures & Official Stamp -->
                        <div class="inv-drag-block" draggable="false" data-section-id="signatures_stamp">
                            <span class="inv-drag-handle">
                                <i class="fa-solid fa-grip-vertical"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'التوقيعات والختم' : 'Signatures & Seal' }}</span>
                            </span>

                            <div class="signatures-grid">
                                <div class="sig-item">
                                    <span class="font-bold">{{ $tpl['signatures']['receiver_label_ar'] ?? 'المستلم' }}:</span>
                                    <div class="sig-line"></div>
                                </div>
                                <div class="sig-item">
                                    <span class="font-bold">{{ $tpl['signatures']['signature_label_ar'] ?? 'التوقيع' }}:</span>
                                    <div class="sig-line"></div>
                                </div>
                                <div class="sig-item">
                                    <span class="font-bold">{{ $tpl['signatures']['stamp_label_ar'] ?? 'الختم' }}:</span>
                                    <div class="stamp-box">
                                        Official Seal
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif($secKey === 'footer_notes')
                        <!-- SECTION: Footer Notes & SFDA Compliance -->
                        @if(!empty($tpl['footer']['enabled']))
                            <div class="inv-drag-block" draggable="false" data-section-id="footer_notes">
                                <span class="inv-drag-handle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'الشروط والملاحظات' : 'Footer Terms' }}</span>
                                </span>

                                <div class="inv-footer-notes">
                                    <p class="live-edit-field" contenteditable="false" data-field="terms_ar">{{ $tpl['footer']['terms_ar'] }}</p>
                                    <p style="font-size: 0.65rem; margin-top: 0.15rem; font-family: 'Outfit', sans-serif; direction: ltr;">
                                        {{ $tpl['footer']['terms_en'] }} &bull; ZATCA Tax Compliance &bull; SFDA Serialized
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Floating 3D Interactive Control Sidebar (Active in Studio Mode) -->
        <aside class="studio-floating-panel" id="studioPanel">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; border-bottom: 1px solid rgba(0,0,0,0.08); padding-bottom: 0.75rem;">
                <div style="font-weight: 900; font-size: 0.95rem; color: #0f172a; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-sliders text-purple-600"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'لوحة التحرير اللحظي 3D' : 'Real-time Studio' }}</span>
                </div>
                <span class="badge" style="background: #6366f1; color: #fff; font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 999px;">Live Sync</span>
            </div>

            <!-- In-Place Edit Switch -->
            <div style="margin-bottom: 1rem; padding: 0.65rem; background: rgba(99, 102, 241, 0.06); border-radius: 8px; border: 1px solid rgba(99, 102, 241, 0.15);">
                <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-weight: 700;">
                    <span>{{ app()->getLocale() === 'ar' ? 'تفعيل التحرير المباشر فوق الفاتورة' : 'Direct In-Place Editing' }}</span>
                    <input type="checkbox" id="chk-inplace-edit" onchange="toggleInPlaceEditing(this.checked)" style="width: 16px; height: 16px; accent-color: #6366f1;">
                </label>
                <p style="font-size: 0.7rem; color: #64748b; margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'يمكنك النقر على أي نص مباشرة داخل الفاتورة وتعديله فورياً.' : 'Click any highlighted text on invoice canvas to edit directly.' }}
                </p>
            </div>

            <!-- Quick Form Controls (Instantly reflected on Invoice) -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 800; color: #334155; display: block; margin-bottom: 0.2rem;">اسم العميل / المركز:</label>
                    <input type="text" id="inp-cust-name" value="{{ $custName }}" oninput="updateFieldLive('customer_name', this.value)" class="form-control text-xs font-bold" style="width: 100%; padding: 0.4rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 800; color: #334155; display: block; margin-bottom: 0.2rem;">رقم هاتف العميل:</label>
                    <input type="text" id="inp-cust-phone" value="{{ $custPhone }}" oninput="updateFieldLive('customer_phone', this.value)" class="form-control text-xs font-mono" dir="ltr" style="width: 100%; padding: 0.4rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 800; color: #334155; display: block; margin-bottom: 0.2rem;">عنوان العميل والفرع:</label>
                    <input type="text" id="inp-cust-addr" value="{{ $custAddress }}" oninput="updateFieldLive('shipping_address', this.value)" class="form-control text-xs" style="width: 100%; padding: 0.4rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 800; color: #334155; display: block; margin-bottom: 0.2rem;">رقم الفاتورة:</label>
                        <input type="text" id="inp-inv-num" value="{{ $invNum }}" oninput="updateFieldLive('invoice_number', this.value)" class="form-control text-xs font-mono font-bold" style="width: 100%; padding: 0.4rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 800; color: #334155; display: block; margin-bottom: 0.2rem;">تاريخ الفاتورة:</label>
                        <input type="date" id="inp-inv-date" value="{{ $invDate }}" oninput="updateFieldLive('date', this.value)" class="form-control text-xs font-mono" style="width: 100%; padding: 0.35rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 800; color: #334155; display: block; margin-bottom: 0.2rem;">الخصم الإضافي (SAR):</label>
                        <input type="number" step="0.01" id="inp-discount" value="{{ $discount }}" oninput="updateFinancialsLive()" class="form-control text-xs font-mono font-bold" style="width: 100%; padding: 0.4rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </div>
                    <div>
                        <label style="font-size: 0.75rem; font-weight: 800; color: #334155; display: block; margin-bottom: 0.2rem;">المبلغ المدفوع (SAR):</label>
                        <input type="number" step="0.01" id="inp-paid" value="{{ $paidAmount }}" oninput="updateFinancialsLive()" class="form-control text-xs font-mono font-bold" style="width: 100%; padding: 0.4rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 800; color: #334155; display: block; margin-bottom: 0.2rem;">طريقة الدفع الحالية:</label>
                    <select id="inp-pay-method" onchange="updatePaymentMethodLive(this.value)" class="form-control text-xs font-bold" style="width: 100%; padding: 0.4rem; border-radius: 6px; border: 1px solid #cbd5e1;">
                        <option value="bank_transfer" {{ $payMethod === 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي Bank</option>
                        <option value="cash" {{ $payMethod === 'cash' ? 'selected' : '' }}>نقداً Cash</option>
                        <option value="cheque" {{ $payMethod === 'cheque' ? 'selected' : '' }}>شيك Cheque</option>
                        <option value="partial" {{ $isPartial ? 'selected' : '' }}>دفع جزئي Partial</option>
                    </select>
                </div>
            </div>

            <!-- Drag and Drop Ordering Helper -->
            <div style="border-top: 1px solid rgba(0,0,0,0.08); padding-top: 0.75rem; margin-bottom: 1rem;">
                <div style="font-weight: 800; font-size: 0.78rem; color: #1e293b; margin-bottom: 0.35rem;">
                    <i class="fa-solid fa-up-down-left-right text-indigo-500 mr-1 ml-1"></i>
                    {{ app()->getLocale() === 'ar' ? 'ترتيب أقسام الفاتورة (سحب وإفلات)' : 'Drag & Drop Section Order' }}
                </div>
                <p style="font-size: 0.68rem; color: #64748b;">
                    {{ app()->getLocale() === 'ar' ? 'امسك أي قسم من علامة (⠿) واسحبه لأعلى أو لأسفل، وسيتم تحديث الفاتورة فورياً.' : 'Drag any section block by its handle up or down to reorder visually.' }}
                </p>
            </div>

            <!-- Save & Reset Action Buttons -->
            <div style="display: flex; gap: 0.5rem; flex-direction: column;">
                <button type="button" class="btn btn-primary btn-sm font-bold w-full" onclick="saveRealtimeInvoice()">
                    <i class="fa-solid fa-check mr-1 ml-1"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'حفظ وتثبيت التعديلات' : 'Commit Changes' }}</span>
                </button>
                <button type="button" class="btn btn-secondary btn-sm font-bold w-full" onclick="resetSectionsOrder()">
                    <i class="fa-solid fa-rotate-left mr-1 ml-1"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'استعادة الترتيب القياسي' : 'Reset Layout' }}</span>
                </button>
            </div>
        </aside>

    </div>

    <!-- Interactive JavaScript Engine for Real-time 3D, In-Place Editing, and Drag & Drop -->
    <script>
        const updateUrl = "{{ route('admin.invoices.update', $orderKey) }}";
        const csrfToken = "{{ csrf_token() }}";
        
        let subtotalBase = {{ (float)$finalSubtotal }};
        let isStudioActive = false;
        let isTiltActive = false;
        let draggedBlock = null;

        // 1. Toggle 3D Studio Mode
        function toggleStudioMode() {
            isStudioActive = !isStudioActive;
            const scene = document.getElementById('invoiceScene');
            const btnTxt = document.getElementById('txt-studio-toggle');
            const blocks = document.querySelectorAll('.inv-drag-block');

            if (isStudioActive) {
                scene.classList.add('studio-active');
                btnTxt.innerText = "{{ app()->getLocale() === 'ar' ? 'إغلاق الاستوديو' : 'Exit Studio' }}";
                blocks.forEach(b => b.setAttribute('draggable', 'true'));
                initDragAndDrop();
            } else {
                scene.classList.remove('studio-active');
                btnTxt.innerText = "{{ app()->getLocale() === 'ar' ? 'استوديو التحرير 3D' : '3D Live Studio' }}";
                blocks.forEach(b => b.setAttribute('draggable', 'false'));
                toggleInPlaceEditing(false);
                document.getElementById('chk-inplace-edit').checked = false;
            }
        }

        // 2. Toggle Realistic 3D Tilt Physics
        function toggleTiltEffect() {
            isTiltActive = !isTiltActive;
            const canvas = document.getElementById('invoiceCanvas');
            const btn = document.getElementById('btn-toggle-tilt');

            if (isTiltActive) {
                canvas.classList.add('is-tilting');
                btn.classList.add('btn-primary');
                btn.classList.remove('btn-secondary');
                attachTiltListeners();
            } else {
                canvas.classList.remove('is-tilting');
                canvas.style.transform = 'none';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-secondary');
                removeTiltListeners();
            }
        }

        function handleMouseMoveTilt(e) {
            if (!isTiltActive) return;
            const canvas = document.getElementById('invoiceCanvas');
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = ((y - centerY) / centerY) * -5; // max 5 deg tilt
            const rotateY = ((x - centerX) / centerX) * 5;
            
            canvas.style.transform = `perspective(1600px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.008, 1.008, 1.008)`;

            const sheen = document.getElementById('canvasSheen');
            if (sheen) {
                sheen.style.background = `radial-gradient(circle at ${(x / rect.width) * 100}% ${(y / rect.height) * 100}%, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 65%)`;
            }
        }

        function handleMouseLeaveTilt() {
            if (!isTiltActive) return;
            const canvas = document.getElementById('invoiceCanvas');
            canvas.style.transform = 'perspective(1600px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
        }

        function attachTiltListeners() {
            const canvas = document.getElementById('invoiceCanvas');
            canvas.addEventListener('mousemove', handleMouseMoveTilt);
            canvas.addEventListener('mouseleave', handleMouseLeaveTilt);
        }

        function removeTiltListeners() {
            const canvas = document.getElementById('invoiceCanvas');
            canvas.removeEventListener('mousemove', handleMouseMoveTilt);
            canvas.removeEventListener('mouseleave', handleMouseLeaveTilt);
        }

        // 3. Native HTML5 Drag and Drop Reordering
        function initDragAndDrop() {
            const container = document.getElementById('invoiceBlocksContainer');
            const blocks = container.querySelectorAll('.inv-drag-block');

            blocks.forEach(block => {
                block.addEventListener('dragstart', (e) => {
                    draggedBlock = block;
                    block.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                });

                block.addEventListener('dragend', () => {
                    draggedBlock = null;
                    block.classList.remove('dragging');
                    blocks.forEach(b => b.classList.remove('drag-over'));
                });

                block.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    if (draggedBlock && draggedBlock !== block) {
                        block.classList.add('drag-over');
                    }
                });

                block.addEventListener('dragleave', () => {
                    block.classList.remove('drag-over');
                });

                block.addEventListener('drop', (e) => {
                    e.preventDefault();
                    block.classList.remove('drag-over');
                    if (draggedBlock && draggedBlock !== block) {
                        const allBlocks = [...container.querySelectorAll('.inv-drag-block')];
                        const fromIdx = allBlocks.indexOf(draggedBlock);
                        const toIdx = allBlocks.indexOf(block);

                        if (fromIdx < toIdx) {
                            block.after(draggedBlock);
                        } else {
                            block.before(draggedBlock);
                        }
                    }
                });
            });
        }

        // 4. In-Place Direct Editing on Canvas
        function toggleInPlaceEditing(enable) {
            const editableFields = document.querySelectorAll('.live-edit-field');
            editableFields.forEach(el => {
                el.contentEditable = enable ? 'true' : 'false';
                if (enable) {
                    el.onblur = function() {
                        const fieldName = el.getAttribute('data-field');
                        const val = el.innerText.trim();
                        syncFieldToInput(fieldName, val);
                    };
                } else {
                    el.onblur = null;
                }
            });
        }

        function syncFieldToInput(field, val) {
            if (field === 'customer_name') document.getElementById('inp-cust-name').value = val;
            if (field === 'customer_phone') document.getElementById('inp-cust-phone').value = val;
            if (field === 'shipping_address') document.getElementById('inp-cust-addr').value = val;
            if (field === 'invoice_number') document.getElementById('inp-inv-num').value = val;
            if (field === 'date') document.getElementById('inp-inv-date').value = val;
        }

        // 5. Update Field in Real Time from Sidebar inputs
        function updateFieldLive(field, value) {
            if (field === 'customer_name') {
                document.getElementById('disp-cust-name').innerText = value;
            } else if (field === 'customer_phone') {
                document.getElementById('disp-cust-phone').innerText = value;
            } else if (field === 'shipping_address') {
                document.getElementById('disp-cust-addr').innerText = value;
            } else if (field === 'invoice_number') {
                document.getElementById('disp-inv-num').innerText = value;
            } else if (field === 'date') {
                document.getElementById('disp-inv-date').innerText = value;
            }
        }

        // 6. Dynamic Financials Calculation in Real Time
        function updateFinancialsLive() {
            const discountInp = parseFloat(document.getElementById('inp-discount').value) || 0;
            const paidInp = parseFloat(document.getElementById('inp-paid').value) || 0;

            const netBeforeVat = Math.max(0, subtotalBase - discountInp);
            const vatAmount = netBeforeVat * 0.15;
            const grandTotal = netBeforeVat + vatAmount;
            const dueBalance = Math.max(0, grandTotal - paidInp);

            document.getElementById('disp-calc-discount').innerText = 'SAR ' + discountInp.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('disp-calc-net').innerText = 'SAR ' + netBeforeVat.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('disp-calc-tax').innerText = 'SAR ' + vatAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('disp-calc-grand-total').innerText = 'SAR ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
            document.getElementById('disp-paid-amount').innerText = 'SAR ' + paidInp.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
            const dueEl = document.getElementById('disp-due-balance');
            dueEl.innerText = 'SAR ' + dueBalance.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            dueEl.style.color = dueBalance > 0 ? '#b91c1c' : '#047857';

            if (dueBalance <= 0) {
                document.getElementById('disp-due-date').innerText = 'مسدد بالكامل';
            }
        }

        // 7. Update Payment Method Checkboxes in Real Time
        function updatePaymentMethodLive(method) {
            document.getElementById('chk-partial').innerText = method === 'partial' ? '✓' : '';
            document.getElementById('chk-cheque').innerText = method === 'cheque' ? '✓' : '';
            document.getElementById('chk-bank').innerText = method === 'bank_transfer' ? '✓' : '';
            document.getElementById('chk-cash').innerText = method === 'cash' ? '✓' : '';
        }

        // 8. Commit & Persist Changes via AJAX
        function saveRealtimeInvoice() {
            const saveBtn = document.getElementById('btn-save-invoice');
            const originalHtml = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 ml-1"></i> <span>جارٍ الحفظ...</span>';
            saveBtn.disabled = true;

            // Collect current ordered sections
            const container = document.getElementById('invoiceBlocksContainer');
            const orderedSections = [...container.querySelectorAll('.inv-drag-block')].map(b => b.getAttribute('data-section-id'));

            const payload = {
                _token: csrfToken,
                customer_name: document.getElementById('inp-cust-name').value,
                customer_phone: document.getElementById('inp-cust-phone').value,
                shipping_address: document.getElementById('inp-cust-addr').value,
                invoice_number: document.getElementById('inp-inv-num').value,
                date: document.getElementById('inp-inv-date').value,
                discount: parseFloat(document.getElementById('inp-discount').value) || 0,
                payment_method: document.getElementById('inp-pay-method').value,
                sections_order: orderedSections,
            };

            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            })
            .then(res => res.json())
            .then(data => {
                saveBtn.innerHTML = '<i class="fa-solid fa-check mr-1 ml-1 text-emerald-300"></i> <span>تم الحفظ!</span>';
                setTimeout(() => {
                    saveBtn.innerHTML = originalHtml;
                    saveBtn.disabled = false;
                }, 2000);
            })
            .catch(err => {
                console.error('Error saving invoice details:', err);
                saveBtn.innerHTML = originalHtml;
                saveBtn.disabled = false;
                alert('حدث خطأ أثناء حفظ التعديلات.');
            });
        }

        // 9. Reset Sections Order
        function resetSectionsOrder() {
            const defaultOrder = [
                'header_company_client',
                'title_banner',
                'items_table',
                'totals_and_payments',
                'signatures_stamp',
                'footer_notes',
            ];
            const container = document.getElementById('invoiceBlocksContainer');
            defaultOrder.forEach(secId => {
                const el = container.querySelector(`[data-section-id="${secId}"]`);
                if (el) container.appendChild(el);
            });
        }
    </script>
</x-layouts.admin>
