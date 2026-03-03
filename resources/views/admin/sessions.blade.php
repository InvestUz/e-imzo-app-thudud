@extends('layouts.app')
@section('title', 'Sessiyalar — Admin')

@section('content')

@if(session('success'))
<div class="platon-alert platon-alert-success" style="margin-bottom:20px">✓ {{ session('success') }}</div>
@endif

{{-- Filter --}}
<form method="GET" action="{{ route('admin.sessions') }}">
<div class="block" style="margin-bottom:20px;padding:16px 20px">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
        <div style="flex:1;min-width:200px">
            <div class="platon-search" style="width:100%">
                <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="#aab0bb" stroke-width="1.5" style="margin-left:10px;flex-shrink:0"><circle cx="9" cy="9" r="6"/><path stroke-linecap="round" d="M14 14l3 3"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="IP, ism, PINFL...">
            </div>
        </div>
        <label style="display:flex;align-items:center;gap:6px;font-size:0.85rem;cursor:pointer">
            <input type="checkbox" name="active_only" value="1" {{ request('active_only') ? 'checked' : '' }}>
            Faqat faol sessiyalar
        </label>
        <button type="submit" class="platon-btn platon-btn-primary platon-btn-sm">Filter</button>
        <a href="{{ route('admin.sessions') }}" class="platon-btn platon-btn-outline platon-btn-sm">Tozalash</a>
    </div>
</div>
</form>

<div class="block">
    <div class="section-heading">
        Sessiyalar <span class="sbadge sbadge-gray" style="font-size:0.8rem">{{ $sessions->total() }}</span>
    </div>
    <div class="platon-table-wrap">
        <table class="platon-table">
            <thead>
                <tr>
                    <th>Foydalanuvchi</th>
                    <th>IP manzil</th>
                    <th>Brauzer / OS</th>
                    <th>Kirish usuli</th>
                    <th>Kirgan vaqt</th>
                    <th>So'nggi faollik</th>
                    <th>Chiqish vaqti</th>
                    <th>Holat</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sessions as $sess)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:0.85rem">{{ $sess->user?->name ?? '—' }}</div>
                    @if($sess->user?->pinfl)
                    <div style="font-size:0.72rem;color:#aab0bb;font-family:monospace">{{ $sess->user->pinfl }}</div>
                    @endif
                    @if($sess->user?->role)
                    <div style="font-size:0.72rem;color:#6e788b">{{ \App\Models\ApplicationApproval::ROLE_LABELS[$sess->user->role] ?? $sess->user->role }}</div>
                    @endif
                </td>
                <td>
                    <code style="font-size:0.82rem">{{ $sess->ip_address ?? '—' }}</code>
                </td>
                <td>
                    <div style="font-size:0.82rem">{{ $sess->browser }}</div>
                    <div style="font-size:0.72rem;color:#6e788b">{{ $sess->os }}</div>
                </td>
                <td>
                    @if($sess->auth_method === 'eimzo')
                    <span class="sbadge sbadge-purple" style="font-size:0.72rem">E-IMZO</span>
                    @elseif($sess->auth_method === 'password')
                    <span class="sbadge sbadge-blue" style="font-size:0.72rem">Parol</span>
                    @else
                    <span class="sbadge sbadge-gray" style="font-size:0.72rem">—</span>
                    @endif
                </td>
                <td style="font-size:0.82rem;white-space:nowrap">
                    {{ $sess->logged_in_at?->format('d.m.Y H:i') ?? '—' }}
                    <div style="font-size:0.72rem;color:#aab0bb">{{ $sess->logged_in_at?->diffForHumans() }}</div>
                </td>
                <td style="font-size:0.82rem">
                    {{ $sess->last_active_at?->format('d.m.Y H:i') ?? '—' }}
                </td>
                <td style="font-size:0.82rem">
                    @if($sess->logged_out_at)
                    {{ $sess->logged_out_at->format('d.m.Y H:i') }}
                    <div style="font-size:0.72rem;color:#aab0bb">{{ $sess->logged_out_at->diffForHumans() }}</div>
                    @else
                    <span style="color:#aab0bb">—</span>
                    @endif
                </td>
                <td>
                    @if($sess->is_active)
                    <span class="sbadge sbadge-success">Faol</span>
                    @else
                    <span class="sbadge sbadge-gray">Tugagan</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:40px;color:#aab0bb">Sessiyalar topilmadi</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($sessions->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center">{{ $sessions->links() }}</div>
    @endif
</div>

{{-- Security info card --}}
<div class="block" style="margin-top:20px;background:rgba(1,140,135,0.04);border:1px solid rgba(1,140,135,0.15)">
    <div style="display:flex;gap:14px;align-items:flex-start">
        <div style="font-size:1.5rem">🔐</div>
        <div>
            <div style="font-weight:700;font-size:0.9rem;color:#018c87;margin-bottom:4px">Xavfsizlik siyosati</div>
            <div style="font-size:0.82rem;color:#6e788b;line-height:1.6">
                Tizimda har bir foydalanuvchi uchun <strong>bitta faol sessiya</strong> ruxsat etiladi.<br>
                Yangi qurilmadan kirish amalga oshsa, avvalgi sessiya avtomatik tugatiladi va foydalanuvchiga bildirishnoma yuboriladi.<br>
                Barcha kirish urinishlari IP manzil, brauzer va vaqt bilan birga qayd etiladi.
            </div>
        </div>
    </div>
</div>
@endsection
