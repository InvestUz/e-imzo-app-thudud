@extends('layouts.app')

@section('title', $document->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $document->title }}</h5>
                @if($document->isSigned())
                    <span class="badge bg-success">Imzolangan</span>
                @else
                    <span class="badge bg-warning text-dark">Imzolanmagan</span>
                @endif
            </div>
            <div class="card-body">
                <div id="eimzo-status" class="mb-2"></div>
                <div id="eimzo-message"></div>
                <div id="eimzo-progress"></div>

                <div class="mb-4">
                    <h6>Hujjat matni:</h6>
                    <div class="bg-light p-3 rounded" style="white-space: pre-wrap;">{{ $document->content }}</div>
                </div>

                @if(!$document->isSigned())
                <hr>
                <h6>Hujjatni imzolash:</h6>
                <div class="mb-3">
                    <label for="eimzo-keys" class="form-label">ERI kalitini tanlang:</label>
                    <select id="eimzo-keys" class="form-select">
                        <option value="">-- Yuklanmoqda... --</option>
                    </select>
                </div>
                <button type="button" class="btn btn-success" onclick="signDocument({{ $document->id }})">
                    Imzolash
                </button>
                @endif

                @if($document->isSigned())
                <hr>
                <h6>Imzo ma'lumotlari:</h6>
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">Imzolovchi:</td>
                        <td>{{ $document->signer_name }}</td>
                    </tr>
                    @if($document->signer_pinfl)
                    <tr>
                        <td class="text-muted">PINFL:</td>
                        <td>{{ $document->signer_pinfl }}</td>
                    </tr>
                    @endif
                    @if($document->signer_organization)
                    <tr>
                        <td class="text-muted">Tashkilot:</td>
                        <td>{{ $document->signer_organization }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Imzolangan sana:</td>
                        <td>{{ $document->signed_at->format('d.m.Y H:i:s') }}</td>
                    </tr>
                </table>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                    Orqaga
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">QR kod</h6>
            </div>
            <div class="card-body text-center">
                <div id="qrcode" class="mb-3"></div>
                <p class="small text-muted">
                    Bu QR kodni skanerlash orqali hujjatni tekshirish mumkin
                </p>
                <a href="{{ route('documents.verify', $document->qr_code) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                    Tekshirish sahifasi
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Hujjat ma'lumotlari</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">ID:</td>
                        <td>{{ $document->id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">QR kod:</td>
                        <td class="small">{{ $document->qr_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Yaratilgan:</td>
                        <td>{{ $document->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
#qrcode {
    display: inline-block;
}
#qrcode img {
    max-width: 200px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var qr = qrcode(0, 'M');
    qr.addData('{{ $document->getVerificationUrl() }}');
    qr.make();
    document.getElementById('qrcode').innerHTML = qr.createImgTag(5);
});
</script>
@endpush
@endsection
