@extends('layouts.app')
@section('title', 'Rol va Huquqlar — Admin')

@push('styles')
<style>
.role-card { background:#fff; border-radius:14px; padding:22px; border:1px solid #f0f2f5; box-shadow:0 2px 8px rgba(0,0,0,.04); margin-bottom:20px; }
.role-card-header { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
.role-icon { width:40px; height:40px; border-radius:10px; background:rgba(1,140,135,.1); display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
.role-title { font-weight:700; font-size:1rem; }
.role-desc { font-size:0.82rem; color:#6e788b; margin-bottom:14px; }
.perm-list { list-style:none; padding:0; margin:0; }
.perm-list li { font-size:0.82rem; padding:4px 0; display:flex; align-items:flex-start; gap:8px; }
.perm-can  { color:#0bc33f; flex-shrink:0; }
.perm-no   { color:#e63260; flex-shrink:0; }
.section-divider { font-size:0.75rem; color:#aab0bb; font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin:8px 0 6px; }
.perm-count-badge { font-size:0.72rem; padding:2px 10px; border-radius:20px; }

/* Permission matrix table */
.matrix-table { width:100%; border-collapse:collapse; font-size:0.8rem; }
.matrix-table th { padding:8px 10px; text-align:center; background:#f4f6f8; font-weight:600; font-size:0.75rem; border:1px solid #e9ecef; }
.matrix-table th.role-col { text-align:left; min-width:160px; }
.matrix-table td { padding:7px 10px; text-align:center; border:1px solid #f0f2f5; }
.matrix-table td.role-col { text-align:left; font-weight:600; }
.matrix-check { color:#0bc33f; font-size:1rem; }
.matrix-cross { color:#d7dde1; font-size:0.9rem; }
.wf-step-badge { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px;
    border-radius:50%; font-size:0.7rem; font-weight:700; background:#018c87; color:#fff; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="mb-0" style="font-size:1.3rem">Rol va Huquqlar</h2>
        <div style="font-size:0.82rem;color:#6e788b">Tizim rollari, ruxsatlar va xavfsizlik siyosati</div>
    </div>
    <a href="{{ route('admin.users') }}" class="platon-btn platon-btn-outline platon-btn-sm">
        ← Foydalanuvchilar
    </a>
</div>

{{-- Stats bar --}}
<div class="stat-cards-row" style="margin-bottom:24px">
    @foreach($allRoles as $rKey => $rLabel)
    @php $cnt = $roleCounts[$rKey] ?? 0; @endphp
    <div class="stat-card" style="min-width:120px">
        <div class="stat-card-value">{{ $cnt }}</div>
        <div class="stat-card-label">{{ $rLabel }}</div>
    </div>
    @endforeach
</div>

{{-- Workflow step order explanation --}}
<div class="block" style="padding:22px;margin-bottom:24px">
    <h5 style="font-weight:700;margin-bottom:16px">Ariza ko'rib chiqish bosqichlari (tartib)</h5>
    <div style="display:flex;align-items:center;gap:0;flex-wrap:wrap">
        @php
        $wfSteps = [
            ['step'=>1,'role'=>'moderator',        'label'=>'Moderator',           'color'=>'#1471f0'],
            ['step'=>2,'role'=>'complaint_officer', 'label'=>'Shikoyat mutaxassisi','color'=>'#0eba94'],
            ['step'=>3,'role'=>'lawyer',            'label'=>'Yurist',              'color'=>'#673cc8'],
            ['step'=>4,'role'=>'executor',          'label'=>'Ijrochi',             'color'=>'#a05a00'],
            ['step'=>5,'role'=>'district_head',     'label'=>"Tuman boshlig'i",     'color'=>'#018c87'],
        ];
        @endphp
        @foreach($wfSteps as $i => $s)
        <div style="display:flex;align-items:center">
            <div style="text-align:center;padding:10px 16px;background:rgba(1,140,135,.05);border-radius:10px;border:1px solid #e0e5ea">
                <div class="wf-step-badge" style="margin:0 auto 6px;background:{{ $s['color'] }}">{{ $s['step'] }}</div>
                <div style="font-size:0.78rem;font-weight:600;color:{{ $s['color'] }}">{{ $s['label'] }}</div>
                @php $cnt = $roleCounts[$s['role']] ?? 0; @endphp
                <div style="font-size:0.7rem;color:#aab0bb;margin-top:2px">{{ $cnt }} ta xodim</div>
            </div>
            @if(!$loop->last)
            <div style="padding:0 6px;color:#d7dde1;font-size:1.2rem">→</div>
            @endif
        </div>
        @endforeach
        <div style="display:flex;align-items:center">
            <div style="padding:0 6px;color:#d7dde1;font-size:1.2rem">→</div>
            <div style="text-align:center;padding:10px 16px;background:rgba(6,184,56,.05);border-radius:10px;border:1px solid #0bc33f">
                <div style="font-size:1rem;margin-bottom:4px">✓</div>
                <div style="font-size:0.78rem;font-weight:600;color:#0bc33f">Tasdiqlandi</div>
                <div style="font-size:0.7rem;color:#aab0bb;margin-top:2px">Ariza yopiladi</div>
            </div>
        </div>
    </div>
    <div style="margin-top:14px;padding:12px 14px;background:#fff3cd;border-radius:8px;font-size:0.82rem;color:#856404">
        <strong>💡 Mintaqaviy zaxira (Regional backup):</strong>
        Agar biror xodim kasal bo'lsa yoki dam olishda bo'lsa, xuddi shu rolda va <code>is_regional_backup = true</code>
        bo'lgan boshqa xodim uning o'rniga tasdiqlashi mumkin — tuman chegarasidan qat'i nazar.
        Admin "Qayta tayinlash" tugmasi orqali bosqich mas'ulini o'zgartira oladi.
    </div>
</div>

{{-- Role cards grid --}}
<div class="row g-3 mb-4">
    @foreach($rolePerms as $rKey => $perm)
    <div class="col-md-6">
        <div class="role-card">
            <div class="role-card-header">
                <div class="role-icon">
                    @if($rKey === 'admin') 🛡️
                    @elseif($rKey === 'moderator') 📋
                    @elseif($rKey === 'complaint_officer') 📝
                    @elseif($rKey === 'lawyer') ⚖️
                    @elseif($rKey === 'executor') 🧮
                    @elseif($rKey === 'district_head') 🏛️
                    @elseif($rKey === 'commission') 🖊️
                    @else 👤
                    @endif
                </div>
                <div>
                    <div class="role-title">{{ $perm['label'] }}</div>
                    <span class="sbadge {{ $perm['color'] }} perm-count-badge">
                        {{ $roleCounts[$rKey] ?? 0 }} ta foydalanuvchi
                    </span>
                </div>
            </div>
            <div class="role-desc">{{ $perm['description'] }}</div>

            <div class="section-divider">✓ Mumkin</div>
            <ul class="perm-list">
                @foreach($perm['can'] as $c)
                <li><span class="perm-can">✓</span> {{ $c }}</li>
                @endforeach
            </ul>

            @if(!empty($perm['cannot']))
            <div class="section-divider mt-2">✗ Mumkin emas</div>
            <ul class="perm-list">
                @foreach($perm['cannot'] as $c)
                <li><span class="perm-no">✗</span> {{ $c }}</li>
                @endforeach
            </ul>
            @endif

            <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f0f2f5">
                <a href="{{ route('admin.users', ['role' => $rKey]) }}"
                    class="platon-btn platon-btn-outline platon-btn-sm">
                    Bu roldagi foydalanuvchilar →
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Permission matrix --}}
<div class="block" style="padding:22px;margin-bottom:24px">
    <h5 style="font-weight:700;margin-bottom:16px">Huquqlar jadvali</h5>
    <div style="overflow-x:auto">
    <table class="matrix-table">
        <thead>
            <tr>
                <th class="role-col">Rol</th>
                <th>Admin panel</th>
                <th>Barcha arizalar</th>
                <th>Foydalanuvchi boshqaruvi</th>
                <th>1-bosqich</th>
                <th>2-bosqich</th>
                <th>3-bosqich</th>
                <th>4-bosqich</th>
                <th>5-bosqich</th>
                <th>Dalolatnoma</th>
                <th>Bildirishnoma olish</th>
            </tr>
        </thead>
        <tbody>
            @php
            $matrix = [
                'admin'             => [1,1,1,0,0,0,0,0,0,1],
                'moderator'         => [0,0,0,1,0,0,0,0,0,1],
                'complaint_officer' => [0,0,0,0,1,0,0,0,0,1],
                'lawyer'            => [0,0,0,0,0,1,0,0,0,1],
                'executor'          => [0,0,0,0,0,0,1,0,0,1],
                'district_head'     => [0,0,0,0,0,0,0,1,0,1],
                'commission'        => [0,0,0,0,0,0,0,0,1,1],
                'consumer'          => [0,0,0,0,0,0,0,0,0,1],
            ];
            @endphp
            @foreach($matrix as $rKey => $perms)
            <tr>
                <td class="role-col">
                    <span class="sbadge {{ $rolePerms[$rKey]['color'] ?? 'sbadge-gray' }}" style="font-size:0.72rem">
                        {{ $allRoles[$rKey] ?? $rKey }}
                    </span>
                </td>
                @foreach($perms as $p)
                <td>
                    @if($p)
                    <span class="matrix-check">✓</span>
                    @else
                    <span class="matrix-cross">—</span>
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

{{-- Security policy --}}
<div class="block" style="padding:22px">
    <h5 style="font-weight:700;margin-bottom:16px">🔒 Xavfsizlik siyosati</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <div style="padding:16px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0">
                <div style="font-weight:700;color:#15803d;margin-bottom:6px">Bir sessiya qoidasi</div>
                <div style="font-size:0.82rem;color:#166534">
                    Bir foydalanuvchi bir vaqtda faqat bitta qurilmadan kirishi mumkin.
                    Yangi login oldingisini avtomatik tugatadi.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="padding:16px;background:#fff7ed;border-radius:10px;border:1px solid #fed7aa">
                <div style="font-weight:700;color:#c2410c;margin-bottom:6px">E-IMZO imzo majburiy</div>
                <div style="font-size:0.82rem;color:#9a3412">
                    Workflow bosqichlari (1–5) va Dalolatnoma imzolash E-IMZO elektron imzosi bilan tasdiqlanadi.
                    Imzosiz tasdiqlash taqiqlangan.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="padding:16px;background:#eff6ff;border-radius:10px;border:1px solid #bfdbfe">
                <div style="font-weight:700;color:#1d4ed8;margin-bottom:6px">Sessiya kuzatuvi</div>
                <div style="font-size:0.82rem;color:#1e40af">
                    Har bir login vaqti, IP manzili, brauzer va autentifikatsiya usuli qayd etiladi.
                    Admin istalgan vaqt sessiyani tugatishi mumkin.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="padding:16px;background:#fdf4ff;border-radius:10px;border:1px solid #e9d5ff">
                <div style="font-weight:700;color:#7e22ce;margin-bottom:6px">Mintaqaviy zaxira</div>
                <div style="font-size:0.82rem;color:#6b21a8">
                    <code>is_regional_backup = true</code> bo'lgan xodim barcha tumanlar arizalarini
                    ko'ra va tasdiqlaya oladi. Biror xodim mavjud bo'lmasa, zaxira xodim uning o'rniga ishlaydi.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="padding:16px;background:#fff1f2;border-radius:10px;border:1px solid #fecdd3">
                <div style="font-weight:700;color:#be123c;margin-bottom:6px">Qayta tayinlash (Reassign)</div>
                <div style="font-size:0.82rem;color:#9f1239">
                    Admin kutilayotgan bosqich mas'ulini boshqa xodimga o'tkaza oladi.
                    Yangi xodim bildirishnoma oladi, eski imzolar saqlanib qoladi.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div style="padding:16px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0">
                <div style="font-weight:700;color:#15803d;margin-bottom:6px">Bildirishnoma tizimi</div>
                <div style="font-size:0.82rem;color:#166534">
                    Ariza yuborilganda, tasdiqlanganda, rad etilganda va yakunlanganda avtomatik bildirishnoma
                    yuboriladi. Admin istalgan foydalanuvchiga qo'lda xabar yubora oladi.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
