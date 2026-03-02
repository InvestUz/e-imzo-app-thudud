<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hujjat tekshirish - E-IMZO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .verify-card {
            max-width: 700px;
            margin: 2rem auto;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 1rem;
        }
        .verify-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem 1rem 0 0;
        }
        .status-signed {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }
        .status-unsigned {
            background-color: #fff3cd;
            border-color: #ffecb5;
            color: #664d03;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card verify-card">
            <div class="verify-header text-center">
                <h4 class="mb-0">Hujjat tekshirish natijasi</h4>
                <p class="mb-0 mt-1 opacity-75">E-IMZO elektron imzo tizimi</p>
            </div>
            <div class="card-body p-4">
                @if($document->isSigned())
                <div class="alert status-signed d-flex align-items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <div>
                        <strong>Hujjat imzolangan</strong>
                        <br>
                        <small>Bu hujjat elektron raqamli imzo bilan tasdiqlangan</small>
                    </div>
                </div>
                @else
                <div class="alert status-unsigned d-flex align-items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill me-2" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div>
                        <strong>Hujjat imzolanmagan</strong>
                        <br>
                        <small>Bu hujjat hali elektron raqamli imzo bilan tasdiqlanmagan</small>
                    </div>
                </div>
                @endif

                <h5 class="border-bottom pb-2 mb-3">Hujjat ma'lumotlari</h5>
                <table class="table">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Sarlavha:</td>
                        <td><strong>{{ $document->title }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Hujjat ID:</td>
                        <td>{{ $document->qr_code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Yaratilgan sana:</td>
                        <td>{{ $document->created_at->format('d.m.Y H:i:s') }}</td>
                    </tr>
                </table>

                @if($document->isSigned())
                <h5 class="border-bottom pb-2 mb-3 mt-4">Imzo ma'lumotlari</h5>
                <table class="table">
                    <tr>
                        <td class="text-muted" style="width: 40%;">Imzolovchi:</td>
                        <td><strong>{{ $document->signer_name }}</strong></td>
                    </tr>
                    @if($document->signer_pinfl)
                    <tr>
                        <td class="text-muted">PINFL:</td>
                        <td>{{ $document->signer_pinfl }}</td>
                    </tr>
                    @endif
                    @if($document->signer_inn)
                    <tr>
                        <td class="text-muted">INN:</td>
                        <td>{{ $document->signer_inn }}</td>
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

                <h5 class="border-bottom pb-2 mb-3 mt-4">Hujjat matni</h5>
                <div class="bg-light p-3 rounded" style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;">{{ $document->content }}</div>
            </div>
            <div class="card-footer bg-white text-center py-3">
                <small class="text-muted">
                    Tekshirish sanasi: {{ now()->format('d.m.Y H:i:s') }}
                </small>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ url('/') }}" class="btn btn-outline-primary">Bosh sahifaga qaytish</a>
        </div>
    </div>
</body>
</html>
