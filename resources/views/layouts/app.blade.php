<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-IMZO Laravel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
        .card { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border: none; }
        .btn-primary { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border: none; }
        .btn-primary:hover { background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%); }
        .eimzo-card { max-width: 500px; margin: 2rem auto; }
        .key-select { font-size: 1rem; padding: 0.75rem; }
        .signed-badge { background-color: #198754; }
        .unsigned-badge { background-color: #dc3545; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <strong>E-IMZO Laravel</strong>
            </a>
            @auth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Bosh sahifa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('documents.index') }}">Hujjatlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('documents.create') }}">Yangi hujjat</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted">{{ auth()->user()->pinfl }}</span></li>
                            @if(auth()->user()->organization)
                            <li><span class="dropdown-item-text text-muted">{{ auth()->user()->organization }}</span></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Chiqish</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <footer class="mt-5 py-4 bg-light">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} E-IMZO Laravel Demo</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/e-imzo.js') }}"></script>
    <script src="{{ asset('js/e-imzo-client.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
