<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dalolatnoma tasdiqlash — TUTASH HUDUDLAR REESTRI</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f4f5;
            min-height: 100vh;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 24px 16px;
        }

        .verify-wrap { width: 100%; max-width: 560px; }

        /* Header bar */
        .verify-brand {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px; text-decoration: none;
        }
        .verify-brand-icon {
            width: 44px; height: 44px; border-radius: 11px;
            background: linear-gradient(135deg, #018c87, #00bfaf);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: 900; color: #fff;
            box-shadow: 0 4px 12px rgba(1,140,135,.3);
        }
        .verify-brand-text strong { display: block; font-size: .78rem; font-weight: 800;
            color: #018c87; text-transform: uppercase; letter-spacing: .05em; }
        .verify-brand-text span { font-size: .72rem; color: #6e788b; }

        /* Card */
        .verify-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* Card header strip */
        .verify-card-head {
            background: linear-gradient(135deg, #018c87, #00bfaf);
            color: #fff; padding: 22px 28px 20px;
            display: flex; align-items: center; gap: 14px;
        }
        .verify-check-icon {
            width: 52px; height: 52px; border-radius: 50%;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; flex-shrink: 0;
        }
        .verify-head-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 3px; }
        .verify-head-sub   { font-size: .82rem; opacity: .85; }

        /* Body */
        .verify-body { padding: 24px 28px; }

        /* Info rows */
        .vi-row {
            display: flex; align-items: baseline; gap: 10px;
            padding: 9px 0; border-bottom: 1px solid #f0f2f5;
        }
        .vi-row:last-child { border-bottom: none; }
        .vi-label  { color: #6e788b; font-size: .82rem; min-width: 160px; flex-shrink: 0; }
        .vi-value  { font-weight: 600; color: #15191e; font-size: .9rem; }
        .vi-value-teal { color: #018c87; }

        /* QR block */
        .qr-block {
            margin-top: 20px; padding: 18px;
            background: #f8fffe;
            border: 1px solid #b2dfdb;
            border-radius: 12px;
            display: flex; align-items: center; gap: 18px;
        }
        .qr-block img { border-radius: 8px; border: 1px solid #e0e5ea; flex-shrink: 0; }
        .qr-info-title { font-weight: 700; color: #015c59; font-size: .88rem; margin-bottom: 5px; }
        .qr-info-url   { font-size: .72rem; color: #6e788b; word-break: break-all; }

        /* Footer */
        .verify-footer {
            padding: 14px 28px; background: #fafafa;
            border-top: 1px solid #f0f2f5;
            font-size: .72rem; color: #aab0bb; text-align: center;
            text-transform: uppercase; letter-spacing: .08em;
        }

        .back-link {
            display: inline-block; margin-top: 18px; font-size: .85rem;
            color: #018c87; text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="verify-wrap">

    {{-- Brand --}}
    <a href="{{ route('home') }}" class="verify-brand">
        <div class="verify-brand-icon">T</div>
        <div class="verify-brand-text">
            <strong>TUTASH HUDUDLAR REESTRI</strong>
            <span>Dalolatnoma tasdiqlash tizimi</span>
        </div>
    </a>

    <div class="verify-card">

        {{-- Header --}}
        <div class="verify-card-head">
            <div class="verify-check-icon">✓</div>
            <div>
                <div class="verify-head-title">Imzo tasdiqlandi</div>
                <div class="verify-head-sub">
                    E-IMZO orqali imzolangan dalolatnoma haqiqiy hisoblanadi
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="verify-body">

            <div class="vi-row">
                <span class="vi-label">Ariza raqami</span>
                <span class="vi-value vi-value-teal">{{ $sig->application->number }}</span>
            </div>
            <div class="vi-row">
                <span class="vi-label">Tuman</span>
                <span class="vi-value">{{ $sig->application->district->name_uz ?? '—' }}</span>
            </div>
            <div class="vi-row">
                <span class="vi-label">Ariza beruvchi</span>
                <span class="vi-value">{{ $sig->application->applicant->name ?? '—' }}</span>
            </div>
            <div class="vi-row">
                <span class="vi-label">Komissiya lavozimi</span>
                <span class="vi-value">{{ $sig->positionLabel() }}</span>
            </div>
            <div class="vi-row">
                <span class="vi-label">Imzolovchi</span>
                <span class="vi-value">{{ $sig->signer->name ?? '—' }}</span>
            </div>
            <div class="vi-row">
                <span class="vi-label">Imzo sanasi</span>
                <span class="vi-value">
                    {{ $sig->signed_at->setTimezone(config('app.timezone', 'Asia/Tashkent'))->format('d.m.Y H:i') }}
                </span>
            </div>
            <div class="vi-row">
                <span class="vi-label">QR kod</span>
                <span class="vi-value" style="font-size:.78rem;font-family:monospace;color:#6e788b;">
                    {{ $sig->qr_code }}
                </span>
            </div>

            {{-- QR Code image + URL --}}
            <div class="qr-block">
                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($sig->getVerifyUrl()) }}&size=110x110&margin=6"
                     width="110" height="110" alt="QR kod">
                <div>
                    <div class="qr-info-title">Tasdiqlash sahifasi</div>
                    <div class="qr-info-url">{{ $sig->getVerifyUrl() }}</div>
                </div>
            </div>

        </div>

        <div class="verify-footer">
            VM №478 &nbsp;·&nbsp; {{ date('Y') }} &nbsp;·&nbsp; Elektron tizim orqali tasdiqlandi
        </div>
    </div>

    <a href="{{ route('home') }}" class="back-link">← Bosh sahifaga qaytish</a>

</div>
</body>
</html>
