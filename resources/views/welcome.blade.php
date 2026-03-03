<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tutash Hudud — Dalolatnoma</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #eef2f5; min-height: 100vh; display: flex; flex-direction: column; }

        /* ─── Header ─── */
        .site-header { background: #018c87; padding: 14px 32px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 12px rgba(1,140,135,.25); }
        .site-header .brand { color: #fff; font-size: 1.1rem; font-weight: 800; text-decoration: none; letter-spacing: .02em; }
        .site-header .brand span { font-weight: 400; opacity: .85; font-size: .9rem; }
        .btn-header { border: 1.5px solid rgba(255,255,255,.4); background: transparent; color: #fff; padding: 7px 20px; border-radius: 8px; font-size: .9rem; cursor: pointer; text-decoration: none; transition: all .2s; }
        .btn-header:hover { background: rgba(255,255,255,.15); color: #fff; border-color: #fff; }

        /* ─── Page ─── */
        .main { flex: 1; display: flex; justify-content: center; padding: 28px 16px 40px; }
        .page-wrap { width: 100%; max-width: 880px; }

        /* ─── Quick cards (login / track) ─── */
        .quick-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; }
        .quick-row { padding: 15px 24px; border-bottom: 1px solid #f0f2f5; }
        .quick-row:last-child { border-bottom: none; }
        .form-control, .form-select { border: 1.5px solid #dde3ee; border-radius: 8px; padding: 9px 13px; font-size: .92rem; color: #2c3e60; transition: border-color .2s, box-shadow .2s; }
        .form-control:focus, .form-select:focus { border-color: #018c87; box-shadow: 0 0 0 3px rgba(1,140,135,.1); outline: none; }

        /* ─── Official Document card ─── */
        .dalo-doc { background: #fff; border-top: 4px solid #009AB6; box-shadow: 0 6px 28px rgba(0,0,0,.09); padding: 36px 40px 28px; }

        /* Document header */
        .dalo-doc-head { text-align: center; margin-bottom: 20px; }
        .dalo-emblem { height: 58px; margin: 0 auto 10px; display: block; }
        .dalo-title { font-family: 'Merriweather', Georgia, serif; font-size: 1.25rem; font-weight: 700; color: #009AB6; text-transform: uppercase; letter-spacing: .08em; margin: 0 0 3px; }
        .dalo-subtitle { font-size: .76rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0; }
        .dalo-meta { display: flex; justify-content: space-between; margin-top: 16px; font-size: .84rem; font-weight: 500; color: #374151; }

        /* Intro paragraph */
        .dalo-intro { font-size: .77rem; text-align: justify; line-height: 1.75; color: #64748b; margin-bottom: 18px; }
        .dalo-intro strong { color: #1e293b; }
        #intro-district, #intro-street { transition: color .2s; border-bottom: 1px dashed #94a3b8; padding-bottom: 1px; }

        /* Two-column layout */
        .dalo-body { display: grid; grid-template-columns: 7fr 5fr; gap: 24px; }

        /* Section boxes */
        .dalo-section { border: 1px solid #e2e8f0; border-radius: 5px; padding: 12px 14px; background: #f8fafc; margin-bottom: 14px; }
        .dalo-sec-title { color: #009AB6; font-weight: 700; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }

        /* Inline document fields */
        .dalo-field-row { display: flex; align-items: baseline; gap: 5px; font-size: .81rem; color: #374151; margin-bottom: 7px; flex-wrap: nowrap; }
        .dalo-lbl { flex-shrink: 0; white-space: nowrap; }
        .dalo-input { flex: 1; min-width: 0; border: none; border-bottom: 1px solid #94a3b8; background: transparent; padding: 1px 3px; font-size: .81rem; font-weight: 500; color: #1e3a8a; outline: none; }
        .dalo-input:focus { border-bottom-color: #009AB6; background: rgba(0,154,182,.03); }
        .dalo-input::placeholder { color: #cbd5e1; font-weight: 400; font-style: italic; }
        .dalo-input-xs { max-width: 60px; flex: none; }
        .dalo-select { flex: 1; min-width: 0; border: none; border-bottom: 1px solid #94a3b8; background: transparent; padding: 1px 3px; font-size: .81rem; font-weight: 500; color: #1e3a8a; outline: none; appearance: none; cursor: pointer; }
        .dalo-select:focus { border-bottom-color: #009AB6; }

        /* Block-style fields (Section II) */
        .dalo-fblock { margin-bottom: 9px; font-size: .81rem; }
        .dalo-fblock > label { font-weight: 600; color: #374151; display: block; margin-bottom: 2px; }
        .dalo-full-inp { width: 100%; border: none; border-bottom: 1px solid #94a3b8; background: white; padding: 3px 5px; font-size: .81rem; font-weight: 500; color: #1e3a8a; outline: none; }
        .dalo-full-inp:focus { border-bottom-color: #009AB6; background: rgba(0,154,182,.03); }
        .dalo-full-inp::placeholder { color: #cbd5e1; font-weight: 400; font-style: italic; }
        .dalo-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .dalo-textarea { width: 100%; border: none; border-bottom: 1px solid #94a3b8; background: white; padding: 3px 5px; font-size: .76rem; font-style: italic; color: #1e3a8a; outline: none; resize: none; min-height: 34px; }
        .dalo-textarea:focus { border-bottom-color: #009AB6; }

        /* Section III – applicant signature */
        .dalo-sec-iii { margin-top: 0; }
        .dalo-sec-iii-title { font-size: .81rem; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
        .dalo-sig-row { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 14px; }
        .dalo-sig-blk { display: flex; flex-direction: column; }
        .dalo-sig-hint { font-size: .7rem; color: #94a3b8; margin-bottom: 3px; }
        .dalo-sig-val  { font-size: .83rem; font-weight: 700; color: #1e3a8a; }

        /* Submit button */
        .btn-dalo { width: 100%; padding: 11px; background: #018c87; color: #fff; border: none; border-radius: 8px; font-size: .9rem; font-weight: 700; cursor: pointer; transition: all .2s; }
        .btn-dalo:hover { background: #017570; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(1,140,135,.3); }
        .dalo-submit-hint { font-size: .71rem; color: #94a3b8; text-align: center; margin-top: 5px; }

        /* Document footer strip */
        .dalo-doc-foot { margin-top: 20px; padding-top: 10px; border-top: 1px solid #f1f5f9; text-align: center; font-size: .65rem; color: #94a3b8; text-transform: uppercase; letter-spacing: .1em; }

        /* Commission right column */
        .dalo-right-col { border-left: 1px solid #e2e8f0; padding-left: 18px; }
        .dalo-comm-title { color: #009AB6; font-weight: 700; font-size: .68rem; text-transform: uppercase; letter-spacing: .12em; text-align: center; margin-bottom: 14px; }
        .dalo-comm-slot { margin-bottom: 13px; }
        .dalo-comm-name { font-weight: 700; font-size: .76rem; color: #475569; margin-bottom: 3px; }
        .dalo-comm-line { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid #cbd5e1; padding-bottom: 3px; }
        .dalo-imzo-ghost { font-size: .7rem; font-style: italic; color: #94a3b8; }

        /* E-IMZO modal */
        .modal-content { border-radius: 16px; border: none; overflow: hidden; }
        .modal-header { background: #018c87; color: #fff; border-bottom: none; padding: 16px 20px; }
        .modal-title { font-weight: 700; font-size: 1rem; color: #fff; }
        .modal-header .btn-close { filter: invert(1); opacity: .8; }
        .modal-footer { border-top: 1px solid #e8ecf1; padding: 12px 16px; gap: 8px; }

        /* Key cards */
        #eimzo-keys-list { max-height: 370px; overflow-y: auto; border: 1.5px solid #e0e0e0; border-radius: 10px; background: #fafafa; }
        .keys-loader { display: flex; align-items: center; gap: .75rem; padding: 1.2rem; color: #666; font-size: .9rem; }
        .keys-spinner { width: 20px; height: 20px; border: 2px solid #e0e0e0; border-top-color: #018c87; border-radius: 50%; animation: spin .8s linear infinite; flex-shrink: 0; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .keys-empty { padding: 1.2rem; color: #999; font-size: .9rem; text-align: center; }
        .key-card { padding: .85rem 1rem; border-bottom: 1px solid #ececec; cursor: pointer; transition: background .15s; background: white; }
        .key-card:last-child { border-bottom: none; }
        .key-card:hover { background: #f0faf9; }
        .key-card-selected { background: #edfaf9 !important; border-left: 3px solid #018c87; }
        .key-card-expired { opacity: .55; }
        .key-card-name { font-weight: 700; color: #018c87; font-size: .93rem; margin-bottom: .3rem; }
        .key-card-badge { display: inline-block; font-size: .73rem; font-weight: 600; padding: .12rem .55rem; border-radius: 20px; margin-bottom: .35rem; }
        .badge-jismoniy { background: #d4f5e2; color: #1a7a40; }
        .badge-yuridik  { background: #fff0d4; color: #996500; }
        .key-card-stir  { font-size: .78rem; color: #888; margin-bottom: .28rem; }
        .key-card-meta  { margin-top: .28rem; }
        .key-card-row   { display: flex; justify-content: space-between; font-size: .78rem; color: #555; line-height: 1.6; }
        .key-card-row span { color: #888; }
        .key-expired-warn { margin-top: .32rem; font-size: .78rem; color: #c0392b; font-weight: 600; }
        #modal-sign-btn { background: #018c87; border-color: #018c87; }
        #modal-sign-btn:hover:not(:disabled) { background: #017570; border-color: #017570; }
        #modal-sign-btn:disabled { opacity: .45; cursor: not-allowed; }
        #modal-sign-btn.signing { opacity: .7; cursor: wait; }

        /* Footer */
        .site-footer { text-align: center; padding: 18px; font-size: .8rem; color: #8a9ab8; }

        /* Responsive */
        @media (max-width: 640px) {
            .dalo-doc { padding: 20px 14px; }
            .dalo-body { grid-template-columns: 1fr; }
            .dalo-right-col { border-left: none; border-top: 1px solid #e2e8f0; padding-left: 0; padding-top: 14px; margin-top: 4px; }
            .dalo-2col { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header class="site-header">
    <a href="{{ route('home') }}" class="brand">TUTASH HUDUDLAR <span>— VM 478 asosida</span></a>
    <a href="{{ route('login') }}" class="btn-header">Xodimlar uchun kirish</a>
</header>

<main class="main">
<div class="page-wrap">

    {{-- Login / Track quick bar --}}
    <div class="quick-card mb-3">
        <div class="quick-row">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div style="font-size:.88rem;font-weight:700;color:#1a2d5a;margin-bottom:2px">Avval ariza bergansizmi?</div>
                    <div style="font-size:.78rem;color:#8a9ab8">E-IMZO bilan kiring va barcha arizalaringizni ko'ring</div>
                </div>
                <a href="{{ route('login') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;background:#018c87;color:#fff;border-radius:10px;font-size:.88rem;font-weight:600;text-decoration:none;white-space:nowrap">
                    🔑 E-IMZO bilan kirish
                </a>
            </div>
        </div>
        <div class="quick-row">
            <div style="font-size:.76rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;margin-bottom:8px">Ariza holatini tekshirish</div>
            @if($errors->has('number'))
                <div class="text-danger small mb-2">{{ $errors->first('number') }}</div>
            @endif
            <form method="GET" action="{{ route('apply.track.search') }}" class="d-flex gap-2">
                <input type="text" name="number" class="form-control"
                    placeholder="Ariza raqami (masalan: ARZ-2026-0001)"
                    value="{{ old('number', request('number')) }}">
                <button type="submit"
                    style="white-space:nowrap;padding:9px 20px;background:#018c87;color:#fff;border:none;border-radius:8px;font-size:.9rem;font-weight:600;cursor:pointer">
                    Tekshirish
                </button>
            </form>
        </div>
    </div>

    @if($errors->any() && !$errors->has('number'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:12px">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ═══ Official Dalolatnoma Document ═══ --}}
    <div class="dalo-doc">

        {{-- Document header --}}
        <div class="dalo-doc-head">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/77/Emblem_of_Uzbekistan.svg" alt="Gerb" class="dalo-emblem">
            <h1 class="dalo-title">Dalolatnoma</h1>
            <p class="dalo-subtitle">Tadbirkorlik subyektlariga tutash hududlarni xatlovdan o'tkazish to'g'risida</p>
            <div class="dalo-meta">
                <span>Toshkent shahri</span>
                <span>{{ date('Y') }}-yil «___» ____________</span>
            </div>
        </div>

        {{-- Intro paragraph --}}
        <p class="dalo-intro">
            Tuzildi mazkur dalolatnoma Toshkent shahri,
            <strong id="intro-district">__________________</strong> tumani,
            <strong id="intro-street">__________________</strong> ko'chasida joylashgan umumiy ovqatlanish, savdo va xizmat ko'rsatish
            sohasidagi tadbirkorlik subyektlari uchun o'ziga tegishli bino va inshootlarga hamda yer uchastkalariga
            tutash bo'lgan, davlat organlari va tashkilotlariga doimiy foydalanishga berilgan aholi punktlarining
            umumiy foydalanishdagi yer uchastkalarini aniqlash bo'yicha.
        </p>

        <form method="POST" action="{{ route('apply') }}" enctype="multipart/form-data" id="apply-form">
            @csrf
            <input type="hidden" name="pkcs7"           id="pkcs7-field">
            <input type="hidden" name="expected_pinfl"  id="expected-pinfl-field">
            <input type="hidden" name="expected_name"   id="expected-name-field">

            <div class="dalo-body">

                {{-- ──────────── LEFT column ──────────── --}}
                <div>

                    {{-- Section I --}}
                    <div class="dalo-section">
                        <div class="dalo-sec-title">I. Xatlov o'tkazilgan ko'chaning tasnifi</div>

                        <div class="dalo-field-row">
                            <span class="dalo-lbl">1. Tuman:</span>
                            <select name="district_id" id="district_id"
                                class="dalo-select @error('district_id') is-invalid @enderror" required>
                                <option value="">— tanlang —</option>
                                @foreach($districts as $d)
                                <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->name_uz }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="dalo-field-row">
                            <span class="dalo-lbl">Ko'cha nomi:</span>
                            <input type="text" name="street_name" class="dalo-input"
                                placeholder="__________________" value="{{ old('street_name') }}">
                        </div>
                        <div class="dalo-field-row" style="align-items:flex-start">
                            <span class="dalo-lbl" style="padding-top:2px">Kesishgan ko'chalar:</span>
                            <input type="text" name="intersecting_streets" class="dalo-input"
                                placeholder="__________________ va __________________" value="{{ old('intersecting_streets') }}">
                        </div>
                        <div class="dalo-field-row" style="margin-top:4px">
                            <span class="dalo-lbl">2. Avtomobil yo'ligacha masofa (m):</span>
                            <input type="number" name="road_distance" class="dalo-input dalo-input-xs"
                                placeholder="____" step="0.1" min="0" value="{{ old('road_distance') }}">
                        </div>
                        <div class="dalo-field-row">
                            <span class="dalo-lbl">3. Piyodalar yo'lagigacha masofa (m):</span>
                            <input type="number" name="pedestrian_distance" class="dalo-input dalo-input-xs"
                                placeholder="____" step="0.1" min="0" value="{{ old('pedestrian_distance') }}">
                        </div>
                    </div>

                    {{-- Section II --}}
                    <div class="dalo-section">
                        <div class="dalo-sec-title">II. Obyekt va tutash hudud tasnifi</div>

                        <div class="dalo-fblock">
                            <label>1. Tadbirkorlik subyekti nomi:</label>
                            <input type="text" name="business_name" class="dalo-full-inp"
                                placeholder='"__________________________________" MChJ'
                                value="{{ old('business_name') }}">
                        </div>

                        <div class="dalo-fblock">
                            <label>2. Yuridik manzil:</label>
                            <input type="text" name="address" class="dalo-full-inp"
                                placeholder="____________ t., MFY, ko'cha, uy raqami"
                                value="{{ old('address') }}">
                        </div>

                        <div class="dalo-2col">
                            <div class="dalo-fblock">
                                <label>3. Faoliyat turi</label>
                                <input type="text" name="activity_type" class="dalo-full-inp"
                                    placeholder="__________________" value="{{ old('activity_type') }}">
                            </div>
                            <div class="dalo-fblock">
                                <label>4. Tutash hudud (kv.m)</label>
                                <input type="number" name="area_sqm" class="dalo-full-inp"
                                    placeholder="________ kv.m" step="0.01" min="0" value="{{ old('area_sqm') }}">
                            </div>
                        </div>

                        <div class="dalo-2col">
                            <div class="dalo-fblock">
                                <label>5. Kadastr raqami <span style="color:#e53e3e">*</span></label>
                                <input type="text" name="cadastral_number" id="cadastral_number"
                                    class="dalo-full-inp @error('cadastral_number') is-invalid @enderror"
                                    placeholder="10:01:00:00:0001"
                                    value="{{ old('cadastral_number') }}" required>
                                @error('cadastral_number')
                                    <div class="invalid-feedback" style="font-size:.72rem">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="dalo-fblock">
                                <label>Foydalanish maqsadi</label>
                                <input type="text" name="purpose" class="dalo-full-inp"
                                    placeholder="________________________________________" value="{{ old('purpose') }}">
                            </div>
                        </div>

                        <div class="dalo-fblock">
                            <label>6. Mavjud bino/inshootlar:</label>
                            <input type="text" name="existing_structures" class="dalo-full-inp"
                                placeholder="________________________________________" value="{{ old('existing_structures') }}">
                        </div>

                        <div class="dalo-fblock">
                            <label>7. Izoh:</label>
                            <textarea name="description" class="dalo-textarea"
                                placeholder="________________________________________">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- File upload --}}
                    <div class="dalo-section" style="background:#fff">
                        <div class="dalo-sec-title">Hujjat yuklash (ixtiyoriy)</div>
                        <p style="font-size:.74rem;color:#94a3b8;margin-bottom:8px">
                            Ariza xati yoki tegishli hujjatlarni biriktiring. (PDF, DOC, maks. 10 MB)
                        </p>
                        <input type="file" name="documents[]" class="form-control form-control-sm"
                            multiple accept=".pdf,.doc,.docx">
                    </div>

                    {{-- Section III – Applicant E-IMZO signature --}}
                    <div class="dalo-sec-iii">
                        <div class="dalo-sec-iii-title">III. Dalolatnoma bilan tanishganlik</div>
                        <div class="dalo-sig-row">
                            <div class="dalo-sig-blk">
                                <span class="dalo-sig-hint">Subyekt rahbari (vakili):</span>
                                <span class="dalo-sig-val">______________________</span>
                            </div>
                            <div class="dalo-sig-blk" style="text-align:right">
                                <span class="dalo-sig-hint">Imzo / Muhr</span>
                                <span class="dalo-sig-val">______________________</span>
                            </div>
                        </div>
                        <button type="button" class="btn-dalo" onclick="openSignModal()">
                            🔑 E-IMZO orqali ariza yuborish
                        </button>
                        <div class="dalo-submit-hint">Ariza yuborishdan oldin elektron imzo bilan tasdiqlanadi</div>
                    </div>

                </div>{{-- /left --}}

                {{-- ──────────── RIGHT column — Commission signatures ──────────── --}}
                <div class="dalo-right-col">
                    <div class="dalo-comm-title">Komissiya a'zolari</div>
                    @foreach(\App\Models\DalolatnomaSignature::POSITIONS as $posLabel)
                    <div class="dalo-comm-slot">
                        <p class="dalo-comm-name">{{ $posLabel }}:</p>
                        <div class="dalo-comm-line">
                            <span class="dalo-imzo-ghost">Imzo</span>
                            <span style="width:56px"></span>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>{{-- /dalo-body --}}
        </form>

        <div class="dalo-doc-foot">
            Elektron tizim orqali shakllantirildi — Tutash Hudud &nbsp;·&nbsp; VM №478
        </div>
    </div>

</div>
</main>

{{-- E-IMZO signing modal --}}
<div class="modal fade" id="eimzo-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">ERI ni tanlang</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="eimzo-status" style="margin-bottom:8px"></div>
                <div id="eimzo-message"></div>
                <div id="eimzo-progress"></div>
                <div id="eimzo-keys-list">
                    <div class="keys-loader">
                        <div class="keys-spinner"></div>
                        <span>E-IMZO bilan ulanilmoqda...</span>
                    </div>
                </div>
                <div id="modal-sign-error" class="text-danger" style="font-size:.82rem;margin-top:8px;display:none"></div>
            </div>
            <div class="modal-footer">
                <button type="button"
                    style="background:transparent;border:1.5px solid #e0e0e0;color:#666;padding:9px 20px;border-radius:10px;font-size:.93rem;cursor:pointer"
                    data-bs-dismiss="modal">
                    Bekor qilish
                </button>
                <button type="button" id="modal-sign-btn" class="btn btn-primary px-4"
                    onclick="signAndSubmit()" disabled>
                    🔑 Imzolash va yuborish
                </button>
            </div>
        </div>
    </div>
</div>

<footer class="site-footer">
    © {{ date('Y') }} Qo'shni hudud tizimi &nbsp;·&nbsp; Vazirlar Mahkamasi Qarori №478 asosida
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/e-imzo.js') }}"></script>
<script src="{{ asset('js/e-imzo-client.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
// Enable sign button when a key card is selected
document.addEventListener('click', function(e) {
    if (e.target.closest('.key-card')) {
        document.getElementById('modal-sign-btn').disabled = false;
    }
});

// Validate required fields → open E-IMZO modal
function openSignModal() {
    var district  = document.getElementById('district_id').value;
    var cadastral = document.getElementById('cadastral_number').value.trim();
    if (!district) {
        document.getElementById('district_id').focus();
        document.getElementById('district_id').classList.add('is-invalid');
        return;
    }
    if (!cadastral) {
        document.getElementById('cadastral_number').focus();
        document.getElementById('cadastral_number').classList.add('is-invalid');
        return;
    }
    document.getElementById('pkcs7-field').value = '';
    document.getElementById('modal-sign-btn').disabled = true;
    var errEl = document.getElementById('modal-sign-error');
    if (errEl) errEl.style.display = 'none';
    new bootstrap.Modal(document.getElementById('eimzo-modal')).show();
}

// Clear invalid state on change
document.getElementById('district_id').addEventListener('change', function() {
    this.classList.remove('is-invalid');
    var introEl = document.getElementById('intro-district');
    if (introEl) {
        var txt = this.options[this.selectedIndex];
        introEl.textContent = (txt && txt.value) ? txt.text : '__________________';
        introEl.style.color = (txt && txt.value) ? '#1e3a8a' : '';
    }
});
document.getElementById('cadastral_number').addEventListener('input', function() { this.classList.remove('is-invalid'); });

// Live-update intro paragraph blanks
document.querySelector('[name="street_name"]').addEventListener('input', function() {
    var introEl = document.getElementById('intro-street');
    if (introEl) {
        introEl.textContent = this.value.trim() || '__________________';
        introEl.style.color = this.value.trim() ? '#1e3a8a' : '';
    }
});

// Sign with E-IMZO then submit form
function signAndSubmit() {
    if (typeof selectedCardVo === 'undefined' || !selectedCardVo) {
        alert('Iltimos, kalitni tanlang');
        return;
    }
    var vo = selectedCardVo;
    if (vo.expired) { alert("Bu kalitning muddati tugagan. Boshqa kalit tanlang."); return; }

    var cadastral = document.getElementById('cadastral_number').value.trim();
    var distEl    = document.getElementById('district_id');
    var distText  = distEl && distEl.selectedIndex >= 0 ? (distEl.options[distEl.selectedIndex].text || '') : '';
    var dataToSign = 'ARIZA|' + cadastral + '|' + distText + '|' + new Date().toISOString();

    var btn = document.getElementById('modal-sign-btn');
    var errEl = document.getElementById('modal-sign-error');
    btn.disabled = true; btn.classList.add('signing'); btn.textContent = 'Kalit yuklanmoqda...';
    if (errEl) errEl.style.display = 'none';

    EIMZOClient.loadKey(vo, function(keyId) {
        btn.textContent = 'Imzolanmoqda...';
        EIMZOClient.createPkcs7(keyId, dataToSign, null, function(pkcs7) {
            document.getElementById('pkcs7-field').value         = pkcs7;
            document.getElementById('expected-pinfl-field').value = vo.PINFL || vo.UID || '';
            document.getElementById('expected-name-field').value  = vo.CN || '';
            var modal = bootstrap.Modal.getInstance(document.getElementById('eimzo-modal'));
            if (modal) {
                document.getElementById('eimzo-modal').addEventListener('hidden.bs.modal', function h() {
                    document.getElementById('eimzo-modal').removeEventListener('hidden.bs.modal', h);
                    document.getElementById('apply-form').submit();
                });
                modal.hide();
            } else {
                document.getElementById('apply-form').submit();
            }
        }, function(err) {
            btn.disabled = false; btn.classList.remove('signing'); btn.innerHTML = '🔑 Imzolash va yuborish';
            if (errEl) { errEl.textContent = 'Imzolashda xatolik: ' + (err || 'nomalum'); errEl.style.display = 'block'; }
        }, false);
    }, function(err) {
        btn.disabled = false; btn.classList.remove('signing'); btn.innerHTML = '🔑 Imzolash va yuborish';
        if (errEl) { errEl.textContent = 'Kalit yuklanmadi: ' + (err || 'nomalum'); errEl.style.display = 'block'; }
    }, true);
}
</script>
</body>
</html>
