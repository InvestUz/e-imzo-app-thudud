<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mening arizalarim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f2f5; min-height: 100vh; display: flex; flex-direction: column;
        }

        /* ─── Header ─── */
        .site-header {
            background: #003399;
            padding: 0 28px; height: 58px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }
        .brand { color: #fff; font-size: 1.1rem; font-weight: 700; text-decoration: none; }
        .user-pill {
            display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,.9);
            font-size: .85rem;
        }
        .user-name { font-weight: 600; }
        .btn-logout {
            border: 1.5px solid rgba(255,255,255,.35); background: transparent; color: #fff;
            padding: 5px 14px; border-radius: 7px; font-size: .82rem; cursor: pointer;
            transition: all .2s;
        }
        .btn-logout:hover { background: rgba(255,255,255,.15); border-color: #fff; }

        /* ─── Main ─── */
        .main { flex: 1; padding: 32px 16px; }
        .wrap { max-width: 800px; margin: 0 auto; }

        /* ─── Page title ─── */
        .page-title { font-size: 1.4rem; font-weight: 700; color: #1a2d5a; margin-bottom: 4px; }
        .page-sub   { color: #8a9ab8; font-size: .88rem; }

        /* ─── New application button ─── */
        .btn-new {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 20px; background: #003399; color: #fff;
            border-radius: 10px; text-decoration: none; font-size: .9rem; font-weight: 600;
            transition: all .2s;
        }
        .btn-new:hover { background: #002266; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,51,153,.25); }

        /* ─── Application cards ─── */
        .app-card {
            background: #fff; border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            padding: 18px 22px; margin-bottom: 14px;
            transition: box-shadow .2s, transform .2s;
            display: block; text-decoration: none; color: inherit;
        }
        .app-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.1); transform: translateY(-2px); color: inherit; }
        .app-card-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; }
        .app-num  { font-family: monospace; font-size: 1rem; font-weight: 800; color: #003399; }
        .app-date { font-size: .78rem; color: #8a9ab8; }
        .app-meta { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 8px; }
        .app-meta-item { font-size: .83rem; color: #5a6a8a; }
        .app-meta-item strong { color: #1a2d5a; }

        /* ─── Status badge ─── */
        .sbadge {
            display: inline-block; padding: 3px 12px; border-radius: 20px;
            font-size: .74rem; font-weight: 700; white-space: nowrap;
        }
        .s-moderator_review { background: rgba(1,140,135,.1); color: #018c87; }
        .s-complaint_review { background: #fff3cd; color: #856404; }
        .s-lawyer_review    { background: #e2d9f3; color: #5a189a; }
        .s-executor_review  { background: #cff4fc; color: #055160; }
        .s-head_review      { background: #d1e7dd; color: #0a3622; }
        .s-approved         { background: #d1e7dd; color: #0a3622; }
        .s-rejected         { background: #f8d7da; color: #58151c; }
        .s-pending          { background: #f0f2f5; color: #5a6a8a; }

        /* ─── Progress bar ─── */
        .progress-row { margin-top: 12px; display: flex; gap: 4px; }
        .step-pip {
            flex: 1; height: 4px; border-radius: 3px; background: #e8ecf1;
        }
        .step-pip.done    { background: #198754; }
        .step-pip.current { background: #003399; }
        .step-pip.rejected-pip { background: #dc3545; }

        /* ─── Empty state ─── */
        .empty-state {
            text-align: center; padding: 60px 24px;
            background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        .empty-icon { font-size: 3rem; margin-bottom: 12px; }
        .empty-state h5 { color: #1a2d5a; font-weight: 700; }
        .empty-state p  { color: #8a9ab8; font-size: .9rem; }

        .site-footer { text-align: center; padding: 20px; font-size: .8rem; color: #8a9ab8; }
    </style>
</head>
<body>

<header class="site-header">
    <a href="{{ route('home') }}" class="brand">Qo'shni hudud — VM 478</a>
    <div class="user-pill">
        <span class="user-name">{{ $user->name }}</span>
        @if($user->pinfl)
        <span style="opacity:.6;font-size:.75rem">PINFL: {{ $user->pinfl }}</span>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout">Chiqish</button>
        </form>
    </div>
</header>

<main class="main">
    <div class="wrap">

        <div class="d-flex align-items-start justify-content-between mb-4 gap-3 flex-wrap">
            <div>
                <div class="page-title">Mening arizalarim</div>
                <div class="page-sub">E-IMZO orqali tasdiqlangan arizalaringiz ro'yxati</div>
            </div>
            <a href="{{ route('home') }}" class="btn-new">+ Yangi ariza</a>
        </div>

        @if($applications->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <h5>Arizalar topilmadi</h5>
            <p>Siz hali hech qanday ariza topshirmadingiz.</p>
            <a href="{{ route('home') }}" class="btn-new mt-3">+ Yangi ariza berish</a>
        </div>
        @else
            @php
                $statusLabels = [
                    'pending'           => ['Kutilmoqda',                   's-pending'],
                    'moderator_review'  => ["Moderator ko'rib chiqmoqda",   's-moderator_review'],
                    'complaint_review'  => ["Shikoyat bo'limi",             's-complaint_review'],
                    'lawyer_review'     => ["Yurist ko'rib chiqmoqda",      's-lawyer_review'],
                    'executor_review'   => ["Ijrochi ko'rib chiqmoqda",     's-executor_review'],
                    'head_review'       => ['Boshqarma rahbari',            's-head_review'],
                    'approved'          => ['Tasdiqlandi ✓',                's-approved'],
                    'rejected'          => ['Rad etildi ✗',                 's-rejected'],
                ];
                $totalSteps = count(\App\Models\Application::STEPS);
            @endphp

            @foreach($applications as $app)
            @php
                $s = $statusLabels[$app->status] ?? ["Noma'lum", 's-pending'];
                $approvedCount = $app->approvals->where('status', 'approved')->count();
                $hasRejected   = $app->approvals->contains('status', 'rejected');
            @endphp
            <a href="{{ route('apply.track', $app->number) }}" class="app-card">
                <div class="app-card-head">
                    <div>
                        <div class="app-num">{{ $app->number }}</div>
                        <div class="app-date">{{ $app->submitted_at?->format('d.m.Y H:i') }}</div>
                    </div>
                    <span class="sbadge {{ $s[1] }}">{{ $s[0] }}</span>
                </div>
                <div class="app-meta">
                    <div class="app-meta-item">📍 <strong>{{ $app->district->name_uz }}</strong></div>
                    <div class="app-meta-item">🏷 Kadastr: <strong>{{ $app->cadastral_number }}</strong></div>
                    @if($app->area_sqm)
                    <div class="app-meta-item">📐 {{ number_format($app->area_sqm, 2) }} m²</div>
                    @endif
                </div>
                {{-- 5-step progress pips --}}
                <div class="progress-row">
                    @for($i = 1; $i <= $totalSteps; $i++)
                    @php
                        $pip = $app->approvals->firstWhere('step_order', $i);
                        $pipClass = match($pip?->status ?? 'waiting') {
                            'approved' => 'done',
                            'rejected' => 'rejected-pip',
                            'pending'  => 'current',
                            default    => '',
                        };
                    @endphp
                    <div class="step-pip {{ $pipClass }}"></div>
                    @endfor
                </div>
                <div style="font-size:.75rem;color:#8a9ab8;margin-top:5px">
                    {{ $approvedCount }}/{{ $totalSteps }} bosqich yakunlandi &nbsp;·&nbsp; Batafsil ko'rish →
                </div>
            </a>
            @endforeach

            {{ $applications->links() }}
        @endif

    </div>
</main>

<footer class="site-footer">
    © {{ date('Y') }} Qo'shni hudud tizimi &nbsp;·&nbsp; Vazirlar Mahkamasi Qarori №478
</footer>

</body>
</html>
