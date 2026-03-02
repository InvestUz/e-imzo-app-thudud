@extends('layouts.app')

@section('title', 'Yangi hujjat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Yangi hujjat yaratish</h5>
            </div>
            <div class="card-body">
                <div id="eimzo-message"></div>

                <form id="document-form">
                    <div class="mb-3">
                        <label for="title" class="form-label">Sarlavha</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Matn</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Saqlash
                        </button>
                        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                            Bekor qilish
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('document-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var title = document.getElementById('title').value;
    var content = document.getElementById('content').value;

    fetch('{{ route('documents.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ title: title, content: content })
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 1) {
            window.location.href = result.redirect;
        } else {
            var el = document.getElementById('eimzo-message');
            el.innerHTML = '<div class="alert alert-danger">' + (result.message || 'Xatolik yuz berdi') + '</div>';
        }
    })
    .catch(err => {
        var el = document.getElementById('eimzo-message');
        el.innerHTML = '<div class="alert alert-danger">Server xatosi: ' + err.message + '</div>';
    });
});
</script>
@endpush
@endsection
