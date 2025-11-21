@extends('layouts.app')

@section('title', 'Kategori Artikel')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Kategori Artikel</h3>
      <p class="text-muted mb-0">Daftar kategori untuk artikel edukatif.</p>
    </div>

    @if(auth()->user()->account_type === 'konselor')
      <a href="{{ route('article-categories.create') }}" class="btn btn-primary btn-sm">
        + Tambah Kategori
      </a>
    @endif
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if($categories->count())
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th style="width: 60px;">#</th>
                <th>Nama</th>
                <th>Slug</th>
                <th>Deskripsi</th>
                <th style="width: 190px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($categories as $index => $category)
                <tr>
                  <td>{{ $categories->firstItem() + $index }}</td>
                  <td>
                    <a href="{{ route('article-categories.show', $category) }}">
                      {{ $category->name }}
                    </a>
                  </td>
                  <td>{{ $category->slug }}</td>
                  <td>{{ $category->description ?? '-' }}</td>
                  <td>
                    <a href="{{ route('article-categories.show', $category) }}"
                       class="btn btn-sm btn-outline-secondary">
                      Detail
                    </a>

                    @if(auth()->user()->account_type === 'konselor')
                      <a href="{{ route('article-categories.edit', $category) }}"
                         class="btn btn-sm btn-outline-primary">
                        Edit
                      </a>
                      <form action="{{ route('article-categories.destroy', $category) }}"
                            method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                          Hapus
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="p-3">
          {{ $categories->links() }}
        </div>
      </div>
    </div>
  @else
    <div class="alert alert-info">
      Belum ada kategori artikel.
    </div>
  @endif
@endsection
