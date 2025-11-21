@extends('layouts.app')

@section('title', $article->title)

@section('content')
  <div class="mb-3">
    <a href="{{ route('articles.public') }}" class="btn btn-sm btn-outline-secondary">
      &larr; Kembali ke Daftar Artikel
    </a>
  </div>

  @if($article->thumbnail_url)
    <div class="mb-3">
      <img src="{{ $article->thumbnail_url }}" class="img-fluid rounded" alt="Thumbnail">
    </div>
  @endif

  <h2 class="mb-1">{{ $article->title }}</h2>
  <p class="text-muted small mb-3">
    {{ $article->category->name ?? 'Tanpa Kategori' }} •
    {{ $article->published_at?->format('d M Y, H:i') ?? $article->created_at->format('d M Y, H:i') }}
    @if($article->author)
      • Oleh {{ $article->author->email }}
    @endif
  </p>

  <div class="border-top pt-3">
    {!! nl2br(e($article->content)) !!}
  </div>
@endsection
