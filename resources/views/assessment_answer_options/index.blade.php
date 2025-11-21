@extends('layouts.app')

@section('title', 'Opsi Jawaban Asesmen')

@section('content')
  <div class="mb-3">
    <h3 class="mb-1">Opsi Jawaban</h3>
    <p class="text-muted mb-0">
      Pertanyaan: <strong>{{ \Illuminate\Support\Str::limit($question->question_text, 100) }}</strong>
    </p>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('assessment-questions.index') }}" class="btn btn-sm btn-outline-secondary">
      &larr; Kembali ke Bank Pertanyaan
    </a>

    <a href="{{ route('assessment-options.create', $question) }}" class="btn btn-sm btn-outline-primary">
      + Tambah Opsi Jawaban
    </a>
  </div>

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
            <th>Label Opsi</th>
            <th style="width:120px;">Nilai Skor</th>
            <th style="width:120px;">Urutan</th>
            <th style="width:170px;">Aksi</th>
          </tr>
          </thead>
          <tbody>
          @forelse($options as $index => $opt)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $opt->option_label }}</td>
              <td>{{ $opt->option_value }}</td>
              <td>{{ $opt->sort_order }}</td>
              <td>
                <a href="{{ route('assessment-options.edit', [$question, $opt]) }}"
                   class="btn btn-sm btn-outline-primary me-1">
                  Edit
                </a>

                <form action="{{ route('assessment-options.destroy', [$question, $opt]) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Yakin menghapus opsi ini?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                Belum ada opsi jawaban untuk pertanyaan ini.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
