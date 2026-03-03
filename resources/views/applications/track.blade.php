<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Ariza — {{ $application->number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #eef2f5; min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; -webkit-text-size-adjust: 100%; }

        /* ─── Header ─── */
        .site-header { background: #018c87; padding: 14px 32px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 12px rgba(1,140,135,.25); }
        .site-header .brand { color: #fff; font-size: 1.1rem; font-weight: 800; text-decoration: none; letter-spacing: .02em; }
        .site-header .brand span { font-weight: 400; opacity: .85; font-size: .9rem; }
        .btn-header { border: 1.5px solid rgba(255,255,255,.4); background: transparent; color: #fff; padding: 7px 20px; border-radius: 8px; font-size: .9rem; cursor: pointer; text-decoration: none; transition: all .2s; display:inline-flex;align-items:center;gap:6px; }
        .btn-header:hover { background: rgba(255,255,255,.15); color: #fff; border-color: #fff; }
        .header-user { display:flex;align-items:center;gap:10px; }
        .header-name { color:rgba(255,255,255,.9);font-size:.85rem;font-weight:600; }

        /* ─── Status ribbon ─── */
        .status-ribbon {
            background: #fff; border-bottom: 1px solid #e8ecf1;
            padding: 9px 32px; display: flex; align-items: center;
            gap: 12px; flex-wrap: wrap;
        }
        .ribbon-num  { font-family: monospace; font-size: .95rem; font-weight: 800; color: #018c87; }
        .ribbon-sep  { color: #c8d0e0; }
        .ribbon-date { font-size: .8rem; color: #8a9ab8; }
        .ribbon-spacer { flex: 1; }
        .pip-row { display: flex; gap: 3px; align-items: center; }
        .pip { width: 22px; height: 5px; border-radius: 3px; background: #e8ecf1; }
        .pip.done    { background: #198754; }
        .pip.current { background: #018c87; }
        .pip.rej     { background: #dc3545; }
        .sbadge {
            display: inline-block; padding: 4px 14px; border-radius: 20px;
            font-size: .74rem; font-weight: 700; border: 1px solid transparent;
        }
        .s-pending               { background:#fff3cd; color:#856404; border-color:#fec524; }
        .s-devon_review          { background:rgba(20,113,240,.1); color:#1471f0; border-color:#1471f0; }
        .s-executor_review       { background:rgba(213,141,61,.1); color:#a05a00; border-color:#d5893d; }
        .s-director_review       { background:rgba(14,186,148,.1); color:#0a8c6e; border-color:#0eba94; }
        .s-district_rep_review   { background:rgba(103,60,200,.1); color:#673cc8; border-color:#673cc8; }
        .s-legal_review          { background:rgba(103,60,200,.15); color:#5230a0; border-color:#7c5cbf; }
        .s-compliance_review     { background:rgba(1,140,135,.1); color:#018c87; border-color:#018c87; }
        .s-director_final_review { background:rgba(14,186,148,.15); color:#0a7a63; border-color:#0eba94; }
        .s-approved              { background:rgba(6,184,56,.1); color:#0a8040; border-color:#0bc33f; }
        .s-rejected              { background:rgba(230,50,96,.1); color:#c0163a; border-color:#e63260; }

        /* ─── Page ─── */
        .main { flex: 1; display: flex; justify-content: center; padding: 28px 16px 40px; }
        .page-wrap { width: 100%; max-width: 880px; }

        /* ─── Official Document card ─── */
        .dalo-doc { background: #fff; border-top: 4px solid #009AB6; box-shadow: 0 6px 28px rgba(0,0,0,.09); padding: 36px 40px 28px; margin-bottom: 20px; }

        /* Document header */
        .dalo-doc-head { text-align: center; margin-bottom: 20px; }
        .dalo-emblem { height: 58px; margin: 0 auto 10px; display: block; }
        .dalo-title { font-family: 'Merriweather', Georgia, serif; font-size: 1.25rem; font-weight: 700; color: #009AB6; text-transform: uppercase; letter-spacing: .08em; margin: 0 0 3px; }
        .dalo-subtitle { font-size: .76rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0; }
        .dalo-meta { display: flex; justify-content: space-between; margin-top: 16px; font-size: .84rem; font-weight: 500; color: #374151; }

        /* Applicant info strip */
        .applicant-strip { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; background: #f8fafc; margin-bottom: 16px; }
        .applicant-strip .as-lbl { font-size: .76rem; color: #94a3b8; margin-bottom: 3px; }
        .applicant-strip .as-body { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; }
        .applicant-strip .as-item { font-size: .78rem; color: #64748b; }
        .applicant-strip .as-item strong { font-size: .88rem; color: #1e3a8a; }
        .eimzo-ok { display:inline-flex; align-items:center; gap:4px; background:rgba(6,184,56,.1); border:1px solid #0bc33f; color:#0a8040; border-radius:12px; padding:2px 10px; font-size:.72rem; font-weight:700; margin-left:8px; }
        .eimzo-dot { width:6px; height:6px; border-radius:50%; background:#0bc33f; flex-shrink:0; }

        /* Body intro paragraph */
        .dalo-intro-box { border: 1px solid #e2e8f0; border-radius: 5px; padding: 12px 14px; background: #f8fafc; margin-bottom: 14px; }
        .dalo-intro-box .dalo-sec-title { color: #009AB6; font-weight: 700; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }
        .dalo-intro-para { font-size: .82rem; color: #374151; line-height: 2.1; margin: 0; }
        .dalo-filled { color: #1e3a8a; font-weight: 600; border-bottom: 1px solid #93c5fd; padding-bottom: 1px; }
        .dalo-blank  { color: #cbd5e1; font-weight: 400; font-style: italic; }

        /* Two-column layout */
        .dalo-body { display: grid; grid-template-columns: 7fr 5fr; gap: 24px; }

        /* Section boxes */
        .dalo-section { border: 1px solid #e2e8f0; border-radius: 5px; padding: 12px 14px; background: #f8fafc; margin-bottom: 14px; }
        .dalo-sec-title { color: #009AB6; font-weight: 700; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }
        .dalo-fblock { margin-bottom: 9px; font-size: .81rem; }
        .dalo-fblock:last-child { margin-bottom: 0; }
        .dalo-fblock > .fb-lbl { font-weight: 600; color: #374151; display: block; margin-bottom: 2px; font-size: .75rem; }
        .dalo-fblock > .fb-val { font-size: .84rem; font-weight: 500; color: #1e3a8a; border-bottom: 1px solid #cbd5e1; padding: 2px 0 3px; min-height: 22px; display: block; }
        .dalo-fblock > .fb-val.empty { color: #94a3b8; font-weight: 400; font-style: italic; }
        .dalo-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

        /* Applicant signature section */
        .dalo-sec-iii { margin-top: 0; }
        .dalo-sec-iii-title { font-size: .81rem; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
        .dalo-sig-row { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 10px; }
        .dalo-sig-blk { display: flex; flex-direction: column; }
        .dalo-sig-hint { font-size: .7rem; color: #94a3b8; margin-bottom: 3px; }
        .dalo-sig-val  { font-size: .83rem; font-weight: 700; color: #1e3a8a; border-bottom: 1px solid #334155; padding-bottom: 3px; display: inline-block; min-width: 140px; }

        /* QR stamp */
        .qr-stamp { display:flex; align-items:center; gap:10px; margin-top:10px; background:#f0faf9; border-radius:8px; border:1px solid #b2dfdb; padding:10px 12px; }
        .qr-img   { width:70px; height:70px; border-radius:6px; flex-shrink:0; }
        .qr-info  { font-size:.76rem; color:#374151; line-height:1.7; }
        .qr-info strong { color:#018c87; }

        /* Document footer */
        .dalo-doc-foot { margin-top: 20px; padding-top: 10px; border-top: 1px solid #f1f5f9; text-align: center; font-size: .65rem; color: #94a3b8; text-transform: uppercase; letter-spacing: .1em; }

        /* ─── Right column: workflow progress ─── */
        .dalo-right-col { border-left: 1px solid #e2e8f0; padding-left: 18px; }
        .dalo-comm-title { color: #009AB6; font-weight: 700; font-size: .68rem; text-transform: uppercase; letter-spacing: .12em; text-align: center; margin-bottom: 14px; }

        .wf-step { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 10px; }
        .wf-num {
            min-width: 22px; height: 22px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .68rem; font-weight: 700; flex-shrink: 0; margin-top: 1px;
        }
        .wf-num.wf-approved { background: #198754; color: #fff; }
        .wf-num.wf-rejected { background: #dc3545; color: #fff; }
        .wf-num.wf-pending  { background: #018c87; color: #fff; box-shadow: 0 0 0 3px rgba(1,140,135,.2); }
        .wf-num.wf-waiting  { background: #e8ecf1; color: #94a3b8; border: 1.5px solid #d0d8e4; }
        .wf-info { flex: 1; min-width: 0; }
        .wf-role { font-weight: 700; font-size: .78rem; color: #1e293b; line-height: 1.3; }
        .wf-desc { font-size: .7rem; color: #64748b; margin-top: 1px; }
        .wf-who  { font-size: .68rem; color: #198754; margin-top: 2px; font-weight: 600; }
        .wf-when { font-size: .66rem; color: #94a3b8; }
        .wf-rej-note { font-size: .68rem; color: #c0163a; margin-top: 2px; font-weight: 600; }
        .wf-comment { font-size: .7rem; color: #374151; background: #fff; border-left: 2px solid #018c87; padding: 3px 7px; border-radius: 0 4px 4px 0; margin-top: 4px; }
        .wf-comment.rej { border-left-color: #dc3545; }

        .wf-connector { width: 2px; height: 8px; background: #e8ecf1; margin: 1px 0 1px 10px; }
        .wf-connector.done { background: #198754; }
        .wf-connector.rej  { background: #dc3545; }

        /* Final outcome */
        .wf-outcome { margin-top: 14px; padding-top: 12px; border-top: 2px solid #e2e8f0; text-align: center; font-size: .78rem; font-weight: 700; }
        .wf-outcome.approved { color: #018c87; }
        .wf-outcome.pending  { color: #94a3b8; }
        .wf-outcome.rejected { color: #dc3545; }

        .wf-note { margin-top: 10px; padding: 9px 11px; background: #f0faf9; border-radius: 8px; border: 1px solid #b2dfdb; font-size: .72rem; color: #374151; }

        /* ── Timeline history card ── */
        .timeline-card {
            background: #fff; border-top: 3px solid #018c87; border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,.07); padding: 20px 28px; margin-bottom: 20px;
        }
        .tc-title { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
        .tc-title::after { content:''; flex:1; height:1px; background:#f0f2f5; }

        .hist-row { display: flex; gap: 12px; padding: 11px 0; border-bottom: 1px solid #f4f6f8; }
        .hist-row:last-child { border-bottom: none; padding-bottom: 0; }
        .hist-dot {
            width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; margin-top: 1px;
            display: flex; align-items: center; justify-content: center; font-size: .74rem; font-weight: 700;
        }
        .hist-dot.approved { background: rgba(6,184,56,.12); color: #198754; }
        .hist-dot.rejected { background: rgba(220,53,69,.12); color: #dc3545; }
        .hist-dot.pending  { background: rgba(1,140,135,.12); color: #018c87; }
        .hist-dot.waiting  { background: #f4f6f8; color: #aab0bb; border: 1.5px solid #dde3ee; }
        .hist-body { flex: 1; min-width: 0; }
        .hist-role-line { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 4px; }
        .hist-role { font-weight: 700; font-size: .86rem; color: #1e3a5f; }
        .hist-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: .69rem; font-weight: 700; flex-shrink: 0; }
        .hb-approved { background: rgba(6,184,56,.12); color: #0a8040; }
        .hb-rejected { background: rgba(230,50,96,.12); color: #c0163a; }
        .hb-pending  { background: #fff3cd; color: #9a6800; }
        .hb-waiting  { background: #f0f2f5; color: #8a9ab8; }
        .hist-meta { display: flex; flex-wrap: wrap; gap: 4px 14px; font-size: .76rem; color: #6e788b; }
        .hist-meta .hm { display: flex; align-items: center; gap: 3px; }
        .hist-meta strong { color: #27314b; }
        .hist-comment { margin-top: 6px; padding: 6px 10px; font-size: .8rem; color: #334155; background: #fff; border-radius: 0 5px 5px 0; border-left: 3px solid #dc3545; }
        .hist-comment.ok { border-left-color: #018c87; }

        /* ── Search again ── */
        .search-card { background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
        .sa-lbl { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 9px; }
        .sa-row { display: flex; gap: 7px; }
        .sa-row input { flex:1; border:1.5px solid #dde3ee; border-radius:8px; padding:7px 11px; font-size:.88rem; outline:none; background:#fafbfd; }
        .sa-row input:focus { border-color:#018c87; box-shadow:0 0 0 3px rgba(1,140,135,.1); }
        .sa-row button { padding:7px 18px; background:#018c87; color:#fff; border:none; border-radius:8px; font-size:.88rem; font-weight:600; cursor:pointer; white-space:nowrap; transition:background .15s; }
        .sa-row button:hover { background:#017570; }

        /* Footer */
        .site-footer { text-align: center; padding: 18px; font-size: .8rem; color: #8a9ab8; }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .site-header { padding: 12px 18px; }
            .site-header .brand span { display: none; }
            .dalo-doc { padding: 26px 22px; }
            .dalo-body { gap: 18px; }
            .dalo-right-col { padding-left: 14px; }
            .status-ribbon { padding: 9px 18px; }
        }
        @media (max-width: 560px) {
            .main { padding: 14px 10px 32px; }
            .site-header { padding: 11px 14px; }
            .site-header .brand span { display: none; }
            .dalo-doc { padding: 18px 14px 20px; }
            .dalo-body { grid-template-columns: 1fr; gap: 0; }
            .dalo-right-col { border-left: none; border-top: 1px solid #e2e8f0; padding-left: 0; padding-top: 16px; margin-top: 8px; }
            .dalo-2col { grid-template-columns: 1fr; }
            .dalo-sig-row { flex-direction: column; gap: 8px; }
            .status-ribbon { padding: 9px 14px; }
            .timeline-card { padding: 16px; }
        }
    </style>
</head>
<body>

<header class="site-header">
    <a href="{{ route('home') }}" class="brand">TUTASH HUDUDLAR <span>— VM 478 asosida</span></a>
    @auth
        <div class="header-user">
            <span class="header-name">{{ auth()->user()->name }}</span>
            @if(auth()->user()->isConsumer())
                <a href="{{ route('my-applications') }}" class="btn-header">Arizalarim</a>
            @else
                <a href="{{ route('dashboard') }}" class="btn-header">Boshqaruv paneli</a>
            @endif
        </div>
    @else
        <a href="{{ route('login') }}" class="btn-header">🔑 Kirish</a>
    @endauth
</header>

@php
    $fd = is_array($application->form_data)
        ? $application->form_data
        : (json_decode($application->form_data ?? '', true) ?? []);

    $statusMap = [
        'pending'               => ['Kutilmoqda',                         's-pending'],
        'devon_review'          => ['Devon qabul qilmoqda',               's-devon_review'],
        'executor_review'       => ["Ijrochi ko'rib chiqmoqda",           's-executor_review'],
        'director_review'       => ['Rahbar topshiriq bermoqda',          's-director_review'],
        'district_rep_review'   => ["Tuman vakili ko'rib chiqmoqda",      's-district_rep_review'],
        'legal_review'          => ["Yurist ko'rib chiqmoqda",            's-legal_review'],
        'compliance_review'     => ["Komplayans tekshirmoqda",            's-compliance_review'],
        'director_final_review' => ['Rahbar yakuniy tasdiqlash',          's-director_final_review'],
        'approved'              => ['Tasdiqlandi ✓ — Shartnoma tuziladi', 's-approved'],
        'rejected'              => ['Rad etildi ✗',                       's-rejected'],
    ];
    $s = $statusMap[$application->status] ?? ["Noma'lum", 's-pending'];
    $roleLabels = \App\Models\ApplicationApproval::ROLE_LABELS;

    // Safe helpers
    $mahalla    = $fd['mahalla']    ?? null;
    $street     = $fd['street_name']  ?? null;
    $house      = $fd['house_number'] ?? null;
    $bizName    = $fd['business_name']    ?? null;
    $actType    = $fd['activity_type']    ?? null;
    $purpose    = $fd['purpose']          ?? null;
    $structures = $fd['existing_structures'] ?? null;
@endphp

{{-- Status ribbon --}}
<div class="status-ribbon">
    <span class="ribbon-num">{{ $application->number }}</span>
    <span class="ribbon-sep">·</span>
    <span class="ribbon-date">{{ $application->submitted_at?->format('d.m.Y H:i') }}</span>
    <span class="ribbon-sep">·</span>
    <span class="ribbon-date">{{ $application->district->name_uz }}</span>
    <span class="ribbon-spacer"></span>
    <div class="pip-row">
        @foreach($application->approvals->sortBy('step_order') as $ap)
        @php $pc = match($ap->status) { 'approved'=>'done','rejected'=>'rej','pending'=>'current',default=>'' }; @endphp
        <div class="pip {{ $pc }}"></div>
        @endforeach
    </div>
    <span class="sbadge {{ $s[1] }}">{{ $s[0] }}</span>
</div>

<main class="main">
<div class="page-wrap">

    {{-- ═══ Official Document Card ═══ --}}
    <div class="dalo-doc">

        {{-- Document header --}}
        <div class="dalo-doc-head">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/77/Emblem_of_Uzbekistan.svg" alt="Gerb" class="dalo-emblem">
            <p style="font-size:.76rem; color:#64748b; text-align:center; margin-bottom:6px; line-height:1.5;">
                Toshkent shahar hokimligi huzuridagi<br>
                <strong style="color:#374151;">&laquo;MIRZO ULUG&lsquo;BEK BUSINESS CITY&raquo;
                tadbirkorlik markazini qurish va ekspluatatsiya qilish Direksiyasi&raquo; DUK</strong>ga
            </p>
            <h1 class="dalo-title">ARIZA</h1>
            <p class="dalo-subtitle">Ijara shartnomasi rasmiylashtirilishi uchun &mdash; VM &numero;478</p>
            <div class="dalo-meta">
                <span>Toshkent shahri</span>
                <span>{{ $application->submitted_at?->format('Y') ?? date('Y') }}-yil
                    &laquo;{{ $application->submitted_at?->format('d') ?? '___' }}&raquo;
                    {{ $application->submitted_at ? \Carbon\Carbon::parse($application->submitted_at)->translatedFormat('F') : '____________' }}
                </span>
            </div>
        </div>

        {{-- Applicant info strip --}}
        <div class="applicant-strip">
            <div class="as-lbl">
                Ariza beruvchi (E-IMZO imzosidan aniqlangan):
                @if($application->applicant_pkcs7)
                    <span class="eimzo-ok"><span class="eimzo-dot"></span> E-IMZO imzolangan</span>
                @endif
            </div>
            <div class="as-body">
                <div class="as-item">
                    FISh / Korxona nomi:&nbsp;
                    <strong>{{ $application->applicant->name }}</strong>
                </div>
                @if($application->applicant->pinfl)
                <div class="as-item">
                    STIR / PINFL:&nbsp;
                    <strong>{{ $application->applicant->pinfl }}</strong>
                </div>
                @endif
                @if($application->applicant->organization)
                <div class="as-item">
                    Tashkilot:&nbsp;
                    <strong>{{ $application->applicant->organization }}</strong>
                </div>
                @endif
            </div>
        </div>

        <div class="dalo-body">

            {{-- ──── LEFT column ──── --}}
            <div>

                {{-- Request body paragraph --}}
                <div class="dalo-intro-box">
                    <div class="dalo-sec-title">Murojaat matni</div>
                    <p class="dalo-intro-para">
                        Vazirlar Mahkamasining &laquo;Tadbirkorlik subyektlari uchun tutash hududlardan
                        mavsumiy foydalanishni tashkil etishni yanada soddalashtirish
                        choratadbirlari to&lsquo;g&lsquo;risida&raquo;gi
                        <strong>2025 yil 31 iyuldagi 478-son</strong> qaroriga asosan

                        <span class="{{ $application->district?->name_uz ? 'dalo-filled' : 'dalo-blank' }}">
                            {{ $application->district->name_uz ?? '__ tuman' }}</span> tumani,

                        <span class="{{ $mahalla ? 'dalo-filled' : 'dalo-blank' }}">
                            {{ $mahalla ?? '_____ MFY' }}</span> MFY,

                        <span class="{{ $street ? 'dalo-filled' : 'dalo-blank' }}">
                            {{ $street ?? '____________ ko\'cha' }}</span> ko&lsquo;chasi,

                        <span class="{{ $house ? 'dalo-filled' : 'dalo-blank' }}">
                            {{ $house ?? '__' }}</span>-uy manzilidagi o&lsquo;zimga tegishli (kadastr raqami
                        <span class="{{ $application->cadastral_number ? 'dalo-filled' : 'dalo-blank' }}">
                            {{ $application->cadastral_number ?? '10:__:__:__:____' }}</span>)
                        bino&#8209;inshoot (yer uchastkasi)ga tutash

                        <span class="{{ $application->area_sqm ? 'dalo-filled' : 'dalo-blank' }}">
                            {{ $application->area_sqm ? number_format($application->area_sqm, 2) : '___' }}</span>
                        kv.&thinsp;m yer uchastkasi hududi bo&lsquo;yicha
                        <strong>ijara shartnomasi rasmiylashtirilishida amaliy yordam
                        berishingizni so&lsquo;raymiz.</strong>
                    </p>
                </div>

                {{-- Extra fields (only shown if present) --}}
                @if($bizName || $actType || $purpose || $structures || $application->description)
                <div class="dalo-section">
                    <div class="dalo-sec-title">Qo'shimcha ma'lumotlar</div>

                    @if($bizName)
                    <div class="dalo-fblock">
                        <span class="fb-lbl">Tadbirkorlik subyekti:</span>
                        <span class="fb-val">{{ $bizName }} MChJ</span>
                    </div>
                    @endif

                    @if($actType || $purpose)
                    <div class="dalo-2col">
                        @if($actType)
                        <div class="dalo-fblock">
                            <span class="fb-lbl">Faoliyat turi:</span>
                            <span class="fb-val">{{ $actType }}</span>
                        </div>
                        @endif
                        @if($purpose)
                        <div class="dalo-fblock">
                            <span class="fb-lbl">Foydalanish maqsadi:</span>
                            <span class="fb-val">{{ $purpose }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($structures)
                    <div class="dalo-fblock">
                        <span class="fb-lbl">Mavjud bino/inshootlar:</span>
                        <span class="fb-val">{{ $structures }}</span>
                    </div>
                    @endif

                    @if($application->description)
                    <div class="dalo-fblock">
                        <span class="fb-lbl">Izoh:</span>
                        <span class="fb-val" style="font-style:italic">{{ $application->description }}</span>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Applicant signature section --}}
                <div class="dalo-sec-iii">
                    <div class="dalo-sec-iii-title">Ariza beruvchi imzosi</div>
                    <div class="dalo-sig-row">
                        <div class="dalo-sig-blk">
                            <span class="dalo-sig-hint">(imzo)</span>
                            <span class="dalo-sig-val">______________________</span>
                        </div>
                        <div class="dalo-sig-blk" style="text-align:right">
                            <span class="dalo-sig-hint">Sana:</span>
                            <span class="dalo-sig-val">{{ $application->submitted_at?->format('d.m.Y') ?? '____.____.______' }}</span>
                        </div>
                    </div>

                    @if($application->applicant_pkcs7)
                    <div class="qr-stamp">
                        <a href="{{ route('apply.track', $application->number) }}" target="_blank">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode(route('apply.track', $application->number)) }}&size=140x140&margin=4"
                                 alt="QR" class="qr-img" title="Ariza holatini tekshirish">
                        </a>
                        <div class="qr-info">
                            <strong>E-IMZO bilan imzolangan</strong><br>
                            Ariza №: {{ $application->number }}<br>
                            Yuborildi: {{ $application->submitted_at?->format('d.m.Y H:i') }}<br>
                            QR skanerlash orqali tekshiriladi
                        </div>
                    </div>
                    @endif
                </div>

            </div>{{-- /left --}}

            {{-- ──── RIGHT column: workflow progress ──── --}}
            <div class="dalo-right-col">
                <div class="dalo-comm-title">Ko&rsquo;rib chiqish tartibi</div>

                @php
                    $approvalsByStep = $application->approvals->sortBy('step_order')->keyBy('step_order');
                    $wfDefs = [
                        1 => ['role' => 'devon',          'label' => 'Devon',        'desc' => 'Ariza qabul qilinadi'],
                        2 => ['role' => 'executor',        'label' => 'Ijrochi',      'desc' => "Rahbarga yo'naltiradi"],
                        3 => ['role' => 'director',        'label' => 'Rahbar',       'desc' => 'Topshiriq beradi'],
                        4 => ['role' => 'district_rep',    'label' => 'Tuman Vakili', 'desc' => "Tutash hududga yo'naltiradi"],
                        5 => ['role' => 'lawyer',          'label' => 'Yurist',       'desc' => 'Huquqiy ekspertiza'],
                        6 => ['role' => 'compliance',      'label' => 'Komplayans',   'desc' => 'Muvofiqlik tekshiruvi'],
                        7 => ['role' => 'director_final',  'label' => 'Rahbar',       'desc' => 'Yakuniy tasdiq'],
                    ];
                @endphp

                @foreach($wfDefs as $stepNum => $def)
                @php
                    $ap = $approvalsByStep->get($stepNum);
                    $st = $ap ? $ap->status : 'waiting';
                    $cls = match($st) {
                        'approved' => 'wf-approved',
                        'rejected' => 'wf-rejected',
                        'pending'  => 'wf-pending',
                        default    => 'wf-waiting',
                    };
                    $icon = match($st) {
                        'approved' => '✓',
                        'rejected' => '✗',
                        'pending'  => '●',
                        default    => $stepNum,
                    };
                    $isLast = $stepNum === count($wfDefs);
                @endphp

                {{-- Connector line above (skip first) --}}
                @if($stepNum > 1)
                @php
                    $prevAp = $approvalsByStep->get($stepNum - 1);
                    $prevSt = $prevAp ? $prevAp->status : 'waiting';
                @endphp
                <div class="wf-connector {{ $prevSt === 'approved' ? 'done' : ($prevSt === 'rejected' ? 'rej' : '') }}"></div>
                @endif

                <div class="wf-step">
                    <span class="wf-num {{ $cls }}">{{ $icon }}</span>
                    <div class="wf-info">
                        <div class="wf-role">{{ $def['label'] }}</div>
                        <div class="wf-desc">{{ $def['desc'] }}</div>
                        @if($ap && $ap->status === 'approved')
                            @if($ap->approver)
                            <div class="wf-who">✓ {{ $ap->approver->name }}</div>
                            @endif
                            @if($ap->approved_at)
                            <div class="wf-when">{{ $ap->approved_at->setTimezone(config('app.timezone','Asia/Tashkent'))->format('d.m.Y H:i') }}</div>
                            @endif
                            @if($ap->comments)
                            <div class="wf-comment ok">{{ $ap->comments }}</div>
                            @endif
                        @elseif($ap && $ap->status === 'rejected')
                            @if($ap->approver)
                            <div class="wf-rej-note">✗ {{ $ap->approver->name }}</div>
                            @endif
                            @if($ap->approved_at)
                            <div class="wf-when">{{ $ap->approved_at->setTimezone(config('app.timezone','Asia/Tashkent'))->format('d.m.Y H:i') }}</div>
                            @endif
                            @if($ap->comments)
                            <div class="wf-comment rej">{{ $ap->comments }}</div>
                            @endif
                        @elseif($ap && $ap->status === 'pending')
                            <div class="wf-desc" style="color:#018c87;font-weight:600;margin-top:2px">⏳ Ko'rilmoqda</div>
                        @endif
                    </div>
                </div>

                @endforeach

                @php $finalSt = $application->status; @endphp
                <div class="wf-outcome {{ $finalSt === 'approved' ? 'approved' : ($finalSt === 'rejected' ? 'rejected' : 'pending') }}">
                    @if($finalSt === 'approved')
                        ↓ Ijara shartnomasi tuziladi
                    @elseif($finalSt === 'rejected')
                        ✗ Ariza rad etildi
                    @else
                        ↓ Ijara shartnomasi tuziladi
                    @endif
                </div>

                <div class="wf-note">
                    <strong>Eslatma:</strong> Ijrochi arizani rad etsa, <em>javob xat</em> yuboriladi.
                    Barcha bosqichlardan o&lsquo;tgach <strong>ijara shartnomasi</strong> tuziladi.
                </div>
            </div>{{-- /right --}}

        </div>{{-- /dalo-body --}}

        <div class="dalo-doc-foot">
            Elektron tizim orqali shakllantirildi &mdash; Tutash Hudud &nbsp;&middot;&nbsp; VM &numero;478
        </div>
    </div>

    {{-- ══════ Detailed history timeline ══════ --}}
    <div class="timeline-card">
        <div class="tc-title">Ko'rib chiqish tarixi</div>

        @foreach($application->approvals->sortBy('step_order') as $approval)
        @php
            $isDone     = in_array($approval->status, ['approved','rejected']);
            $isRejected = $approval->status === 'rejected';
            $isPending  = $approval->status === 'pending';
            $dc = match($approval->status) {
                'approved' => 'approved',
                'rejected' => 'rejected',
                'pending'  => 'pending',
                default    => 'waiting',
            };
            $di = match($approval->status) {
                'approved' => '✓', 'rejected' => '✗', 'pending' => '●', default => $approval->step_order
            };
        @endphp
        <div class="hist-row">
            <div class="hist-dot {{ $dc }}">{{ $di }}</div>
            <div class="hist-body">
                <div class="hist-role-line">
                    <div class="hist-role">
                        <span style="color:#94a3b8;font-weight:400;font-size:.74rem">{{ $approval->step_order }}.</span>
                        {{ $roleLabels[$approval->step_role] ?? $approval->step_role }}
                    </div>
                    @if($approval->status === 'approved')
                        <span class="hist-badge hb-approved">✓ Tasdiqlandi</span>
                    @elseif($approval->status === 'rejected')
                        <span class="hist-badge hb-rejected">✗ Rad etildi</span>
                    @elseif($approval->status === 'pending')
                        <span class="hist-badge hb-pending">⏳ Ko'rilmoqda</span>
                    @else
                        <span class="hist-badge hb-waiting">Navbatda</span>
                    @endif
                </div>

                @if($isDone)
                <div class="hist-meta">
                    @if($approval->approver)
                    <div class="hm">👤 <strong>{{ $approval->approver->name }}</strong></div>
                    @endif
                    @if($approval->approved_at)
                    <div class="hm">🕒 <strong>{{ $approval->approved_at->setTimezone(config('app.timezone','Asia/Tashkent'))->format('d.m.Y H:i') }}</strong></div>
                    @endif
                </div>
                @if($approval->comments)
                <div class="hist-comment {{ $isRejected ? '' : 'ok' }}">
                    <span style="font-weight:700;font-size:.72rem;color:{{ $isRejected ? '#c0163a' : '#018c87' }}">
                        {{ $isRejected ? '❌ Rad sababi' : '📋 Izoh' }}:
                    </span><br>{{ $approval->comments }}
                </div>
                @endif
                @elseif($isPending)
                <div style="font-size:.74rem;color:#018c87;margin-top:3px">⏳ Hozirgi bosqich — ko'rib chiqilmoqda</div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════ Search another ══════ --}}
    <div class="search-card">
        <div class="sa-lbl">Boshqa ariza tekshirish</div>
        <form method="GET" action="{{ route('apply.track.search') }}" class="sa-row">
            <input type="text" name="number" placeholder="ARZ-2026-00001">
            <button type="submit">Tekshirish</button>
        </form>
    </div>

</div>
</main>

<footer class="site-footer">
    © {{ date('Y') }} Qo'shni hudud tizimi &nbsp;·&nbsp; Vazirlar Mahkamasi Qarori №478 asosida
</footer>
</body>
</html>
