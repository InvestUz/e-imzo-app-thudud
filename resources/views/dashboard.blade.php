@extends('layouts.app')

@section('title', 'Bosh sahifa')

@section('content')
@php
    use App\Models\Application;
    use App\Models\ApplicationApproval;
    $user = auth()->user();

    if ($user->isAdmin()) {
        $total     = Application::count();
        $approved  = Application::where('status','approved')->count();
        $pending   = Application::whereNotIn('status',['approved','rejected'])->count();
        $rejected  = Application::where('status','rejected')->count();
        $myPending = 0;
    } else {
        $total     = Application::where('district_id', $user->district_id)->count();
        $approved  = Application::where('district_id', $user->district_id)->where('status','approved')->count();
        $pending   = Application::where('district_id', $user->district_id)->whereNotIn('status',['approved','rejected'])->count();
        $rejected  = Application::where('district_id', $user->district_id)->where('status','rejected')->count();
        $myPending = ApplicationApproval::where('step_role', $user->role)->where('status','pending')
                        ->whereHas('application', fn($q) => $q->where('district_id', $user->district_id))->count();
        if ($user->is_regional_backup) {
            $myPending = ApplicationApproval::where('step_role', $user->role)->where('status','pending')->count();
        }
    }
@endphp

{{-- Welcome --}}
<div style="margin-bottom:20px">
    <div style="font-size:1.1rem;font-weight:700;color:#15191e">
        Xush kelibsiz, {{ $user->name }}
    </div>
    <div style="font-size:0.85rem;color:#6e788b;margin-top:3px">
        @if($user->district){{ $user->district->name_uz }} &nbsp;·&nbsp; @endif
        {{ \App\Models\ApplicationApproval::ROLE_LABELS[$user->role] ?? $user->role }}
        @if($user->is_regional_backup)
        &nbsp;<span style="background:#fff3cd;color:#9a6800;font-size:0.72rem;font-weight:700;padding:2px 8px;border-radius:10px">⚡ Mintaqaviy zaxira</span>
        @endif
    </div>
</div>

{{-- Stat cards --}}
<div class="stat-cards-row">
    <div class="stat-card-p sc-teal">
        <div class="sc-label">Jami arizalar</div>
        <div class="sc-value">{{ $total }}</div>
    </div>
    <div class="stat-card-p sc-orange">
        <div class="sc-label">Ko'rib chiqilmoqda</div>
        <div class="sc-value">{{ $pending }}</div>
    </div>
    <div class="stat-card-p sc-green-dk">
        <div class="sc-label">Tasdiqlangan</div>
        <div class="sc-value">{{ $approved }}</div>
    </div>
    <div class="stat-card-p sc-red">
        <div class="sc-label">Rad etilgan</div>
        <div class="sc-value">{{ $rejected }}</div>
    </div>
</div>

{{-- Quick links + info --}}
<div class="row g-3">
    <div class="col-lg-6">
        <div class="block">
            <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#aab0bb;margin-bottom:16px">Tez o'tish</div>

            @if(!$user->isConsumer())
            <a href="{{ route('applications.inbox') }}" class="quick-link">
                <div class="quick-link-icon ql-yellow">📥</div>
                <div class="quick-link-info">
                    <strong>Kiruvchi arizalar</strong>
                    <span>Sizni kutayotgan: {{ $myPending }} ta</span>
                </div>
                @if($myPending > 0)
                <span class="sbadge sbadge-warning ms-auto">{{ $myPending }}</span>
                @endif
            </a>
            @endif

            <a href="{{ route('applications.index') }}" class="quick-link">
                <div class="quick-link-icon ql-teal">📋</div>
                <div class="quick-link-info">
                    <strong>Arizalar ro'yxati</strong>
                    <span>Qidirish va ko'rish</span>
                </div>
            </a>

            @if($user->isAdmin())
            <a href="{{ route('applications.create') }}" class="quick-link">
                <div class="quick-link-icon ql-green">➕</div>
                <div class="quick-link-info">
                    <strong>Yangi ariza qo'shish</strong>
                    <span>Yozma ariza ro'yxatdan o'tkazish</span>
                </div>
            </a>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="block h-100">
            <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#aab0bb;margin-bottom:16px">Jarayon bosqichlari</div>
            <div style="font-size:0.85rem;color:#5a6a8a;line-height:1.6;margin-bottom:14px">
                <strong style="color:#15191e">Vazirlar Mahkamasi Qarori №478</strong> asosida
                qo'shni hududga ariza berish va ko'rib chiqish jarayoni.
            </div>
            <div>
                @foreach(\App\Models\ApplicationApproval::ROLE_LABELS as $roleKey => $roleLabel)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                    <div style="width:24px;height:24px;border-radius:50%;background:{{ $roleKey === $user->role ? '#018c87' : '#f0f2f5' }};color:{{ $roleKey === $user->role ? '#fff' : '#6e788b' }};font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ $loop->iteration }}</div>
                    <span style="font-size:0.85rem;{{ $roleKey === $user->role ? 'color:#018c87;font-weight:700' : 'color:#27314b' }}">{{ $roleLabel }}</span>
                    @if($roleKey === $user->role)
                    <span class="sbadge sbadge-info" style="font-size:0.65rem;padding:2px 8px">Siz</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
