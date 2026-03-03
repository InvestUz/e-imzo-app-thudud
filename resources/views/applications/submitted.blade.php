<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ariza qabul qilindi — {{ $application->number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f2f5; min-height: 100vh; display: flex; flex-direction: column;
        }
        .site-header {
            background: linear-gradient(135deg, #018c87 0%, #00bfaf 100%);
            padding: 14px 32px; display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0,0,0,.18);
        }
        .site-header .brand { color: #fff; font-size: 1.25rem; font-weight: 700; text-decoration: none; }
        .main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 16px; }
        .success-card {
            background: #fff; border-radius: 20px; padding: 48px 40px;
            text-align: center; max-width: 520px; width: 100%;
            box-shadow: 0 4px 32px rgba(0,0,0,.08);
        }
        .success-icon {
            width: 72px; height: 72px; background: #d1e7dd; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; margin: 0 auto 20px;
        }
        .success-card h2 { font-size: 1.4rem; font-weight: 700; color: #1a2d5a; margin-bottom: 8px; }
        .success-card p { color: #5a6a8a; margin-bottom: 0; }
        .number-box {
            margin: 24px 0; padding: 16px 24px; background: #f5f8ff;
            border: 2px dashed #018c87; border-radius: 12px;
        }
        .number-label { font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; color: #8a9ab8; margin-bottom: 4px; }
        .number-value { font-size: 1.6rem; font-weight: 800; color: #018c87; font-family: monospace; }
        .info-rows { text-align: left; margin: 20px 0; }
        .info-row { display: flex; gap: 12px; padding: 8px 0; border-bottom: 1px solid #f0f2f5; font-size: .88rem; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #8a9ab8; min-width: 130px; }
        .info-value { font-weight: 600; color: #2c3e60; }
        .steps-list { text-align: left; margin: 20px 0; }
        .step-item {
            display: flex; gap: 12px; align-items: flex-start;
            padding: 8px 0; font-size: .875rem; color: #5a6a8a;
        }
        .step-num {
            width: 22px; height: 22px; border-radius: 50%; background: #e8f0fe;
            color: #018c87; font-size: .75rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;
        }
        .btn-home {
            display: inline-block; padding: 12px 32px; margin-top: 8px;
            background: linear-gradient(135deg, #018c87 0%, #00bfaf 100%);
            color: #fff; border-radius: 10px; font-weight: 700; text-decoration: none;
            transition: all .2s;
        }
        .btn-home:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(45,98,184,.3); }
        .site-footer { text-align: center; padding: 20px; font-size: .8rem; color: #8a9ab8; }
    </style>
</head>
<body>
<header class="site-header">
    <a href="{{ route('home') }}" class="brand">Qo'shni hudud — VM 478</a>
</header>

<main class="main">
    <div class="success-card">
        <div class="success-icon">✓</div>
        <h2>Arizangiz qabul qilindi!</h2>
        <p>Ariza muvaffaqiyatli ro'yxatdan o'tkazildi va ko'rib chiqish jarayoni boshlandi.</p>

        <div class="number-box">
            <div class="number-label">Ariza raqami</div>
            <div class="number-value">{{ $application->number }}</div>
        </div>

        <div class="info-rows">
            <div class="info-row">
                <span class="info-label">Kadastr raqami</span>
                <span class="info-value">{{ $application->cadastral_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tuman</span>
                <span class="info-value">{{ $application->district->name_uz }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ariza beruvchi</span>
                <span class="info-value">{{ $application->applicant->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Yuborilgan vaqt</span>
                <span class="info-value">
                    {{ $application->submitted_at?->setTimezone(config('app.timezone'))->format('d.m.Y H:i') }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Holati</span>
                <span class="info-value" style="color:#0d6efd">Moderator ko'rib chiqmoqda</span>
            </div>
        </div>

        <div class="steps-list">
            <div class="mb-2" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#8a9ab8">Ko'rib chiqish bosqichlari</div>
            @foreach(\App\Models\ApplicationApproval::ROLE_LABELS as $key => $label)
            <div class="step-item">
                <div class="step-num">{{ $loop->iteration }}</div>
                <div>
                    {{ $label }}
                    @if($loop->first)<span style="color:#0d6efd;font-size:.78rem"> ← Hozirgi bosqich</span>@endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-muted small mb-3">
            Bu ariza raqamini saqlang — holat haqida ma'lumot olish uchun kerak bo'ladi.
        </div>

        {{-- Login to see all applications --}}
        <div style="background:#f5f8ff;border-radius:12px;padding:14px 18px;margin-bottom:16px;border:1px solid #d8e6fe;text-align:left">
            <div style="font-size:.82rem;font-weight:700;color:#1a2d5a;margin-bottom:3px">Barcha arizalaringizni ko'rmoqchimisiz?</div>
            <div style="font-size:.78rem;color:#5a6a8a;margin-bottom:10px">E-IMZO bilan kiring — barcha arizalaringiz avtomatik ko'rsatiladi.</div>
            <a href="{{ route('login') }}"
                style="display:inline-flex;align-items:center;gap:5px;padding:7px 16px;background:#018c87;color:#fff;border-radius:8px;font-size:.82rem;font-weight:600;text-decoration:none">
                🔑 E-IMZO bilan kirish
            </a>
        </div>

        <a href="{{ route('apply.track', $application->number) }}" class="btn-home" style="background:transparent;border:2px solid #018c87;color:#018c87;margin-right:8px">Ariza holatini ko'rish</a>
        <a href="{{ route('home') }}" class="btn-home">Bosh sahifaga qaytish</a>
    </div>
</main>

<footer class="site-footer">
    © {{ date('Y') }} Qo'shni hudud tizimi
</footer>
</body>
</html>
