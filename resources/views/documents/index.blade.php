@extends('layouts.app')

@section('title', 'Hujjatlar')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Mening hujjatlarim</h5>
        <a href="{{ route('documents.create') }}" class="btn btn-primary btn-sm">
            + Yangi hujjat
        </a>
    </div>
    <div class="card-body">
        @if($documents->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sarlavha</th>
                        <th>Status</th>
                        <th>Imzolangan</th>
                        <th>Yaratilgan</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $document)
                    <tr>
                        <td>{{ $document->id }}</td>
                        <td>{{ $document->title }}</td>
                        <td>
                            @if($document->isSigned())
                                <span class="badge bg-success">Imzolangan</span>
                            @else
                                <span class="badge bg-warning text-dark">Imzolanmagan</span>
                            @endif
                        </td>
                        <td>
                            @if($document->signed_at)
                                {{ $document->signed_at->format('d.m.Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $document->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-outline-primary">
                                Ko'rish
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $documents->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <p class="text-muted">Hujjatlar topilmadi</p>
            <a href="{{ route('documents.create') }}" class="btn btn-primary">
                Birinchi hujjatni yarating
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
