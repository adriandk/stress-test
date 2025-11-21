@extends('layouts.app')

@section('title', 'Beranda Mahasiswa')

@section('content')
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h3 class="mb-2">Selamat Datang di Sistem Asesmen Stres Mahasiswa</h3>

          <p class="text-muted mb-4">
            Halaman ini dapat diakses mahasiswa secara anonim.
            Jawaban asesmen tidak akan menampilkan identitas pribadi.
          </p>

          <p class="mb-3">
            Silakan klik tombol di bawah untuk memulai asesmen mandiri atau membaca artikel edukatif.
          </p>

          {{-- TOMBOL RATA KIRI --}}
          <div class="d-flex flex-wrap gap-2">

            {{-- Mulai Asesmen --}}
            <a href="{{ route('assessment.start') }}" class="btn btn-primary">
              Mulai Asesmen
            </a>

            {{-- Artikel Edukatif --}}
            <a href="{{ route('articles.public') }}"
               class="btn btn-outline-primary">
              Lihat Artikel Edukatif
            </a>

            {{-- Login Staff / Konselor --}}
            <a href="{{ route('login') }}"
               class="btn btn-outline-secondary">
              Login Staff / Konselor
            </a>

          </div>

        </div>
      </div>
    </div>
  </div>
@endsection