@extends('layouts.app')

@section('title', 'Kontak Darurat & Konseling')

@section('content')
  <div class="mb-4">
    <h3 class="mb-1 fw-semibold">Kontak Darurat & Layanan Konseling</h3>
    <p class="text-muted mb-0">
      Jika kamu membutuhkan bantuan segera, silakan hubungi salah satu kontak terpercaya berikut ini.
    </p>
  </div>

  @if($contacts->count())
    <div class="row g-4">

      @foreach($contacts as $contact)
        <div class="col-md-6 col-lg-4">

          <div class="card h-100 rounded-3 shadow-sm"
               style="border: 1px solid #000 !important; background: #f2f2f2;">

            {{-- HEADER --}}
            <div class="px-3 pt-3 pb-2 border-bottom"
                 style="background:#e0e0e0; border-top-left-radius:10px; border-top-right-radius:10px;">
              <h5 class="fw-bold mb-0 text-dark">{{ $contact->name }}</h5>
            </div>

            <div class="card-body d-flex flex-column">

              {{-- DESKRIPSI --}}
              @if($contact->description)
                <p class="text-muted small mb-3">
                  {{ $contact->description }}
                </p>
              @endif

              {{-- KETERSEDIAAN --}}
              @if($contact->available_days || $contact->available_time_start || $contact->available_time_end)
                <div class="mb-3">

                  <div class="fw-semibold small text-secondary mb-1">
                    Ketersediaan
                  </div>

                  {{-- Hari --}}
                  @if($contact->available_days)
                    <div class="d-flex flex-wrap gap-1 mb-2">
                      @foreach(explode(',', $contact->available_days) as $day)
                        <span class="badge rounded-pill"
                              style="background:#d9d9d9; color:#333; border:1px solid #000;">
                          {{ trim($day) }}
                        </span>
                      @endforeach
                    </div>
                  @endif

                  {{-- Jam --}}
                  @if($contact->available_time_start || $contact->available_time_end)
                    <div class="small text-muted">
                      <i class="bi bi-clock me-1"></i>
                      {{ $contact->available_time_start ? substr($contact->available_time_start,0,5) : '–' }}
                      —
                      {{ $contact->available_time_end ? substr($contact->available_time_end,0,5) : '–' }}
                    </div>
                  @endif

                </div>
              @endif

              {{-- NOMOR --}}
              <div class="mb-3">
                <div class="fw-semibold small text-secondary mb-1">Nomor WhatsApp</div>
                <div class="fs-5 fw-bold text-dark">
                  {{ $contact->whatsapp_number }}
                </div>
              </div>

              {{-- TOMBOL --}}
              <a href="https://wa.me/{{ $contact->whatsapp_number }}"
                 target="_blank"
                 class="btn w-100 mt-auto rounded-2 d-flex align-items-center justify-content-center gap-2"
                 style="background:#d0e0d0; border:1px solid #000; color:#1b3a1b;">
                <i class="bi bi-whatsapp"></i>
                Hubungi via WhatsApp
              </a>

            </div>
          </div>

        </div>
      @endforeach

    </div>
  @else
    <div class="alert alert-info">
      Belum ada kontak darurat yang tersedia saat ini.
    </div>
  @endif
@endsection
