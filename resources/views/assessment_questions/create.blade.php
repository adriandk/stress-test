@extends('layouts.app')

@section('title', 'Tambah Pertanyaan Asesmen')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Tambah Pertanyaan Asesmen</h3>
      <p class="text-muted mb-0">
        Pertanyaan ini akan ditampilkan kepada mahasiswa dalam asesmen mandiri.
      </p>
    </div>

    <a href="{{ route('assessment-questions.index') }}" class="btn btn-sm btn-outline-secondary">
      &larr; Kembali
    </a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('assessment-questions.store') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Teks Pertanyaan</label>
          <textarea name="question_text"
                    rows="3"
                    class="form-control @error('question_text') is-invalid @enderror"
                    required>{{ old('question_text') }}</textarea>
          @error('question_text')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Kategori (opsional)</label>
          <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">— Tanpa Kategori —</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}"
                      {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
          @error('category_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Urutan (opsional)</label>
            <input type="number"
                   name="sort_order"
                   class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', 0) }}">
            @error('sort_order')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6 d-flex align-items-center">
            <div class="form-check mt-4">
              <input class="form-check-input"
                     type="checkbox"
                     name="is_active"
                     id="is_active"
                     value="1"
                     {{ old('is_active', true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">
                Pertanyaan aktif
              </label>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary">
          Simpan Pertanyaan
        </button>
      </form>
    </div>
  </div>
@endsection
