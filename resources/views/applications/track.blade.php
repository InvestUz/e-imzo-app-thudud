<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Dalolatnoma — {{ $application->number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Header ── */
        .site-header {
            background: #018c87; height: 54px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; box-shadow: 0 2px 8px rgba(0,0,0,.18);
        }
        .brand { color: #fff; font-size: 1rem; font-weight: 700; text-decoration: none; }
        .btn-back {
            color: #fff; border: 1.5px solid rgba(255,255,255,.4); background: transparent;
            padding: 5px 16px; border-radius: 7px; font-size: .82rem; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
            transition: all .2s;
        }
        .btn-back:hover { background: rgba(255,255,255,.15); color: #fff; }

        /* ── Content back link ── */
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            color: #64748b; font-size: .82rem; font-weight: 600; text-decoration: none;
            margin-bottom: 14px; padding: 6px 14px 6px 10px;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            transition: all .18s;
        }
        .back-link:hover { border-color: #018c87; color: #018c87; background: #f3fafa; }

        /* ── Status ribbon ── */
        .status-ribbon {
            background: #fff; border-bottom: 1px solid #e8ecf1;
            padding: 10px 28px; display: flex; align-items: center;
            gap: 16px; flex-wrap: wrap;
        }
        .ribbon-num { font-family: monospace; font-size: 1rem; font-weight: 800; color: #018c87; }
        .ribbon-sep { color: #c8d0e0; }
        .ribbon-date { font-size: .82rem; color: #8a9ab8; }
        .ribbon-spacer { flex: 1; }
        .sbadge {
            display: inline-block; padding: 4px 14px; border-radius: 20px;
            font-size: .75rem; font-weight: 700;
        }
        .s-moderator_review { background: rgba(1,140,135,.1); color: #018c87; }
        .s-complaint_review { background: #fff3cd; color: #856404; }
        .s-lawyer_review    { background: #e2d9f3; color: #5a189a; }
        .s-executor_review  { background: #cff4fc; color: #055160; }
        .s-head_review      { background: #d1e7dd; color: #0a3622; }
        .s-approved         { background: #d1e7dd; color: #0a3622; }
        .s-rejected         { background: #f8d7da; color: #58151c; }
        .s-pending          { background: #f0f2f5; color: #5a6a8a; }

        /* ── Step pips ── */
        .pip-row { display: flex; gap: 4px; align-items: center; }
        .pip { width: 28px; height: 5px; border-radius: 3px; background: #e8ecf1; }
        .pip.done    { background: #198754; }
        .pip.current { background: #018c87; }
        .pip.rej     { background: #dc3545; }

        /* ── Main wrap ── */
        .main { flex: 1; padding: 24px 16px; display: flex; justify-content: center; }
        .wrap { width: 100%; max-width: 880px; }

        /* ── Document card ── */
        .dalo-doc {
            background: #fff;
            border-top: 4px solid #009AB6;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 28px rgba(0,0,0,.09);
            padding: 36px 40px 28px;
            margin-bottom: 20px;
        }

        /* ── Doc header ── */
        .dalo-head { text-align: center; margin-bottom: 20px; }
        .dalo-emblem { height: 60px; margin-bottom: 10px; }
        .dalo-title {
            font-family: 'Merriweather', serif;
            font-size: 1.15rem; font-weight: 700; text-transform: uppercase;
            color: #009AB6; letter-spacing: .06em; margin-bottom: 4px;
        }
        .dalo-subtitle { font-size: .8rem; font-weight: 600; color: #64748b; text-transform: uppercase; }
        .dalo-meta {
            display: flex; justify-content: space-between;
            margin-top: 14px; font-size: .82rem; font-weight: 500; color: #475569;
        }

        /* ── Intro paragraph ── */
        .dalo-intro {
            font-size: .78rem; color: #64748b; text-align: justify;
            line-height: 1.7; margin-bottom: 20px;
        }
        .dalo-intro strong { color: #1e3a8a; border-bottom: 1px solid #93c5fd; }

        /* ── Two-column body ── */
        .dalo-body { display: grid; grid-template-columns: 7fr 5fr; gap: 24px; }

        /* ── Section blocks ── */
        .dalo-section {
            border: 1px solid #e2e8f0; border-radius: 6px;
            padding: 14px 16px; background: #f8fafc; margin-bottom: 14px;
        }
        .dalo-section:last-child { margin-bottom: 0; }
        .sec-title {
            font-size: .72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .05em; color: #009AB6;
            border-bottom: 1px solid #e2e8f0; padding-bottom: 7px; margin-bottom: 10px;
        }

        /* ── Field rows ── */
        .field-row {
            display: flex; justify-content: space-between; align-items: flex-end;
            gap: 8px; font-size: .82rem; color: #334155;
            margin-bottom: 8px;
        }
        .field-row:last-child { margin-bottom: 0; }
        .field-lbl { flex-shrink: 0; color: #475569; }
        .field-val {
            border-bottom: 1px solid #cbd5e1; padding-bottom: 2px;
            color: #1e3a8a; font-weight: 500; text-align: right;
            min-width: 120px; font-size: .82rem;
        }
        .field-val.empty { color: #94a3b8; font-weight: 400; }

        .field-block { margin-bottom: 10px; }
        .field-block:last-child { margin-bottom: 0; }
        .field-block .fb-lbl { font-size: .78rem; font-weight: 600; color: #334155; margin-bottom: 3px; }
        .field-block .fb-val {
            border-bottom: 1px solid #cbd5e1; padding: 3px 0 2px;
            color: #1e3a8a; font-weight: 500; font-size: .82rem; min-height: 22px;
        }
        .field-block .fb-val.empty { color: #94a3b8; font-weight: 400; }

        .field-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* ── Section III applicant ── */
        .sig-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 0; border-top: 1px solid #e2e8f0; margin-top: 12px; padding-top: 10px;
        }
        .sig-cell { }
        .sig-cell:last-child { text-align: right; }
        .sig-lbl { color: #94a3b8; font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
        .sig-name { font-weight: 700; color: #0f172a; font-size: .88rem; line-height: 1.3; border-bottom: 1px solid #334155; padding-bottom: 4px; display: inline-block; }
        .sig-date { font-weight: 700; color: #0f172a; font-size: .88rem; border-bottom: 1px solid #334155; padding-bottom: 4px; display: inline-block; }

        /* E-IMZO verification stamp */
        .eimzo-stamp {
            margin-top: 12px;
            display: flex; align-items: center; gap: 10px;
        }
        .stamp-qr {
            width: 90px; height: 90px; border-radius: 6px;
            box-shadow: 0 1px 6px rgba(1,140,135,.2); flex-shrink: 0;
        }
        .stamp-badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: .75rem; font-weight: 700; color: #0d9488;
        }
        .stamp-badge-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #10b981; flex-shrink: 0;
        }

        /* ── Commission right column ── */
        .comm-col { }
        .comm-title {
            font-size: .72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: #009AB6; text-align: center; margin-bottom: 14px;
        }
        .comm-slot { margin-bottom: 14px; }
        .comm-pos { font-size: .78rem; font-weight: 700; color: #334155; margin-bottom: 4px; }
        .comm-line {
            display: flex; justify-content: space-between; align-items: flex-end;
            border-bottom: 1px solid #cbd5e1; padding-bottom: 3px;
        }
        .comm-signed { display: flex; align-items: flex-start; gap: 8px; }
        .comm-signed-info { }
        .comm-signer-name { font-size: .75rem; font-weight: 700; color: #15803d; }
        .comm-signer-date { font-size: .68rem; color: #64748b; }
        .comm-waiting { font-size: .73rem; color: #94a3b8; font-style: italic; }
        .comm-qr { width: 44px; height: 44px; object-fit: contain; border-radius: 4px; }

        /* ── Timeline card ── */
        .timeline-card {
            background: #fff; border-radius: 12px; padding: 20px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 20px;
        }
        .tl-title-row { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
        .tl-title-row::after { content:''; flex:1; height:1px; background:#f0f2f5; }
        .timeline { display: flex; gap: 0; overflow-x: auto; padding-bottom: 4px; }
        .tl-step { flex: 1; min-width: 100px; position: relative; text-align: center; padding: 0 6px; }
        .tl-step::before { content:''; position:absolute; top:13px; left:-50%; width:100%; height:2px; background:#e8ecf1; z-index:0; }
        .tl-step:first-child::before { display:none; }
        .tl-dot2 {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 700; margin: 0 auto 6px; position: relative; z-index: 1;
        }
        .tl-dot2.approved { background: #198754; color: #fff; }
        .tl-dot2.pending  { background: #018c87; color: #fff; box-shadow: 0 0 0 4px rgba(1,140,135,.18); }
        .tl-dot2.waiting  { background: #e8ecf1; color: #94a3b8; border: 2px solid #d0d8e4; }
        .tl-dot2.rejected { background: #dc3545; color: #fff; }
        .tl-step-lbl { font-size: .68rem; color: #64748b; line-height: 1.3; }
        .tl-step-lbl.active { color: #018c87; font-weight: 700; }

        /* ── Search again ── */
        .search-again { background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .sa-lbl { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 10px; }
        .sa-row { display: flex; gap: 8px; }
        .sa-row input { flex:1; border:1.5px solid #dde3ee; border-radius:8px; padding:7px 12px; font-size:.88rem; outline:none; }
        .sa-row input:focus { border-color:#018c87; box-shadow:0 0 0 3px rgba(1,140,135,.12); }
        .sa-row button { padding:7px 18px; background:#018c87; color:#fff; border:none; border-radius:8px; font-size:.88rem; font-weight:600; cursor:pointer; white-space:nowrap; transition:background .15s; }
        .sa-row button:hover { background:#017570; }

        /* ── Footer ── */
        .doc-footer { text-align: center; font-size: .68rem; color: #94a3b8; text-transform: uppercase; letter-spacing: .08em; margin-top: 20px; padding-top: 14px; border-top: 1px solid #f0f2f5; }
        .site-footer { text-align: center; padding: 16px; font-size: .78rem; color: #8a9ab8; }

        /* ── Responsive ── */
        @media (max-width: 700px) {
            .dalo-doc { padding: 20px 16px 18px; }
            .dalo-body { grid-template-columns: 1fr; }
            .comm-col { border-top: 1px solid #e2e8f0; padding-top: 16px; margin-top: 8px; }
            .status-ribbon { padding: 10px 16px; }
            .site-header { padding: 0 16px; }
        }
    </style>
</head>
<body>

<header class="site-header">
    <a href="{{ route('home') }}" class="brand">TUTASH HUDUDLAR — VM 478</a>
    @auth
        @if(auth()->user()->isConsumer())
            <a href="{{ route('my-applications') }}" class="btn-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Arizalarim
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="btn-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Boshqaruv paneli
            </a>
        @endif
    @else
        <a href="{{ route('home') }}" class="btn-back">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Bosh sahifa
        </a>
    @endauth
</header>

@php
    $statusMap = [
        'pending'           => ['Kutilmoqda',                   's-pending'],
        'moderator_review'  => ["Moderator ko'rib chiqmoqda",   's-moderator_review'],
        'complaint_review'  => ["Shikoyat bo'limi",             's-complaint_review'],
        'lawyer_review'     => ["Yurist ko'rib chiqmoqda",      's-lawyer_review'],
        'executor_review'   => ["Ijrochi ko'rib chiqmoqda",     's-executor_review'],
        'head_review'       => ['Boshqarma rahbari',            's-head_review'],
        'approved'          => ['Tasdiqlandi ✓',                's-approved'],
        'rejected'          => ['Rad etildi ✗',                 's-rejected'],
    ];
    $s = $statusMap[$application->status] ?? ["Noma'lum", 's-pending'];
    $totalSteps = count(\App\Models\Application::STEPS);
    $fd = is_array($application->form_data) ? $application->form_data : (json_decode($application->form_data, true) ?? []);
@endphp

{{-- Status ribbon --}}
<div class="status-ribbon">
    <span class="ribbon-num">{{ $application->number }}</span>
    <span class="ribbon-sep">·</span>
    <span class="ribbon-date">{{ $application->submitted_at?->format('d.m.Y H:i') }}</span>
    <span class="ribbon-sep">·</span>
    <span class="ribbon-date">{{ $application->district->name_uz }}</span>
    <span class="ribbon-spacer"></span>
    {{-- pips --}}
    <div class="pip-row">
        @foreach($application->approvals->sortBy('step_order') as $ap)
        @php
            $pc = match($ap->status) {
                'approved' => 'done',
                'rejected' => 'rej',
                'pending'  => 'current',
                default    => '',
            };
        @endphp
        <div class="pip {{ $pc }}"></div>
        @endforeach
    </div>
    <span class="sbadge {{ $s[1] }}">{{ $s[0] }}</span>
</div>

<main class="main">
<div class="wrap">

    {{-- Back link above document --}}
    @auth
        @if(auth()->user()->isConsumer())
        <a href="{{ route('my-applications') }}" class="back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Mening arizalarim
        </a>
        @endif
    @endauth

    {{-- ══════ Official Dalolatnoma Document ══════ --}}
    <div class="dalo-doc">

        {{-- Document header --}}
        <div class="dalo-head">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/77/Emblem_of_Uzbekistan.svg" alt="Gerb" class="dalo-emblem">
            <div class="dalo-title">Dalolatnoma</div>
            <div class="dalo-subtitle">Tadbirkorlik subyektlariga tutash hududlarni xatlovdan o'tkazish to'g'risida</div>
            <div class="dalo-meta">
                <span>Toshkent shahri</span>
                <span>{{ $application->submitted_at?->format('Y') ?? date('Y') }}-yil «{{ $application->submitted_at?->format('d') ?? '___' }}» {{ $application->submitted_at ? \Carbon\Carbon::parse($application->submitted_at)->translatedFormat('F') : '____________' }}</span>
            </div>
        </div>

        {{-- Intro paragraph --}}
        <p class="dalo-intro">
            Tuzildi mazkur dalolatnoma Toshkent shahri,
            <strong>{{ $application->district->name_uz }}</strong> tumani,
            <strong>{{ $fd['street_name'] ?? '__________________' }}</strong>
            ko'chasida joylashgan umumiy ovqatlanish, savdo va xizmat ko'rsatish sohasidagi tadbirkorlik
            subyektlari uchun o'ziga tegishli bino va inshootlarga hamda yer uchastkalariga tutash bo'lgan,
            davlat organlari va tashkilotlariga doimiy foydalanishga berilgan aholi punktlarining umumiy
            foydalanishdagi yer uchastkalarini aniqlash bo'yicha.
        </p>

        <div class="dalo-body">

            {{-- ──── LEFT column ──── --}}
            <div>

                {{-- Section I --}}
                <div class="dalo-section">
                    <div class="sec-title">I. Xatlov o'tkazilgan ko'chaning tasnifi</div>

                    <div class="field-row">
                        <span class="field-lbl">1. Tuman:</span>
                        <span class="field-val">{{ $application->district->name_uz }}</span>
                    </div>
                    <div class="field-row">
                        <span class="field-lbl">Ko'cha nomi:</span>
                        <span class="field-val {{ empty($fd['street_name']) ? 'empty' : '' }}">
                            {{ $fd['street_name'] ?? '__________________' }}
                        </span>
                    </div>
                    <div class="field-row" style="align-items:flex-start">
                        <span class="field-lbl" style="flex:0 0 45%">Kesishgan ko'chalar:</span>
                        <span class="field-val {{ empty($fd['intersecting_streets']) ? 'empty' : '' }}" style="min-width:0;flex:1">
                            {{ $fd['intersecting_streets'] ?? '__________________ va __________________' }}
                        </span>
                    </div>
                    <div class="field-row">
                        <span class="field-lbl">2. Avtomobil yo'ligacha masofa (m):</span>
                        <span class="field-val dalo-input-xs {{ empty($fd['road_distance']) ? 'empty' : '' }}" style="min-width:50px">
                            {{ $fd['road_distance'] ?? '____' }}
                        </span>
                    </div>
                    <div class="field-row">
                        <span class="field-lbl">3. Piyodalar yo'lagigacha masofa (m):</span>
                        <span class="field-val dalo-input-xs {{ empty($fd['pedestrian_distance']) ? 'empty' : '' }}" style="min-width:50px">
                            {{ $fd['pedestrian_distance'] ?? '____' }}
                        </span>
                    </div>
                </div>

                {{-- Section II --}}
                <div class="dalo-section">
                    <div class="sec-title">II. Obyekt va tutash hudud tasnifi</div>

                    <div class="field-block">
                        <div class="fb-lbl">1. Tadbirkorlik subyekti nomi:</div>
                        <div class="fb-val {{ empty($fd['business_name']) ? 'empty' : '' }}">
                            {{ $fd['business_name'] ? '"'.$fd['business_name'].'" MChJ' : '"__________________________________" MChJ' }}
                        </div>
                    </div>

                    <div class="field-block">
                        <div class="fb-lbl">2. Yuridik manzil:</div>
                        <div class="fb-val {{ empty($application->address) ? 'empty' : '' }}">
                            {{ $application->address ?? '____________ t., MFY, ko\'cha, uy raqami' }}
                        </div>
                    </div>

                    <div class="field-2col">
                        <div class="field-block">
                            <div class="fb-lbl">3. Faoliyat turi</div>
                            <div class="fb-val {{ empty($fd['activity_type']) ? 'empty' : '' }}">
                                {{ $fd['activity_type'] ?? '__________________' }}
                            </div>
                        </div>
                        <div class="field-block">
                            <div class="fb-lbl">4. Tutash hudud (kv.m)</div>
                            <div class="fb-val {{ empty($application->area_sqm) ? 'empty' : '' }}">
                                {{ $application->area_sqm ? number_format($application->area_sqm, 2).' kv.m' : '________ kv.m' }}
                            </div>
                        </div>
                    </div>

                    <div class="field-2col">
                        <div class="field-block">
                            <div class="fb-lbl">5. Kadastr raqami</div>
                            <div class="fb-val">{{ $application->cadastral_number }}</div>
                        </div>
                        <div class="field-block">
                            <div class="fb-lbl">Foydalanish maqsadi</div>
                            <div class="fb-val {{ empty($fd['purpose']) ? 'empty' : '' }}">
                                {{ $fd['purpose'] ?? '________________________________________' }}
                            </div>
                        </div>
                    </div>

                    <div class="field-block">
                        <div class="fb-lbl">6. Mavjud bino/inshootlar:</div>
                        <div class="fb-val {{ empty($fd['existing_structures']) ? 'empty' : '' }}">
                            {{ $fd['existing_structures'] ?? '________________________________________' }}
                        </div>
                    </div>

                    @if($application->description)
                    <div class="field-block">
                        <div class="fb-lbl">7. Izoh:</div>
                        <div class="fb-val" style="font-style:italic;font-size:.78rem">{{ $application->description }}</div>
                    </div>
                    @endif
                </div>

                {{-- Section III applicant signature --}}
                <div class="dalo-section">
                    <div class="sec-title">III. Dalolatnoma bilan tanishganlik</div>

                    <div class="sig-grid">
                        <div class="sig-cell">
                            <div class="sig-lbl">Subyekt rahbari (vakili):</div>
                            <div class="sig-name">{{ $application->applicant->name }}</div>
                        </div>
                        <div class="sig-cell">
                            <div class="sig-lbl">Imzo / Sana:</div>
                            <div class="sig-date">{{ $application->submitted_at?->format('d.m.Y') ?? '____.____.______' }}</div>
                        </div>
                    </div>

                    @if($application->applicant_pkcs7)
                    <div class="eimzo-stamp">
                        <a href="{{ route('apply.track', $application->number) }}" target="_blank">
                            <img
                                src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode(route('apply.track', $application->number)) }}&size=180x180&margin=4"
                                alt="QR" class="stamp-qr"
                                title="Skanerlang — dalolatnomani tekshirish">
                        </a>
                        <div class="stamp-badge">
                            <span class="stamp-badge-dot"></span>
                            E-IMZO imzolangan
                        </div>
                    </div>
                    @endif
                </div>

            </div>

            {{-- ──── RIGHT column: Commission signatures ──── --}}
            <div class="comm-col">
                <div class="comm-title">Komissiya a'zolari</div>

                @php
                    $dalSigs = $application->dalolatnomaSignatures->keyBy('commission_position');
                @endphp

                @foreach(\App\Models\DalolatnomaSignature::POSITIONS as $posKey => $posLabel)
                @php $sig = $dalSigs->get($posKey); @endphp
                <div class="comm-slot">
                    <div class="comm-pos">{{ $posLabel }}:</div>
                    @if($sig)
                    <div class="comm-signed">
                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode(route('dalolatnoma.verify', $sig->qr_code)) }}&size=44x44&margin=2"
                            alt="QR"
                            class="comm-qr"
                            title="Imzoni tekshirish">
                        <div class="comm-signed-info">
                            <div class="comm-signer-name">✓ {{ $sig->signer->name }}</div>
                            <div class="comm-signer-date">{{ \Carbon\Carbon::parse($sig->signed_at)->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                    @else
                    <div class="comm-line">
                        <span class="comm-waiting">Imzo kutilmoqda</span>
                    </div>
                    @endif
                </div>
                @endforeach

            </div>
        </div>

        <div class="doc-footer">
            Elektron tizim orqali shakllantirildi — Tutash Hudud &nbsp;·&nbsp; {{ $application->number }}
        </div>
    </div>

    {{-- ══════ Approval timeline ══════ --}}
    <div class="timeline-card">
        <div class="tl-title-row">Ko'rib chiqish bosqichlari</div>
        @php $roleLabels = \App\Models\ApplicationApproval::ROLE_LABELS; @endphp
        <div class="timeline">
            @foreach($application->approvals->sortBy('step_order') as $ap)
            @php
                $dc = match($ap->status) {
                    'approved' => 'approved', 'rejected' => 'rejected', 'pending' => 'pending', default => 'waiting'
                };
                $di = match($ap->status) {
                    'approved' => '✓', 'rejected' => '✗', default => $ap->step_order
                };
            @endphp
            <div class="tl-step">
                <div class="tl-dot2 {{ $dc }}">{{ $di }}</div>
                <div class="tl-step-lbl {{ $ap->status === 'pending' ? 'active' : '' }}">
                    {{ $roleLabels[$ap->step_role] ?? $ap->step_role }}
                    @if($ap->status === 'approved' && $ap->approver)
                        <div style="color:#198754;font-size:.65rem">{{ $ap->approver->name }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══════ Search another ══════ --}}
    <div class="search-again">
        <div class="sa-lbl">Boshqa ariza tekshirish</div>
        <form method="GET" action="{{ route('apply.track.search') }}" class="sa-row">
            <input type="text" name="number" placeholder="ARZ-2026-0001">
            <button type="submit">Tekshirish</button>
        </form>
    </div>

</div>
</main>

<footer class="site-footer">
    © {{ date('Y') }} Qo'shni hudud tizimi &nbsp;·&nbsp; Vazirlar Mahkamasi Qarori №478
</footer>
</body>
</html>
