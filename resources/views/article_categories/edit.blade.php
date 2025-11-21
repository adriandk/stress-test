@extends('layouts.app')

@section('title', 'Edit Kategori Artikel')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Edit Kategori Artikel</h3>
      <p class="text-muted mb-0">Perbarui informasi kategori artikel.</p>
    </div>

    <a href="{{ route('article-categories.index') }}" class="btn btn-outline-secondary btn-sm">
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

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form method="POST" action="{{ route('article-categories.update', $category) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Nama Kategori</label>
          <input type="text"
                 name="name"
                 class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name', $category->name) }}"
                 required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Slug</label>
          <input type="text"
                 name="slug"
                 class="form-control @error('slug') is-invalid @enderror"
                 value="{{ old('slug', $category->slug) }}">
          @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    rows="3">{{ old('description', $category->description) }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-primary">
          Simpan Perubahan
        </button>
      </form>
    </div>
  </div>
@endsection
