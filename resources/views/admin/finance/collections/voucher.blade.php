<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Voucher - {{ $entry->collection_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Tajawal', 'Outfit', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .action-bar {
            width: 100%;
            max-width: 800px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .voucher-sheet {
            width: 100%;
            max-width: 800px;
            background: #ffffff;
            border: 2px solid #0A4F78;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 2rem 2.5rem;
        }
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0A4F78;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .header-brand img {
            height: 55px;
            object-fit: contain;
        }
        .brand-title h1 {
            font-size: 1.25rem;
            font-weight: 900;
            color: #0A4F78;
        }
        .brand-title p {
            font-size: 0.75rem;
            color: #64748b;
        }
        .voucher-badge {
            text-align: center;
            border: 2px dashed #0A4F78;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            background: #f8fafc;
        }
        .voucher-badge h2 {
            font-size: 1.15rem;
            font-weight: 900;
            color: #0A4F78;
        }
        .voucher-badge span {
            font-family: monospace;
            font-weight: bold;
            font-size: 0.85rem;
            color: #334155;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }
        .meta-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dotted #cbd5e1;
            padding-bottom: 0.35rem;
        }
        .meta-label {
            font-weight: 700;
            color: #64748b;
        }
        .meta-value {
            font-weight: 800;
            color: #0f172a;
        }
        .financial-box {
            border: 1.5px solid #0A4F78;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .fin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .fin-table th, .fin-table td {
            padding: 0.65rem 1rem;
            border: 1px solid #e2e8f0;
        }
        .fin-table th {
            background: #D9E2EC;
            color: #0A4F78;
            font-weight: 900;
            text-align: center;
        }
        .fin-table td {
            text-align: center;
            font-family: 'Outfit', 'Tajawal', sans-serif;
            font-variant-numeric: tabular-nums;
            font-size: 0.95rem;
            font-weight: 700;
        }
        .fin-total-row {
            background: #0A4F78;
            color: #ffffff;
            font-weight: 900;
        }
        .fin-total-row td {
            border-color: #0A4F78;
            font-size: 1.15rem;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 1rem;
        }
        .sig-block {
            text-align: center;
            width: 28%;
        }
        .sig-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #334155;
            margin-bottom: 3rem;
        }
        .sig-line {
            border-top: 1.5px solid #64748b;
        }
        .stamp-circle {
            width: 80px;
            height: 80px;
            border: 2px dashed #0A4F78;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0A4F78;
            font-size: 0.75rem;
            font-weight: 900;
            transform: rotate(-10deg);
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }
        .btn-primary { background: #0A4F78; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #334155; }
        @media print {
            body { background: #fff; padding: 0; }
            .action-bar { display: none; }
            .voucher-sheet { border: 2px solid #000; box-shadow: none; max-width: 100%; border-radius: 0; }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <a href="{{ route('admin.finance.collections.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {{ app()->getLocale() === 'ar' ? 'العودة لقيود التحصيل' : 'Back to Ledger' }}
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa-solid fa-print"></i> {{ app()->getLocale() === 'ar' ? 'طباعة سند القبض' : 'Print Voucher' }}
        </button>
    </div>

    <div class="voucher-sheet">
        <!-- Header -->
        <div class="top-header">
            <div class="header-brand">
                <img src="/images/logo.png" alt="Blue Zone" onerror="this.style.display='none'">
                <div class="brand-title">
                    <h1>BLUE ZONE PHARMACEUTICALS</h1>
                    <p>{{ app()->getLocale() === 'ar' ? 'بلو زون للأدوية والمستحضرات الطبية' : 'Clinical Longevity & Botanical Medicine' }}</p>
                    <p style="font-size: 0.7rem; color: #94a3b8;">VAT No: 31004829100003 &bull; CR: 1010884920</p>
                </div>
            </div>

            <div class="voucher-badge">
                <h2>{{ app()->getLocale() === 'ar' ? 'سند قبض مالي' : 'OFFICIAL RECEIPT' }}</h2>
                <span>{{ $entry->collection_number }}</span>
            </div>
        </div>

        <!-- Meta Details -->
        <div class="meta-grid">
            <div class="meta-item">
                <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'التاريخ / Date:' : 'Date:' }}</span>
                <span class="meta-value font-mono">{{ $entry->entry_date ? $entry->entry_date->format('Y-m-d') : '-' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'التصنيف / Category:' : 'Category:' }}</span>
                <span class="meta-value">{{ $entry->category_meta['name_en'] }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'اسم العميل / المصدر:' : 'Customer / Source:' }}</span>
                <span class="meta-value">{{ $entry->customer_source ?: '-' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'كود العميل / Code:' : 'Customer Code:' }}</span>
                <span class="meta-value font-mono">{{ $entry->customer_code ?: '-' }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'طريقة القبض / Method:' : 'Payment Method:' }}</span>
                <span class="meta-value">{{ $entry->payment_method }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">{{ app()->getLocale() === 'ar' ? 'الحساب المودع فيه:' : 'Deposited Account:' }}</span>
                <span class="meta-value">{{ $entry->financialAccount ? $entry->financialAccount->name_en : '-' }}</span>
            </div>
        </div>

        <!-- Description Note -->
        @if($entry->description)
            <div style="background: #f8fafc; border-left: 3px solid #0A4F78; padding: 0.65rem 1rem; font-size: 0.85rem; margin-bottom: 1.5rem;">
                <strong>{{ app()->getLocale() === 'ar' ? 'البيان:' : 'Description:' }}</strong>
                <span>{{ $entry->description }}</span>
            </div>
        @endif

        <!-- Cheque Info if applicable -->
        @if($entry->cheque_number)
            <div style="background: #faf5ff; border: 1px solid #d8b4fe; border-radius: 6px; padding: 0.75rem 1rem; font-size: 0.82rem; margin-bottom: 1.5rem; color: #6b21a8;">
                <strong><i class="fa-solid fa-money-check"></i> {{ app()->getLocale() === 'ar' ? 'بيانات الشيك البنكي:' : 'Cheque Details:' }}</strong>
                <span class="font-mono ml-2 mr-2">No: {{ $entry->cheque_number }} &bull; Bank: {{ $entry->cheque_bank }} &bull; Due: {{ $entry->cheque_due_date ? $entry->cheque_due_date->format('Y-m-d') : '-' }}</span>
            </div>
        @endif

        <!-- Financial Breakdown Table -->
        <div class="financial-box">
            <table class="fin-table">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'المبلغ بدون الضريبة (Excl. VAT)' : 'Amount Excl. VAT' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'نسبة الضريبة (VAT %)' : 'VAT %' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'قيمة الضريبة (VAT)' : 'VAT Amount' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الإجمالي المقبوض (Gross Amount)' : 'Gross Amount' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ number_format($entry->amount_excl_vat, 2) }} SAR</td>
                        <td>{{ number_format($entry->vat_rate, 1) }}%</td>
                        <td style="color: #b45309; font-weight: bold;">{{ number_format($entry->vat_amount, 2) }} SAR</td>
                        <td style="font-weight: 900; color: #0A4F78;">{{ number_format($entry->gross_amount, 2) }} SAR</td>
                    </tr>
                    <tr class="fin-total-row">
                        <td colspan="3" style="text-align: right;">{{ app()->getLocale() === 'ar' ? 'إجمالي المبلغ المقبوض شامل الضريبة / Total Paid:' : 'Total Amount Collected:' }}</td>
                        <td>{{ number_format($entry->gross_amount, 2) }} SAR</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signatures & Official Stamp -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-title">{{ app()->getLocale() === 'ar' ? 'المحاسب المعتمد' : 'Accountant' }}</div>
                <div class="sig-line"></div>
            </div>
            <div class="sig-block">
                <div class="stamp-circle">
                    BLUE ZONE<br>SEAL
                </div>
            </div>
            <div class="sig-block">
                <div class="sig-title">{{ app()->getLocale() === 'ar' ? 'أمين الصندوق / المستلم' : 'Cashier / Collector' }}</div>
                <div class="sig-line"></div>
            </div>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-size: 0.65rem; color: #94a3b8; border-top: 1px dotted #e2e8f0; padding-top: 0.5rem;">
            Generated electronically by Blue Zone ERP &bull; Timestamp: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>

</body>
</html>
