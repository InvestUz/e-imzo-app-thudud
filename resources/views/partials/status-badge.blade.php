@php
$map = [
    'pending'          => ['sbadge-warning',  'Kutilmoqda'],
    'moderator_review' => ['sbadge-info',     'Moderator'],
    'complaint_review' => ['sbadge-blue',     'Shikoyat bo\'limi'],
    'legal_review'     => ['sbadge-purple',   'Yurist'],
    'executor_review'  => ['sbadge-info',     'Ijrochi'],
    'head_review'      => ['sbadge-blue',     'Rahbar'],
    'approved'         => ['sbadge-success',  'Tasdiqlandi'],
    'rejected'         => ['sbadge-danger',   'Rad etildi'],
];
$cls   = $map[$status][0] ?? 'sbadge-gray';
$label = $map[$status][1] ?? $status;
@endphp
<span class="sbadge {{ $cls }}">{{ $label }}</span>
