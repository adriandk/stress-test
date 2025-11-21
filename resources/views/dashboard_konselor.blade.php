@extends('layouts.app')

@section('title', 'Dashboard Konselor')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Dashboard Konselor</h3>
      <p class="text-muted mb-0">Kelola artikel dan pantau asesmen mahasiswa.</p>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title mb-2">Konten Edukatif</h5>
          <p class="text-muted mb-3">
            Tambah, kelola, dan verifikasi artikel yang akan dibaca mahasiswa.
          </p>

          <a href="{{ route('article-categories.index') }}"
             class="btn btn-sm btn-outline-primary me-2">
            Kategori Artikel
          </a>

          <a href="{{ route('articles.index') }}"
             class="btn btn-sm btn-outline-primary me-2">
            Kelola Artikel
          </a>

          <a href="{{ route('articles.public') }}"
             class="btn btn-sm btn-outline-secondary">
            Lihat Halaman Publik
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection
