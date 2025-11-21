@extends('layouts.app')

@section('title', 'Tambah Kontak Darurat')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Tambah Kontak Darurat</h3>
      <p class="text-muted mb-0">
        Kontak ini akan ditampilkan kepada mahasiswa sebagai rujukan layanan bantuan.
      </p>
    </div>

    <a href="{{ route('emergency-contacts.index') }}" class="btn btn-sm btn-outline-secondary">
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

  @php
    $allDays = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
    $selectedDays = old('available_days', []);
    if (is_string($selectedDays)) {
        $selectedDays = array_map('trim', explode(',', $selectedDays));
    }
  @endphp

  <form method="POST" action="{{ route('emergency-contacts.store') }}">
    @csrf

    {{-- Nama --}}
    <div class="mb-3">
      <label class="form-label">Nama Kontak</label>
      <input type="text"
             name="name"
             class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name') }}"
             required>
      @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Deskripsi --}}
    <div class="mb-3">
      <label class="form-label">Deskripsi (opsional)</label>
      <input type="text"
             name="description"
             class="form-control @error('description') is-invalid @enderror"
             value="{{ old('description') }}">
      @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Nomor WhatsApp --}}
    <div class="mb-3">
      <label class="form-label">Nomor WhatsApp</label>
      <input type="text"
             name="whatsapp_number"
             class="form-control @error('whatsapp_number') is-invalid @enderror"
             value="{{ old('whatsapp_number') }}"
             placeholder="Contoh: 08123456789 atau 628123456789"
             required>
      @error('whatsapp_number')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <div class="form-text">
        Tanpa tanda +. Jika dimulai 0 akan otomatis diubah ke format 62.
      </div>
    </div>

    {{-- Hari Tersedia --}}
    <div class="mb-3">
      <label class="form-label d-block">Hari Tersedia (opsional)</label>

      <div class="row">
        @foreach($allDays as $day)
          <div class="col-6 col-md-4 mb-1">
            <div class="form-check">
              <input class="form-check-input"
                     type="checkbox"
                     name="available_days[]"
                     id="day_{{ $day }}"
                     value="{{ $day }}"
                     {{ in_array($day, $selectedDays ?? []) ? 'checked' : '' }}>
              <label class="form-check-label" for="day_{{ $day }}">
                {{ $day }}
              </label>
            </div>
          </div>
        @endforeach
      </div>

      @error('available_days')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
      @error('available_days.*')
        <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
    </div>

    {{-- Jam Tersedia --}}
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Jam Mulai (opsional)</label>
        <input type="time"
               name="available_time_start"
               class="form-control @error('available_time_start') is-invalid @enderror"
               value="{{ old('available_time_start') }}">
        @error('available_time_start')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Jam Selesai (opsional)</label>
        <input type="time"
               name="available_time_end"
               class="form-control @error('available_time_end') is-invalid @enderror"
               value="{{ old('available_time_end') }}">
        @error('available_time_end')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    {{-- Status Aktif --}}
    <div class="form-check mb-3">
      <input class="form-check-input"
             type="checkbox"
             name="is_active"
             id="is_active"
             value="1"
             {{ old('is_active', true) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_active">
        Aktif (tampilkan kepada mahasiswa)
      </label>
    </div>

    <button type="submit" class="btn btn-primary">
      Simpan Kontak
    </button>
  </form>
@endsection
