@extends('layouts.app')
@section('title', 'Barcha arizalar — Admin')

@section('content')

{{-- Filter toolbar --}}
<form method="GET" action="{{ route('admin.applications') }}">
<div class="block" style="margin-bottom:20px;padding:16px 20px">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
        <div style="flex:1;min-width:200px">
            <label style="font-size:0.78rem;color:#6e788b;display:block;margin-bottom:4px">Qidiruv</label>
            <div class="platon-search" style="width:100%">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="#aab0bb" stroke-width="1.5" style="margin-left:10px;flex-shrink:0">
                    <circle cx="9" cy="9" r="6"/><path stroke-linecap="round" d="M14 14l3 3"/>
                </svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Ariza №, kadastr, PINFL, ism...">
            </div>
        </div>
        <div>
            <label style="font-size:0.78rem;color:#6e788b;display:block;margin-bottom:4px">Holat</label>
            <select name="status" class="form-select form-select-sm" style="min-width:160px">
                <option value="">Barcha holatlar</option>
                @foreach(\App\Models\Application::STATUS_LABELS as $val => $lbl)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:0.78rem;color:#6e788b;display:block;margin-bottom:4px">Tuman</label>
            <select name="district_id" class="form-select form-select-sm" style="min-width:160px">
                <option value="">Barcha tumanlar</option>
                @foreach($districts as $d)
                <option value="{{ $d->id }}" {{ request('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name_uz }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:0.78rem;color:#6e788b;display:block;margin-bottom:4px">Dan</label>
            <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
        </div>
        <div>
            <label style="font-size:0.78rem;color:#6e788b;display:block;margin-bottom:4px">Gacha</label>
            <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
        </div>
        <div style="display:flex;gap:6px">
            <button type="submit" class="platon-btn platon-btn-primary platon-btn-sm">Filter</button>
            <a href="{{ route('admin.applications') }}" class="platon-btn platon-btn-outline platon-btn-sm">Tozalash</a>
        </div>
    </div>
</div>
</form>

{{-- Results --}}
<div class="block">
    <div class="section-heading">
        <span>Arizalar <span class="sbadge sbadge-gray" style="font-size:0.8rem">{{ $applications->total() }}</span></span>
    </div>
    <div class="platon-table-wrap">
        <table class="platon-table">
            <thead>
                <tr>
                    <th>№ Ariza</th>
                    <th>Ariza beruvchi</th>
                    <th>Tuman</th>
                    <th>Kadastr №</th>
                    <th>Holat</th>
                    <th>Bosqichlar</th>
                    <th>Yuborilgan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($applications as $app)
            <tr onclick="showTimeline({{ $app->id }})" style="cursor:pointer">
                <td>
                    <strong style="font-family:monospace;font-size:0.82rem">{{ $app->number }}</strong>
                </td>
                <td>
                    <div style="font-weight:600;font-size:0.85rem">{{ $app->applicant?->name ?? '—' }}</div>
                    @if($app->applicant?->pinfl)
                    <div style="font-size:0.72rem;color:#6e788b">{{ $app->applicant->pinfl }}</div>
                    @endif
                </td>
                <td>{{ $app->district?->name_uz ?? '—' }}</td>
                <td style="font-family:monospace;font-size:0.82rem">{{ $app->cadastral_number ?? '—' }}</td>
                <td>@include('partials.status-badge', ['status' => $app->status])</td>
                <td>
                    {{-- Step pips --}}
                    @php
                        $stepRoles = ['moderator','complaint_officer','lawyer','executor','district_head'];
                        $stepNames = ['Mod','Shik','Yur','Ijr','Rah'];
                        $approvalsByRole = $app->approvals->keyBy('step_role');
                    @endphp
                    <div style="display:flex;gap:3px;align-items:center">
                    @foreach($stepRoles as $i => $role)
                        @php $appr = $approvalsByRole[$role] ?? null; @endphp
                        @if($appr)
                            @if($appr->status === 'approved')
                                <span title="{{ $stepNames[$i] }}: Tasdiqlandi" style="width:22px;height:22px;border-radius:50%;background:#0bc33f;color:#fff;font-size:0.6rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center">✓</span>
                            @elseif($appr->status === 'rejected')
                                <span title="{{ $stepNames[$i] }}: Rad etildi" style="width:22px;height:22px;border-radius:50%;background:#e63260;color:#fff;font-size:0.6rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center">✗</span>
                            @elseif($appr->status === 'pending')
                                <span title="{{ $stepNames[$i] }}: Kutilmoqda" style="width:22px;height:22px;border-radius:50%;background:#fec524;color:#5c3d00;font-size:0.6rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center">⏳</span>
                            @else
                                <span title="{{ $stepNames[$i] }}: Navbat" style="width:22px;height:22px;border-radius:50%;background:#e4e7ea;color:#6e788b;font-size:0.6rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center">{{ $stepNames[$i][0] }}</span>
                            @endif
                        @else
                            <span title="{{ $stepNames[$i] }}" style="width:22px;height:22px;border-radius:50%;background:#f4f6f8;color:#aab0bb;font-size:0.6rem;display:inline-flex;align-items:center;justify-content:center">·</span>
                        @endif
                    @endforeach
                    </div>
                </td>
                <td style="font-size:0.82rem;color:#6e788b;white-space:nowrap">{{ $app->submitted_at?->format('d.m.Y') ?? '—' }}</td>
                <td>
                    <div style="display:flex;gap:5px;flex-wrap:wrap">
                    <a href="{{ route('applications.show', $app) }}" class="platon-btn platon-btn-outline platon-btn-sm" onclick="event.stopPropagation()">Ko'rish</a>
                    @php $pendingStep = $app->approvals->firstWhere('status','pending'); @endphp
                    @if($pendingStep)
                    <button type="button" class="platon-btn platon-btn-sm" style="background:#fff3cd;color:#856404;border:1px solid #fec524"
                        onclick="event.stopPropagation();openReassign({{ $pendingStep->id }}, '{{ addslashes($pendingStep->roleLabel()) }}', '{{ addslashes($app->number) }}')">Tayinlash</button>
                    @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:40px;color:#aab0bb">Arizalar topilmadi</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($applications->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center">
        {{ $applications->links() }}
    </div>
    @endif
</div>

{{-- Reassign modal --}}
<div id="reassign-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:600;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:20px;padding:28px;width:480px;max-width:95vw;position:relative">
        <button onclick="document.getElementById('reassign-modal').style.display='none'" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:1.2rem;cursor:pointer;color:#6e788b">✕</button>
        <h5 style="font-weight:700;margin-bottom:6px">Bosqich mas'ulini o'zgartirish</h5>
        <p id="reassign-info" style="font-size:0.82rem;color:#6e788b;margin-bottom:18px"></p>
        <form id="reassign-form" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold">Yangi mas'ul xodim</label>
                <select name="assigned_to" class="form-select" id="reassign-select" required>
                    <option value="">— Tanlang —</option>
                    {{-- options loaded via JS from embedded data --}}
                </select>
                <div class="form-text">Faqat bir xil roldagi xodimlar ko'rsatiladi</div>
            </div>
            <button type="submit" class="platon-btn platon-btn-primary" style="width:100%">Qayta tayinlash</button>
        </form>
    </div>
</div>

{{-- Embed staff users for reassign select --}}
@php
$staffByRole = \App\Models\User::whereNotIn('role', ['consumer', 'admin'])
    ->select('id','name','role','district_id','is_regional_backup')
    ->get()->groupBy('role');
@endphp
<script id="staff-data" type="application/json">{!! $staffByRole->toJson() !!}</script>


<div id="timeline-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:500;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:20px;padding:28px;width:560px;max-width:95vw;max-height:85vh;overflow-y:auto;position:relative">
        <button onclick="closeTimeline()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:1.3rem;cursor:pointer;color:#6e788b">✕</button>
        <div id="timeline-content"></div>
    </div>
</div>

@push('scripts')
<script>
var _apps = {!! $applications->map(fn($a) => [
    'id'     => $a->id,
    'number' => $a->number,
    'status' => $a->status,
    'address' => $a->address,
    'area'   => $a->area_sqm,
    'submitted' => $a->submitted_at?->format('d.m.Y H:i'),
    'applicant' => $a->applicant?->name,
    'pinfl'     => $a->applicant?->pinfl,
    'district'  => $a->district?->name_uz,
    'approvals' => $a->approvals->map(fn($ap) => [
        'role'        => $ap->step_role,
        'roleLabel'   => \App\Models\ApplicationApproval::ROLE_LABELS[$ap->step_role] ?? $ap->step_role,
        'status'      => $ap->status,
        'approvedBy'  => $ap->approver?->name,
        'comments'    => $ap->comments,
        'approvedAt'  => $ap->approved_at?->format('d.m.Y H:i'),
    ])->values()->all(),
])->keyBy('id')->toJson() !!};

function showTimeline(id) {
    var a = _apps[id];
    if (!a) return;
    var html = `
        <h4 style="font-size:1rem;font-weight:700;margin-bottom:4px">${escHtml(a.number)}</h4>
        <div style="font-size:0.82rem;color:#6e788b;margin-bottom:16px">
            ${escHtml(a.applicant||'')} · ${escHtml(a.pinfl||'')} · ${escHtml(a.district||'')}
        </div>
        <div style="font-size:0.82rem;color:#6e788b;margin-bottom:20px">
            📍 ${escHtml(a.address||'—')} | Maydon: ${a.area ? a.area + ' m²' : '—'} | Yuborilgan: ${a.submitted||'—'}
        </div>
        <div style="font-weight:700;font-size:0.88rem;margin-bottom:12px;color:#15191e">Tasdiqlash jarayoni</div>
        <div style="position:relative;padding-left:28px">`;

    (a.approvals||[]).forEach((ap, i) => {
        var dot = ap.status === 'approved' ? '#0bc33f' :
                  ap.status === 'rejected' ? '#e63260' :
                  ap.status === 'pending'  ? '#fec524' : '#d7dde1';
        var icon = ap.status === 'approved' ? '✓' : ap.status === 'rejected' ? '✗' : ap.status === 'pending' ? '⏳' : '○';
        var isLast = i === a.approvals.length - 1;
        html += `
            <div style="position:relative;padding-bottom:${isLast?'0':'20px'}">
                ${!isLast ? `<span style="position:absolute;left:-20px;top:20px;width:2px;height:100%;background:#e4e7ea"></span>` : ''}
                <span style="position:absolute;left:-28px;top:0;width:20px;height:20px;border-radius:50%;background:${dot};color:#fff;font-size:0.65rem;display:flex;align-items:center;justify-content:center;font-weight:700">${icon}</span>
                <div style="font-weight:600;font-size:0.88rem;color:#15191e">${escHtml(ap.roleLabel)}</div>
                ${ap.approvedBy ? `<div style="font-size:0.8rem;color:#6e788b">👤 ${escHtml(ap.approvedBy)} · ${ap.approvedAt||''}</div>` : ''}
                ${ap.comments ? `<div style="font-size:0.8rem;color:#6e788b;margin-top:3px;font-style:italic">"${escHtml(ap.comments)}"</div>` : ''}
            </div>`;
    });
    html += '</div>';

    document.getElementById('timeline-content').innerHTML = html;
    var m = document.getElementById('timeline-modal');
    m.style.display = 'flex';
}
function closeTimeline() {
    document.getElementById('timeline-modal').style.display = 'none';
}
document.getElementById('timeline-modal').addEventListener('click', function(e){
    if (e.target === this) closeTimeline();
});
document.getElementById('reassign-modal').addEventListener('click', function(e){
    if (e.target === this) this.style.display = 'none';
});
var _staffData = JSON.parse(document.getElementById('staff-data').textContent);
function openReassign(approvalId, roleLabel, appNumber) {
    document.getElementById('reassign-info').textContent =
        appNumber + ' — ' + roleLabel + ' bosqichi';
    document.getElementById('reassign-form').action = '/admin/approvals/' + approvalId + '/reassign';
    // Determine role key from roleLabel map
    var roleMap = {
        'Moderator': 'moderator',
        'Shikoyat mutaxassisi': 'complaint_officer',
        'Yurist': 'lawyer',
        'Ijrochi': 'executor',
        "Tuman boshlig'i": 'district_head',
    };
    var roleKey = roleMap[roleLabel] || roleLabel;
    var users = _staffData[roleKey] || [];
    var sel = document.getElementById('reassign-select');
    sel.innerHTML = '<option value="">— Tanlang —</option>';
    users.forEach(function(u) {
        var opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = u.name + (u.is_regional_backup ? ' (zaxira)' : '');
        sel.appendChild(opt);
    });
    if (users.length === 0) {
        sel.innerHTML = '<option value="">Bu roldagi xodim topilmadi</option>';
    }
    document.getElementById('reassign-modal').style.display = 'flex';
}

</script>
@endpush
@endsection
