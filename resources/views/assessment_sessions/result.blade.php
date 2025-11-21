@extends('layouts.app')

@section('title', 'Hasil Asesmen Mandiri')

@section('content')
  @php
    $totalScore  = $summary['total_score'] ?? $session->total_score ?? 0;
    $riskLevel   = $summary['risk_level'] ?? $session->risk_level ?? null;
    $levelLabel  = null;
    $badgeClass  = 'bg-secondary';

    if ($riskLevel) {
        $levelLabel = match ($riskLevel) {
            'low'    => 'Rendah',
            'medium' => 'Sedang',
            'high'   => 'Tinggi',
            default  => ucfirst($riskLevel),
        };

        $badgeClass = match ($riskLevel) {
            'low'    => 'bg-success',
            'medium' => 'bg-warning text-dark',
            'high'   => 'bg-danger',
            default  => 'bg-secondary',
        };
    }
  @endphp

  <div class="mb-4">
    <h3 class="mb-1">Hasil Asesmen Mandiri</h3>
    <p class="text-muted mb-0">
      Terima kasih telah mengisi asesmen. Berikut adalah ringkasan hasil berdasarkan jawabanmu.
    </p>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Ringkasan Umum</h5>

          <p class="mb-2">
            <span class="text-muted">Total Skor:</span><br>
            <span class="fs-4 fw-semibold">{{ $totalScore }}</span>
          </p>

          <p class="mb-3">
            <span class="text-muted">Tingkat Risiko Keseluruhan:</span><br>
            @if($levelLabel)
              <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">
                {{ $levelLabel }}
              </span>
            @else
              <span class="text-muted">Belum dapat ditentukan dari aturan yang ada.</span>
            @endif
          </p>

          @if($rule && $rule->description)
            <p class="mb-0 small text-muted">
              <strong>Interpretasi:</strong><br>
              {{ $rule->description }}
            </p>
          @else
            <p class="mb-0 small text-muted">
              Jika kamu merasa sangat terganggu oleh kondisi yang kamu alami, jangan ragu untuk menghubungi layanan BK atau konselor kampus.
            </p>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Catatan Penting</h5>
          <p class="small text-muted mb-3">
            Asesmen ini hanya memberikan gambaran awal mengenai tingkat stres berdasarkan jawaban mandiri.
            Hasil ini <strong>bukan</strong> diagnosis medis atau psikologis resmi.
          </p>
          <ul class="small text-muted mb-0">
            <li>Gunakan hasil ini sebagai bahan refleksi untuk memahami kondisi diri.</li>
            <li>Jika kamu merasa kesulitan mengelola stres, pertimbangkan untuk berkonsultasi dengan konselor.</li>
            <li>Jaga pola tidur, makan teratur, dan lakukan aktivitas yang menyenangkan untuk membantu mengurangi stres.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  {{-- Rekap per kategori + tingkat risiko kategori --}}
  @if(!empty($categorySummary) && count($categorySummary) > 0)
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-light">
        <strong>Rekap Skor & Risiko per Kategori</strong>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-dark">
              <tr>
                <th style="width:60px;">#</th>
                <th>Kategori</th>
                <th style="width:120px;">Total Skor</th>
                <th style="width:140px;">Jumlah Pertanyaan</th>
                <th style="width:100px;">Rata-rata</th>
                <th style="width:140px;">Tingkat Risiko</th>
              </tr>
            </thead>
            <tbody>
              @foreach($categorySummary as $index => $cat)
                @php
                  $qCount  = $cat['questions'] ?? 0;
                  $cScore  = $cat['total_score'] ?? 0;
                  $avg     = $qCount > 0 ? round($cScore / $qCount, 2) : 0;
                  $catRisk = $cat['risk_level'] ?? null;

                  $catBadgeClass = 'bg-secondary';
                  $catRiskLabel  = null;

                  if ($catRisk) {
                      $catRiskLabel = match ($catRisk) {
                          'low'    => 'Rendah',
                          'medium' => 'Sedang',
                          'high'   => 'Tinggi',
                          default  => ucfirst($catRisk),
                      };

                      $catBadgeClass = match ($catRisk) {
                          'low'    => 'bg-success',
                          'medium' => 'bg-warning text-dark',
                          'high'   => 'bg-danger',
                          default  => 'bg-secondary',
                      };
                  }
                @endphp
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $cat['category_name'] ?? 'Umum' }}</td>
                  <td>{{ $cScore }}</td>
                  <td>{{ $qCount }}</td>
                  <td>{{ $avg }}</td>
                  <td>
                    @if($catRiskLabel)
                      <span class="badge {{ $catBadgeClass }}">{{ $catRiskLabel }}</span>
                    @else
                      <span class="text-muted small">–</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- Interpretasi per Kategori --}}
    @php
      $categoriesWithDescription = collect($categorySummary)->filter(fn($c) => !empty($c['description']));
    @endphp

    @if($categoriesWithDescription->count() > 0)
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
          <strong>Interpretasi per Kategori</strong>
        </div>
        <div class="card-body">
          @foreach($categoriesWithDescription as $cat)
            @php
              $catRisk = $cat['risk_level'] ?? null;
              $catBadgeClass = match ($catRisk) {
                  'low'    => 'bg-success',
                  'medium' => 'bg-warning text-dark',
                  'high'   => 'bg-danger',
                  default  => 'bg-secondary',
              };
              $catRiskLabel = match ($catRisk) {
                  'low'    => 'Rendah',
                  'medium' => 'Sedang',
                  'high'   => 'Tinggi',
                  default  => ucfirst($catRisk ?? '-'),
              };
            @endphp
            <div class="mb-3 pb-3 border-bottom">
              <div class="d-flex align-items-center mb-2">
                <strong class="me-2">{{ $cat['category_name'] ?? 'Umum' }}</strong>
                <span class="badge {{ $catBadgeClass }}">{{ $catRiskLabel }}</span>
              </div>
              <p class="small text-muted mb-0">{{ $cat['description'] }}</p>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Alert untuk kategori high --}}
    @php
      $highCategories = collect($categorySummary)
          ->filter(fn($c) => ($c['risk_level'] ?? null) === 'high')
          ->pluck('category_name')
          ->all();
    @endphp

    @if(!empty($highCategories))
      <div class="alert alert-danger border-0 shadow-sm mb-4">
        <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu perhatian khusus:</strong>
        <br>
        Secara keseluruhan tingkat risiko kamu adalah
        <strong>{{ $levelLabel ?? '—' }}</strong>,
        namun pada kategori berikut tingkat risiko kamu tergolong
        <strong>tinggi</strong>:
        <strong>{{ implode(', ', $highCategories) }}</strong>.
        <br>
        <span class="small">Kami sangat menyarankan untuk berkonsultasi dengan konselor kampus.</span>
      </div>
    @endif
  @endif

  {{-- Kontak Darurat --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <strong>Butuh Bantuan? Hubungi Layanan Resmi</strong>
    </div>
    <div class="card-body">
      @if($contacts->count())
        <p class="small text-muted">
          Jika kamu ingin bercerita atau merasa membutuhkan bantuan lebih lanjut, kamu bisa menghubungi kontak-kontak berikut:
        </p>

        <div class="row g-3">
          @foreach($contacts as $contact)
            <div class="col-md-6 col-lg-4">
              <div class="card border border-dark-subtle h-100">
                <div class="card-body d-flex flex-column">
                  <h6 class="mb-1">{{ $contact->name }}</h6>
                  @if($contact->description)
                    <p class="small text-muted mb-2">{{ $contact->description }}</p>
                  @endif

                  @if($contact->available_days || $contact->available_time_start || $contact->available_time_end)
                    <p class="small mb-2">
                      <strong>Waktu layanan:</strong><br>
                      {{ $contact->available_days ?? '-' }}<br>
                      @if($contact->available_time_start || $contact->available_time_end)
                        Jam:
                        {{ $contact->available_time_start ? \Illuminate\Support\Str::of($contact->available_time_start)->substr(0,5) : '–' }}
                        -
                        {{ $contact->available_time_end ? \Illuminate\Support\Str::of($contact->available_time_end)->substr(0,5) : '–' }}
                      @endif
                    </p>
                  @endif

                  <p class="small text-muted mb-3">
                    WhatsApp: {{ $contact->whatsapp_number }}
                  </p>

                  <a href="https://wa.me/{{ $contact->whatsapp_number }}"
                     target="_blank"
                     class="btn btn-sm btn-outline-dark mt-auto">
                    Hubungi via WhatsApp
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-muted mb-0 small">
          Belum ada kontak darurat yang terdaftar. Silakan hubungi pihak kampus atau BK secara langsung jika diperlukan.
        </p>
      @endif
    </div>
  </div>
@endsection