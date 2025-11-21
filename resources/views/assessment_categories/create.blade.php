@extends('layouts.app')

@section('title', 'Tambah Kategori Asesmen')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Tambah Kategori Asesmen</h3>
      <p class="text-muted mb-0">
        Kategori berguna untuk mengelompokkan pertanyaan, misalnya berdasarkan domain atau topik.
      </p>
    </div>

    <a href="{{ route('assessment-categories.index') }}" class="btn btn-sm btn-outline-secondary">
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

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('assessment-categories.store') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Nama Kategori</label>
          <input type="text"
                 name="name"
                 class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name') }}"
                 required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Deskripsi (opsional)</label>
          <textarea name="description"
                    rows="3"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Urutan (opsional)</label>
          <input type="number"
                 name="sort_order"
                 class="form-control @error('sort_order') is-invalid @enderror"
                 value="{{ old('sort_order', 0) }}">
          @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <div class="form-text">
            Semakin kecil angka, semakin atas posisinya di daftar.
          </div>
        </div>

        <button type="submit" class="btn btn-primary">
          Simpan Kategori
        </button>
      </form>
    </div>
  </div>
@endsection
