@extends('layouts.app')
@section('title', 'Mening profilim')

@section('content')

@if(session('success'))
<div class="platon-alert platon-alert-success" style="margin-bottom:20px">✓ {{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1.4fr;gap:20px" class="profile-grid">

    {{-- Left: user info + E-IMZO key --}}
    <div>
        {{-- Profile card --}}
        <div class="block" style="margin-bottom:20px">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
                <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#018c87,#00bfaf);display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:700;color:#fff;flex-shrink:0">
                    {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:1.05rem;font-weight:700;color:#15191e">{{ $user->name }}</div>
                    @php $roleLabel = \App\Models\ApplicationApproval::ROLE_LABELS[$user->role] ?? $user->role; @endphp
                    <div style="font-size:0.82rem;color:#6e788b">{{ $roleLabel }}</div>
                    @if($user->district)
                    <div style="font-size:0.82rem;color:#6e788b">{{ $user->district->name_uz }}</div>
                    @endif
                </div>
            </div>

            {{-- Info rows --}}
            @php
            $rows = [
                ['PINFL', $user->pinfl],
                ['INN / STIR', $user->inn],
                ['Email', $user->email],
                ['Tashkilot', $user->organization],
                ['Lavozim', $user->position],
                ['Komissiya lavozimi', $user->commission_position],
            ];
            @endphp
            @foreach($rows as [$lbl, $val])
            @if($val)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f4f6f8;font-size:0.85rem">
                <span style="color:#6e788b">{{ $lbl }}</span>
                <span style="font-weight:500;font-family:{{ in_array($lbl, ['PINFL','INN / STIR']) ? 'monospace' : 'inherit' }}">{{ $val }}</span>
            </div>
            @endif
            @endforeach
        </div>

        {{-- E-IMZO certificate card --}}
        <div class="block">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#018c87,#00bfaf);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0">🔑</div>
                <div>
                    <div style="font-weight:700;font-size:0.92rem">E-IMZO Kaliti</div>
                    <div style="font-size:0.78rem;color:#6e788b">Raqamli imzo sertifikati</div>
                </div>
            </div>

            @if($user->serial_number)
            <div style="background:#f7f9fa;border-radius:10px;padding:14px 16px;margin-bottom:12px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                    <span style="font-size:0.78rem;color:#6e788b">Holat</span>
                    @if($user->isCertificateValid())
                    <span class="sbadge sbadge-success">✓ Amal qilmoqda</span>
                    @else
                    <span class="sbadge sbadge-danger">Muddati o'tgan</span>
                    @endif
                </div>
                @php
                $certRows = [
                    ['Seriya raqami', $user->serial_number],
                    ['Amal boshlanishi', $user->certificate_valid_from?->format('d.m.Y H:i')],
                    ['Amal tugashi',    $user->certificate_valid_to?->format('d.m.Y H:i')],
                ];
                @endphp
                @foreach($certRows as [$cl, $cv])
                @if($cv)
                <div style="display:flex;justify-content:space-between;padding:5px 0;font-size:0.82rem;border-bottom:1px solid #eee">
                    <span style="color:#6e788b">{{ $cl }}</span>
                    <span style="font-family:monospace;font-size:0.78rem;word-break:break-all;text-align:right;max-width:200px">{{ $cv }}</span>
                </div>
                @endif
                @endforeach

                @if($user->certificate_valid_to)
                @php $daysLeft = now()->diffInDays($user->certificate_valid_to, false); @endphp
                <div style="margin-top:10px;padding:8px 12px;border-radius:8px;font-size:0.8rem;
                    {{ $daysLeft > 30 ? 'background:rgba(6,184,56,0.08);color:#0bc33f' : ($daysLeft > 0 ? 'background:#fff3cd;color:#9a6800' : 'background:#f8d7da;color:#721c24') }}">
                    @if($daysLeft > 0)
                    ⏱ Sertifikat amal qilish muddati: {{ $daysLeft }} kun qoldi
                    @else
                    ⚠️ Sertifikat muddati {{ abs($daysLeft) }} kun oldin tugagan
                    @endif
                </div>
                @endif
            </div>
            <div style="font-size:0.78rem;color:#aab0bb;line-height:1.5">
                E-IMZO kaliti oxirgi marta tizimga kirishda yangilangan.<br>
                Hozirgi kalit sizning hisob qaydnomangizga bog'langan.
            </div>
            @else
            <div style="text-align:center;padding:28px 0;color:#aab0bb">
                <div style="font-size:2rem;margin-bottom:8px">🔑</div>
                <div style="font-size:0.85rem">E-IMZO kaliti topilmadi</div>
                <div style="font-size:0.78rem;margin-top:4px">Tizimga E-IMZO orqali kiring</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Right: sessions --}}
    <div>
        <div class="block">
            <div class="section-heading">
                Sessiyalar tarixi
                <form action="{{ route('profile.sessions.terminate-all') }}" method="POST" onsubmit="return confirm('Barcha sessiyalarni tugatib, tizimdan chiqmoqchimisiz?')">
                    @csrf
                    <button type="submit" class="platon-btn platon-btn-danger platon-btn-sm">Hammasini tugatish</button>
                </form>
            </div>

            @forelse($sessions as $sess)
            <div style="display:flex;gap:12px;align-items:flex-start;padding:14px 0;border-bottom:1px solid #f4f6f8">
                <div style="width:36px;height:36px;border-radius:10px;background:{{ $sess->is_active ? 'rgba(1,140,135,0.12)' : '#f4f6f8' }};display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0">
                    {{ $sess->browser === 'Chrome' ? '🌐' : ($sess->browser === 'Firefox' ? '🦊' : ($sess->browser === 'Safari' ? '🧭' : '💻')) }}
                </div>
                <div style="flex:1">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px">
                        <span style="font-weight:600;font-size:0.88rem">{{ $sess->browser }} / {{ $sess->os }}</span>
                        @if($sess->is_active)
                        <span class="sbadge sbadge-success" style="font-size:0.7rem">Faol</span>
                        @else
                        <span class="sbadge sbadge-gray" style="font-size:0.7rem">Tugagan</span>
                        @endif
                        @if($sess->auth_method === 'eimzo')
                        <span class="sbadge sbadge-purple" style="font-size:0.7rem">E-IMZO</span>
                        @elseif($sess->auth_method === 'password')
                        <span class="sbadge sbadge-blue" style="font-size:0.7rem">Parol</span>
                        @endif
                    </div>
                    <div style="font-size:0.78rem;color:#6e788b">
                        🌐 IP: <code>{{ $sess->ip_address ?? '—' }}</code>
                    </div>
                    <div style="font-size:0.75rem;color:#aab0bb;margin-top:2px">
                        Kirdi: {{ $sess->logged_in_at?->format('d.m.Y H:i') ?? '—' }}
                        @if($sess->logged_out_at)
                         · Chiqdi: {{ $sess->logged_out_at->format('d.m.Y H:i') }}
                        @elseif($sess->last_active_at)
                         · Faol: {{ $sess->last_active_at->diffForHumans() }}
                        @endif
                    </div>
                </div>
                @if($sess->is_active)
                <form action="{{ route('profile.session.terminate', $sess) }}" method="POST" onsubmit="return confirm('Bu sessiyani tugatmoqchimisiz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="platon-btn platon-btn-outline platon-btn-sm" style="color:#e63260;border-color:#e63260">Tugatish</button>
                </form>
                @endif
            </div>
            @empty
            <p style="text-align:center;padding:32px 0;color:#aab0bb;font-size:0.85rem">Sessiya tarixi yo'q</p>
            @endforelse
        </div>

        {{-- Security tips --}}
        <div class="block" style="margin-top:20px;background:rgba(1,140,135,0.04);border:1px solid rgba(1,140,135,0.15)">
            <div style="font-weight:700;font-size:0.88rem;color:#018c87;margin-bottom:10px">🔐 Xavfsizlik maslahatlari</div>
            <ul style="margin:0;padding-left:18px;font-size:0.82rem;color:#6e788b;line-height:1.8">
                <li>Tanish bo'lmagan qurilmadan kirish ko'rsangiz, darhol barcha sessiyalarni tugatib, parolingizni o'zgartiring.</li>
                <li>Tizimda bitta qurilmadan faqat bitta sessiya ishlaydi.</li>
                <li>E-IMZO kalitingiz muddati tugamagunga qaytib yangilang.</li>
                <li>Umumiy kompyuterdan foydalansangiz, albatta "Tizimdan chiqish" tugmasini bosing.</li>
            </ul>
        </div>
    </div>
</div>

<style>
@media(max-width:760px){ .profile-grid { grid-template-columns:1fr !important; } }
</style>
@endsection
