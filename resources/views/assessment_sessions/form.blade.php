@extends('layouts.app')

@section('title', 'Asesmen Mandiri Tingkat Stres')

@section('content')
  <div class="mb-4">
    <h3 class="mb-1">Asesmen Mandiri Tingkat Stres</h3>
    <p class="text-muted mb-0">
      Jawablah semua pertanyaan di bawah ini dengan jujur. Asesmen ini bersifat anonim dan tidak menyimpan identitas pribadi.
    </p>
  </div>

  @if($errors->has('general'))
    <div class="alert alert-danger">
      {{ $errors->first('general') }}
    </div>
  @endif

  @if(! $questions->count())
    <div class="alert alert-info">
      Saat ini belum ada pertanyaan asesmen yang aktif. Silakan hubungi pihak BK.
    </div>
  @else
    <form method="POST" action="{{ route('assessment.submit', $session) }}">
      @csrf

      @php
        $currentCategoryId = null;
      @endphp

      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          @foreach($questions as $index => $q)
            @if($q->category_id !== $currentCategoryId)
              @php
                $currentCategoryId = $q->category_id;
              @endphp

              @if($index > 0)
                <hr class="my-4">
              @endif

              <h5 class="mb-3">
                {{ $q->category?->name ?? 'Umum' }}
              </h5>
            @endif

            <div class="mb-3">
              <div class="fw-semibold">
                {{ $loop->iteration }}.
                {{ $q->question_text }}
              </div>

              @error("answers.{$q->id}")
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror

              <div class="mt-2">
                @forelse($q->answerOptions as $opt)
                  <div class="form-check">
                    <input class="form-check-input"
                           type="radio"
                           name="answers[{{ $q->id }}]"
                           id="q{{ $q->id }}_opt{{ $opt->id }}"
                           value="{{ $opt->id }}"
                           {{ old("answers.{$q->id}") == $opt->id ? 'checked' : '' }}
                           required>
                    <label class="form-check-label" for="q{{ $q->id }}_opt{{ $opt->id }}">
                      {{ $opt->option_label }}
                    </label>
                  </div>
                @empty
                  <p class="text-muted small">
                    Belum ada opsi jawaban untuk pertanyaan ini.
                  </p>
                @endforelse
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0 small">
          Setelah mengirim jawaban, kamu akan melihat ringkasan hasil dan rekomendasi singkat.
        </p>
        <button type="submit" class="btn btn-primary">
          Kirim Jawaban &amp; Lihat Hasil
        </button>
      </div>
    </form>
  @endif
@endsection
