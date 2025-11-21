<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Stress Test BK')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap & Icons CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100 bg-body-secondary">

@php
    $user = auth()->user();
    $role = $user->account_type ?? null;
@endphp

{{-- HEADER (sticky) --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('landing.mahasiswa') }}">
            <i class="bi bi-heart-pulse me-2"></i>
            <span class="fw-semibold">Stress Test BK</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="topNavbar">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center">
                @if($user)
                    <li class="nav-item me-3">
                        <span class="nav-link disabled text-light d-flex flex-column flex-lg-row align-items-lg-center">
                            <i class="bi bi-person-circle me-lg-2 mb-1 mb-lg-0"></i>
                            <span class="small">
                                {{ $user->email }}<br class="d-lg-none">
                                <span class="d-none d-lg-inline">•</span>
                                <span class="text-uppercase">{{ $role }}</span>
                            </span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button class="btn btn-outline-light btn-sm" type="submit">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

{{-- BODY: sidebar + konten, ditarik full tinggi --}}
<div class="container-fluid flex-grow-1 d-flex py-3">
    <div class="row flex-grow-1 w-100">

        {{-- SIDEBAR --}}
        <aside class="col-md-3 col-lg-2 mb-3 mb-md-0 d-flex">
            @if(!$user)
                <div class="card bg-dark text-light border-0 shadow-sm w-100 h-100">
                    <div class="card-body p-3">
                        <div class="fw-semibold text-uppercase small text-secondary mb-2">Umum</div>

                        <a href="{{ route('landing.mahasiswa') }}"
                        class="btn btn-sm w-100 text-start mb-2
                                {{ request()->routeIs('landing.mahasiswa') ? 'btn-primary' : 'btn-outline-light' }}">
                            <i class="bi bi-house-door me-2"></i> Beranda Mahasiswa
                        </a>

                        <a href="{{ route('articles.public') }}"
                        class="btn btn-sm w-100 text-start
                                {{ request()->routeIs('articles.public') || request()->routeIs('articles.show') ? 'btn-primary' : 'btn-outline-light' }}">
                            <i class="bi bi-journal-text me-2"></i> Artikel Edukatif
                        </a>
                    </div>
                </div>

            @elseif($role === 'staff_bk')
                <div class="card bg-dark text-light border-0 shadow-sm w-100 h-100">
                    <div class="card-body p-3 d-flex flex-column">
                        <div>
                            <div class="fw-semibold text-uppercase small text-secondary mb-2">Staff BK</div>

                            <a href="{{ route('dashboard.staff') }}"
                            class="btn btn-sm w-100 text-start mb-2
                                    {{ request()->routeIs('dashboard.staff') ? 'btn-primary' : 'btn-outline-light' }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>

                            <hr class="border-secondary my-3">

                            <div class="fw-semibold text-uppercase small text-secondary mb-2">Konten</div>

                            <a href="{{ route('article-categories.index') }}"
                            class="btn btn-sm w-100 text-start mb-2
                                    {{ request()->routeIs('article-categories.*') ? 'btn-primary' : 'btn-outline-light' }}">
                                <i class="bi bi-tags me-2"></i> Kategori Artikel
                            </a>

                            <a href="{{ route('articles.index') }}"
                            class="btn btn-sm w-100 text-start mb-2
                                    {{ request()->routeIs('articles.index') || request()->routeIs('articles.create') || request()->routeIs('articles.edit') ? 'btn-primary' : 'btn-outline-light' }}">
                                <i class="bi bi-pencil-square me-2"></i> Kelola Artikel
                            </a>

                            <a href="{{ route('articles.public') }}"
                            class="btn btn-sm w-100 text-start
                                    {{ request()->routeIs('articles.public') || request()->routeIs('articles.show') ? 'btn-primary' : 'btn-outline-light' }}">
                                <i class="bi bi-journal-text me-2"></i> Artikel (Publik)
                            </a>
                        </div>
                    </div>
                </div>

            @elseif($role === 'konselor')
                <div class="card bg-dark text-light border-0 shadow-sm w-100 h-100">
                    <div class="card-body p-3 d-flex flex-column">
                        <div>
                            <div class="fw-semibold text-uppercase small text-secondary mb-2">Konselor</div>

                            <a href="{{ route('dashboard.konselor') }}"
                            class="btn btn-sm w-100 text-start mb-2
                                    {{ request()->routeIs('dashboard.konselor') ? 'btn-primary' : 'btn-outline-light' }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>

                            <hr class="border-secondary my-3">

                            <div class="fw-semibold text-uppercase small text-secondary mb-2">Konten</div>

                            <a href="{{ route('article-categories.index') }}"
                            class="btn btn-sm w-100 text-start mb-2
                                    {{ request()->routeIs('article-categories.*') ? 'btn-primary' : 'btn-outline-light' }}">
                                <i class="bi bi-tags me-2"></i> Kategori Artikel
                            </a>

                            <a href="{{ route('articles.index') }}"
                            class="btn btn-sm w-100 text-start mb-2
                                    {{ request()->routeIs('articles.index') || request()->routeIs('articles.create') || request()->routeIs('articles.edit') ? 'btn-primary' : 'btn-outline-light' }}">
                                <i class="bi bi-pencil-square me-2"></i> Kelola Artikel
                            </a>

                            <a href="{{ route('articles.public') }}"
                            class="btn btn-sm w-100 text-start
                                    {{ request()->routeIs('articles.public') || request()->routeIs('articles.show') ? 'btn-primary' : 'btn-outline-light' }}">
                                <i class="bi bi-journal-text me-2"></i> Artikel (Publik)
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="col-md-9 col-lg-10 d-flex">
            <div class="card border-0 shadow-sm w-100 h-100">
                <div class="card-body">
                    @yield('content')
                </div>
            </div>
        </main>

    </div>
</div>

{{-- FOOTER --}}
<footer class="bg-dark text-light py-2 mt-auto">
    <div class="container-fluid d-flex justify-content-between align-items-center small">
        <span>
            <i class="bi bi-c-circle me-1"></i>{{ date('Y') }} Stress Test BK
        </span>
        <span class="d-flex align-items-center">
            <i class="bi bi-heart-fill text-danger me-1"></i>
            <span>Dikembangkan untuk layanan BK & Konseling</span>
        </span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
