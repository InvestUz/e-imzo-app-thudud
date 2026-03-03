<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TUTASH HUDUDLAR REESTRI') — Kabinet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ===== TUTASH HUDUDLAR REESTRI — Platon Style ===== */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body, html { height: 100%; }
        body {
            background: #f9f9f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #15191e;
        }

        /* ── Root layout ── */
        .platon-root { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .platon-aside {
            background: #fff;
            border-right: 1px solid #d7dde1;
            width: 280px;
            min-width: 280px;
            height: 100vh;
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 50;
        }

        .platon-logo {
            padding: 18px 20px 16px;
            border-bottom: 1px solid #f0f2f5;
        }

        .platon-logo a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #15191e;
        }

        .platon-logo-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, #018c87 0%, #00bfaf 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 900;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(1,140,135,.3);
        }

        .platon-logo-text { display: flex; flex-direction: column; }

        .platon-logo-title {
            font-size: 0.72rem;
            font-weight: 800;
            color: #018c87;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .platon-logo-sub {
            font-size: 0.7rem;
            color: #6e788b;
            line-height: 1.4;
            margin-top: 2px;
        }

        /* ── Sidebar nav ── */
        .platon-nav { padding: 12px 10px; list-style: none; flex: 1; }
        .platon-nav li { margin-bottom: 3px; }

        .platon-nav-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #aab0bb;
            padding: 10px 14px 4px;
        }

        .platon-nav li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.88rem;
            color: #27314b;
            font-weight: 500;
            transition: all 0.15s;
        }

        .platon-nav li a:hover { background: #f4f6f8; color: #018c87; }

        .platon-nav li a.active {
            background: rgba(1,140,135,0.1);
            color: #018c87;
            font-weight: 700;
        }

        .platon-nav li a svg {
            width: 20px; height: 20px; flex-shrink: 0;
            stroke-width: 1.5; opacity: 0.7;
        }
        .platon-nav li a.active svg { opacity: 1; }

        .nav-badge {
            margin-left: auto;
            background: #fec524;
            color: #5c3d00;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 10px;
        }

        /* ── Sidebar footer ── */
        .platon-aside-footer {
            padding: 14px 16px;
            border-top: 1px solid #f0f2f5;
            font-size: 0.72rem;
            color: #aab0bb;
            text-align: center;
        }

        /* ── Main container ── */
        .platon-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

        /* ── Top header ── */
        .platon-header {
            background: #fff;
            border-bottom: 1px solid #d7dde1;
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            gap: 16px;
        }

        .platon-page-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #15191e;
            white-space: nowrap;
        }

        .platon-header-actions {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .platon-icon-btn {
            background: none;
            border: none;
            color: #6e788b;
            cursor: pointer;
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
            text-decoration: none;
        }
        .platon-icon-btn:hover { background: #f4f6f8; color: #15191e; }

        /* ── User avatar + dropdown ── */
        .platon-user-wrap { position: relative; }

        .platon-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #018c87, #00bfaf);
            color: #fff;
            font-size: 0.82rem;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            user-select: none;
        }

        .platon-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #fff;
            border: 1px solid #e0e5ea;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,.12);
            min-width: 230px;
            z-index: 300;
            display: none;
            overflow: hidden;
        }
        .platon-dropdown.show { display: block; }

        .platon-dropdown-meta {
            padding: 14px 16px 10px;
            font-size: 0.8rem;
            color: #6e788b;
            border-bottom: 1px solid #f0f2f5;
        }
        .platon-dropdown-meta strong {
            display: block;
            font-size: 0.9rem;
            color: #15191e;
            margin-bottom: 3px;
        }

        .platon-dropdown-item {
            padding: 10px 16px;
            font-size: 0.875rem;
            color: #27314b;
            cursor: pointer;
            display: block;
            text-decoration: none;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            transition: background 0.15s;
        }
        .platon-dropdown-item:hover { background: #f4f6f8; }
        .platon-dropdown-item.text-danger { color: #e63260 !important; }

        /* ── Content area ── */
        .platon-content { padding: 24px 28px; flex: 1; }

        /* ── Block (white card) ── */
        .block {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
        }

        /* ── Stat/Invoice cards ── */
        .stat-cards-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card-p {
            border-radius: 16px;
            color: #fff;
            padding: 18px 20px 20px;
            display: flex;
            flex-direction: column;
        }
        .stat-card-p .sc-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            opacity: 0.88;
            margin-bottom: 8px;
        }
        .stat-card-p .sc-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        .sc-green      { background: linear-gradient(103deg, #d5f26d -56%, #66a62a 112%); }
        .sc-red        { background: linear-gradient(101deg, #ff999b -48%, #d53d40 111%); }
        .sc-orange     { background: linear-gradient(101deg, #ffce99 -113%, #d5893d 111%); }
        .sc-green-dk   { background: linear-gradient(98deg, #77e4a0 -98%, #2da159 100%); }
        .sc-teal       { background: linear-gradient(135deg, #018c87 0%, #00bfaf 100%); }
        .sc-blue       { background: linear-gradient(135deg, #1471f0 0%, #0d52b8 100%); }

        /* ── Platon table ── */
        .platon-table-wrap {
            border: 1px solid #d7dde1;
            border-radius: 8px;
            overflow: hidden;
        }

        .platon-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .platon-table thead tr { border-bottom: 1px solid #d7dde1; }

        .platon-table thead th {
            padding: 12px 14px;
            text-align: left;
            font-weight: 600;
            color: #27314b;
            white-space: nowrap;
            background: #fff;
        }

        .platon-table thead th .th-inner {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .platon-table tbody tr:not(:last-child) { border-bottom: 1px solid #eee; }
        .platon-table tbody tr { transition: background 0.1s; cursor: pointer; }
        .platon-table tbody tr:hover { background: #f7f9fa; }
        .platon-table tbody td { padding: 10px 14px; vertical-align: middle; }

        /* ── Status outline badges ── */
        .sbadge {
            display: inline-flex;
            align-items: center;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 500;
            padding: 5px 11px;
            border: 1px solid;
            white-space: nowrap;
        }
        .sbadge-success  { background: rgba(6,184,56,.1);   border-color: #0bc33f; color: #0bc33f; }
        .sbadge-danger   { background: rgba(230,50,96,.1);  border-color: #e63260; color: #e63260; }
        .sbadge-warning  { background: rgba(254,197,36,.15);border-color: #fec524; color: #9a6800; }
        .sbadge-info     { background: rgba(14,186,148,.1); border-color: #0eba94; color: #0eba94; }
        .sbadge-blue     { background: rgba(20,113,240,.1); border-color: #1471f0; color: #1471f0; }
        .sbadge-gray     { background: #f4f6f8; border-color: #d7dde1; color: #6e788b; }
        .sbadge-purple   { background: rgba(103,60,200,.1); border-color: #673cc8; color: #673cc8; }

        /* ── Toolbar ── */
        .platon-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .platon-toolbar-left { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .platon-search {
            display: flex;
            align-items: center;
            border: 1px solid #dcddde;
            border-radius: 8px;
            background: #fff;
            overflow: hidden;
        }
        .platon-search input {
            border: none; outline: none;
            padding: 8px 14px;
            font-size: 0.875rem;
            background: transparent;
            min-width: 200px;
        }

        /* ── Buttons ── */
        .platon-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s;
        }
        .platon-btn-primary  { background: #018c87; color: #fff; }
        .platon-btn-primary:hover  { background: #017570; color: #fff; }
        .platon-btn-outline  { background: #fff; border: 1px solid #d7dde1; color: #27314b; }
        .platon-btn-outline:hover  { background: #f4f6f8; color: #27314b; }
        .platon-btn-danger   { background: #e63260; color: #fff; }
        .platon-btn-danger:hover   { background: #c0284f; color: #fff; }
        .platon-btn-sm { padding: 6px 13px; font-size: 0.8rem; }

        /* ── Quick links ── */
        .quick-link {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
            margin-bottom: 10px;
            border: 1px solid #f0f2f5;
        }
        .quick-link:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.1); color: inherit; }
        .quick-link-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; flex-shrink: 0;
        }
        .ql-teal   { background: rgba(1,140,135,.12); }
        .ql-yellow { background: #fff3cd; }
        .ql-green  { background: #d1e7dd; }
        .quick-link-info strong { display: block; font-size: 0.9rem; font-weight: 600; color: #1a2d5a; }
        .quick-link-info span   { font-size: 0.78rem; color: #8a9ab8; }

        /* ── Thumbnail / thumb blocks ── */
        .thumb {
            background: linear-gradient(92deg, #d5e8f6 0%, #dbf5ee 100%);
            border-radius: 16px;
        }
        .thumb:not(:last-child) { margin-bottom: 20px; }
        .thumb a {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px;
            text-decoration: none;
            color: #000;
        }
        .thumb a .caption { display: flex; align-items: center; gap: 20px; }
        .thumb a .caption p { font-size: 1rem; line-height: 1.4; }
        .thumb a .caption p strong { display: block; font-size: 1.25rem; font-weight: 700; margin-bottom: 4px; }

        /* ── Alert ── */
        .platon-alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .platon-alert-success { background: #d4edda; color: #155724; }
        .platon-alert-danger  { background: #f8d7da; color: #721c24; }
        .platon-alert-warning { background: #fff3cd; color: #856404; }
        .platon-alert-info    { background: #cff4fc; color: #055160; }

        /* ── Section heading inside content ── */
        .section-heading {
            font-size: 1.05rem;
            font-weight: 700;
            color: #15191e;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ── Bootstrap overrides to match Platon teal ── */
        .btn-primary { background: #018c87; border-color: #018c87; }
        .btn-primary:hover, .btn-primary:focus { background: #017570; border-color: #017570; }
        .btn-primary:active { background: #016460 !important; }
        .btn-outline-primary { color: #018c87; border-color: #018c87; }
        .btn-outline-primary:hover { background: #018c87; border-color: #018c87; color: #fff; }
        .badge.bg-primary { background-color: #018c87 !important; }
        a { color: #018c87; }
        a:hover { color: #017570; }
        .form-control:focus, .form-select:focus {
            border-color: #018c87;
            box-shadow: 0 0 0 .2rem rgba(1,140,135,.2);
        }
        .card { border-radius: 12px; border: 1px solid #f0f2f5; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
        .table { font-size: 0.875rem; }
        .table-hover tbody tr:hover td { background: #f7f9fa; }

        /* ── Notification bell badge ── */
        .bell-badge {
            position: absolute;
            top: 4px; right: 4px;
            background: #e63260;
            color: #fff;
            font-size: 0.6rem;
            font-weight: 800;
            min-width: 16px;
            height: 16px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            padding: 0 4px;
            pointer-events: none;
            border: 2px solid #fff;
        }

        /* ── Notification item ── */
        .notif-item {
            padding: 10px 16px;
            border-bottom: 1px solid #f4f6f8;
            cursor: pointer;
            transition: background 0.1s;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            text-decoration: none;
            color: inherit;
        }
        .notif-item:hover { background: #f7f9fa; }
        .notif-item.unread { background: rgba(1,140,135,0.04); }
        .notif-item.unread .notif-title { font-weight: 700; }
        .notif-icon { font-size: 1.2rem; flex-shrink: 0; margin-top: 2px; }
        .notif-title { font-size: 0.85rem; color: #15191e; line-height: 1.3; }
        .notif-body { font-size: 0.78rem; color: #6e788b; margin-top: 2px; }
        .notif-meta { font-size: 0.72rem; color: #aab0bb; margin-top: 3px; }

        /* ── Admin nav items ── */
        .admin-nav-section { background: rgba(1,140,135,0.06); border-radius: 8px; margin: 4px 0; }

        @media (max-width: 960px) {
            .platon-aside { display: none; }
            .platon-content { padding: 16px; }
            .platon-header { padding: 12px 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="platon-root">

    {{-- ──────────── SIDEBAR ──────────── --}}
    <aside class="platon-aside">
        {{-- Logo --}}
        <div class="platon-logo">
            <a href="{{ auth()->check() && auth()->user()->isConsumer() ? route('my-applications') : route('dashboard') }}">
                <div class="platon-logo-icon">T</div>
                <div class="platon-logo-text">
                    <span class="platon-logo-title">TUTASH HUDUDLAR</span>
                    <span class="platon-logo-sub">REESTRI · Kabinet</span>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav>
            <ul class="platon-nav">
                @auth
                @if(!auth()->user()->isConsumer())
                <div class="platon-nav-label">Ishchi panel</div>
                <li>
                    <a href="{{ route('applications.inbox') }}" class="{{ request()->routeIs('applications.inbox') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 13.12V9.38m.001 6.745a.25.25 0 10.002.5.25.25 0 00-.002-.5M14.029 4.14l7.659 13.403c.89 1.558-.235 3.497-2.03 3.497H4.342c-1.795 0-2.92-1.939-2.03-3.497L9.972 4.14c.897-1.57 3.16-1.57 4.058 0z"/></svg>
                        Kiruvchi arizalar
                        @php
                            $inboxCount = \App\Models\ApplicationApproval::where('step_role', auth()->user()->role)
                                ->where('status','pending')
                                ->when(!auth()->user()->is_regional_backup && auth()->user()->district_id,
                                    fn($q) => $q->whereHas('application', fn($q2) => $q2->where('district_id', auth()->user()->district_id))
                                )->count();
                        @endphp
                        @if($inboxCount > 0)
                        <span class="nav-badge">{{ $inboxCount }}</span>
                        @endif
                    </a>
                </li>
                @endif

                <li>
                    <a href="{{ route('applications.index') }}" class="{{ request()->routeIs('applications.index') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 13h8M8 17h8"/><path clip-rule="evenodd" stroke-linecap="round" stroke-linejoin="round" d="M6 3h9.172a2 2 0 011.414.586l2.828 2.828A2 2 0 0120 7.828V19a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20 8h-4a1 1 0 01-1-1V3M8 9h3"/></svg>
                        Arizalar ro'yxati
                    </a>
                </li>

                @if(!auth()->user()->isConsumer())
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Bosh sahifa
                    </a>
                </li>
                @endif

                @if(auth()->user()->isAdmin())
                <div class="platon-nav-label" style="margin-top:8px">Boshqaruv</div>
                <li>
                    <a href="{{ route('applications.create') }}" class="{{ request()->routeIs('applications.create') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Yangi ariza qo'shish
                    </a>
                </li>
                <div class="platon-nav-label" style="margin-top:8px">IT Boshqaruv</div>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Admin panel
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.applications') }}" class="{{ request()->routeIs('admin.applications') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 13h8M8 17h8"/><path clip-rule="evenodd" stroke-linecap="round" stroke-linejoin="round" d="M6 3h9.172a2 2 0 011.414.586l2.828 2.828A2 2 0 0120 7.828V19a2 2 0 01-2 2H6a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                        Barcha arizalar
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        Foydalanuvchilar
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.sessions') }}" class="{{ request()->routeIs('admin.sessions') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                        Sessiyalar
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.roles') }}" class="{{ request()->routeIs('admin.roles') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Rollar & Huquqlar
                    </a>
                </li>
                @endif
                @endauth
            </ul>
        </nav>

        <div class="platon-aside-footer">
            VM №478 &nbsp;·&nbsp; {{ date('Y') }}
        </div>
    </aside>

    {{-- ──────────── MAIN AREA ──────────── --}}
    <section class="platon-main">

        {{-- Header --}}
        <header class="platon-header">
            <div class="platon-page-title">@yield('title', 'Kabinet')</div>

            <div class="platon-header-actions">
                {{-- Search shortcut --}}
                <a href="{{ route('applications.index') }}" class="platon-icon-btn" title="Arizalar">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="9" cy="9" r="6.5"/><path stroke-linecap="round" d="M14 14l3.5 3.5"/>
                    </svg>
                </a>

                @auth
                {{-- Notification bell --}}
                <div class="platon-user-wrap" style="position:relative">
                    <button class="platon-icon-btn" id="bell-btn" onclick="toggleBell()" title="Bildirishnomalar" style="position:relative">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" d="M10 2a6 6 0 00-6 6v3l-1.5 2.5h15L16 11V8a6 6 0 00-6-6zM8 16a2 2 0 004 0"/>
                        </svg>
                        <span id="bell-badge" class="bell-badge" style="display:none"></span>
                    </button>
                    {{-- Notification dropdown --}}
                    <div class="platon-dropdown" id="bell-dropdown" style="min-width:340px;right:0;left:auto">
                        <div style="padding:12px 16px 8px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f0f2f5">
                            <strong style="font-size:0.9rem">Bildirishnomalar</strong>
                            <button onclick="markAllRead()" class="platon-btn platon-btn-outline platon-btn-sm" style="font-size:0.75rem;padding:4px 10px">Barchasini o'qildi</button>
                        </div>
                        <div id="bell-list" style="max-height:380px;overflow-y:auto">
                            <div style="padding:24px;text-align:center;color:#aab0bb;font-size:0.85rem" id="bell-empty">Bildirishnoma yo'q</div>
                        </div>
                        <div style="border-top:1px solid #f0f2f5;padding:8px 16px">
                            <a href="{{ route('notifications.page') }}" style="font-size:0.82rem;color:#018c87">Barcha bildirishnomalar →</a>
                        </div>
                    </div>
                </div>

                {{-- Inbox bell (staff) --}}
                @if(!auth()->user()->isConsumer())
                <a href="{{ route('applications.inbox') }}" class="platon-icon-btn" title="Kiruvchi">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" d="M10 2a6 6 0 00-6 6v3l-1.5 2.5h15L16 11V8a6 6 0 00-6-6zM8 16a2 2 0 004 0"/>
                    </svg>
                </a>
                @endif

                {{-- User avatar + dropdown --}}
                <div class="platon-user-wrap">
                    <div class="platon-avatar" id="avatar-btn" onclick="togglePlatonDropdown()">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="platon-dropdown" id="user-dropdown">
                        <div class="platon-dropdown-meta">
                            <strong>{{ auth()->user()->name }}</strong>
                            @php $roleLabel = \App\Models\ApplicationApproval::ROLE_LABELS[auth()->user()->role] ?? auth()->user()->role; @endphp
                            {{ $roleLabel }}
                            @if(auth()->user()->district)
                            <br>{{ auth()->user()->district->name_uz }}
                            @endif
                            @if(auth()->user()->pinfl)
                            <br><span style="font-size:0.72rem">PINFL: {{ auth()->user()->pinfl }}</span>
                            @endif
                        </div>
                        <a href="{{ route('profile') }}" class="platon-dropdown-item">Mening profilim</a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="platon-dropdown-item">IT Boshqaruv paneli</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" style="margin:0">
                            @csrf
                            <button type="submit" class="platon-dropdown-item text-danger">
                                Tizimdan chiqish
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        {{-- Page content --}}
        <div class="platon-content">
            @yield('content')
        </div>

    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/e-imzo.js') }}"></script>
<script src="{{ asset('js/e-imzo-client.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    function togglePlatonDropdown() {
        document.getElementById('user-dropdown').classList.toggle('show');
        document.getElementById('bell-dropdown')?.classList.remove('show');
    }
    function toggleBell() {
        var dd = document.getElementById('bell-dropdown');
        var isOpen = dd.classList.toggle('show');
        document.getElementById('user-dropdown').classList.remove('show');
        if (isOpen) loadNotifications();
    }
    document.addEventListener('click', function(e) {
        var avatarBtn = document.getElementById('avatar-btn');
        var userDd    = document.getElementById('user-dropdown');
        var bellBtn   = document.getElementById('bell-btn');
        var bellDd    = document.getElementById('bell-dropdown');
        if (avatarBtn && userDd && !avatarBtn.contains(e.target) && !userDd.contains(e.target)) {
            userDd.classList.remove('show');
        }
        if (bellBtn && bellDd && !bellBtn.contains(e.target) && !bellDd.contains(e.target)) {
            bellDd.classList.remove('show');
        }
    });

    @auth
    var _notifLoaded = false;
    var _csrfToken = '{{ csrf_token() }}';
    var _notifUrl  = '{{ route("notifications.json") }}';
    var _markAllUrl = '{{ route("notifications.read-all") }}';

    function loadNotifications() {
        fetch(_notifUrl, {headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}})
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                updateBellBadge(data.unread);
                renderNotifications(data.notifications);
            })
            .catch(function(err) {
                // Silently ignore - may be unauthenticated or network issue
                console.debug('Bell fetch:', err.message);
            });
    }

    function updateBellBadge(count) {
        var badge = document.getElementById('bell-badge');
        if (!badge) return;
        if (count > 0) {
            badge.style.display = 'flex';
            badge.textContent = count > 99 ? '99+' : count;
        } else {
            badge.style.display = 'none';
        }
    }

    function renderNotifications(items) {
        var list = document.getElementById('bell-list');
        var empty = document.getElementById('bell-empty');
        if (!items || items.length === 0) {
            list.innerHTML = '<div style="padding:24px;text-align:center;color:#aab0bb;font-size:0.85rem">Bildirishnoma yo\'q</div>';
            return;
        }
        empty && (empty.style.display = 'none');
        list.innerHTML = items.map(n => {
            var href = n.url ? n.url : '#';
            return `<a class="notif-item ${n.read ? '' : 'unread'}" href="${href}"
                       onclick="markRead(event, ${n.id}, this)">
                <span class="notif-icon">${n.icon}</span>
                <div style="flex:1;min-width:0">
                    <div class="notif-title">${escHtml(n.title)}</div>
                    ${n.body ? '<div class="notif-body">' + escHtml(n.body) + '</div>' : ''}
                    <div class="notif-meta">
                        ${n.created_at}
                        ${n.creator ? ' · ' + escHtml(n.creator) : ''}
                        ${n.read && n.read_at ? ' · o\'qildi: ' + n.read_at : ''}
                    </div>
                </div>
                ${!n.read ? '<span style="width:8px;height:8px;border-radius:50%;background:#018c87;flex-shrink:0;margin-top:5px"></span>' : ''}
            </a>`;
        }).join('');
    }

    function markRead(e, id, el) {
        if (!el.classList.contains('unread')) return;
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': _csrfToken, 'X-Requested-With': 'XMLHttpRequest'},
        }).then(() => {
            el.classList.remove('unread');
            el.querySelector('span[style*="border-radius:50%"]')?.remove();
            loadNotifications();
        });
    }

    function markAllRead() {
        fetch(_markAllUrl, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': _csrfToken, 'X-Requested-With': 'XMLHttpRequest'},
        }).then(() => loadNotifications());
    }

    function escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // Poll unread count every 30 seconds
    loadNotifications();
    setInterval(loadNotifications, 30000);
    @endauth
</script>
@stack('scripts')
</body>
</html>
