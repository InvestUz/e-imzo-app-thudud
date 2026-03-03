@extends('layouts.app')

@section('title', 'Yangi ariza — Dalolatnoma')

@push('styles')
<style>
/* ── Dalolatnoma document card ── */
.dalo-wrap        { max-width: 980px; margin: 0 auto; }
.dalo-card        { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,.09); }

/* Header */
.dalo-header      { text-align: center; padding: 28px 36px 20px; border-bottom: 3px solid #018c87; background: #fafefe; }
.dalo-emblem      { height: 64px; display: block; margin: 0 auto 12px; }
.dalo-doc-title   { font-family: 'Georgia', 'Times New Roman', serif; font-size: 1.45rem; font-weight: 700;
                    color: #018c87; letter-spacing: .06em; text-transform: uppercase; margin-bottom: 4px; }
.dalo-doc-sub     { font-size: .78rem; font-weight: 600; color: #555; text-transform: uppercase; letter-spacing: .04em; }
.dalo-doc-meta    { display: flex; justify-content: space-between; margin-top: 16px; font-size: .84rem; font-weight: 500; color: #333; }

/* Intro paragraph */
.dalo-intro       { font-size: .8rem; color: #555; line-height: 1.75; text-align: justify;
                    padding: 14px 36px; border-bottom: 1px solid #e9ecef; background: #fcfcfc; }

/* Body */
.dalo-body        { padding: 26px 36px 28px; }

/* Sections */
.dalo-section     { border: 1px solid #dee2e6; border-radius: 7px; padding: 16px 20px; background: #fafafa; margin-bottom: 18px; }
.dalo-section-title {
    color: #018c87; font-weight: 700; font-size: .76rem; text-transform: uppercase;
    letter-spacing: .06em; border-bottom: 1px solid #dee2e6; padding-bottom: 8px; margin-bottom: 14px;
}

/* Underline inputs (matches the dalolatnoma blanks) */
.dalo-field       { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 10px; gap: 12px; font-size: .84rem; }
.dalo-field label { white-space: nowrap; color: #444; flex-shrink: 0; }
.dalo-in          { border: none; border-bottom: 1px solid #94a3b8; background: transparent;
                    color: #1e3a8a; font-weight: 500; padding: 2px 4px; font-size: .84rem; flex: 1; min-width: 100px; }
.dalo-in:focus    { outline: none; border-bottom-color: #018c87; }
.dalo-in-w        { width: 100%; margin-top: 4px; }
.dalo-in-sm       { width: 70px; min-width: 70px; flex: none; text-align: center; }

/* Field label block */
.dalo-label-block { font-size: .84rem; }
.dalo-label-block .lb { font-weight: 600; display: block; margin-bottom: 3px; }
.dalo-label-block .sub { font-size: .74rem; color: #64748b; display: block; margin-bottom: 3px; }

/* Section III signature area */
.sig-row          { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 16px; gap: 16px; }
.sig-box          { flex: 1; }
.sig-hint         { font-size: .72rem; color: #64748b; margin-bottom: 6px; }
.sig-line         { border-bottom: 1px solid #ccc; padding-bottom: 4px; padding-top: 6px; font-weight: 700;
                    min-width: 160px; color: #1a1a1a; font-size: .85rem; }

/* Commission column */
.comm-title       { color: #018c87; font-weight: 700; font-size: .74rem; text-transform: uppercase;
                    letter-spacing: .07em; text-align: center; margin-bottom: 18px; }
.comm-member      { margin-bottom: 16px; }
.comm-role        { font-weight: 700; color: #475569; font-size: .8rem; margin-bottom: 4px; }
.comm-line        { border-bottom: 1px solid #cbd5e1; padding-bottom: 3px;
                    display: flex; justify-content: space-between; }
.comm-hint        { font-size: .7rem; color: #94a3b8; font-style: italic; }

/* E-IMZO section */
.eimzo-box        { background: #f0fdf4; border: 1px solid #86efac; border-radius: 7px; padding: 16px 20px; margin-top: 18px; }
.eimzo-box-title  { color: #166534; font-weight: 700; font-size: .84rem; margin-bottom: 10px; }
.sign-badge       { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px;
                    border-radius: 20px; font-size: .78rem; font-weight: 600; }
.sign-none        { background: #fff3cd; color: #856404; }
.sign-ok          { background: #d4edda; color: #155724; }

/* Footer */
.dalo-footer      { padding: 11px 36px; border-top: 1px solid #e9ecef; text-align: center;
                    font-size: .64rem; color: #94a3b8; letter-spacing: .1em; text-transform: uppercase;
                    background: #fafafa; }
</style>
@endpush

@section('content')
<div class="dalo-wrap">

    @if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('applications.store') }}" enctype="multipart/form-data" id="application-form">
    @csrf

    <div class="dalo-card">

        {{-- ══ Official document header ══ --}}
        <div class="dalo-header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/77/Emblem_of_Uzbekistan.svg"
                 alt="O'zbekiston gerbi" class="dalo-emblem">
            <div class="dalo-doc-title">Dalolatnoma</div>
            <div class="dalo-doc-sub">Tadbirkorlik subyektlariga tutash hududlarni xatlovdan o'tkazish to'g'risida</div>
            <div class="dalo-doc-meta">
                <span id="district-display">Toshkent shahri</span>
                <span>{{ now()->year }}-yil «___» ____________</span>
            </div>
        </div>

        {{-- ══ Introductory legal text ══ --}}
        <div class="dalo-intro">
            Tuzildi mazkur dalolatnoma Toshkent shahri,
            <strong id="district-name-display">__________________</strong> tumani,
            <span id="street-name-display">__________________</span>
            ko'chasida joylashgan umumiy ovqatlanish, savdo va xizmat ko'rsatish sohasidagi
            tadbirkorlik subyektlari uchun o'ziga tegishli bino va inshootlarga hamda yer
            uchastkalariga tutash bo'lgan, davlat organlari va tashkilotlariga doimiy foydalanishga
            berilgan aholi punktlarining umumiy foydalanishdagi yer uchastkalarini aniqlash bo'yicha.
        </div>

        {{-- ══ Body ══ --}}
        <div class="dalo-body">
            <div class="row g-4">

                {{-- ────── Left column (7/12) ────── --}}
                <div class="col-md-7">

                    {{-- Section I --}}
                    <div class="dalo-section">
                        <div class="dalo-section-title">I. Xatlov o'tkazilgan ko'chaning tasnifi</div>

                        {{-- District --}}
                        <div class="dalo-field">
                            <label>1. Tuman:</label>
                            <select name="district_id" id="district-select"
                                    class="dalo-in @error('district_id') is-invalid @enderror"
                                    style="flex:1;min-width:150px;" required>
                                <option value="">— Tumanni tanlang —</option>
                                @foreach($districts as $d)
                                <option value="{{ $d->id }}"
                                        data-name="{{ $d->name_uz }}"
                                        {{ old('district_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->name_uz }}
                                </option>
                                @endforeach
                            </select>
                            @error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Street name --}}
                        <div class="mb-2 dalo-label-block">
                            <span class="sub">Ko'cha nomi:</span>
                            <input type="text" name="street_name" id="street-name-input"
                                   class="dalo-in dalo-in-w"
                                   placeholder="__________________"
                                   value="{{ old('street_name') }}">
                        </div>

                        {{-- Intersecting streets --}}
                        <div class="mb-2 dalo-label-block">
                            <span class="sub">Kesishgan ko'chalar:</span>
                            <input type="text" name="intersecting_streets"
                                   class="dalo-in dalo-in-w"
                                   placeholder="__________________ va __________________"
                                   value="{{ old('intersecting_streets') }}">
                        </div>

                        {{-- Road distance --}}
                        <div class="dalo-field mt-2">
                            <label>2. Avtomobil yo'ligacha masofa (m):</label>
                            <input type="number" name="road_distance"
                                   class="dalo-in dalo-in-sm"
                                   placeholder="____" step="0.1" min="0"
                                   value="{{ old('road_distance') }}">
                        </div>

                        {{-- Pedestrian distance --}}
                        <div class="dalo-field">
                            <label>3. Piyodalar yo'lagigacha masofa (m):</label>
                            <input type="number" name="pedestrian_distance"
                                   class="dalo-in dalo-in-sm"
                                   placeholder="____" step="0.1" min="0"
                                   value="{{ old('pedestrian_distance') }}">
                        </div>
                    </div>

                    {{-- Section II --}}
                    <div class="dalo-section">
                        <div class="dalo-section-title">II. Obyekt va tutash hudud tasnifi</div>

                        {{-- Business name --}}
                        <div class="mb-3 dalo-label-block">
                            <span class="lb">1. Tadbirkorlik subyekti nomi:</span>
                            <input type="text" name="business_name" class="dalo-in dalo-in-w"
                                   placeholder='"__________________________________" MChJ'
                                   value="{{ old('business_name') }}">
                        </div>

                        {{-- Legal address --}}
                        <div class="mb-3 dalo-label-block">
                            <span class="lb">2. Yuridik manzil:</span>
                            <input type="text" name="address" class="dalo-in dalo-in-w"
                                   placeholder="t., &quot;____&quot; MFY, ______ ko'ch., ____-uy"
                                   value="{{ old('address') }}">
                        </div>

                        {{-- Activity type + Area --}}
                        <div class="row g-3 mb-3">
                            <div class="col-6 dalo-label-block">
                                <span class="sub">3. Faoliyat turi</span>
                                <input type="text" name="activity_type" class="dalo-in dalo-in-w"
                                       placeholder="__________________"
                                       value="{{ old('activity_type') }}">
                            </div>
                            <div class="col-6 dalo-label-block">
                                <span class="sub">4. Tutash hudud (kv.m)</span>
                                <input type="number" name="area_sqm" class="dalo-in dalo-in-w"
                                       placeholder="________ kv.m" step="0.01" min="0"
                                       value="{{ old('area_sqm') }}">
                            </div>
                        </div>

                        {{-- Cadastral number --}}
                        <div class="mb-3 dalo-label-block">
                            <span class="lb">5. Kadastr raqami: <span class="text-danger">*</span></span>
                            <input type="text" name="cadastral_number"
                                   class="dalo-in dalo-in-w @error('cadastral_number') is-invalid @enderror"
                                   placeholder="10:01:00:00:0001"
                                   value="{{ old('cadastral_number') }}" required>
                            @error('cadastral_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Purpose --}}
                        <div class="mb-3 dalo-label-block">
                            <span class="lb">6. Foydalanish maqsadi:</span>
                            <input type="text" name="purpose" class="dalo-in dalo-in-w"
                                   placeholder="________________________________________"
                                   value="{{ old('purpose') }}">
                        </div>

                        {{-- Existing structures --}}
                        <div class="mb-3 dalo-label-block">
                            <span class="lb">7. Mavjud bino / inshootlar:</span>
                            <input type="text" name="existing_structures" class="dalo-in dalo-in-w"
                                   placeholder="________________________________________"
                                   value="{{ old('existing_structures') }}">
                        </div>

                        {{-- Notes --}}
                        <div class="mb-1 dalo-label-block">
                            <span class="lb">8. Izoh:</span>
                            <input type="text" name="description" class="dalo-in dalo-in-w"
                                   placeholder="________________________________________"
                                   value="{{ old('description') }}">
                        </div>
                    </div>

                    {{-- Section III --}}
                    <div style="font-size:.84rem; padding: 0 2px;">
                        <div style="font-weight:700; color:#1a1a1a; font-size:.88rem;">
                            III. Dalolatnoma bilan tanishganlik
                        </div>
                        <div class="sig-row">
                            <div class="sig-box">
                                <div class="sig-hint">Subyekt rahbari (vakili):</div>
                                <div class="sig-line">______________________</div>
                            </div>
                            <div class="sig-box text-end">
                                <div class="sig-hint">Imzo / Muhr</div>
                                <div class="sig-line">______________________</div>
                            </div>
                        </div>
                    </div>

                    {{-- ── E-IMZO ── --}}
                    <div class="eimzo-box">
                        <div class="eimzo-box-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" style="vertical-align:-2px;margin-right:5px;">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            E-IMZO bilan tasdiqlash
                        </div>

                        <div id="eimzo-message" class="mb-2"></div>

                        <div class="mb-3">
                            <div id="sign-state">
                                <span class="sign-badge sign-none">&#9888; Imzosiz</span>
                            </div>
                            <div id="signed-state" style="display:none">
                                <span class="sign-badge sign-ok">&#10003; Imzolandi</span>
                                <span class="ms-2 text-muted small" id="signed-info"></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label style="font-size:.78rem;font-weight:600;display:block;margin-bottom:6px;">
                                Kalitni tanlang
                            </label>
                            <select id="eimzo-keys" class="form-select form-select-sm">
                                <option value="">— Kalitni tanlang —</option>
                            </select>
                        </div>

                        <button type="button" class="platon-btn platon-btn-outline platon-btn-sm"
                                onclick="signApplication()">
                            E-IMZO bilan imzolash
                        </button>
                        <input type="hidden" name="pkcs7" id="application-pkcs7">
                    </div>

                    {{-- ── File upload ── --}}
                    <div class="dalo-section mt-3">
                        <div class="dalo-section-title">Hujjatlar yuklash (PDF, DOC, DOCX)</div>
                        <p class="text-muted" style="font-size:.78rem;margin-bottom:10px;">
                            Ariza xati va boshqa tegishli hujjatlarni yuklang. Bir nechta fayl tanlash mumkin.
                        </p>
                        <input type="file" name="documents[]" class="form-control form-control-sm"
                               multiple accept=".pdf,.doc,.docx">
                    </div>

                    <input type="hidden" name="source" value="online">

                    {{-- ── Action buttons ── --}}
                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <a href="{{ route('applications.index') }}" class="platon-btn platon-btn-outline">
                            Bekor qilish
                        </a>
                        <button type="submit" class="platon-btn platon-btn-primary" style="padding:0 36px;">
                            Ariza yuborish
                        </button>
                    </div>

                </div>{{-- /col-md-7 --}}

                {{-- ────── Right column (5/12) – Commission ────── --}}
                <div class="col-md-5 border-start ps-4">
                    <div class="comm-title">Komissiya a'zolari</div>

                    @php
                    $members = [
                        "Hokim o'rinbosari (Qurilish)",
                        "Qurilish bo'limi",
                        "Ekologiya bo'limi",
                        "Obodonlashtirish",
                        "Kadastr agentligi",
                        "FVV (ChS) bo'limi",
                        "Sanepidqo'mita (SES)",
                        "Soliq inspeksiyasi",
                        "IIB vakili",
                        "Hokim yordamchisi",
                    ];
                    @endphp

                    @foreach($members as $m)
                    <div class="comm-member">
                        <div class="comm-role">{{ $m }}:</div>
                        <div class="comm-line">
                            <span class="comm-hint">Imzo</span>
                            <span></span>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4 p-3 rounded text-center"
                         style="background:#f8fafc;border:1px dashed #cbd5e1;font-size:.72rem;color:#64748b;">
                        Komissiya a'zolari dalolatnomaga tasdiqlash jarayonida imzo qo'yadilar
                    </div>
                </div>{{-- /col-md-5 --}}

            </div>{{-- /row --}}
        </div>{{-- /dalo-body --}}

        {{-- ══ Footer ══ --}}
        <div class="dalo-footer">
            Elektron tizim orqali shakllantirildi — Tutash Hudud · VM №478
        </div>

    </div>{{-- /dalo-card --}}
    </form>

</div>{{-- /dalo-wrap --}}
@endsection

@push('scripts')
<script>
// Live-update district name in header + intro
document.getElementById('district-select').addEventListener('change', function () {
    var opt  = this.options[this.selectedIndex];
    var name = opt.getAttribute('data-name') || '—';
    document.getElementById('district-name-display').textContent = name;
    document.getElementById('district-display').textContent = name + ' tumani';
});

// Live-update street name in intro
document.getElementById('street-name-input').addEventListener('input', function () {
    document.getElementById('street-name-display').textContent = this.value || '__________________';
});

// Build signed payload
function getApplicationDataToSign() {
    var cadastral   = document.querySelector('[name=cadastral_number]').value || '';
    var districtEl  = document.getElementById('district-select');
    var districtTxt = districtEl ? (districtEl.options[districtEl.selectedIndex]?.text || '') : '';
    return 'ARIZA|' + cadastral + '|' + districtTxt + '|' + new Date().toISOString();
}

function signApplication() {
    var keyEl = document.getElementById('eimzo-keys');
    if (!keyEl || !keyEl.value) { alert('Iltimos, avval kalitni tanlang'); return; }

    EIMZOClient.createPkcs7(keyEl.value, getApplicationDataToSign(), null,
        function (pkcs7) {
            document.getElementById('application-pkcs7').value = pkcs7;
            document.getElementById('sign-state').style.display  = 'none';
            document.getElementById('signed-state').style.display = 'block';
            var opt = keyEl.options[keyEl.selectedIndex];
            var vo  = null;
            if (opt && opt.getAttribute('data-vo')) {
                try { vo = JSON.parse(opt.getAttribute('data-vo')); } catch (e) {}
            }
            if (vo) {
                document.getElementById('signed-info').textContent =
                    (vo.CN || '') + (vo.serialNumber ? ' · ' + vo.serialNumber : '');
            }
        },
        function (err) { alert('Imzolashda xatolik: ' + (err || 'nomalum')); }
    );
}
</script>
@endpush
