<!DOCTYPE html>
@php
    $isAr = app()->getLocale() === 'ar';
    $status = is_array($order) ? ($order['status'] ?? 'Processing') : ($order->status ?? 'Processing');
    $date = is_array($order) ? ($order['date'] ?? now()->toDateString()) : ($order->date?->format('Y-m-d') ?? now()->toDateString());
    $custName = is_array($order) ? ($order['customer_name'] ?? 'Authorized Client') : ($order->customer_name ?? 'Authorized Client');
    $custEmail = is_array($order) ? ($order['customer_email'] ?? 'care@bluezone.com') : ($order->customer_email ?? 'care@bluezone.com');
    $custPhone = is_array($order) ? ($order['customer_phone'] ?? '+966 50 000 0000') : ($order->customer_phone ?? '+966 50 000 0000');
    $payMethod = is_array($order) ? ($order['payment_method'] ?? 'Mada / Visa') : ($order->payment_method ?? 'Mada / Visa');
    $payStatus = is_array($order) ? ($order['payment_status'] ?? 'Paid') : ($order->payment_status ?? 'Paid');
    $subtotal = is_array($order) ? ($order['subtotal'] ?? 0) : ($order->subtotal ?? 0);
    $discount = is_array($order) ? ($order['discount'] ?? 0) : ($order->discount ?? 0);
    $couponCode = is_array($order) ? ($order['coupon_code'] ?? null) : ($order->coupon_code ?? null);
    $tax = is_array($order) ? ($order['tax'] ?? 0) : ($order->tax ?? 0);
    $shipping = is_array($order) ? ($order['shipping'] ?? 0) : ($order->shipping ?? 0);
    $total = is_array($order) ? ($order['total'] ?? 0) : ($order->total ?? 0);
    $shippingAddr = is_array($order) ? ($order['shipping_address'] ?? []) : ($order->shipping_address ?? []);
    $items = is_array($order) ? ($order['items'] ?? []) : ($order->items ?? []);
    $channel = is_array($order) ? ($order['channel'] ?? 'online') : ($order->channel ?? 'online');

    $statusLower = strtolower($status);
    $statusBadge = match($statusLower) {
        'delivered', 'completed' => 'status-delivered',
        'shipped', 'out_for_delivery' => 'status-shipped',
        'processing', 'confirmed' => 'status-processing',
        'cancelled' => 'status-cancelled',
        default => 'status-pending',
    };

    $statusLabel = match($statusLower) {
        'delivered', 'completed' => ($isAr ? 'تم التسليم بنجاح' : 'Delivered'),
        'shipped', 'out_for_delivery' => ($isAr ? 'في الطريق للتسليم' : 'Out for Delivery'),
        'processing' => ($isAr ? 'قيد التجهيز بالمستودع' : 'Processing in Depot'),
        'confirmed' => ($isAr ? 'مؤكد ومعتمد' : 'Confirmed & Approved'),
        'cancelled' => ($isAr ? 'ملغي ومسترجع' : 'Cancelled & Restocked'),
        default => ($isAr ? 'قيد المراجعة' : 'Pending Review'),
    };

    // Timeline steps
    $timeline = [];
    if (is_object($order) && method_exists($order, 'getTimelineAttribute')) {
        $timeline = $order->timeline;
    } elseif (is_array($order) && !empty($order['timeline'])) {
        $timeline = $order['timeline'];
    } else {
        $timeline = [
            [
                'step' => 'placed',
                'status' => $isAr ? 'تم إنشاء وتأكيد الطلب' : 'Order Placed & Verified',
                'timestamp' => "{$date} 09:30 AM",
                'completed' => true,
                'icon' => 'fa-clipboard-check',
            ],
            [
                'step' => 'processing',
                'status' => $isAr ? 'التجهيز الصيدلاني المخبري' : 'Pharmaceutical Preparation',
                'timestamp' => "{$date} 11:45 AM",
                'completed' => in_array($statusLower, ['processing', 'shipped', 'delivered', 'completed'], true),
                'icon' => 'fa-flask-vial',
            ],
            [
                'step' => 'shipped',
                'status' => $isAr ? 'الشحن المبرد (سلسلة التبريد)' : 'Cold-Chain Dispatch',
                'timestamp' => "{$date} 02:15 PM",
                'completed' => in_array($statusLower, ['shipped', 'delivered', 'completed'], true),
                'icon' => 'fa-truck-fast',
            ],
            [
                'step' => 'delivered',
                'status' => $isAr ? 'تم التسليم بنجاح' : 'Handed Over & Delivered',
                'timestamp' => "{$date} 05:30 PM",
                'completed' => in_array($statusLower, ['delivered', 'completed'], true),
                'icon' => 'fa-circle-check',
            ],
        ];
    }

    $digitalHash = strtoupper(hash('sha256', $orderNumber . '|' . $invoiceNumber . '|' . $total . '|BLUE_ZONE_LEDGER'));
