@extends('layouts.app')

@section('title', 'Kiruvchi arizalar')

@push('styles')
<style>
.inbox-card { background:#fff; border-radius:14px; padding:0; border:1px solid #f0f2f5; box-shadow:0 2px 8px rgba(0,0,0,.05); overflow:hidden; margin-bottom:12px; transition:box-shadow .2s; }
.inbox-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.10); }
.inbox-card-body { padding:16px 20px; }
.inbox-meta { font-size:.8rem; color:#6e788b; display:flex; flex-wrap:wrap; gap:12px; margin-top:6px; }
.step-badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:.75rem; font-weight:700; background:rgba(1,140,135,.1); color:#018c87; border:1px solid #018c87; }
.backup-note { font-size:.75rem; color:#9a6800; background:#fff3cd; padding:2px 8px; border-radius:10px; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-0">Kiruvchi arizalar</h2>
        <p class="text-muted small mb-0">Sizning rolingiz: <strong>{{ \App\Models\ApplicationApproval::ROLE_LABELS[auth()->user()->role] ?? auth()->user()->role }}</strong>
            @if(auth()->user()->is_regional_backup)
            &nbsp;<span class="backup-note">Mintaqaviy zaxira</span>
            @endif
        </p>
    </div>
    <span class="badge bg-primary rounded-pill fs-6">{{ $pendingApprovals->total() }}</span>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($pendingApprovals->isEmpty())
<div class="text-center py-5 text-muted">
    <div style="font-size:3rem">📭</div>
    <p class="mt-2">Hozircha kutayotgan arizalar yo'q</p>
</div>
@else
@foreach($pendingApprovals as $approval)
@php $app = $approval->application; @endphp
<div class="inbox-card">
    <div class="inbox-card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="step-badge">{{ $approval->roleLabel() }}</span>
                    @if($app->district && $app->district->id !== auth()->user()->district_id)
                    <span class="backup-note">Zaxira sifatida</span>
                    @endif
                    <strong>{{ $app->number }}</strong>
                </div>
                <div class="fw-semibold">
                    Kadastr: {{ $app->cadastral_number }}
                    &nbsp;&bull;&nbsp;{{ $app->district->name_uz ?? '—' }}
                </div>
                <div class="inbox-meta">
                    @if($app->address)
                    <span>📍 {{ Str::limit($app->address, 50) }}</span>
                    @endif
                    @if($app->area_sqm)
                    <span>📐 {{ number_format($app->area_sqm, 2) }} m²</span>
                    @endif
                    <span>👤 {{ $app->applicant->name ?? '—' }}</span>
                    <span>📅 {{ $app->submitted_at?->setTimezone(config('app.timezone'))->format('d.m.Y H:i') }}</span>
                    <span>{{ $app->source === 'online' ? '🌐 Onlayn' : '📄 Yozma' }}</span>
                </div>
            </div>
            <a href="{{ route('applications.show', $app) }}" class="btn btn-primary btn-sm px-4">
                Ko'rish &rarr;
            </a>
        </div>
        @if($approval->assigned_to && $approval->assigned_to !== auth()->user()->id)
        <div class="text-muted small mt-2">
            Belgilangan: {{ $approval->assignee->name ?? '—' }}
            (Siz zaxira sifatida ko'rib chiqyapsiz)
        </div>
        @endif
    </div>
</div>
@endforeach

@if($pendingApprovals->hasPages())
<div class="mt-3">{{ $pendingApprovals->links() }}</div>
@endif
@endif
@endsection
