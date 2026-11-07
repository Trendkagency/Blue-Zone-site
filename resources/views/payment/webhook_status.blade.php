<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ strtoupper($gateway) }} Payment Webhook Endpoint — BZ-OS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,600,700,900|inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --bg-body: #031827;
            --bg-card: #062B49;
            --border-color: rgba(255, 255, 255, 0.1);
            --primary: #0A4F78;
            --accent: #2A8FC2;
            --emerald: #10B981;
            --text-main: #FFFFFF;
            --text-muted: #94A3B8;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', 'Cairo', sans-serif;
            background: radial-gradient(circle at 50% 20%, #0A4F78 0%, #062B49 45%, #031827 100%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .container {
            width: 100%;
            max-width: 620px;
            background: rgba(6, 43, 73, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 1.75rem;
            padding: 2.5rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
            position: relative;
            overflow: hidden;
        }
        .top-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0A4F78, #2A8FC2, #10B981);
        }
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: rgba(16, 185, 129, 0.15);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            margin-bottom: 1rem;
        }
        .dot-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 10px #10B981;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }
        h1 {
            font-size: 1.65rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        p.subtitle {
            color: var(--text-muted);
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 2rem;
        }
        .spec-box {
            background: rgba(2, 24, 39, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1.75rem;
            font-size: 0.825rem;
        }
        .spec-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .spec-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .spec-label {
            color: var(--text-muted);
            font-weight: 600;
        }
        .spec-value {
            font-weight: 700;
            font-family: ui-monospace, monospace;
            color: #E2E8F0;
        }
        .method-pill {
            background: #0A4F78;
            color: #fff;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 800;
            font-family: monospace;
        }
        .notice-card {
            background: rgba(42, 143, 194, 0.1);
            border: 1px solid rgba(42, 143, 194, 0.25);
            border-radius: 0.875rem;
            padding: 1rem 1.25rem;
            font-size: 0.8rem;
            color: #BAE6FD;
            line-height: 1.5;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            margin-bottom: 2rem;
        }
        .btn-return {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.85rem 1.5rem;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, #0A4F78, #2A8FC2);
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 800;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 20px rgba(10, 79, 120, 0.4);
        }
        .btn-return:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(42, 143, 194, 0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-accent"></div>
        
        <div class="badge-status">
            <span class="dot-pulse"></span>
            <span>Webhook Listener Active</span>
        </div>

        <h1>
            <i class="fa-solid fa-satellite-dish text-sky-400" style="color: #38BDF8;"></i>
            <span>{{ strtoupper($gateway) }} Payment Webhook</span>
        </h1>

        <p class="subtitle">
            The Blue Zone BZ-OS webhook notification listener for gateway <strong>{{ strtoupper($gateway) }}</strong> is fully operational and waiting for incoming payment event dispatches.
        </p>

        <div class="spec-box">
            <div class="spec-row">
                <span class="spec-label">Gateway Driver</span>
                <span class="spec-value">{{ strtoupper($gateway) }}</span>
            </div>
            <div class="spec-row">
                <span class="spec-label">Accepted HTTP Methods</span>
                <div>
                    <span class="method-pill">POST</span>
                </div>
            </div>
            <div class="spec-row">
                <span class="spec-label">CSRF Protection</span>
                <span class="spec-value" style="color: #34D399;">Excluded (Signature Verified)</span>
            </div>
            <div class="spec-row">
                <span class="spec-label">Webhook URL</span>
                <span class="spec-value" style="font-size: 0.72rem; word-break: break-all;">{{ $endpoint }}</span>
            </div>
            <div class="spec-row">
                <span class="spec-label">System Time</span>
                <span class="spec-value">{{ $timestamp->format('Y-m-d H:i:s T') }}</span>
            </div>
        </div>

        <div class="notice-card">
            <i class="fa-solid fa-circle-info" style="font-size: 1.1rem; margin-top: 0.1rem; color: #38BDF8;"></i>
            <div>
                <strong>Integration Note:</strong> This URL is intended to be registered in your {{ ucfirst($gateway) }} Developer Dashboard under <em>Webhooks</em>. Real payment events (e.g., <code>checkout.session.completed</code>, <code>charge.refunded</code>) must be posted over HTTP POST with the proper signature header.
            </div>
        </div>

        <a href="{{ route('admin.settings.index') }}" class="btn-return">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Return to Admin Settings</span>
        </a>
    </div>
</body>
</html>
