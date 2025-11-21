@extends('layouts.app')

@section('title', 'Detail Kategori Artikel')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Detail Kategori</h3>
      <p class="text-muted mb-0">Informasi lengkap kategori artikel.</p>
    </div>

    <a href="{{ route('article-categories.index') }}" class="btn btn-outline-secondary btn-sm">
      &larr; Kembali
    </a>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h5 class="mb-2">{{ $category->name }}</h5>
      <p class="text-muted mb-1"><strong>Slug:</strong> {{ $category->slug }}</p>
      <p class="mb-3">
        <strong>Deskripsi:</strong><br>
        {{ $category->description ?? '-' }}
      </p>

      @if(auth()->user()->account_type === 'konselor')
        <a href="{{ route('article-categories.edit', $category) }}" class="btn btn-primary btn-sm">
          Edit
        </a>
      @endif
    </div>
  </div>
@endsection
