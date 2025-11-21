@extends('layouts.app')

@section('title', 'Edit Aturan Risiko')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Edit Aturan Risiko</h3>
      <p class="text-muted mb-0">
        Perbarui rentang skor, tingkat risiko, atau deskripsi.
      </p>
    </div>

    <a href="{{ route('assessment-risk-rules.index') }}" class="btn btn-sm btn-outline-secondary">
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
      <form method="POST" action="{{ route('assessment-risk-rules.update', $rule) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label class="form-label">Kategori (opsional)</label>
          <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
            <option value="">Berlaku untuk semua kategori</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}"
                      {{ old('category_id', $rule->category_id) == $cat->id ? 'selected' : '' }}>
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
            <label class="form-label">Skor Minimum</label>
            <input type="number"
                   name="min_total_score"
                   class="form-control @error('min_total_score') is-invalid @enderror"
                   value="{{ old('min_total_score', $rule->min_total_score) }}"
                   required>
            @error('min_total_score')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Skor Maksimum</label>
            <input type="number"
                   name="max_total_score"
                   class="form-control @error('max_total_score') is-invalid @enderror"
                   value="{{ old('max_total_score', $rule->max_total_score) }}"
                   required>
            @error('max_total_score')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Tingkat Risiko</label>
          <select name="risk_level" class="form-select @error('risk_level') is-invalid @enderror" required>
            <option value="">Pilih salah satu</option>
            <option value="low" {{ old('risk_level', $rule->risk_level) === 'low' ? 'selected' : '' }}>Rendah</option>
            <option value="medium" {{ old('risk_level', $rule->risk_level) === 'medium' ? 'selected' : '' }}>Sedang</option>
            <option value="high" {{ old('risk_level', $rule->risk_level) === 'high' ? 'selected' : '' }}>Tinggi</option>
          </select>
          @error('risk_level')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Deskripsi (opsional)</label>
          <textarea name="description"
                    rows="3"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $rule->description) }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input"
                 type="checkbox"
                 name="is_active"
                 id="is_active"
                 value="1"
                 {{ old('is_active', $rule->is_active) ? 'checked' : '' }}>
          <label class="form-check-label" for="is_active">
            Aturan aktif digunakan dalam perhitungan
          </label>
        </div>

        <button type="submit" class="btn btn-primary">
          Simpan Perubahan
        </button>
      </form>
    </div>
  </div>
@endsection
