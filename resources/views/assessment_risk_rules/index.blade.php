@extends('layouts.app')

@section('title', 'Aturan Risiko Asesmen')

@section('content')
  @php
    $user = auth()->user();
    $role = $user->account_type ?? null;
    $isKonselor = $role === 'konselor';
  @endphp

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Aturan Risiko Asesmen</h3>
      <p class="text-muted mb-0">
        Aturan ini digunakan untuk menentukan tingkat risiko berdasarkan total skor asesmen.
      </p>
    </div>

    @if($isKonselor)
      <a href="{{ route('assessment-risk-rules.create') }}" class="btn btn-sm btn-outline-primary">
        + Tambah Aturan Risiko
      </a>
    @endif
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-light">
      <strong>Ringkasan Aturan Aktif</strong>
    </div>
    <div class="card-body">
      @if($rulesActive->count())
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
            <tr>
              <th>Kategori</th>
              <th>Rentang Skor</th>
              <th>Tingkat Risiko</th>
              <th>Deskripsi</th>
            </tr>
            </thead>
            <tbody>
            @foreach($rulesActive as $rule)
              <tr>
                <td>
                  {{ $rule->category?->name ?? 'Semua Kategori' }}
                </td>
                <td>
                  {{ $rule->min_total_score }} &ndash; {{ $rule->max_total_score }}
                </td>
                <td>
                  @php
                    $badgeClass = match($rule->risk_level) {
                      'low' => 'bg-success',
                      'medium' => 'bg-warning text-dark',
                      'high' => 'bg-danger',
                      default => 'bg-secondary'
                    };
                  @endphp
                  <span class="badge {{ $badgeClass }}">
                    {{ $rule->risk_level_label }}
                  </span>
                </td>
                <td class="small text-muted">
                  {{ $rule->description ?: '—' }}
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      @else
        <p class="text-muted mb-0">
          Belum ada aturan risiko aktif. Tambahkan minimal satu aturan agar hasil asesmen dapat dipetakan ke tingkat risiko.
        </p>
      @endif
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-light">
      <strong>Daftar Lengkap Aturan Risiko</strong>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-dark">
          <tr>
            <th style="width:60px;">#</th>
            <th>Kategori</th>
            <th>Rentang Skor</th>
            <th>Tingkat Risiko</th>
            <th>Aktif</th>
            <th>Deskripsi</th>
            @if($isKonselor)
              <th style="width:160px;">Aksi</th>
            @endif
          </tr>
          </thead>
          <tbody>
          @forelse($rules as $index => $rule)
            <tr>
              <td>{{ $rules->firstItem() + $index }}</td>
              <td>{{ $rule->category?->name ?? 'Semua Kategori' }}</td>
              <td>{{ $rule->min_total_score }} &ndash; {{ $rule->max_total_score }}</td>
              <td>
                @php
                  $badgeClass = match($rule->risk_level) {
                    'low' => 'bg-success',
                    'medium' => 'bg-warning text-dark',
                    'high' => 'bg-danger',
                    default => 'bg-secondary'
                  };
                @endphp
                <span class="badge {{ $badgeClass }}">
                  {{ $rule->risk_level_label }}
                </span>
              </td>
              <td>
                @if($rule->is_active)
                  <span class="badge bg-success">Aktif</span>
                @else
                  <span class="badge bg-secondary">Nonaktif</span>
                @endif
              </td>
              <td class="small text-muted">
                {{ $rule->description ?: '—' }}
              </td>

              @if($isKonselor)
                <td>
                  <a href="{{ route('assessment-risk-rules.edit', $rule) }}"
                     class="btn btn-sm btn-outline-primary me-1 mb-1">
                    Edit
                  </a>

                  <form action="{{ route('assessment-risk-rules.destroy', $rule) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Yakin menghapus aturan risiko ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger mb-1">
                      Hapus
                    </button>
                  </form>
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="{{ $isKonselor ? 7 : 6 }}" class="text-center text-muted py-4">
                Belum ada aturan risiko.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($rules->hasPages())
      <div class="card-footer bg-light border-0">
        {{ $rules->links() }}
      </div>
    @endif
  </div>
@endsection
