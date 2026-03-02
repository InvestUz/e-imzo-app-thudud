@extends('layouts.app')

@section('title', 'Bosh sahifa')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Xush kelibsiz, {{ auth()->user()->name }}!</h5>
            </div>
            <div class="card-body">
                <p>Siz E-IMZO orqali muvaffaqiyatli kirdingiz.</p>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 class="text-primary">{{ auth()->user()->documents()->count() }}</h3>
                                <p class="text-muted mb-0">Jami hujjatlar</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 class="text-success">{{ auth()->user()->documents()->whereNotNull('signed_at')->count() }}</h3>
                                <p class="text-muted mb-0">Imzolangan hujjatlar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                        Yangi hujjat yaratish
                    </a>
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-primary">
                        Hujjatlarni ko'rish
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Profil ma'lumotlari</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">F.I.O:</td>
                        <td>{{ auth()->user()->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">PINFL:</td>
                        <td>{{ auth()->user()->pinfl }}</td>
                    </tr>
                    @if(auth()->user()->inn)
                    <tr>
                        <td class="text-muted">INN:</td>
                        <td>{{ auth()->user()->inn }}</td>
                    </tr>
                    @endif
                    @if(auth()->user()->organization && auth()->user()->position)
                    <tr>
                        <td class="text-muted">Tashkilot:</td>
                        <td>{{ auth()->user()->organization }}</td>
                    </tr>
                    @endif
                    @if(auth()->user()->position)
                    <tr>
                        <td class="text-muted">Lavozim:</td>
                        <td>{{ auth()->user()->position }}</td>
                    </tr>
                    @endif
                    @if(auth()->user()->certificate_valid_to)
                    <tr>
                        <td class="text-muted">Sertifikat amal qilish muddati:</td>
                        <td>
                            @if(auth()->user()->isCertificateValid())
                                <span class="text-success">{{ auth()->user()->certificate_valid_to->format('d.m.Y') }}</span>
                            @else
                                <span class="text-danger">Muddati tugagan</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
