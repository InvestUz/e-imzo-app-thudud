<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ariza holati — {{ $application->number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f2f5; min-height: 100vh; display: flex; flex-direction: column;
        }
        .site-header {
            background: #003399;
            padding: 14px 32px; display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0,0,0,.18);
        }
        .site-header .brand { color: #fff; font-size: 1.2rem; font-weight: 700; text-decoration: none; }
        .btn-header {
            border: 1.5px solid rgba(255,255,255,.4); background: transparent; color: #fff;
            padding: 6px 18px; border-radius: 8px; font-size: .88rem; text-decoration: none;
            transition: all .2s;
        }
        .btn-header:hover { background: rgba(255,255,255,.15); color: #fff; }

        .main { flex: 1; display: flex; justify-content: center; padding: 40px 16px; }
        .wrap { width: 100%; max-width: 680px; }

        /* ─── Card ─── */
        .track-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; margin-bottom: 20px;
        }
        .card-head {
            background: #003399; color: #fff;
            padding: 20px 28px;
        }
        .card-head .label { font-size: .75rem; opacity: .75; text-transform: uppercase; letter-spacing: .06em; }
        .card-head .num   { font-size: 1.6rem; font-weight: 800; font-family: monospace; letter-spacing: .05em; }
        .card-head .sub   { font-size: .85rem; opacity: .8; margin-top: 4px; }

        .card-body { padding: 20px 28px; }
        .section-label {
            font-size: .75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: #8a9ab8; margin-bottom: 12px;
            display: flex; align-items: center; gap: 8px;
        }
        .section-label::after { content: ''; flex: 1; height: 1px; background: #f0f2f5; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 4px; }
        @media (max-width: 500px) { .info-grid { grid-template-columns: 1fr; } }
        .info-item {}
        .info-item .ilabel { font-size: .75rem; color: #8a9ab8; margin-bottom: 2px; }
        .info-item .ival   { font-size: .9rem; font-weight: 600; color: #1a2d5a; }

        /* ─── Status badge ─── */
        .status-badge {
            display: inline-block; padding: 5px 14px; border-radius: 20px;
            font-size: .8rem; font-weight: 700;
        }
        .s-moderator_review  { background: rgba(1,140,135,.1); color: #018c87; }
        .s-complaint_review  { background: #fff3cd; color: #856404; }
        .s-lawyer_review     { background: #e2d9f3; color: #5a189a; }
        .s-executor_review   { background: #cff4fc; color: #055160; }
        .s-head_review       { background: #d1e7dd; color: #0a3622; }
        .s-approved          { background: #d1e7dd; color: #0a3622; }
        .s-rejected          { background: #f8d7da; color: #58151c; }
        .s-pending           { background: #f0f2f5; color: #5a6a8a; }

        /* ─── Timeline ─── */
        .timeline { position: relative; padding-left: 32px; }
        .timeline::before {
            content: ''; position: absolute; left: 10px; top: 8px; bottom: 8px;
            width: 2px; background: #e8ecf1;
        }
        .tl-item { position: relative; margin-bottom: 20px; }
        .tl-item:last-child { margin-bottom: 0; }

        .tl-dot {
            position: absolute; left: -28px; top: 2px;
            width: 20px; height: 20px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 700; z-index: 1;
        }
        .tl-dot.approved { background: #198754; color: #fff; }
        .tl-dot.pending  { background: #003399; color: #fff; box-shadow: 0 0 0 3px #e8f0fe; }
        .tl-dot.waiting  { background: #e8ecf1; color: #8a9ab8; border: 2px solid #d0d8e4; }
        .tl-dot.rejected { background: #dc3545; color: #fff; }

        .tl-title { font-size: .9rem; font-weight: 700; color: #1a2d5a; }
        .tl-title .current-tag {
            display: inline-block; font-size: .7rem; padding: 1px 8px;
            background: #003399; color: #fff; border-radius: 10px; margin-left: 6px; font-weight: 600;
        }
        .tl-meta  { font-size: .8rem; color: #8a9ab8; margin-top: 2px; }
        .tl-meta .approver { color: #1a7a40; font-weight: 600; }
        .tl-meta .rejecter { color: #c0392b; font-weight: 600; }
        .tl-comment { margin-top: 6px; font-size: .8rem; background: #f8fafc; border-left: 3px solid #dde3ee; padding: 6px 10px; border-radius: 0 6px 6px 0; color: #5a6a8a; }

        /* ─── Search again ─── */
        .search-again { background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .site-footer { text-align: center; padding: 20px; font-size: .8rem; color: #8a9ab8; }
    </style>
</head>
<body>

<header class="site-header">
    <a href="{{ route('home') }}" class="brand">Qo'shni hudud — VM 478</a>
    <a href="{{ route('home') }}" class="btn-header">← Bosh sahifa</a>
</header>

<main class="main">
    <div class="wrap">

        {{-- Number header card --}}
        <div class="track-card">
            <div class="card-head">
                <div class="label">Ariza raqami</div>
                <div class="num">{{ $application->number }}</div>
                <div class="sub">
                    {{ $application->district->name_uz }} &nbsp;·&nbsp;
                    {{ $application->submitted_at?->format('d.m.Y H:i') }}
                </div>
            </div>
            <div class="card-body">
                <div class="section-label">Ma'lumotlar</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="ilabel">Ariza beruvchi</div>
                        <div class="ival">{{ $application->applicant->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="ilabel">Kadastr raqami</div>
                        <div class="ival">{{ $application->cadastral_number }}</div>
                    </div>
                    @if($application->address)
                    <div class="info-item">
                        <div class="ilabel">Manzil</div>
                        <div class="ival">{{ $application->address }}</div>
                    </div>
                    @endif
                    @if($application->area_sqm)
                    <div class="info-item">
                        <div class="ilabel">Maydon</div>
                        <div class="ival">{{ number_format($application->area_sqm, 2) }} m²</div>
                    </div>
                    @endif
                    <div class="info-item">
                        <div class="ilabel">Holati</div>
                        <div class="ival">
                            @php
                                $statusMap = [
                                    'pending'           => ['Kutilmoqda',            's-pending'],
                                    'moderator_review'  => ['Moderator ko\'rib chiqmoqda', 's-moderator_review'],
                                    'complaint_review'  => ['Shikoyat bo\'limi',     's-complaint_review'],
                                    'lawyer_review'     => ['Yurist ko\'rib chiqmoqda', 's-lawyer_review'],
                                    'executor_review'   => ['Ijrochi ko\'rib chiqmoqda', 's-executor_review'],
                                    'head_review'       => ['Boshqarma rahbari',     's-head_review'],
                                    'approved'          => ['Tasdiqlandi ✓',         's-approved'],
                                    'rejected'          => ['Rad etildi ✗',          's-rejected'],
                                ];
                                $s = $statusMap[$application->status] ?? ['Noma\'lum', 's-pending'];
                            @endphp
                            <span class="status-badge {{ $s[1] }}">{{ $s[0] }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="ilabel">Yuborilgan sana</div>
                        <div class="ival">{{ $application->submitted_at?->format('d.m.Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Approval timeline --}}
        <div class="track-card">
            <div class="card-body">
                <div class="section-label">Ko'rib chiqish bosqichlari</div>

                @php
                    $roleLabels = \App\Models\ApplicationApproval::ROLE_LABELS;
                @endphp

                <div class="timeline">
                    @foreach($application->approvals->sortBy('step_order') as $approval)
                    @php
                        $dotClass = match($approval->status) {
                            'approved' => 'approved',
                            'rejected' => 'rejected',
                            'pending'  => 'pending',
                            default    => 'waiting',
                        };
                        $dotIcon = match($approval->status) {
                            'approved' => '✓',
                            'rejected' => '✗',
                            'pending'  => $approval->step_order,
                            default    => $approval->step_order,
                        };
                        $isCurrent = $approval->status === 'pending';
                    @endphp
                    <div class="tl-item">
                        <div class="tl-dot {{ $dotClass }}">{{ $dotIcon }}</div>
                        <div>
                            <div class="tl-title">
                                {{ $roleLabels[$approval->step_role] ?? $approval->step_role }}
                                @if($isCurrent)<span class="current-tag">Hozirgi bosqich</span>@endif
                            </div>
                            <div class="tl-meta">
                                @if($approval->status === 'approved' && $approval->approver)
                                    <span class="approver">✓ {{ $approval->approver->name }}</span>
                                    @if($approval->approved_at)
                                     &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($approval->approved_at)->format('d.m.Y H:i') }}
                                    @endif
                                    @if($approval->is_backup_approval)
                                     &nbsp;·&nbsp; <em style="color:#856404">Mintaqa o'rinbosari</em>
                                    @endif
                                @elseif($approval->status === 'rejected' && $approval->approver)
                                    <span class="rejecter">✗ {{ $approval->approver->name }}</span>
                                    @if($approval->approved_at)
                                     &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($approval->approved_at)->format('d.m.Y H:i') }}
                                    @endif
                                @elseif($approval->status === 'pending')
                                    @if($approval->assignee)
                                        {{ $approval->assignee->name }} tomonidan ko'rilmoqda
                                    @else
                                        Mas'ul tayinlanmagan
                                    @endif
                                @else
                                    Kutilmoqda
                                @endif
                            </div>
                            @if($approval->comments)
                            <div class="tl-comment">{{ $approval->comments }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Search another --}}
        <div class="search-again">
            <div class="section-label" style="margin-bottom:10px">Boshqa ariza tekshirish</div>
            <form method="GET" action="{{ route('apply.track.search') }}" class="d-flex gap-2">
                <input type="text" name="number" class="form-control form-control-sm"
                    placeholder="Ariza raqami"
                    style="border-radius:8px;border:1.5px solid #dde3ee">
                <button type="submit"
                    style="padding:6px 16px;background:#003399;color:#fff;border:none;border-radius:8px;font-size:.88rem;cursor:pointer;white-space:nowrap">
                    Tekshirish
                </button>
            </form>
        </div>

    </div>
</main>

<footer class="site-footer">
    © {{ date('Y') }} Qo'shni hudud tizimi &nbsp;·&nbsp; Vazirlar Mahkamasi Qarori №478
</footer>

</body>
</html>
