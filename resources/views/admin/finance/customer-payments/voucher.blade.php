<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt — {{ $payment->receipt_voucher_number ?? $payment->transaction_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Outfit', 'Tajawal', sans-serif; background: #f8fafc; color: #0f172a; margin: 0; padding: 2rem; }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .voucher-card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>
    <div class="max-w-3xl mx-auto mb-4 no-print flex justify-between items-center">
        <button onclick="window.print()" class="btn btn-primary font-bold px-4 py-2 rounded-xl">
            <i class="fa-solid fa-print mr-1.5 ml-1.5"></i> Print Receipt Voucher
        </button>
        <button onclick="window.close()" class="btn btn-secondary font-bold px-4 py-2 rounded-xl">
            Close
        </button>
    </div>

    <div class="voucher-card max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-xl relative overflow-hidden">
        <!-- Watermark / Background Accent -->
        <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-emerald-500/5 pointer-events-none"></div>

        <!-- Header -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 m-0 tracking-tight">BLUE ZONE™ BIO-PHARMA</h1>
                <p class="text-xs text-slate-500 m-0 mt-1">Cellular Longevity & Clinical Medicine</p>
                <p class="text-xs text-slate-400 font-mono m-0">ZATCA Tax ID: 31004829100003 &bull; CR: 1010884920</p>
            </div>
            <div class="text-right rtl:text-left">
                <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider mb-1">
                    Official Receipt Voucher / سند قبض
                </span>
                <div class="font-mono font-black text-lg text-slate-900">{{ $payment->receipt_voucher_number }}</div>
                <div class="text-xs text-slate-400 font-mono">{{ $payment->transaction_date ? $payment->transaction_date->format('Y-m-d') : date('Y-m-d') }}</div>
            </div>
        </div>

        <!-- Receipt Body Details -->
        <div class="space-y-4 text-sm mb-8">
            <div class="flex border-b border-slate-100 pb-3">
                <span class="w-44 text-slate-400 font-bold uppercase text-xs">Received From / استلمنا من:</span>
                <span class="font-bold text-slate-900 text-base">{{ $payment->customer?->name ?? ($payment->order?->customer_name ?? 'Authorized Client') }}</span>
            </div>

            <div class="flex border-b border-slate-100 pb-3">
                <span class="w-44 text-slate-400 font-bold uppercase text-xs">Amount / المبلغ:</span>
                <span class="font-mono font-black text-xl text-emerald-600">{{ number_format($payment->amount, 2) }} SAR</span>
            </div>

            <div class="flex border-b border-slate-100 pb-3">
                <span class="w-44 text-slate-400 font-bold uppercase text-xs">Payment Method / طريقة الدفع:</span>
                <span class="font-bold text-slate-800 uppercase font-mono">{{ $payment->payment_method }}</span>
            </div>

            <div class="flex border-b border-slate-100 pb-3">
                <span class="w-44 text-slate-400 font-bold uppercase text-xs">Deposited Into / أودع في:</span>
                <span class="font-bold text-slate-800">{{ $payment->account?->localized_name }}</span>
            </div>

            @if($payment->order)
                <div class="flex border-b border-slate-100 pb-3">
                    <span class="w-44 text-slate-400 font-bold uppercase text-xs">For Order & Invoice / للطلب:</span>
                    <span class="font-mono font-bold text-slate-800">Order #{{ $payment->order->order_number }} (Invoice: {{ $payment->order->invoice_number }})</span>
                </div>
            @endif

            <div class="flex border-b border-slate-100 pb-3">
                <span class="w-44 text-slate-400 font-bold uppercase text-xs">Reference / المرجع:</span>
                <span class="font-mono text-slate-700">{{ $payment->reference_number ?? 'Auto-generated transaction ' . $payment->transaction_number }}</span>
            </div>

            <div class="flex pb-2">
                <span class="w-44 text-slate-400 font-bold uppercase text-xs">Description / البيان:</span>
                <span class="text-slate-600">{{ $payment->notes }}</span>
            </div>
        </div>

        <!-- Signatures & Official Stamp -->
        <div class="grid grid-cols-2 gap-8 pt-8 border-t-2 border-slate-900 mt-12 text-center text-xs">
            <div>
                <p class="text-slate-400 font-bold uppercase mb-12">Authorized Accountant / المحاسب المسئول</p>
                <div class="border-b border-slate-400 w-44 mx-auto mb-1"></div>
                <p class="font-mono font-bold text-slate-700">Financial Treasury Officer</p>
            </div>
            <div>
                <p class="text-slate-400 font-bold uppercase mb-12">Corporate Seal / الختم المعتمد</p>
                <div class="w-20 h-20 rounded-full border-2 border-dashed border-emerald-600/40 mx-auto flex items-center justify-center text-[10px] text-emerald-700 font-bold uppercase rotate-12">
                    Verified Paid
                </div>
            </div>
        </div>
    </div>
</body>
</html>
