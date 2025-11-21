@extends('layouts.app')

@section('title', 'Tambah Akun Baru')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Tambah Akun Staff / Konselor</h3>
      <p class="text-muted mb-0">
        Hanya Staff BK yang dapat menambahkan akun baru.
      </p>
    </div>

    <a href="{{ route('dashboard.staff') }}" class="btn btn-outline-secondary btn-sm">
      &larr; Kembali ke Dashboard
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

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
      <form method="POST" action="{{ route('accounts.store') }}">
        @csrf

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nama Lengkap</label>
            <input type="text"
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">No. HP (opsional)</label>
            <input type="text"
                   name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}">
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Role / Jenis Akun</label>
            <select name="account_type"
                    class="form-select @error('account_type') is-invalid @enderror"
                    required>
              <option value="">-- Pilih Role --</option>
              <option value="staff_bk" {{ old('account_type') === 'staff_bk' ? 'selected' : '' }}>
                Staff BK
              </option>
              <option value="konselor" {{ old('account_type') === 'konselor' ? 'selected' : '' }}>
                Konselor
              </option>
            </select>
            @error('account_type')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Password</label>
            <input type="password"
                   name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required>
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control"
                   required>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-primary">
            Simpan Akun
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
