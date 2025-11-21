@extends('layouts.app')

@section('title', 'Tambah Artikel')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Tambah Artikel</h3>
      <p class="text-muted mb-0">Tulis artikel edukatif untuk mahasiswa.</p>
    </div>

    <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary btn-sm">
      &larr; Kembali
    </a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('articles.store') }}">
    @csrf

    <div class="mb-3">
      <label class="form-label">Judul</label>
      <input type="text"
             name="title"
             class="form-control @error('title') is-invalid @enderror"
             value="{{ old('title') }}"
             required>
      @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Kategori</label>
        <select name="category_id"
                class="form-select @error('category_id') is-invalid @enderror"
                required>
          <option value="">-- Pilih Kategori --</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
        @error('category_id')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Thumbnail URL (opsional)</label>
        <input type="url"
               name="thumbnail_url"
               class="form-control @error('thumbnail_url') is-invalid @enderror"
               value="{{ old('thumbnail_url') }}"
               placeholder="https://...">
        @error('thumbnail_url')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="mb-3 mt-3">
      <label class="form-label">Status Awal</label>
      <select name="status"
              class="form-select @error('status') is-invalid @enderror"
              required>
        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Ajukan Verifikasi</option>
      </select>
      @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Konten</label>
      <textarea name="content"
                rows="10"
                class="form-control @error('content') is-invalid @enderror"
                required>{{ old('content') }}</textarea>
      @error('content')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary">
      Simpan Artikel
    </button>
  </form>
@endsection
