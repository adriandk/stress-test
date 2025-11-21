@extends('layouts.app')

@section('title', 'Manajemen Artikel')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Manajemen Artikel</h3>
      <p class="text-muted mb-0">Kelola artikel edukatif untuk mahasiswa.</p>
    </div>

    <a href="{{ route('articles.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i> Tambah Artikel
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if($articles->count())
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 60px;">#</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Status</th>
            <th>Dibuat Oleh</th>
            <th>Dipublikasikan</th>
            <th style="width: 220px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($articles as $index => $article)
            <tr>
              <td>{{ $articles->firstItem() + $index }}</td>
              <td>{{ $article->title }}</td>
              <td>{{ $article->category->name ?? '-' }}</td>
              <td>
                <span class="badge 
                  @switch($article->status)
                    @case('draft') bg-secondary @break
                    @case('pending') bg-warning text-dark @break
                    @case('published') bg-success @break
                  @endswitch">
                  {{ strtoupper($article->status) }}
                </span>
              </td>
              <td>{{ $article->author->email ?? '-' }}</td>
              <td>{{ $article->published_at ? $article->published_at->format('d/m/Y H:i') : '-' }}</td>
              <td>
                <a href="{{ route('articles.show', $article->slug) }}"
                   class="btn btn-sm btn-outline-secondary">
                  Lihat
                </a>

                @php $user = auth()->user(); @endphp

                @if($user && ($user->account_type === 'konselor' || $user->id === $article->created_by))
                  <a href="{{ route('articles.edit', $article) }}"
                     class="btn btn-sm btn-outline-primary">
                    Edit
                  </a>
                  <form action="{{ route('articles.destroy', $article) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      Hapus
                    </button>
                  </form>
                @endif

                @if($user && $user->account_type === 'konselor' && $article->status !== 'published')
                  <form action="{{ route('articles.verify', $article) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Verifikasi dan publikasikan artikel ini?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                      Verifikasi
                    </button>
                  </form>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $articles->links() }}
    </div>
  @else
    <div class="alert alert-info mb-0">
      Belum ada artikel.
    </div>
  @endif
@endsection
