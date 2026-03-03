@extends('layouts.app')

@section('title', 'Arizalar')

@push('styles')
<style>
.status-badge { font-size: 0.78rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
.status-pending          { background:#fff3cd; color:#856404; }
.status-moderator_review { background:rgba(20,113,240,.1); color:#1471f0; border:1px solid #1471f0; }
.status-complaint_review { background:rgba(14,186,148,.1); color:#0eba94; border:1px solid #0eba94; }
.status-legal_review     { background:rgba(103,60,200,.1); color:#673cc8; border:1px solid #673cc8; }
.status-executor_review  { background:rgba(213,141,61,.1); color:#a05a00; border:1px solid #d5893d; }
.status-head_review      { background:rgba(1,140,135,.1);  color:#018c87; border:1px solid #018c87; }
.status-approved         { background:rgba(6,184,56,.1);   color:#0bc33f; border:1px solid #0bc33f; }
.status-rejected         { background:rgba(230,50,96,.1);  color:#e63260; border:1px solid #e63260; }
</style>
@endpush

@section('content')

{{-- Toolbar --}}
<div class="platon-toolbar">
    <div class="platon-toolbar-left">
        <span style="font-size:1rem;font-weight:700;color:#15191e">Arizalar</span>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
        @if(auth()->user()->isConsumer() || auth()->user()->isAdmin())
        <a href="{{ route('applications.create') }}" class="platon-btn platon-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
            Yangi ariza
        </a>
        @endif
    </div>
</div>

@if(session('success'))
<div class="platon-alert platon-alert-success" style="margin-bottom:16px">
    ✓ {{ session('success') }}
</div>
@endif

{{-- Table block --}}
<div class="block" style="padding:0;overflow:hidden">
    @if($applications->isEmpty())
    <div style="text-align:center;padding:64px 24px;color:#aab0bb">
        <svg width="48" height="48" fill="none" stroke="#d7dde1" stroke-width="1.5" viewBox="0 0 24 24" style="display:block;margin:0 auto 16px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p style="font-size:0.95rem;margin-bottom:12px">Hozircha arizalar yo'q</p>
        @if(auth()->user()->isConsumer())
        <a href="{{ route('applications.create') }}" class="platon-btn platon-btn-primary">Ariza yuborish</a>
        @endif
    </div>
    @else
    <table class="platon-table">
        <thead>
            <tr>
                <th><div class="th-inner">№ zaявки
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 14a.6.6 0 01-.47-.2L4.2 10.47a.64.64 0 01.93-.93L8 12.4l2.87-2.86a.64.64 0 01.93.93L8.47 13.8A.6.6 0 018 14zM4.67 6.67a.6.6 0 01-.47-.2.64.64 0 010-.93L7.53 2.2a.64.64 0 01.94 0L11.8 5.54a.64.64 0 010 .93.64.64 0 01-.93 0L8 3.6 5.13 6.47a.6.6 0 01-.46.2z" fill="#78829D"/></svg>
                </div></th>
                <th>Kadastr raqami</th>
                <th>Tuman</th>
                @if(!auth()->user()->isConsumer())
                <th>Ariza beruvchi</th>
                @endif
                <th><div class="th-inner">Sana
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 14a.6.6 0 01-.47-.2L4.2 10.47a.64.64 0 01.93-.93L8 12.4l2.87-2.86a.64.64 0 01.93.93L8.47 13.8A.6.6 0 018 14zM4.67 6.67a.6.6 0 01-.47-.2.64.64 0 010-.93L7.53 2.2a.64.64 0 01.94 0L11.8 5.54a.64.64 0 010 .93.64.64 0 01-.93 0L8 3.6 5.13 6.47a.6.6 0 01-.46.2z" fill="#78829D"/></svg>
                </div></th>
                <th>Holati</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr onclick="window.location='{{ route('applications.show', $app) }}'">
                <td><strong style="color:#018c87">{{ $app->number }}</strong></td>
                <td>{{ $app->cadastral_number }}</td>
                <td>{{ $app->district->name_uz ?? '—' }}</td>
                @if(!auth()->user()->isConsumer())
                <td style="color:#6e788b;font-size:0.83rem">{{ $app->applicant->name ?? '—' }}</td>
                @endif
                <td style="color:#6e788b;font-size:0.83rem;white-space:nowrap">
                    {{ $app->submitted_at?->setTimezone(config('app.timezone'))->format('d.m.Y') }}
                </td>
                <td>
                    <span class="status-badge status-{{ $app->status }}">
                        {{ $app->statusLabel() }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('applications.show', $app) }}" class="platon-btn platon-btn-outline platon-btn-sm" onclick="event.stopPropagation()">
                        Ko'rish →
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($applications->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #eee">
        {{ $applications->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
