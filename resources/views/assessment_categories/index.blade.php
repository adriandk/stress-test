@extends('layouts.app')

@section('title', 'Kategori Asesmen')

@section('content')
  @php
    $user = auth()->user();
    $role = $user->account_type ?? null;
    $isKonselor = $role === 'konselor';
  @endphp

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Kategori Asesmen</h3>
      <p class="text-muted mb-0">
        Digunakan untuk mengelompokkan pertanyaan asesmen (misal: Stres Akademik, Kesehatan Mental, dll).
      </p>
    </div>

    @if($isKonselor)
      <a href="{{ route('assessment-categories.create') }}" class="btn btn-sm btn-outline-primary">
        + Tambah Kategori
      </a>
    @endif
  </div>

  @if(session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-dark">
          <tr>
            <th style="width: 60px;">#</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th style="width: 120px;">Urutan</th>
            @if($isKonselor)
              <th style="width: 150px;">Aksi</th>
            @endif
          </tr>
          </thead>
          <tbody>
          @forelse($categories as $index => $category)
            <tr>
              <td>{{ $categories->firstItem() + $index }}</td>
              <td class="fw-semibold">{{ $category->name }}</td>
              <td class="text-muted small">
                {{ $category->description ?: '-' }}
              </td>
              <td>{{ $category->sort_order }}</td>

              @if($isKonselor)
                <td>
                  <a href="{{ route('assessment-categories.edit', $category) }}"
                     class="btn btn-sm btn-outline-primary me-1">
                    Edit
                  </a>

                  <form action="{{ route('assessment-categories.destroy', $category) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Yakin menghapus kategori ini? Pertanyaan yang terkait akan kehilangan kategori.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      Hapus
                    </button>
                  </form>
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ $isKonselor ? 5 : 4 }}" class="text-center text-muted py-4">
                Belum ada kategori asesmen.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($categories->hasPages())
      <div class="card-footer bg-light border-0">
        {{ $categories->links() }}
      </div>
    @endif
  </div>
@endsection