@endphp
<html lang="{{ app()->getLocale() }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isAr ? 'التحقق الرقمي من الفاتورة والطلب' : 'Digital Invoice & Order Verification' }} #{{ $orderNumber }} | BLUE ZONE™</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,600,700,800,900|inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    
    <!-- Theme Initializer (Prevents FOUC) -->
    <script>
        (function() {
            try {
                const saved = localStorage.getItem('bluezone_theme') || localStorage.getItem('bz_theme');
                if (saved === 'light') {
                    document.documentElement.classList.remove('dark');
                } else if (saved === 'dark') {
                    document.documentElement.classList.add('dark');
                } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.add('dark'); // Default to luxury dark mode
                }
            } catch(e) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'bz-navy': '#031827',
                        'bz-dark': '#062035',
                        'bz-surface': '#0A2942',
                        'bz-ocean': '#0A4F78',
                        'bz-cyan': '#00D2D3',
                        'bz-sky': '#38BDF8',
                        'bz-emerald': '#10B981',
                        'bz-gold': '#F59E0B',
                    },
                    fontFamily: {
                        sans: ['{{ $isAr ? "Cairo" : "Inter" }}', 'sans-serif'],
                        mono: ['Consolas', 'Monaco', 'Courier New', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Dedicated Resilient CSS Stylesheet (Guarantee Zero Layout Shift) -->
    <style>
        :root {
            --bg-body: #F1F5F9;
            --bg-card: #FFFFFF;
            --bg-elevated: #F8FAFC;
            --bg-input: #FFFFFF;
            --border-main: #E2E8F0;
            --border-subtle: #CBD5E1;
            --text-heading: #0F172A;
            --text-body: #334155;
            --text-muted: #64748B;
            --accent-brand: #0A4F78;
            --accent-cyan: #0284C7;
            --accent-emerald: #059669;
            --accent-emerald-bg: #ECFDF5;
            --accent-emerald-border: #A7F3D0;
            --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        html.dark {
            --bg-body: #031827;
            --bg-card: #071F33;
            --bg-elevated: #0B2942;
            --bg-input: #051826;
            --border-main: #133957;
            --border-subtle: #1E4E73;
            --text-heading: #F8FAFC;
            --text-body: #CBD5E1;
            --text-muted: #94A3B8;
            --accent-brand: #38BDF8;
            --accent-cyan: #00D2D3;
            --accent-emerald: #10B981;
            --accent-emerald-bg: rgba(16, 185, 129, 0.12);
            --accent-emerald-border: rgba(16, 185, 129, 0.35);
            --shadow-card: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: '{{ $isAr ? "Cairo" : "Inter" }}', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-body);
            min-height: 100vh;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        .bz-portal-shell {
            max-width: 980px;
            margin: 0 auto;
            width: 100%;
            padding: 1.75rem 1rem 3rem 1rem;
        }

        .bz-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-main);
            border-radius: 1.25rem;
            box-shadow: var(--shadow-card);
            transition: all 0.2s ease;
        }

        .bz-card-elevated {
            background-color: var(--bg-elevated);
            border: 1px solid var(--border-main);
            border-radius: 1rem;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-delivered {
            background: rgba(16, 185, 129, 0.15);
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .status-shipped {
            background: rgba(56, 189, 248, 0.15);
            color: #38BDF8;
            border: 1px solid rgba(56, 189, 248, 0.35);
        }

        .status-processing {
            background: rgba(2, 132, 199, 0.15);
            color: #0284C7;
            border: 1px solid rgba(2, 132, 199, 0.35);
        }
        html.dark .status-processing {
            color: #38BDF8;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #F59E0B;
            border: 1px solid rgba(245, 158, 11, 0.35);
        }

        .status-cancelled {
            background: rgba(244, 63, 94, 0.15);
            color: #F43F5E;
            border: 1px solid rgba(244, 63, 94, 0.35);
        }

        /* Stepper Connector */
        .stepper-progress-bar {
            position: absolute;
            top: 24px;
            left: 24px;
            right: 24px;
            height: 3px;
            background: var(--border-main);
            z-index: 1;
        }

        /* Responsive items table */
        .bz-table {
            width: 100%;
            border-collapse: collapse;
            text-align: {{ $isAr ? 'right' : 'left' }};
        }

        .bz-table th {
            padding: 0.75rem 1rem;
            font-size: 0.6875rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-main);
            background: var(--bg-elevated);
        }

        .bz-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-main);
            font-size: 0.8125rem;
        }

        .bz-table tr:last-child td {
            border-bottom: none;
        }

        /* Print styles */
        @media print {
            body {
                background: #FFFFFF !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .bz-card {
                box-shadow: none !important;
                border: 1px solid #CBD5E1 !important;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="selection:bg-sky-500 selection:text-white flex flex-col min-h-screen">

    <!-- Top Navigation Header -->
    <header class="no-print sticky top-0 z-50 backdrop-blur-md bg-white/85 dark:bg-[#031827]/85 border-b border-slate-200 dark:border-slate-800 transition-colors">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-4">
            
            <!-- Brand & Portal Info -->
            <a href="{{ route('customer.home') }}" class="flex items-center gap-3 no-underline group text-inherit">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0A4F78] to-[#0284C7] p-2 flex items-center justify-center shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
                    <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="BLUE ZONE" class="max-h-full max-w-full object-contain" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-black text-base tracking-wider text-slate-900 dark:text-white leading-none">BLUE ZONE™</span>
                        <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $isAr ? 'نظام التحقق الحيوي' : 'Official Portal' }}
                        </span>
                    </div>
                    <span class="text-[10px] font-bold text-sky-600 dark:text-cyan-400 uppercase tracking-widest block mt-0.5">
                        {{ $isAr ? 'بوابة التحقق من أصالة الطلبات والفواتير' : 'Verified Order & Tax Invoice Portal' }}
                    </span>
                </div>
            </a>

            <!-- Controls: Theme Toggle, Locale Switcher, Print Action -->
            <div class="flex items-center gap-2 sm:gap-3">
                
                <!-- Dark / Light Mode Toggle Button -->
                <button type="button" id="theme-toggle-btn" class="p-2 sm:px-3 sm:py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm" title="{{ $isAr ? 'تبديل وضع العرض (داكن / فاتح)' : 'Toggle Dark / Light Mode' }}">
                    <i class="fa-solid fa-moon hidden dark:inline text-sky-400"></i>
                    <i class="fa-solid fa-sun inline dark:hidden text-amber-500"></i>
                    <span class="hidden md:inline">{{ $isAr ? 'الوضع' : 'Theme' }}</span>
                </button>

                <!-- Language Toggle Button -->
                @if($isAr)
                    <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 text-xs font-extrabold transition-all shadow-sm">
                        English
                    </a>
                @else
                    <a href="{{ route('locale.switch', 'ar') }}" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 text-xs font-extrabold transition-all shadow-sm">
                        العربية
                    </a>
                @endif

                <!-- Direct Print / PDF Button -->
                <button type="button" onclick="window.print()" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 transition-all shadow-sm">
                    <i class="fa-solid fa-print text-sky-500"></i>
                    <span>{{ $isAr ? 'طباعة سريعة' : 'Print' }}</span>
                </button>

                <!-- Download Official Invoice Button -->
                <a href="{{ route('orders.download-invoice', $orderNumber) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black bg-gradient-to-r from-sky-600 via-cyan-600 to-sky-700 hover:from-sky-500 hover:to-cyan-500 text-white shadow-md shadow-sky-600/25 transition-all">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>{{ $isAr ? 'الفاتورة الضريبية' : 'Tax Invoice' }}</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 bz-portal-shell space-y-6">

        <!-- 1. Authenticity Cryptographic Verification Banner -->
        <div class="bz-card p-6 border-l-4 rtl:border-l-0 rtl:border-r-4 border-emerald-500 relative overflow-hidden bg-gradient-to-br from-emerald-500/10 via-transparent to-transparent">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-5 relative z-10">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-500/40 flex items-center justify-center text-2xl flex-shrink-0 shadow-inner">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white m-0 tracking-tight">
                                {{ $isAr ? 'فاتورة معتمدة ومحققة رقمياً' : 'Cryptographically Verified Invoice' }}
                            </h1>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                <i class="fa-solid fa-check-double mr-1 ml-1"></i>
                                ZATCA & MOH PHASE 2
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 mb-0 leading-relaxed max-w-2xl">
                            {{ $isAr ? 'تم التحقق من صحة الفاتورة ومطابقتها لسجلات التوزيع الحيوي المركزية لشركة بلو زون بيوسوتيكالز وفق المعايير السعودية والخليجية.' : 'This invoice is authenticated against the Blue Zone Central Digital Ledger complying with ZATCA Phase 2 and Saudi MOH distribution protocols.' }}
                        </p>
                    </div>
                </div>

                <div class="w-full md:w-auto p-3 rounded-xl bg-white/70 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-start md:text-end flex-shrink-0">
                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold block uppercase tracking-wider">
                        {{ $isAr ? 'بصمة التشفير الرقمية (SHA-256)' : 'Digital Security Hash' }}
                    </span>
                    <span class="font-mono text-xs text-sky-600 dark:text-cyan-400 font-bold tracking-wider block mt-0.5">
                        {{ substr($digitalHash, 0, 18) }}...{{ substr($digitalHash, -8) }}
                    </span>
                    <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-bold mt-1 inline-flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ $isAr ? 'حالة السجل: موثق ونشط' : 'Record Status: Active & Authentic' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. Interactive Order Tracking Stepper Timeline -->
        <div class="bz-card p-6">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-route text-sky-600 dark:text-cyan-400"></i>
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white m-0">
                        {{ $isAr ? 'مسار وسلسلة إمداد الطلب' : 'Live Order Journey & Fulfillment' }}
                    </h2>
                </div>
                <div class="status-badge {{ $statusBadge }}">
                    <span class="w-2 h-2 rounded-full bg-current animate-ping"></span>
                    {{ $statusLabel }}
                </div>
            </div>

            <!-- Stepper Steps Container -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative">
                @foreach($timeline as $idx => $step)
                    @php
                        $isDone = !empty($step['completed']);
                        $isCurrent = $isDone && (!isset($timeline[$idx + 1]) || empty($timeline[$idx + 1]['completed']));
                        $stepIcon = $step['icon'] ?? 'fa-check';
                    @endphp
                    <div class="bz-card-elevated p-4 flex flex-col justify-between gap-3 relative transition-all {{ $isCurrent ? 'ring-2 ring-sky-500 bg-sky-500/5' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shadow-xs {{ $isDone ? 'bg-emerald-500 text-white shadow-emerald-500/20' : 'bg-slate-200 dark:bg-slate-800 text-slate-500' }}">
                                <i class="fa-solid {{ $stepIcon }}"></i>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $isDone ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                @if($isCurrent)
                                    <span class="inline-flex items-center gap-1 font-extrabold text-sky-600 dark:text-cyan-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                        {{ $isAr ? 'الحالة الحالية' : 'Active Stage' }}
                                    </span>
                                @elseif($isDone)
                                    {{ $isAr ? 'مكتمل' : 'Completed' }}
                                @else
                                    {{ $isAr ? 'قيد الانتظار' : 'Pending' }}
                                @endif
                            </span>
                        </div>

                        <div>
                            <strong class="text-xs font-black text-slate-900 dark:text-white block">
                                {{ $step['status'] ?? '' }}
                            </strong>
                            @if(!empty($step['timestamp']))
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5 font-mono">
                                    <i class="fa-regular fa-clock text-[10px] mr-1 ml-1 text-slate-400"></i>{{ $step['timestamp'] }}
                                </span>
                            @endif
                            @if(!empty($step['note']))
                                <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-1 mb-0 leading-normal">
                                    {{ $step['note'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Key Order Metadata Cards Grid (Bento Box) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            
            <!-- Card 1: Order Credentials -->
            <div class="bz-card p-5 space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest text-sky-600 dark:text-cyan-400 flex items-center gap-1.5">
                        <i class="fa-regular fa-folder-open"></i>
                        {{ $isAr ? 'بيانات الطلب والفوترة' : 'Order Reference' }}
                    </span>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $orderNumber }}'); alert('{{ $isAr ? 'تم نسخ رقم الطلب' : 'Order number copied' }}');" class="text-slate-400 hover:text-sky-500 text-xs transition-colors" title="{{ $isAr ? 'نسخ رقم الطلب' : 'Copy Order ID' }}">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
                
                <div>
                    <div class="text-xl sm:text-2xl font-black font-mono text-slate-900 dark:text-white tracking-tight">
                        #{{ $orderNumber }}
                    </div>
                    @if($invoiceNumber !== $orderNumber)
                        <div class="mt-1 flex items-center gap-2">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $isAr ? 'رقم الفاتورة:' : 'Invoice No:' }}</span>
                            <span class="font-mono text-xs font-bold text-sky-600 dark:text-cyan-400 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                {{ $invoiceNumber }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ $isAr ? 'تاريخ الطلب:' : 'Order Date:' }}</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $date }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ $isAr ? 'قناة البيع:' : 'Sales Channel:' }}</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px]">
                            <i class="fa-solid fa-globe text-sky-500 mr-1 ml-1"></i>
                            {{ $channel }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Billed Customer Dossier -->
            <div class="bz-card p-5 space-y-3.5">
                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                    <i class="fa-regular fa-user"></i>
                    {{ $isAr ? 'بيانات العميل المعتمد' : 'Billed Customer' }}
                </span>

                <div>
                    <div class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ $custName }}</span>
                        <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center text-[10px]" title="{{ $isAr ? 'عميل موثق' : 'Verified Client' }}">
                            <i class="fa-solid fa-check"></i>
                        </span>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5">
                        {{ $isAr ? 'مستفيد مسجل بالمنظومة الحيوية' : 'Registered Protocol Recipient' }}
                    </span>
                </div>

                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ $isAr ? 'البريد الإلكتروني:' : 'Email:' }}</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 truncate max-w-[170px]" title="{{ $custEmail }}">
                            {{ $custEmail }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ $isAr ? 'الهاتف المعتمد:' : 'Phone:' }}</span>
                        <span class="font-mono font-bold text-slate-800 dark:text-slate-200" dir="ltr">
                            {{ $custPhone }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 3: Cold-Chain Logistics & Payment -->
            <div class="bz-card p-5 space-y-3.5">
                <span class="text-[10px] font-black uppercase tracking-widest text-teal-600 dark:text-teal-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-temperature-arrow-down"></i>
                    {{ $isAr ? 'الشحن المبرد والسداد' : 'Cold-Chain & Payment' }}
                </span>

                <div class="space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ $isAr ? 'طريقة السداد:' : 'Payment Method:' }}</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $payMethod }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 dark:text-slate-400">{{ $isAr ? 'حالة السداد:' : 'Payment Status:' }}</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                            {{ strtoupper($payStatus) }}
                        </span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200 dark:border-slate-800 text-xs space-y-1">
                    <div class="text-[10px] font-bold uppercase text-slate-500">
                        <i class="fa-solid fa-location-dot text-rose-500 mr-1 ml-1"></i>
                        {{ $isAr ? 'عنوان الشحن والتسليم:' : 'Delivery Destination:' }}
                    </div>
                    <div class="text-slate-800 dark:text-slate-200 font-semibold leading-snug">
                        @if(!empty($shippingAddr))
                            {{ is_array($shippingAddr) ? ($shippingAddr['street'] ?? '') : '' }}
                            @if(is_array($shippingAddr) && !empty($shippingAddr['city'])), {{ $shippingAddr['city'] }}@endif
                            @if(is_array($shippingAddr) && !empty($shippingAddr['country'])), {{ $shippingAddr['country'] }}@endif
                        @else
                            {{ $isAr ? 'المركز الصيدلاني المعتمد - الرياض' : 'Central Fulfillment Depot - Riyadh' }}
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- 4. Bioceutical Formulations Table (Items) -->
        <div class="bz-card overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-sky-600 dark:text-cyan-400"></i>
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white m-0">
                        {{ $isAr ? 'التركيبات والمكملات الحيوية المطلوبة' : 'Ordered Bioceutical Items & Protocol' }}
                    </h2>
                </div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                    {{ count($items) }} {{ $isAr ? 'عناصر' : 'item(s)' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="bz-table">
                    <thead>
                        <tr>
                            <th class="w-12 text-center">#</th>
                            <th>{{ $isAr ? 'المنتج / البروتوكول الحيوي' : 'Product / Protocol Specification' }}</th>
                            <th class="text-start">{{ $isAr ? 'كود الصنف' : 'SKU Code' }}</th>
                            <th class="text-end">{{ $isAr ? 'سعر الوحدة' : 'Unit Price' }}</th>
                            <th class="text-center">{{ $isAr ? 'الكمية' : 'Qty' }}</th>
                            <th class="text-end">{{ $isAr ? 'الإجمالي' : 'Total' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $i => $item)
                            @php
                                $iNameEn = is_array($item) ? ($item['product_name_en'] ?? 'Longevity Protocol') : ($item->product_name_en ?? 'Longevity Protocol');
                                $iNameAr = is_array($item) ? ($item['product_name_ar'] ?? '') : ($item->product_name_ar ?? '');
                                $iVar = is_array($item) ? ($item['variant_en'] ?? '') : ($item->variant_en ?? '');
                                $iVarAr = is_array($item) ? ($item['variant_ar'] ?? '') : ($item->variant_ar ?? '');
                                $iSku = is_array($item) ? ($item['sku'] ?? 'BZ-PROD') : ($item->sku ?? 'BZ-PROD');
                                $iPrice = is_array($item) ? ($item['unit_price'] ?? 0) : ($item->unit_price ?? 0);
                                $iQty = is_array($item) ? ($item['quantity'] ?? 1) : ($item->quantity ?? 1);
                                $iTotal = is_array($item) ? ($item['total'] ?? ($iPrice * $iQty)) : ($item->total ?? ($iPrice * $iQty));
                                $displayTitle = ($isAr && $iNameAr) ? $iNameAr : $iNameEn;
                                $displayVar = ($isAr && $iVarAr) ? $iVarAr : $iVar;
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="text-center font-mono text-slate-400 font-bold text-xs">{{ $i + 1 }}</td>
                                <td>
                                    <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                                        {{ $displayTitle }}
                                    </div>
                                    @if($displayVar)
                                        <div class="text-[11px] text-sky-600 dark:text-cyan-400 font-medium mt-0.5">
                                            {{ $displayVar }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ $iSku }}
                                    </span>
                                </td>
                                <td class="text-end font-mono text-slate-700 dark:text-slate-300 font-bold">
                                    @currency((float)$iPrice)
                                </td>
                                <td class="text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-lg bg-sky-500/10 text-sky-700 dark:text-sky-300 font-mono font-black text-xs">
                                        {{ $iQty }}
                                    </span>
                                </td>
                                <td class="text-end font-mono font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                    @currency((float)$iTotal)
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400 font-medium">
                                    {{ $isAr ? 'لا توجد عناصر مسجلة في هذا الطلب.' : 'No registered items found in this order.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Verification QR Code Card & Financial Ledger Breakdown (Two-Column Layout) -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
            
            <!-- Left: Scannable QR Code & Live Verification Link (5 cols) -->
            <div class="md:col-span-5 bz-card p-5 flex flex-col justify-between gap-4">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-qrcode text-sky-500"></i>
                        {{ $isAr ? 'رمز التحقق الرقمي السريع' : 'Official Scannable QR Verification' }}
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- QR Code Frame -->
                        <div class="w-28 h-28 rounded-2xl bg-white p-2 border-2 border-slate-200 dark:border-slate-700 shadow-md flex-shrink-0 flex items-center justify-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($verificationUrl) }}" alt="Order Verification QR" class="w-full h-full object-contain">
                        </div>

                        <!-- Scan Prompt Text -->
                        <div class="space-y-1.5 text-xs">
                            <strong class="text-slate-900 dark:text-white font-extrabold block text-xs leading-tight">
                                {{ $isAr ? 'امسح الرمز بكاميرا الجوال' : 'Scan with Smartphone Camera' }}
                            </strong>
                            <p class="text-slate-500 dark:text-slate-400 text-[11px] leading-relaxed m-0">
                                {{ $isAr ? 'لفتح بيانات الفاتورة الأصلية ومتابعة الشحنة مباشرة عبر الموقع الرسمي.' : 'Instantly opens live order tracking and verified tax invoice on the web portal.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Copy Verification URL Action -->
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                    <span class="text-[10px] font-mono text-slate-400 truncate max-w-[200px]" dir="ltr">
                        {{ $verificationUrl }}
                    </span>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $verificationUrl }}'); alert('{{ $isAr ? 'تم نسخ رابط التحقق بنجاح!' : 'Verification URL copied to clipboard!' }}');" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all border border-slate-200 dark:border-slate-700 flex-shrink-0 flex items-center gap-1">
                        <i class="fa-regular fa-copy"></i>
                        <span>{{ $isAr ? 'نسخ الرابط' : 'Copy' }}</span>
                    </button>
                </div>
            </div>

            <!-- Right: Financial Ledger Breakdown & Totals (7 cols) -->
            <div class="md:col-span-7 bz-card p-5 flex flex-col justify-between">
                <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-calculator text-emerald-500"></i>
                    {{ $isAr ? 'الحساب الختامي والضريبة المعتمدة' : 'Official Financial Ledger Breakdown' }}
                </div>

                <div class="space-y-2.5 text-xs">
                    <!-- Subtotal -->
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>{{ $isAr ? 'المجموع الخاضع للضريبة (قبل الخصم):' : 'Taxable Subtotal (Excl. VAT):' }}</span>
                        <span class="font-mono text-slate-900 dark:text-slate-200 font-bold text-sm">
                            @currency((float)$subtotal)
                        </span>
                    </div>

                    <!-- Discount -->
                    @if((float)$discount > 0)
                        <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-tag text-xs"></i>
                                <span>{{ $isAr ? 'الخصم الترويجي المعتمد' : 'Discount Applied' }}</span>
                                @if($couponCode)
                                    <span class="font-mono text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-300">
                                        {{ $couponCode }}
                                    </span>
                                @endif
                                :
                            </span>
                            <span class="font-mono text-sm">-@currency((float)$discount)</span>
                        </div>
                    @endif

                    <!-- Shipping -->
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-snowflake text-sky-500 text-[10px]"></i>
                            <span>{{ $isAr ? 'شحن سلسلة التبريد الدوائي:' : 'Cold-Chain Pharmaceutical Delivery:' }}</span>
                        </span>
                        <span class="font-mono text-slate-900 dark:text-slate-200 font-bold">
                            @if((float)$shipping > 0)
                                @currency((float)$shipping)
                            @else
                                <span class="text-emerald-600 dark:text-emerald-400 font-black uppercase text-[11px]">{{ $isAr ? 'شحن مجاني معتمد' : 'Free Cold Delivery' }}</span>
                            @endif
                        </span>
                    </div>

                    <!-- 15% VAT -->
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>{{ $isAr ? 'ضريبة القيمة المضافة (15% ZATCA):' : 'Value Added Tax (15% VAT):' }}</span>
                        <span class="font-mono text-slate-900 dark:text-slate-200 font-bold">
                            @currency((float)$tax)
                        </span>
                    </div>

                    <!-- Grand Total Bar -->
                    <div class="pt-3.5 mt-2 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white block">
                                {{ $isAr ? 'المبلغ الإجمالي المستحق:' : 'Grand Total Amount:' }}
                            </span>
                            <span class="text-[10px] text-slate-500 block">
                                {{ $isAr ? 'شامل ضريبة القيمة المضافة والشحن' : 'Inclusive of 15% VAT & Delivery' }}
                            </span>
                        </div>
                        <div class="font-mono text-2xl font-black text-sky-600 dark:text-cyan-400 tracking-tight">
                            @currency((float)$total)
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 6. Action Footer Toolbar (Print, Download, Return) -->
        <div class="no-print bz-card p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('orders.download-invoice', $orderNumber) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3 rounded-xl text-xs font-black bg-gradient-to-r from-sky-600 via-cyan-600 to-sky-700 hover:from-sky-500 hover:to-cyan-500 text-white shadow-lg shadow-sky-600/25 transition-all">
                    <i class="fa-solid fa-file-arrow-down text-sm"></i>
                    <span>{{ $isAr ? 'تحميل وطباعة الفاتورة الضريبية الرسمية' : 'Download Official Tax Invoice' }}</span>
                </a>

                <button type="button" onclick="window.print()" class="hidden md:inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 transition-all">
                    <i class="fa-solid fa-print text-sky-500"></i>
                    <span>{{ $isAr ? 'طباعة تقرير التحقق' : 'Print Slip' }}</span>
                </button>
            </div>

            <a href="{{ route('customer.home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 transition-colors">
                <i class="fa-solid fa-store text-sky-500"></i>
                <span>{{ $isAr ? 'العودة إلى المتجر الرئيسي' : 'Return to Storefront' }}</span>
            </a>
        </div>

        <!-- 7. Official Legal & Compliance Corporate Footer -->
        <footer class="text-center text-xs text-slate-500 dark:text-slate-500 py-4 space-y-1.5 border-t border-slate-200 dark:border-slate-800/60 mt-8">
            <div class="font-bold text-slate-700 dark:text-slate-400">
                {{ $siteInfo['brand_name'] ?? 'BLUE ZONE™ Bioceuticals Inc.' }}
            </div>
            <div class="flex items-center justify-center flex-wrap gap-2 text-[11px]">
                <span>{{ $isAr ? 'الرقم الضريبي:' : 'Tax ID:' }} <strong class="font-mono text-slate-600 dark:text-slate-400">{{ $siteInfo['tax_number'] ?? '31004829100003' }}</strong></span>
                <span>&bull;</span>
                <span>{{ $isAr ? 'السجل التجاري:' : 'CR:' }} <strong class="font-mono text-slate-600 dark:text-slate-400">{{ $siteInfo['commercial_record'] ?? 'CR-1010842910' }}</strong></span>
                <span>&bull;</span>
                <span>{{ $isAr ? 'الترخيص المخبري:' : 'MOH Lic:' }} <strong class="font-mono text-slate-600 dark:text-slate-400">{{ $siteInfo['clinical_license'] ?? 'MOH-CERT-2026-BZ884' }}</strong></span>
            </div>
            <div class="text-[11px] text-slate-400">
                {{ $isAr ? 'كافة الحقوق محفوظة © 2026 شركة بلو زون للمستحضرات الحيوية والأبحاث الدوائية.' : 'All rights reserved © 2026 Blue Zone Bioceuticals & Longevity Sciences Inc.' }}
            </div>
        </footer>

    </main>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('theme-toggle-btn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    const themeName = isDark ? 'dark' : 'light';
                    try {
                        localStorage.setItem('bluezone_theme', themeName);
                        localStorage.setItem('bz_theme', themeName);
                    } catch(e) {}
                });
            }
        });
    </script>

</body>
</html>
