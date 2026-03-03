@extends('layouts.app')
@section('title', 'IT Boshqaruv paneli')

@section('content')
{{-- Flash --}}
@if(session('success'))
<div class="platon-alert platon-alert-success" style="margin-bottom:20px">✓ {{ session('success') }}</div>
@endif

{{-- Stat cards --}}
<div class="stat-cards-row">
    <div class="stat-card-p sc-teal">
        <span class="sc-label">Jami arizalar</span>
        <span class="sc-value">{{ $stats['total'] }}</span>
    </div>
    <div class="stat-card-p sc-orange">
        <span class="sc-label">Jarayonda</span>
        <span class="sc-value">{{ $stats['pending'] }}</span>
    </div>
    <div class="stat-card-p sc-green-dk">
        <span class="sc-label">Tasdiqlangan</span>
        <span class="sc-value">{{ $stats['approved'] }}</span>
    </div>
    <div class="stat-card-p sc-red">
        <span class="sc-label">Rad etilgan</span>
        <span class="sc-value">{{ $stats['rejected'] }}</span>
    </div>
    <div class="stat-card-p sc-blue">
        <span class="sc-label">Foydalanuvchilar</span>
        <span class="sc-value">{{ $stats['users'] }}</span>
    </div>
    <div class="stat-card-p sc-green">
        <span class="sc-label">Faol sessiyalar</span>
        <span class="sc-value">{{ $stats['sessions'] }}</span>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px" class="admin-grid">

    {{-- Recent Applications --}}
    <div class="block" style="grid-column:1 / -1">
        <div class="section-heading">
            So'nggi arizalar
            <a href="{{ route('admin.applications') }}" class="platon-btn platon-btn-outline platon-btn-sm">Barchasini ko'rish</a>
        </div>
        <div class="platon-table-wrap">
            <table class="platon-table">
                <thead>
                    <tr>
                        <th>№ Ariza</th>
                        <th>Ariza beruvchi</th>
                        <th>Tuman</th>
                        <th>Holat</th>
                        <th>Yuborilgan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recent as $app)
                <tr onclick="window.location='{{ route('applications.show', $app) }}'">
                    <td><strong style="font-family:monospace;font-size:0.82rem">{{ $app->number }}</strong></td>
                    <td>
                        <div style="font-weight:600;font-size:0.85rem">{{ $app->applicant?->name ?? '—' }}</div>
                        @if($app->applicant?->pinfl)
                        <div style="font-size:0.75rem;color:#6e788b">{{ $app->applicant->pinfl }}</div>
                        @endif
                    </td>
                    <td>{{ $app->district?->name_uz ?? '—' }}</td>
                    <td>@include('partials.status-badge', ['status' => $app->status])</td>
                    <td style="font-size:0.82rem;color:#6e788b">{{ $app->submitted_at?->format('d.m.Y H:i') ?? '—' }}</td>
                    <td><a href="{{ route('applications.show', $app) }}" class="platon-btn platon-btn-outline platon-btn-sm" onclick="event.stopPropagation()">Ko'rish</a></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;color:#aab0bb">Arizalar yo'q</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Active Sessions --}}
    <div class="block">
        <div class="section-heading">
            Faol sessiyalar
            <a href="{{ route('admin.sessions') }}" class="platon-btn platon-btn-outline platon-btn-sm">Hammasi</a>
        </div>
        @forelse($activeSessions as $sess)
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f4f6f8">
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(1,140,135,0.12);display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;color:#018c87;flex-shrink:0">
                {{ strtoupper(mb_substr($sess->user?->name ?? '?', 0, 2)) }}
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $sess->user?->name ?? '—' }}</div>
                <div style="font-size:0.75rem;color:#6e788b">{{ $sess->ip_address }} · {{ $sess->browser }} / {{ $sess->os }}</div>
                <div style="font-size:0.72rem;color:#aab0bb">{{ $sess->logged_in_at?->diffForHumans() }}</div>
            </div>
            <span class="sbadge sbadge-success" style="font-size:0.72rem">Faol</span>
        </div>
        @empty
        <p style="color:#aab0bb;font-size:0.85rem;text-align:center;padding:24px 0">Faol sessiya yo'q</p>
        @endforelse
    </div>

    {{-- Quick links --}}
    <div class="block">
        <div class="section-heading">Tezkor havolalar</div>
        <a href="{{ route('admin.applications') }}" class="quick-link">
            <div class="quick-link-icon ql-teal">📋</div>
            <div class="quick-link-info">
                <strong>Barcha arizalar</strong>
                <span>Filter, qidiruv, holat bo'yicha</span>
            </div>
        </a>
        <a href="{{ route('admin.users') }}" class="quick-link">
            <div class="quick-link-icon ql-yellow">👥</div>
            <div class="quick-link-info">
                <strong>Foydalanuvchilar</strong>
                <span>Rol, sessiya, xabar yuborish</span>
            </div>
        </a>
        <a href="{{ route('admin.sessions') }}" class="quick-link">
            <div class="quick-link-icon ql-green">🔐</div>
            <div class="quick-link-info">
                <strong>Sessiyalar monitor</strong>
                <span>IP, vaqt, qurilma, majburiy chiqish</span>
            </div>
        </a>
        <a href="{{ route('notifications.page') }}" class="quick-link">
            <div class="quick-link-icon" style="background:#f3e8ff">🔔</div>
            <div class="quick-link-info">
                <strong>Bildirishnomalar</strong>
                <span>Tizim xabarlari</span>
            </div>
        </a>
    </div>
</div>

<style>
@media(max-width:760px){ .admin-grid { grid-template-columns:1fr !important; } }
</style>
@endsection
