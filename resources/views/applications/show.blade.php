@extends('layouts.app')

@section('title', 'Ariza — ' . $application->number)

@push('styles')
<style>
/* ─── Layout ─── */
.detail-card { background:#fff; border-radius:14px; padding:24px; margin-bottom:20px; border:1px solid #f0f2f5; box-shadow:0 2px 8px rgba(0,0,0,.05); }
.detail-card h5 { font-weight:700; color:#15191e; border-bottom:1px solid #f0f2f5; padding-bottom:10px; margin-bottom:16px; }
.info-row { display:flex; align-items:baseline; gap:8px; margin-bottom:10px; }
.info-label { color:#6e788b; font-size:.85rem; min-width:160px; flex-shrink:0; }
.info-value { font-weight:500; color:#15191e; }

/* ─── Status badge ─── */
.status-badge { font-size:.78rem; padding:4px 12px; border-radius:20px; font-weight:600; border:1px solid transparent; }
.status-pending          { background:#fff3cd; color:#856404; border-color:#fec524; }
.status-moderator_review { background:rgba(20,113,240,.08); color:#1471f0; border-color:#1471f0; }
.status-complaint_review { background:rgba(14,186,148,.08); color:#0eba94; border-color:#0eba94; }
.status-legal_review     { background:rgba(103,60,200,.08); color:#673cc8; border-color:#673cc8; }
.status-executor_review  { background:rgba(213,141,61,.08); color:#a05a00; border-color:#d5893d; }
.status-head_review      { background:rgba(1,140,135,.08);  color:#018c87; border-color:#018c87; }
.status-approved         { background:rgba(6,184,56,.08);   color:#0bc33f; border-color:#0bc33f; }
.status-rejected         { background:rgba(230,50,96,.08);  color:#e63260; border-color:#e63260; }

/* ─── Workflow timeline ─── */
.timeline { position:relative; padding-left:30px; }
.timeline::before { content:''; position:absolute; left:14px; top:0; bottom:0; width:2px; background:#f0f2f5; }
.tl-step { position:relative; margin-bottom:24px; }
.tl-dot {
    position:absolute; left:-22px; top:4px;
    width:18px; height:18px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:10px; font-weight:700; border:2px solid #d7dde1; background:#fff;
}
.tl-dot.waiting  { background:#f4f6f8; border-color:#d7dde1; color:#aab0bb; }
.tl-dot.pending  { background:#fff3cd; border-color:#fec524; color:#9a6800; }
.tl-dot.approved { background:rgba(6,184,56,.1); border-color:#0bc33f; color:#0bc33f; }
.tl-dot.rejected { background:rgba(230,50,96,.1); border-color:#e63260; color:#e63260; }
.tl-body { background:#f9fafb; border-radius:10px; padding:14px 16px; border:1px solid #f0f2f5; }
.tl-body.tl-active { background:rgba(1,140,135,.05); border:1px solid #018c87; }
.tl-role { font-weight:700; font-size:.9rem; margin-bottom:4px; color:#15191e; }
.tl-meta { font-size:.8rem; color:#6e788b; }
.tl-comment { font-size:.85rem; color:#27314b; margin-top:8px; padding:8px 12px; background:#fff; border-radius:6px; border-left:3px solid #018c87; }

/* ─── Approve form ─── */
.approve-form { background:#fff; border:2px solid #018c87; border-radius:12px; padding:20px; margin-top:10px; }
.approve-form h6 { font-weight:700; color:#018c87; margin-bottom:14px; }
.sign-box { border:2px dashed #018c87; border-radius:8px; padding:16px; text-align:center; background:rgba(1,140,135,.04); margin-bottom:12px; }
.sign-status { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:20px; font-size:.8rem; font-weight:600; }
.sign-ok   { background:rgba(6,184,56,.1); color:#0bc33f; }
.sign-none { background:#fff3cd; color:#9a6800; }

/* ─── Dalolatnoma slots ─── */
.dalo-slot { border-radius:10px; padding:14px 16px; border:1px solid #f0f2f5; background:#f9fafb; min-height:100px; position:relative; }
.dalo-slot.dalo-signed  { background:rgba(6,184,56,.05); border-color:#0bc33f; }
.dalo-slot.dalo-mine    { background:rgba(1,140,135,.05); border-color:#018c87; border-style:dashed; }
.dalo-slot.dalo-waiting { background:#f4f6f8; border-color:#e0e5ea; }
.dalo-pos { font-size:.83rem; font-weight:700; color:#27314b; margin-bottom:8px; }
.dalo-signed-body { display:flex; align-items:flex-start; gap:10px; margin-top:4px; }
.dalo-check { background:rgba(6,184,56,.12); color:#0bc33f; border-radius:50%; width:22px; height:22px;
    display:inline-flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700; flex-shrink:0; }
.dalo-signer-info { flex:1; min-width:0; }
.dalo-name { font-weight:600; font-size:.85rem; color:#15191e; }
.dalo-date { font-size:.75rem; color:#6e788b; display:block; }
.dalo-qr-link { display:block; flex-shrink:0; }
.dalo-qr-img { border-radius:6px; border:1px solid #d7dde1; display:block; }
.dalo-waiting-label { font-size:.8rem; color:#aab0bb; margin-top:6px; }
.doc-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:8px; background:#f8f9fa; margin-bottom:8px; }
.doc-icon { font-size:1.4rem; flex-shrink:0; }
.doc-info { flex:1; min-width:0; }
.doc-name { font-weight:500; font-size:.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.doc-meta { font-size:.75rem; color:#6c757d; }
.doc-type-badge { font-size:.7rem; padding:2px 8px; border-radius:10px; background:#e2d9f3; color:#432874; }
</style>
@endpush

@section('content')
{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('applications.index') }}" class="btn btn-sm btn-outline-secondary">← Orqaga</a>
        <div>
            <h2 class="mb-0">{{ $application->number }}</h2>
            <div class="text-muted small">{{ $application->submitted_at?->setTimezone(config('app.timezone'))->format('d.m.Y H:i') }}</div>
        </div>
    </div>
    <span class="status-badge status-{{ $application->status }}">{{ $application->statusLabel() }}</span>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- Left column --}}
    <div class="col-lg-7">

        {{-- Application info --}}
        <div class="detail-card">
            <h5>Ariza tafsilotlari</h5>
            <div class="info-row"><span class="info-label">Kadastr raqami</span><span class="info-value">{{ $application->cadastral_number }}</span></div>
            <div class="info-row"><span class="info-label">Tuman</span><span class="info-value">{{ $application->district->name_uz }}</span></div>
            @if($application->address)
            <div class="info-row"><span class="info-label">Manzil</span><span class="info-value">{{ $application->address }}</span></div>
            @endif
            @if($application->area_sqm)
            <div class="info-row"><span class="info-label">Maydon</span><span class="info-value">{{ number_format($application->area_sqm, 2) }} m²</span></div>
            @endif
            <div class="info-row"><span class="info-label">Ariza turi</span><span class="info-value">{{ $application->source === 'online' ? 'Onlayn' : 'Qog\'oz (yozma)' }}</span></div>
            @if($application->description)
            <div class="info-row"><span class="info-label">Izoh</span><span class="info-value">{{ $application->description }}</span></div>
            @endif
            @if(!auth()->user()->isConsumer())
            <hr>
            <div class="info-row"><span class="info-label">Ariza beruvchi</span><span class="info-value">{{ $application->applicant->name }}</span></div>
            @if($application->applicant->pinfl)
            <div class="info-row"><span class="info-label">PINFL</span><span class="info-value">{{ $application->applicant->pinfl }}</span></div>
            @endif
            @if($application->applicant->organization)
            <div class="info-row"><span class="info-label">Tashkilot</span><span class="info-value">{{ $application->applicant->organization }}</span></div>
            @endif
            @if($application->applicant_pkcs7)
            <div class="info-row"><span class="info-label">E-IMZO imzo</span><span class="info-value text-success small">&#10003; Mavjud</span></div>
            @endif
            @endif
        </div>

        {{-- Workflow timeline --}}
        <div class="detail-card">
            <h5>Jarayon holati</h5>
            @php $canApproveStep = null; @endphp
            <div class="timeline">
                @foreach($application->approvals as $approval)
                @php
                    $isActive = $approval->status === 'pending';
                    $userCanApprove = auth()->user()->canApproveStep($approval->step_role, $application->district_id)
                                   && $isActive;
                @endphp
                <div class="tl-step">
                    <div class="tl-dot {{ $approval->status }}">
                        @if($approval->status === 'approved') ✓
                        @elseif($approval->status === 'rejected') ✗
                        @elseif($approval->status === 'pending') ●
                        @else {{ $approval->step_order }}
                        @endif
                    </div>
                    <div class="tl-body {{ $isActive ? 'tl-active' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="tl-role">
                                {{ $approval->step_order }}. {{ $approval->roleLabel() }}
                                @if($approval->is_backup_approval)
                                <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem">Zaxira</span>
                                @endif
                            </div>
                            <div>
                                @if($approval->status === 'approved')
                                    <span class="badge bg-success">Tasdiqlandi</span>
                                @elseif($approval->status === 'rejected')
                                    <span class="badge bg-danger">Rad etildi</span>
                                @elseif($approval->status === 'pending')
                                    <span class="badge bg-warning text-dark">Kutilmoqda</span>
                                @else
                                    <span class="badge bg-secondary">Navbatda</span>
                                @endif
                            </div>
                        </div>
                        <div class="tl-meta">
                            @if($approval->assignee)
                            Mas'ul: {{ $approval->assignee->name }}
                            @if($approval->assignee->district)
                            ({{ $approval->assignee->district->name }})
                            @endif
                            @endif
                            @if($approval->approved_at)
                            &bull; {{ $approval->approved_at->setTimezone(config('app.timezone'))->format('d.m.Y H:i') }}
                            @endif
                            @if($approval->approver && $approval->approver->id !== $approval->assigned_to)
                            &bull; {{ $approval->approver->name }} tomonidan
                            @endif
                        </div>
                        @if($approval->comments)
                        <div class="tl-comment">{{ $approval->comments }}</div>
                        @endif

                        {{-- Approve/reject form for current user --}}
                        @if($userCanApprove)
                        <div class="approve-form mt-3" id="approve-form-{{ $approval->id }}">
                            <h6>Tasdiqlash / Rad etish</h6>
                            <form method="POST" action="{{ route('workflow.approve', $approval) }}" id="wf-form-{{ $approval->id }}">
                                @csrf
                                <input type="hidden" name="action" id="action-{{ $approval->id }}" value="approve">
                                <input type="hidden" name="pkcs7" id="wf-pkcs7-{{ $approval->id }}">

                                {{-- Executor extra fields --}}
                                @if($approval->step_role === 'executor')
                                <div class="alert alert-info small mb-3">
                                    Ijrochi sifatida hisob-kitob ma'lumotlarini to'ldiring:
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">To'lovchi FIO</label>
                                        <input type="text" name="payer_name" class="form-control form-control-sm"
                                            value="{{ $application->calculation?->payer_name ?? $application->applicant->name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">To'lovchi PINFL</label>
                                        <input type="text" name="payer_pinfl" class="form-control form-control-sm"
                                            value="{{ $application->calculation?->payer_pinfl ?? $application->applicant->pinfl }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Maydon (m²)</label>
                                        <input type="number" name="area_sqm" class="form-control form-control-sm"
                                            step="0.01" value="{{ $application->calculation?->area_sqm ?? $application->area_sqm }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">1 m² narxi (so'm)</label>
                                        <input type="number" name="rate_per_sqm" class="form-control form-control-sm"
                                            step="0.01" value="{{ $application->calculation?->rate_per_sqm }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">Jarima (so'm)</label>
                                        <input type="number" name="penalty_amount" class="form-control form-control-sm"
                                            step="0.01" value="{{ $application->calculation?->penalty_amount ?? 0 }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">To'langan (so'm)</label>
                                        <input type="number" name="paid_amount" class="form-control form-control-sm"
                                            step="0.01" value="{{ $application->calculation?->paid_amount ?? 0 }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">To'lov muddati</label>
                                        <input type="date" name="payment_deadline" class="form-control form-control-sm"
                                            value="{{ $application->calculation?->payment_deadline?->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small">To'lov davri</label>
                                        <input type="text" name="payment_period" class="form-control form-control-sm"
                                            placeholder="Masalan: 12 oy" value="{{ $application->calculation?->payment_period }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small">Hisob-kitob izohi</label>
                                        <textarea name="calc_notes" class="form-control form-control-sm" rows="2">{{ $application->calculation?->notes }}</textarea>
                                    </div>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Izoh (ixtiyoriy)</label>
                                    <textarea name="comments" class="form-control form-control-sm" rows="2"
                                        placeholder="Qaror asosini yozing..."></textarea>
                                </div>

                                {{-- E-IMZO signing --}}
                                <div id="eimzo-status" class="mb-1"></div>
                                <div id="eimzo-message" class="mb-2"></div>
                                <div class="sign-box mb-3">
                                    <div id="sign-state-{{ $approval->id }}">
                                        <span class="sign-status sign-none">&#9888; Imzosiz</span>
                                    </div>
                                    <div id="signed-state-{{ $approval->id }}" style="display:none">
                                        <span class="sign-status sign-ok">&#10003; Imzolandi</span>
                                        <div class="text-muted small mt-1" id="signed-info-{{ $approval->id }}"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <select id="eimzo-keys" class="form-select form-select-sm">
                                        <option value="">— Kalitni tanlang —</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mb-3"
                                    onclick="signApproval({{ $approval->id }})">
                                    E-IMZO bilan imzolash
                                </button>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success"
                                        onclick="submitApproval({{ $approval->id }}, 'approve')">
                                        ✓ Tasdiqlash
                                    </button>
                                    <button type="button" class="btn btn-danger"
                                        onclick="submitApproval({{ $approval->id }}, 'reject')">
                                        ✗ Rad etish
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Calculation (if exists) --}}
        @if($application->calculation)
        <div class="detail-card">
            <h5>Hisob-kitob</h5>
            @php $calc = $application->calculation; @endphp
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-row"><span class="info-label">To'lovchi</span><span class="info-value">{{ $calc->payer_name ?? '—' }}</span></div>
                    <div class="info-row"><span class="info-label">PINFL</span><span class="info-value">{{ $calc->payer_pinfl ?? '—' }}</span></div>
                    <div class="info-row"><span class="info-label">Maydon</span><span class="info-value">{{ $calc->area_sqm ? number_format($calc->area_sqm, 2) . ' m²' : '—' }}</span></div>
                    <div class="info-row"><span class="info-label">1 m² narxi</span><span class="info-value">{{ $calc->rate_per_sqm ? number_format($calc->rate_per_sqm, 0, '.', ' ') . ' so\'m' : '—' }}</span></div>
                </div>
                <div class="col-md-6">
                    <div class="info-row"><span class="info-label">Jami summa</span><span class="info-value fw-bold text-primary">{{ $calc->total_amount ? number_format($calc->total_amount, 0, '.', ' ') . ' so\'m' : '—' }}</span></div>
                    <div class="info-row"><span class="info-label">Jarima</span><span class="info-value text-danger">{{ number_format($calc->penalty_amount, 0, '.', ' ') }} so'm</span></div>
                    <div class="info-row"><span class="info-label">To'langan</span><span class="info-value text-success">{{ number_format($calc->paid_amount, 0, '.', ' ') }} so'm</span></div>
                    <div class="info-row">
                        <span class="info-label">Qoldiq</span>
                        <span class="info-value fw-bold {{ $calc->remainingAmount() > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($calc->remainingAmount(), 0, '.', ' ') }} so'm
                        </span>
                    </div>
                    @if($calc->payment_deadline)
                    <div class="info-row"><span class="info-label">To'lov muddati</span><span class="info-value">{{ $calc->payment_deadline->format('d.m.Y') }}</span></div>
                    @endif
                    @if($calc->payment_period)
                    <div class="info-row"><span class="info-label">To'lov davri</span><span class="info-value">{{ $calc->payment_period }}</span></div>
                    @endif
                </div>
                @if($calc->notes)
                <div class="col-12">
                    <div class="info-label mb-1">Izoh:</div>
                    <div class="tl-comment">{{ $calc->notes }}</div>
                </div>
                @endif
                <div class="col-12 text-muted small">
                    Hisoblagan: {{ $calc->calculator?->name }}
                    &bull; {{ $calc->updated_at->setTimezone(config('app.timezone'))->format('d.m.Y H:i') }}
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- Right column — documents --}}
    <div class="col-lg-5">
        <div class="detail-card">
            <h5>Hujjatlar</h5>

            @if($application->documents->isEmpty())
            <p class="text-muted small mb-3">Hujjat yuklanmagan.</p>
            @else
            <div class="mb-3">
                @foreach($application->documents as $doc)
                <div class="doc-item">
                    <div class="doc-icon">📄</div>
                    <div class="doc-info">
                        <div class="doc-name">{{ $doc->original_name }}</div>
                        <div class="doc-meta">
                            <span class="doc-type-badge">{{ $doc->typeLabel() }}</span>
                            &nbsp;{{ round(($doc->size ?? 0) / 1024) }} KB
                            &nbsp;&bull;&nbsp;{{ $doc->uploader->name ?? '—' }}
                            &nbsp;&bull;&nbsp;{{ $doc->created_at->setTimezone(config('app.timezone'))->format('d.m.Y') }}
                        </div>
                        @if($doc->notes)
                        <div class="text-muted" style="font-size:.75rem">{{ $doc->notes }}</div>
                        @endif
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        <a href="{{ asset('storage/' . $doc->path) }}" target="_blank"
                            class="btn btn-sm btn-outline-primary" title="Ko'rish">⬇</a>
                        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <form method="POST" action="{{ route('applications.documents.destroy', $doc) }}"
                            onsubmit="return confirm('O\'chirilsinmi?')" class="m-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="O'chirish">✕</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Upload form: visible to staff and admin --}}
            @if(!auth()->user()->isConsumer() || $application->status === 'pending')
            <hr>
            <h6 class="fw-semibold mb-3 small text-muted text-uppercase">Yangi fayl yuklash</h6>
            <form method="POST" action="{{ route('applications.documents.store', $application) }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <select name="doc_type" class="form-select form-select-sm">
                        <option value="application_letter">Ariza xati</option>
                        <option value="contract">Shartnoma</option>
                        <option value="other">Boshqa</option>
                    </select>
                </div>
                <div class="mb-2">
                    <input type="file" name="document" class="form-control form-control-sm"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="doc_notes" class="form-control form-control-sm" placeholder="Izoh (ixtiyoriy)">
                </div>
                <button type="submit" class="btn btn-sm btn-primary w-100">Yuklash</button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- ═══ Dalolatnoma imzolari ═══ --}}
@if(!auth()->user()->isConsumer())
@php
    $dalSigs = $application->dalolatnomaSignatures->keyBy('commission_position');
    /** @var \App\Models\User $authUser */
    $authUser = auth()->user();
    $isCommissionUser = $authUser->isCommission() && $authUser->commission_position;
@endphp
<div class="detail-card mt-2">
    <h5>Dalolatnoma imzolari — Komissiya a'zolari</h5>

    @if(session('dalo_success'))
    <div class="alert alert-success alert-dismissible fade show py-2">
        {{ session('dalo_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">
    @foreach(\App\Models\DalolatnomaSignature::POSITIONS as $posKey => $posLabel)
    @php
        $sig = $dalSigs->get($posKey);
        $isMine = $isCommissionUser && $authUser->commission_position === $posKey;
    @endphp
    <div class="col-md-6">
        <div class="dalo-slot {{ $sig ? 'dalo-signed' : ($isMine ? 'dalo-mine' : 'dalo-waiting') }}">
            <div class="dalo-pos">{{ $posLabel }}</div>

            @if($sig)
                {{-- ✓ Signed --}}
                <div class="dalo-signed-body">
                    <span class="dalo-check">✓</span>
                    <div class="dalo-signer-info">
                        <div class="dalo-name">{{ $sig->signer->name ?? '—' }}</div>
                        <span class="dalo-date">{{ $sig->signed_at->setTimezone(config('app.timezone', 'Asia/Tashkent'))->format('d.m.Y H:i') }}</span>
                    </div>
                    <a href="{{ $sig->getVerifyUrl() }}" target="_blank" class="dalo-qr-link" title="Tasdiqlash">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($sig->getVerifyUrl()) }}&size=80x80&margin=4"
                             width="80" height="80" alt="QR" class="dalo-qr-img">
                    </a>
                </div>

            @elseif($isMine)
                {{-- 🔑 Current commission user – unsigned slot --}}
                <div id="eimzo-status" class="mb-1 small"></div>
                <div id="eimzo-message" class="mb-1 small"></div>
                <div id="eimzo-progress" class="mb-1 small"></div>
                <form method="POST" action="{{ route('dalolatnoma.sign', $application) }}" id="dalo-form">
                    @csrf
                    <input type="hidden" name="pkcs7" id="dalo-pkcs7">
                    <div class="mb-2">
                        <select id="eimzo-keys" class="form-select form-select-sm">
                            <option value="">— Kalitni tanlang —</option>
                        </select>
                    </div>
                    <div id="dalo-sign-state" class="mb-2">
                        <span class="sign-status sign-none" style="font-size:.75rem">&#9888; Imzosiz</span>
                    </div>
                    <div id="dalo-signed-state" style="display:none" class="mb-2">
                        <span class="sign-status sign-ok" style="font-size:.75rem">&#10003; Imzolandi</span>
                        <div class="text-muted small mt-1" id="dalo-signed-info"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="signDalolatnoma()">
                            E-IMZO bilan imzolash
                        </button>
                        <button type="submit" class="btn btn-sm btn-success" id="dalo-submit-btn" disabled>
                            Saqlash
                        </button>
                    </div>
                </form>

            @else
                {{-- ⏳ Other's slot, waiting --}}
                <div class="dalo-waiting-label">&#8987; Imzo kutilmoqda</div>
            @endif
        </div>
    </div>
    @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function signApproval(approvalId) {
    var keyEl = document.getElementById('eimzo-keys');
    if (!keyEl || !keyEl.value) {
        alert('Iltimos, avval kalitni tanlang');
        return;
    }
    var itmKey = keyEl.value;
    var dataToSign = 'APPROVAL|{{ $application->number }}|' + approvalId + '|' + new Date().toISOString();

    EIMZOClient.createPkcs7(itmKey, dataToSign, null, function(pkcs7) {
        document.getElementById('wf-pkcs7-' + approvalId).value = pkcs7;

        document.getElementById('sign-state-' + approvalId).style.display = 'none';
        document.getElementById('signed-state-' + approvalId).style.display = 'block';

        var vo = null;
        var opt = keyEl.options[keyEl.selectedIndex];
        if (opt && opt.getAttribute('data-vo')) {
            try { vo = JSON.parse(opt.getAttribute('data-vo')); } catch(e) {}
        }
        if (vo) {
            var el = document.getElementById('signed-info-' + approvalId);
            if (el) el.textContent = (vo.CN || '') + (vo.serialNumber ? ' · ' + vo.serialNumber : '');
        }
    }, function(err) {
        alert('Imzolashda xatolik: ' + (err || 'nomalum'));
    });
}

function submitApproval(approvalId, action) {
    document.getElementById('action-' + approvalId).value = action;
    if (action === 'reject') {
        var comments = document.querySelector('#wf-form-' + approvalId + ' [name=comments]').value;
        if (!comments.trim()) {
            alert('Rad etish uchun izoh yozing');
            return;
        }
    }
    document.getElementById('wf-form-' + approvalId).submit();
}
</script>

<script>
function signDalolatnoma() {
    var keyEl = document.getElementById('eimzo-keys');
    if (!keyEl || !keyEl.value) {
        alert('Iltimos, avval E-IMZO kalitini tanlang');
        return;
    }
    var itmKey = keyEl.value;
    var dataToSign = 'DALOLATNOMA|{{ $application->number }}|{{ $application->id }}|' + new Date().toISOString();

    EIMZOClient.createPkcs7(itmKey, dataToSign, null, function(pkcs7) {
        document.getElementById('dalo-pkcs7').value = pkcs7;
        document.getElementById('dalo-sign-state').style.display = 'none';
        document.getElementById('dalo-signed-state').style.display = 'block';

        var opt = keyEl.options[keyEl.selectedIndex];
        if (opt && opt.getAttribute('data-vo')) {
            try {
                var vo = JSON.parse(opt.getAttribute('data-vo'));
                var el = document.getElementById('dalo-signed-info');
                if (el) el.textContent = (vo.CN || '') + (vo.serialNumber ? ' · ' + vo.serialNumber : '');
            } catch(e) {}
        }
        document.getElementById('dalo-submit-btn').disabled = false;
    }, function(err) {
        alert('Imzolashda xatolik: ' + (err || 'nomalum'));
    });
}
</script>
@endpush
