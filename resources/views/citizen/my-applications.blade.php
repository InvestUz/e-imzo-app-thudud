<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mening arizalarim — TUTASH HUDUDLAR</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body,html{height:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,sans-serif;background:#f9f9f9}
        .flex{display:flex}

        /* ── Root layout ── */
        #root{display:flex;min-height:100%}
        #root .container{flex:1 1;display:flex;flex-direction:column;overflow:hidden}

        /* ── Sidebar ── */
        aside{
            background:#fff;border-right:1px solid #d7dde1;
            width:260px;min-width:260px;height:100vh;
            position:sticky;top:0;display:flex;flex-direction:column;
        }
        aside .logo{margin:24px}
        aside .logo a{
            display:flex;align-items:flex-start;gap:8px;
            color:#000;text-decoration:none;font-size:13px;line-height:18px;
        }
        aside .logo a .logo-icon{
            width:36px;height:36px;background:#018c87;border-radius:8px;
            display:flex;align-items:center;justify-content:center;flex-shrink:0;
        }
        aside .logo a .logo-icon svg{display:block}
        aside .logo a .logo-text .logo-title{
            font-weight:700;font-size:14px;color:#018c87;line-height:18px;display:block;
        }
        aside .logo a .logo-text .logo-sub{
            font-size:11px;color:#8a9ab8;display:block;margin-top:2px;
        }
        aside nav ul{padding:8px 12px;list-style:none}
        aside nav ul li{margin-bottom:4px}
        aside nav ul li a{
            display:flex;align-items:center;gap:10px;
            color:#27314b;font-size:14px;line-height:22px;
            padding:10px 14px;border-radius:8px;text-decoration:none;
            transition:background .15s,color .15s;
        }
        aside nav ul li a:hover{background:#f3fafa;color:#018c87}
        aside nav ul li a.active{background:#018c8733;color:#018c87;font-weight:600}
        aside nav ul li a svg{flex-shrink:0}
        aside .sidebar-footer{
            margin-top:auto;padding:16px 24px;border-top:1px solid #d7dde1;
            font-size:12px;color:#a0aabb;
        }

        /* ── Header ── */
        .header{
            display:flex;align-items:center;justify-content:space-between;
            background:#fff;border-bottom:1px solid #d7dde1;
            padding:14px 28px;position:sticky;top:0;z-index:10;
        }
        .header h1{font-size:17px;font-weight:700;color:#1a2d5a;line-height:24px}
        .header-right{display:flex;align-items:center;gap:12px}
        .header-user-name{font-size:13px;font-weight:600;color:#27314b}
        .header-pinfl{font-size:11px;color:#a0aabb}
        .btn-logout-hdr{
            background:none;border:1.5px solid #d7dde1;color:#5a6a8a;
            padding:6px 14px;border-radius:8px;font-size:13px;cursor:pointer;
            transition:all .15s;
        }
        .btn-logout-hdr:hover{border-color:#018c87;color:#018c87}

        /* ── Content area ── */
        .content{margin:24px;flex:1}

        /* ── White block card ── */
        .block{background:#fff;border-radius:14px;padding:22px}

        /* ── Toolbar: search + filter ── */
        .toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:12px;flex-wrap:wrap}
        .toolbar h3{font-size:16px;font-weight:700;color:#1a2d5a;line-height:24px}
        .toolbar-right{display:flex;align-items:center;gap:8px}
        .search-input{
            border:1px solid #dcddde;border-radius:8px;font-size:14px;
            height:38px;outline:none;padding:0 14px;width:220px;
            transition:border-color .15s;
        }
        .search-input:focus{border-color:#018c87}
        .btn-new-app{
            display:inline-flex;align-items:center;gap:6px;
            background:#018c87;color:#fff;border:none;border-radius:8px;
            font-size:14px;font-weight:600;padding:8px 16px;
            text-decoration:none;cursor:pointer;transition:background .15s;
        }
        .btn-new-app:hover{background:#017570;color:#fff}

        /* ── Table ── */
        .main-table{border:1px solid #d7dde1;border-radius:10px;overflow:hidden;margin-top:0}
        .main-table table{border-collapse:collapse;font-size:13px;width:100%}
        .main-table table thead tr{border-bottom:1px solid #d7dde1;background:#fafafa}
        .main-table table thead tr th{
            font-size:12px;font-weight:600;color:#78829d;
            padding:11px 14px;text-align:left;white-space:nowrap;
        }
        .main-table table tbody tr:not(:last-child){border-bottom:1px solid #f0f2f5}
        .main-table table tbody tr{transition:background .1s}
        .main-table table tbody tr:hover{background:#f8fffe}
        .main-table table tbody tr td{padding:10px 14px;vertical-align:middle;color:#27314b}
        .cell-num{font-family:monospace;font-weight:700;color:#018c87;white-space:nowrap}
        .cell-addr{max-width:180px}
        .cell-addr .addr-main{font-weight:600;font-size:13px;color:#1a2d5a}
        .cell-addr .addr-sub{font-size:11px;color:#a0aabb;margin-top:1px}
        .cell-date{white-space:nowrap;font-size:12px;color:#5a6a8a}
        .cell-kadastr{font-family:monospace;font-size:12px;color:#444}
        .cell-area{font-size:13px;white-space:nowrap}
        .cell-pips{white-space:nowrap}

        /* ── Status badges (Platon style) ── */
        .status{
            display:inline-flex;align-items:center;justify-content:center;
            border-radius:8px;font-size:12px;font-weight:500;
            line-height:18px;padding:4px 10px;white-space:nowrap;
        }
        .status.outline{border:1px solid}
        .status.outline.success{background:rgba(6,184,56,.12);border-color:#0bc33f;color:#0bc33f}
        .status.outline.danger{background:#e6326020;border-color:#e63260;color:#e63260}
        .status.outline.warning{background:rgba(254,197,36,.14);border-color:#d4a017;color:#b8820c}
        .status.outline.info{background:rgba(1,140,135,.1);border-color:#018c87;color:#018c87}
        .status.outline.muted{background:#f0f2f5;border-color:#c8d0de;color:#6a7a9a}

        /* ── Progress pips ── */
        .pips{display:flex;gap:3px;align-items:center}
        .pip{width:18px;height:4px;border-radius:2px;background:#e8ecf1}
        .pip.done{background:#10b981}
        .pip.current{background:#018c87}
        .pip.rejected{background:#e63260}

        /* ── Action button ── */
        .btn-view{
            display:inline-flex;align-items:center;gap:4px;
            background:none;border:1px solid #d7dde1;border-radius:7px;
            color:#5a6a8a;font-size:12px;padding:5px 11px;cursor:pointer;
            text-decoration:none;transition:all .15s;
        }
        .btn-view:hover{border-color:#018c87;color:#018c87;background:#f3fafa}

        /* ── Pagination ── */
        .pagination-wrap{margin-top:18px;display:flex;justify-content:flex-end}
        .pagination-wrap .pagination{gap:4px;list-style:none;display:flex;flex-wrap:wrap}
        .pagination-wrap .pagination li a,
        .pagination-wrap .pagination li span{
            display:flex;align-items:center;justify-content:center;
            min-width:32px;height:32px;padding:0 8px;
            border:1px solid #d7dde1;border-radius:6px;
            font-size:12px;color:#5a6a8a;text-decoration:none;
            transition:all .15s;background:#fff;
        }
        .pagination-wrap .pagination li.active span{
            background:#018c87;border-color:#018c87;color:#fff;font-weight:700;
        }
        .pagination-wrap .pagination li a:hover{border-color:#018c87;color:#018c87}
        .pagination-wrap .pagination li.disabled span{opacity:.45;pointer-events:none}

        /* ── Empty state ── */
        .empty-state{text-align:center;padding:60px 20px}
        .empty-state .empty-icon{font-size:2.8rem;margin-bottom:12px}
        .empty-state h5{font-size:16px;font-weight:700;color:#1a2d5a;margin-bottom:6px}
        .empty-state p{font-size:13px;color:#8a9ab8}

        /* ── Responsive ── */
        @media(max-width:900px){
            aside{display:none}
            .content{margin:16px}
        }
        @media(max-width:640px){
            .main-table{overflow-x:auto}
            .search-input{width:140px}
        }
    </style>
</head>
<body>
<div id="root">

    {{-- ════════════════ SIDEBAR ════════════════ --}}
    <aside>
        <div class="logo">
            <a href="{{ route('home') }}">
                <div class="logo-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 22V12h6v10" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="logo-text">
                    <span class="logo-title">TUTASH HUDUDLAR</span>
                    <span class="logo-sub">Vazirlar Mahkamasi № 478</span>
                </div>
            </a>
        </div>

        <nav>
            <ul>
                <li>
                    <a href="{{ route('my-applications') }}" class="active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" d="M6 3h9.172a2 2 0 011.414.586l2.828 2.828A2 2 0 0120 7.828V19a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 13h8M8 17h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Mening arizalarim
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Ariza holati tekshirish
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        Yangi ariza berish
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            © {{ date('Y') }} Tutash Hududlar Tizimi
        </div>
    </aside>

    {{-- ════════════════ MAIN ════════════════ --}}
    <section class="container">

        {{-- ── Header ── --}}
        <header class="header">
            <h1>Mening arizalarim</h1>
            <div class="header-right">
                <div>
                    <div class="header-user-name">{{ $user->name }}</div>
                    @if($user->pinfl)
                    <div class="header-pinfl">PINFL: {{ $user->pinfl }}</div>
                    @endif
                </div>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn-logout-hdr">Chiqish</button>
                </form>
            </div>
        </header>

        {{-- ── Content ── --}}
        <div class="content">
            <div class="block">

                {{-- Toolbar --}}
                <div class="toolbar">
                    <h3>Arizalar ro'yxati
                        @if($applications->total() > 0)
                        <span style="font-size:12px;font-weight:500;color:#a0aabb;margin-left:8px">({{ $applications->total() }} ta)</span>
                        @endif
                    </h3>
                    <div class="toolbar-right">
                        <input class="search-input" type="text" placeholder="Raqam yoki kadastr…" id="searchInp">
                        <a href="{{ route('home') }}" class="btn-new-app">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            Yangi ariza
                        </a>
                    </div>
                </div>

                @if($applications->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h5>Arizalar topilmadi</h5>
                    <p>Siz hali hech qanday ariza topshirmadingiz.</p>
                    <a href="{{ route('home') }}" class="btn-new-app" style="margin-top:16px;display:inline-flex">
                        + Yangi ariza berish
                    </a>
                </div>
                @else
                @php
                    $statusMap = [
                        'pending'           => ['Kutilmoqda',                  'muted'],
                        'moderator_review'  => ["Moderator ko'rib chiqmoqda",  'info'],
                        'complaint_review'  => ["Shikoyat bo'limi",            'warning'],
                        'lawyer_review'     => ["Yurist ko'rib chiqmoqda",     'warning'],
                        'executor_review'   => ["Ijrochi ko'rib chiqmoqda",    'warning'],
                        'head_review'       => ['Boshqarma rahbari',           'warning'],
                        'approved'          => ['Tasdiqlandi',                 'success'],
                        'rejected'          => ['Rad etildi',                  'danger'],
                    ];
                    $totalSteps = count(\App\Models\Application::STEPS);
                @endphp

                {{-- Table --}}
                <div class="main-table">
                    <table>
                        <thead>
                            <tr>
                                <th>№ Ariza</th>
                                <th>Ko'cha / Manzil</th>
                                <th>Yuborilgan sana</th>
                                <th>Kadastr №</th>
                                <th>Maydon</th>
                                <th>Holat</th>
                                <th>Bosqichlar</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="appTable">
                        @foreach($applications as $app)
                        @php
                            $st   = $statusMap[$app->status] ?? ['Noma\'lum', 'muted'];
                            $fd   = is_array($app->form_data) ? $app->form_data : json_decode($app->form_data, true);
                            $addr = $fd['street_name'] ?? ($app->district->name_uz ?? '—');
                        @endphp
                        <tr data-search="{{ strtolower($app->number . ' ' . $app->cadastral_number . ' ' . $addr) }}">
                            <td class="cell-num">{{ $app->number }}</td>
                            <td class="cell-addr">
                                <div class="addr-main">{{ $addr }}</div>
                                <div class="addr-sub">{{ $app->district->name_uz ?? '' }}</div>
                            </td>
                            <td class="cell-date">{{ $app->submitted_at?->format('d.m.Y') }}<br><span style="color:#c0c8d8">{{ $app->submitted_at?->format('H:i') }}</span></td>
                            <td class="cell-kadastr">{{ $app->cadastral_number ?: '—' }}</td>
                            <td class="cell-area">
                                @if($app->area_sqm)
                                    {{ number_format($app->area_sqm, 0) }} m²
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="status outline {{ $st[1] }}">{{ $st[0] }}</span>
                            </td>
                            <td class="cell-pips">
                                <div class="pips">
                                @for($i = 1; $i <= $totalSteps; $i++)
                                @php
                                    $pip = $app->approvals->firstWhere('step_order', $i);
                                    $pc  = match($pip?->status ?? 'waiting') {
                                        'approved' => 'done',
                                        'rejected' => 'rejected',
                                        'pending'  => 'current',
                                        default    => '',
                                    };
                                @endphp
                                <div class="pip {{ $pc }}" title="Bosqich {{ $i }}"></div>
                                @endfor
                                </div>
                                <div style="font-size:10px;color:#a0aabb;margin-top:3px">
                                    {{ $app->approvals->where('status','approved')->count() }}/{{ $totalSteps }}
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('apply.track', $app->number) }}" class="btn-view">
                                    Ko'rish
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($applications->hasPages())
                <div class="pagination-wrap">
                    {{ $applications->links() }}
                </div>
                @endif

                @endif

            </div>
        </div>

    </section>
</div>

<script>
// Live search filter
document.getElementById('searchInp').addEventListener('input', function(){
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('#appTable tr').forEach(function(row){
        row.style.display = (!q || row.dataset.search.includes(q)) ? '' : 'none';
    });
});
</script>
</body>
</html>
