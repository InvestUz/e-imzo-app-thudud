@extends('layouts.app')
@section('title', 'Bildirishnomalar')

@section('content')
<div class="block">
    <div class="section-heading">
        Barcha bildirishnomalar
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="platon-btn platon-btn-outline platon-btn-sm">Barchasini o'qildi</button>
        </form>
    </div>

    @forelse($notifications as $n)
    <div class="notif-item {{ $n->isRead() ? '' : 'unread' }}" style="border-radius:0">
        <span class="notif-icon">{{ $n->icon }}</span>
        <div style="flex:1">
            <div class="notif-title">{{ $n->title }}</div>
            @if($n->body)
            <div class="notif-body">{{ $n->body }}</div>
            @endif
            <div class="notif-meta">
                {{ $n->created_at->format('d.m.Y H:i') }}
                @if($n->creator)· {{ $n->creator->name }}@endif
                @if($n->read_at) · <span style="color:#018c87">O'qildi: {{ $n->read_at->format('d.m.Y H:i') }}</span>@endif
            </div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
            <span class="sbadge {{ $n->color_class }}" style="font-size:0.72rem">{{ $n->type }}</span>
            @if(!$n->isRead())
            <form action="{{ route('notifications.read', $n) }}" method="POST">
                @csrf
                <button type="submit" class="platon-btn platon-btn-outline platon-btn-sm" style="font-size:0.72rem;padding:3px 9px">O'qildi</button>
            </form>
            @endif
            @if($n->related_type === 'application' && $n->related_id)
            <a href="{{ route('applications.show', $n->related_id) }}" class="platon-btn platon-btn-outline platon-btn-sm" style="font-size:0.72rem;padding:3px 9px">Ko'rish</a>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:60px;color:#aab0bb">
        <div style="font-size:3rem;margin-bottom:12px">🔔</div>
        <div style="font-size:0.9rem">Bildirishnomalar yo'q</div>
    </div>
    @endforelse

    @if($notifications->hasPages())
    <div style="margin-top:20px;display:flex;justify-content:center">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
