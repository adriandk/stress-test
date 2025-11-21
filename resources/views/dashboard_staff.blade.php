@extends('layouts.app')

@section('title', 'Dashboard Staff BK')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Dashboard Staff BK</h3>
      <p class="text-muted mb-0">Ringkasan tugas dan pengelolaan sistem.</p>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title mb-2">Manajemen Akun</h5>
          <p class="text-muted mb-3">
            Tambahkan akun Staff BK atau Konselor baru untuk mengakses sistem.
          </p>
          <a href="{{ route('accounts.create') }}" class="btn btn-sm btn-primary">
            + Tambah Akun
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title mb-2">Konten Edukatif</h5>
          <p class="text-muted mb-3">
            Kelola kategori dan artikel edukatif yang akan ditampilkan ke mahasiswa.
          </p>
          <a href="{{ route('article-categories.index') }}" class="btn btn-sm btn-outline-primary me-2">
            Kategori Artikel
          </a>
          <a href="{{ route('articles.index') }}" class="btn btn-sm btn-primary">
            Kelola Artikel
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection
