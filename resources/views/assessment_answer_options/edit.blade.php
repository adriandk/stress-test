@extends('layouts.app')

@section('title', 'Edit Opsi Jawaban')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Edit Opsi Jawaban</h3>
      <p class="text-muted mb-0">
        Pertanyaan: <strong>{{ \Illuminate\Support\Str::limit($question->question_text, 100) }}</strong>
      </p>
    </div>

    <a href="{{ route('assessment-options.index', $question) }}" class="btn btn-sm btn-outline-secondary">
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
      <form method="POST" action="{{ route('assessment-options.update', [$question, $option]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Label Opsi</label>
          <input type="text"
                 name="option_label"
                 class="form-control @error('option_label') is-invalid @enderror"
                 value="{{ old('option_label', $option->option_label) }}"
                 required>
          @error('option_label')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Nilai Skor</label>
            <input type="number"
                   name="option_value"
                   class="form-control @error('option_value') is-invalid @enderror"
                   value="{{ old('option_value', $option->option_value) }}"
                   required>
            @error('option_value')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Urutan (opsional)</label>
            <input type="number"
                   name="sort_order"
                   class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', $option->sort_order) }}">
            @error('sort_order')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <button type="submit" class="btn btn-primary">
          Simpan Perubahan
        </button>
      </form>
    </div>
  </div>
@endsection
