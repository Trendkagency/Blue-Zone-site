<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ app()->getLocale() == 'ar' ? 'عرض سعر' : 'Quotation' }} - {{ $quotation->quotation_number }} | BLUE ZONE™</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,600,700,800,900|inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

        .quote-sheet {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 3.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(3, 24, 39, 0.08);
            position: relative;
        }

        .quote-sheet::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #0284c7, #0ea5e9, #10b981);
            border-radius: 16px 16px 0 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 2rem;
            border-bottom: 2px solid var(--bz-gray-100);
            margin-bottom: 2rem;
        }

        .brand-title {
            font-size: 1.75rem;
            font-weight: 900;
            letter-spacing: 0.05em;
            color: var(--bz-navy);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-sub {
            font-size: 0.8rem;
            color: var(--bz-gray-500);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .meta-box {
            text-align: right;
        }
        [dir="rtl"] .meta-box {
            text-align: left;
        }

        .quote-badge {
            display: inline-block;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            font-size: 0.8rem;
            font-weight: 800;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .doc-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--bz-navy);
            margin-bottom: 0.25rem;
        }

        .doc-number {
            font-family: monospace;
            font-size: 1rem;
            font-weight: 700;
            color: var(--bz-teal);
        }

        .party-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2.5rem;
            background: var(--bz-gray-50);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--bz-gray-200);
        }

        .party-box h4 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--bz-gray-500);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .party-box p {
            font-size: 0.875rem;
            color: var(--bz-gray-700);
            margin-bottom: 0.25rem;
        }

        .party-box .name {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--bz-navy);
            margin-bottom: 0.35rem;
        }

        table.quote-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        table.quote-table th {
            background-color: var(--bz-navy);
            color: #ffffff;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.85rem 1rem;
            text-align: left;
            font-weight: 700;
        }
        [dir="rtl"] table.quote-table th {
            text-align: right;
        }

        table.quote-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--bz-gray-200);
            font-size: 0.875rem;
            color: var(--bz-gray-700);
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2.5rem;
        }

        .totals-table {
            width: 320px;
            border-collapse: collapse;
        }

        .totals-table tr td {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .totals-table tr td:last-child {
            text-align: right;
            font-family: monospace;
            font-weight: 600;
        }
        [dir="rtl"] .totals-table tr td:last-child {
            text-align: left;
        }

        .totals-table tr.grand-total {
            border-top: 2px solid var(--bz-navy);
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--bz-navy);
        }

        .terms-box {
            border-top: 1px solid var(--bz-gray-200);
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: var(--bz-gray-500);
            line-height: 1.6;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px dashed var(--bz-gray-200);
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            height: 1px;
            background: #94a3b8;
            margin: 2.5rem 1rem 0.5rem 1rem;
        }

        .signature-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--bz-gray-500);
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print-toolbar {
                display: none;
            }
            .quote-sheet {
                box-shadow: none;
                padding: 1.5rem;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-toolbar">
        <div>
            <strong>{{ app()->getLocale() == 'ar' ? 'معاينة العرض للطباعة' : 'Quotation Print Document' }}</strong>
            <span style="color: #64748B; font-size: 0.85rem; margin-left: 0.5rem; margin-right: 0.5rem;">• {{ $quotation->quotation_number }}</span>
        </div>
        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa-solid fa-print"></i> {{ app()->getLocale() == 'ar' ? 'طباعة / PDF' : 'Print / Export PDF' }}
            </button>
            <a href="{{ route('admin.sales.quotations.show', $quotation->id) }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> {{ app()->getLocale() == 'ar' ? 'عودة للعرض' : 'Back to Quotation' }}
            </a>
        </div>
    </div>

    <div class="quote-sheet">
        <!-- Header -->
        <div class="header">
            <div>
                <div class="brand-title">
                    <span style="color: #0284c7;">BLUE</span> ZONE
                </div>
                <div class="brand-sub">Advanced Health, Wellness & Longevity Clinics</div>
                <p style="font-size: 0.8rem; color: #64748B; margin-top: 0.5rem;">
                    Riyadh, Kingdom of Saudi Arabia • CR: 1010892041<br>
                    VAT ID: 31004829100003 • Tel: +966 11 000 0000
                </p>
            </div>

            <div class="meta-box">
                <span class="quote-badge">{{ $quotation->status }}</span>
                <div class="doc-title">{{ app()->getLocale() == 'ar' ? 'عرض سعر رسمي' : 'COMMERCIAL QUOTATION' }}</div>
                <div class="doc-number">{{ $quotation->quotation_number }}</div>
                <div style="font-size: 0.8rem; color: #64748B; margin-top: 0.5rem;">
                    <strong>{{ app()->getLocale() == 'ar' ? 'تاريخ العرض:' : 'Date:' }}</strong> {{ $quotation->date?->format('Y-m-d') ?? $quotation->created_at->format('Y-m-d') }}<br>
                    <strong>{{ app()->getLocale() == 'ar' ? 'صالح حتى:' : 'Valid Until:' }}</strong> {{ $quotation->valid_until?->format('Y-m-d') ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Parties Info -->
        <div class="party-grid">
            <div class="party-box">
                <h4>{{ app()->getLocale() == 'ar' ? 'مقدم العرض (الطرف الأول):' : 'Offered By:' }}</h4>
                <div class="name">Blue Zone Health Systems</div>
                <p>Commercial Longevity & Clinical Solutions</p>
                <p><strong>Prepared By:</strong> {{ $quotation->creator?->name ?? 'Sales Officer' }}</p>
                <p>contact@bluezone.sa | www.bluezone.sa</p>
            </div>

            <div class="party-box">
                <h4>{{ app()->getLocale() == 'ar' ? 'مقدم إلى (العميل / المنشأة):' : 'Quotation Prepared For:' }}</h4>
                <div class="name">{{ $quotation->customer_name }}</div>
                @if($quotation->company_name)
                    <p style="font-weight: 700; color: #0284c7;">{{ $quotation->company_name }}</p>
                @endif
                <p><strong>Email:</strong> {{ $quotation->customer_email ?? '—' }}</p>
                <p><strong>Phone:</strong> {{ $quotation->customer_phone ?? '—' }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="quote-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th>{{ app()->getLocale() == 'ar' ? 'الوصف / البند' : 'Item Description' }}</th>
                    <th style="text-align: center; width: 80px;">{{ app()->getLocale() == 'ar' ? 'الكمية' : 'Qty' }}</th>
                    <th style="text-align: right; width: 120px;">{{ app()->getLocale() == 'ar' ? 'سعر الوحدة' : 'Unit Price' }}</th>
                    <th style="text-align: right; width: 100px;">{{ app()->getLocale() == 'ar' ? 'الخصم' : 'Discount' }}</th>
                    <th style="text-align: right; width: 130px;">{{ app()->getLocale() == 'ar' ? 'الإجمالي' : 'Total (SAR)' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $idx => $item)
                    <tr>
                        <td style="text-align: center; color: #94A3B8;">{{ $idx + 1 }}</td>
                        <td>
                            <strong style="color: #031827;">{{ $item->item_name }}</strong>
                            <div style="font-size: 0.75rem; color: #64748B; font-family: monospace;">SKU: {{ $item->sku ?? 'N/A' }}</div>
                        </td>
                        <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-family: monospace;">{{ number_format($item->unit_price, 2) }}</td>
                        <td style="text-align: right; font-family: monospace; color: #e11d48;">
                            {{ $item->discount > 0 ? '-' . number_format($item->discount, 2) : '0.00' }}
                        </td>
                        <td style="text-align: right; font-family: monospace; font-weight: 700; color: #031827;">
                            {{ number_format($item->total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>{{ app()->getLocale() == 'ar' ? 'المجموع الفرعي:' : 'Subtotal:' }}</td>
                    <td>{{ number_format($quotation->subtotal, 2) }} SAR</td>
                </tr>
                @if($quotation->discount > 0)
                    <tr style="color: #e11d48;">
                        <td>{{ app()->getLocale() == 'ar' ? 'إجمالي الخصم:' : 'Discount Total:' }}</td>
                        <td>-{{ number_format($quotation->discount, 2) }} SAR</td>
                    </tr>
                @endif
                <tr>
                    <td>{{ app()->getLocale() == 'ar' ? 'ضريبة القيمة المضافة (15%):' : 'VAT (15%):' }}</td>
                    <td>{{ number_format($quotation->tax, 2) }} SAR</td>
                </tr>
                <tr class="grand-total">
                    <td>{{ app()->getLocale() == 'ar' ? 'المجموع الإجمالي:' : 'Grand Total:' }}</td>
                    <td>{{ number_format($quotation->total, 2) }} SAR</td>
                </tr>
            </table>
        </div>

        <!-- Terms -->
        <div class="terms-box">
            <strong>{{ app()->getLocale() == 'ar' ? 'الشروط والأحكام التجارية:' : 'Commercial Terms & Conditions:' }}</strong>
            <p>{{ $quotation->terms_conditions ?? (app()->getLocale() == 'ar' ? 'يسري هذا العرض لمدة 30 يوماً من تاريخ الإصدار. الأسعار تشمل ضريبة القيمة المضافة 15%. يُعتبر العرض معتمداً فور التوقيع أدناه أو استلام أمر الشراء الرسمي.' : 'This quotation is valid for 30 days from the issue date. All prices are inclusive of 15% VAT. Terms become binding upon signature or receipt of an authorized Purchase Order.') }}</p>
        </div>

        <!-- Signatures -->
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">{{ app()->getLocale() == 'ar' ? 'توقيع المعتمد - Blue Zone' : 'Authorized Representative (Blue Zone)' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-title">{{ app()->getLocale() == 'ar' ? 'اعتماد العميل / الختم الرسمي' : 'Client Acceptance & Official Seal' }}</div>
            </div>
        </div>
    </div>

</body>
</html>
