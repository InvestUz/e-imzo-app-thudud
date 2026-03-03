@extends('layouts.app')
@section('title', 'Foydalanuvchini tahrirlash — ' . $user->name)

@section('content')

@if(session('success'))
<div class="platon-alert platon-alert-success" style="margin-bottom:20px">✓ {{ session('success') }}</div>
@endif

<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <a href="{{ route('admin.users') }}" class="platon-btn platon-btn-outline platon-btn-sm">← Orqaga</a>
    <div>
        <h2 class="mb-0" style="font-size:1.2rem">{{ $user->name }}</h2>
        <div style="font-size:0.8rem;color:#6e788b">Foydalanuvchini tahrirlash</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="block" style="padding:28px">
            <h5 style="font-weight:700;margin-bottom:22px">Asosiy ma'lumotlar</h5>

            @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf @method('PATCH')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">To'liq ism <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required maxlength="255"
                            value="{{ old('name', $user->name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">PINFL</label>
                        <input type="text" name="pinfl" class="form-control" maxlength="20"
                            value="{{ old('pinfl', $user->pinfl) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Yangi parol</label>
                        <input type="password" name="password" class="form-control" minlength="6"
                            placeholder="Bo'sh qoldirsa o'zgarmaydi">
                        <div class="form-text">Kamida 6 ta belgi. Bo'sh qoldirsangiz parol o'zgarmaydi.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Rol <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            @foreach($allRoles as $val => $lbl)
                            <option value="{{ $val }}" {{ old('role', $user->role) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tuman</label>
                        <select name="district_id" class="form-select">
                            <option value="">— Tanlang —</option>
                            @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ old('district_id', $user->district_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->name_uz }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Lavozim (komissiya uchun)</label>
                        <input type="text" name="commission_position" class="form-control" maxlength="100"
                            placeholder="Masalan: Kadastr mutaxassisi"
                            value="{{ old('commission_position', $user->commission_position) }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check" style="padding:14px 16px;background:#f8f9fa;border-radius:10px;border:1px solid #e9ecef">
                            <input class="form-check-input" type="checkbox" name="is_regional_backup" value="1"
                                id="cb-backup" {{ old('is_regional_backup', $user->is_regional_backup) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cb-backup">
                                <strong>Mintaqaviy zaxira (Regional backup)</strong>
                                <div class="form-text mt-1">
                                    Bu flag yoqilsa foydalanuvchi <strong>barcha tumanlar</strong> arizalarini
                                    ko'ra va tasdiqlaya oladi (o'z tumani bo'lmasa ham).
                                    Workflow xodimlar uchun tavsiya etiladi.
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="platon-btn platon-btn-primary" style="flex:1">
                                Saqlash
                            </button>
                            <a href="{{ route('admin.users') }}" class="platon-btn platon-btn-outline">Bekor qilish</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Right column: info + danger zone --}}
    <div class="col-lg-5">
        {{-- User info card --}}
        <div class="block" style="padding:20px;margin-bottom:16px">
            <h6 style="font-weight:700;margin-bottom:14px">Hozirgi holat</h6>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                <div style="width:48px;height:48px;border-radius:50%;background:rgba(1,140,135,0.1);display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;color:#018c87">
                    {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div style="font-weight:700">{{ $user->name }}</div>
                    <div style="font-size:0.8rem;color:#6e788b">{{ $allRoles[$user->role] ?? $user->role }}</div>
                </div>
            </div>
            <div style="font-size:0.82rem;line-height:1.9">
                <div><span style="color:#6e788b;display:inline-block;min-width:110px">Email:</span> {{ $user->email ?? '—' }}</div>
                <div><span style="color:#6e788b;display:inline-block;min-width:110px">PINFL:</span> <code>{{ $user->pinfl ?? '—' }}</code></div>
                <div><span style="color:#6e788b;display:inline-block;min-width:110px">Tuman:</span> {{ $user->district?->name_uz ?? '—' }}</div>
                <div><span style="color:#6e788b;display:inline-block;min-width:110px">Zaxira:</span>
                    @if($user->is_regional_backup)
                    <span class="sbadge sbadge-success" style="font-size:0.72rem">Ha</span>
                    @else
                    <span class="sbadge sbadge-gray" style="font-size:0.72rem">Yo'q</span>
                    @endif
                </div>
                <div><span style="color:#6e788b;display:inline-block;min-width:110px">E-IMZO:</span>
                    @if($user->serial_number)
                    <span class="sbadge {{ $user->isCertificateValid() ? 'sbadge-success' : 'sbadge-danger' }}" style="font-size:0.72rem">
                        {{ $user->isCertificateValid() ? 'Faol ('.$user->certificate_valid_to?->format('d.m.Y').')' : 'Muddati o\'tgan' }}
                    </span>
                    @else
                    <span style="color:#aab0bb">Yo'q</span>
                    @endif
                </div>
                <div><span style="color:#6e788b;display:inline-block;min-width:110px">Yaratilgan:</span> {{ $user->created_at->format('d.m.Y') }}</div>
            </div>
        </div>

        {{-- Active sessions --}}
        @php $sessions = $user->activeSessions; @endphp
        @if($sessions->count())
        <div class="block" style="padding:20px;margin-bottom:16px">
            <h6 style="font-weight:700;margin-bottom:12px">Faol sessiyalar ({{ $sessions->count() }})</h6>
            @foreach($sessions as $s)
            <div style="padding:10px 12px;background:#f9fafb;border-radius:8px;margin-bottom:8px;font-size:0.8rem">
                <div style="font-weight:600">{{ $s->ip_address }}</div>
                <div style="color:#6e788b">{{ $s->user_agent }}</div>
                <div style="color:#aab0bb">{{ $s->logged_in_at?->format('d.m.Y H:i') }}</div>
            </div>
            @endforeach
            <form action="{{ route('admin.users.force-logout', $user) }}" method="POST"
                onsubmit="return confirm('Barcha sessiyalarni tugatish?')">
                @csrf
                <button type="submit" class="platon-btn platon-btn-danger platon-btn-sm" style="width:100%">
                    Barcha sessiyalarni tugatish
                </button>
            </form>
        </div>
        @endif

        {{-- Danger zone --}}
        @if(auth()->id() !== $user->id)
        <div class="block" style="padding:20px;border:2px solid rgba(230,50,96,.2);border-radius:14px">
            <h6 style="font-weight:700;color:#e63260;margin-bottom:10px">Xavfli zona</h6>
            <p style="font-size:0.8rem;color:#6e788b;margin-bottom:14px">
                Foydalanuvchi o'chirilsa, uning sessiyalari va bildirishnomalari ham o'chiriladi.
                Arizalari saqlanib qoladi.
            </p>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                onsubmit="return confirm('{{ addslashes($user->name) }} ni o\'chirasizmi? Bu amalni bekor qilib bo\'lmaydi!')">
                @csrf @method('DELETE')
                <button type="submit" class="platon-btn platon-btn-danger" style="width:100%">
                    Foydalanuvchini o'chirish
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

@endsection
