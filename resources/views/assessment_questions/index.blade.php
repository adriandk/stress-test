@extends('layouts.app')

@section('title', 'Bank Pertanyaan Asesmen')

@section('content')
  @php
    $user = auth()->user();
    $role = $user->account_type ?? null;
  @endphp

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Bank Pertanyaan Asesmen</h3>
      <p class="text-muted mb-0">
        Kelola daftar pertanyaan yang akan digunakan dalam asesmen mandiri mahasiswa.
      </p>
    </div>

    <a href="{{ route('assessment-questions.create') }}" class="btn btn-sm btn-outline-primary">
      + Tambah Pertanyaan
    </a>
  </div>

  {{-- Filter --}}
  <form method="GET" action="{{ route('assessment-questions.index') }}" class="card mb-3 border-0 shadow-sm">
    <div class="card-body row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select">
          <option value="">Semua Kategori</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}"
                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="">Semua</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>

      <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary mt-auto">
          Terapkan
        </button>
        <a href="{{ route('assessment-questions.index') }}" class="btn btn-outline-secondary mt-auto">
          Reset
        </a>
      </div>
    </div>
  </form>

  {{-- List pertanyaan --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-dark">
          <tr>
            <th style="width:60px;">#</th>
            <th>Pertanyaan</th>
            <th style="width:180px;">Kategori</th>
            <th style="width:100px;">Status</th>
            <th style="width:100px;">Opsi</th>
            <th style="width:120px;">Urutan</th>
            <th style="width:210px;">Aksi</th>
          </tr>
          </thead>
          <tbody>
          @forelse($questions as $index => $q)
            <tr>
              <td>{{ $questions->firstItem() + $index }}</td>
              <td>
                <div class="fw-semibold">
                  {{ \Illuminate\Support\Str::limit($q->question_text, 80) }}
                </div>
              </td>
              <td>
                <span class="small text-muted">
                  {{ $q->category?->name ?? '—' }}
                </span>
              </td>
              <td>
                @if($q->is_active)
                  <span class="badge bg-success">Aktif</span>
                @else
                  <span class="badge bg-secondary">Nonaktif</span>
                @endif
              </td>
              <td>
                <span class="badge bg-dark">
                  {{ $q->answer_options_count }} opsi
                </span>
              </td>
              <td>{{ $q->sort_order }}</td>
              <td>
                <a href="{{ route('assessment-options.index', $q) }}"
                   class="btn btn-sm btn-outline-dark me-1 mb-1">
                  Opsi Jawaban
                </a>

                <a href="{{ route('assessment-questions.edit', $q) }}"
                   class="btn btn-sm btn-outline-primary me-1 mb-1">
                  Edit
                </a>

                <form action="{{ route('assessment-questions.destroy', $q) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Yakin menghapus pertanyaan ini? Opsi yang terkait juga akan terhapus.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger mb-1">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                Belum ada pertanyaan asesmen.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($questions->hasPages())
      <div class="card-footer bg-light border-0">
        {{ $questions->links() }}
      </div>
    @endif
  </div>
@endsection
