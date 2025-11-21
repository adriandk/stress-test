@extends('layouts.app')

@section('title', 'Artikel Edukatif')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Artikel Edukatif</h3>
      <p class="text-muted mb-0">
        Kumpulan artikel yang telah diverifikasi untuk mendukung kesehatan mental mahasiswa.
      </p>
    </div>
  </div>

  @if($articles->count())
    <div class="row g-3">
      @foreach($articles as $article)
        <div class="col-md-4">
          <div class="card h-100 shadow-sm border-0">
            @if($article->thumbnail_url)
              <img src="{{ $article->thumbnail_url }}" class="card-img-top" alt="Thumbnail">
            @endif
            <div class="card-body d-flex flex-column">
              <h6 class="card-title mb-1">{{ $article->title }}</h6>
              <p class="text-muted small mb-2">
                {{ $article->category->name ?? 'Tanpa Kategori' }} •
                {{ $article->published_at?->format('d M Y') }}
              </p>
              <p class="card-text small flex-grow-1">
                {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 120) }}
              </p>
              <a href="{{ route('articles.show', $article->slug) }}" class="btn btn-sm btn-primary mt-2">
                Baca Selengkapnya
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $articles->links() }}
    </div>
  @else
    <div class="alert alert-info mb-0">
      Belum ada artikel yang dipublikasikan.
    </div>
  @endif
@endsection
