@extends('layouts.app')

@section('title', 'Kontak Darurat')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-1">Kontak Darurat</h3>

    <a href="{{ route('emergency-contacts.create') }}"
       class="btn btn-sm btn-outline-primary">
      + Tambah Kontak
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Nama</th>
        <th>Hari</th>
        <th>Waktu</th>
        <th>WA</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
      </thead>

      <tbody>
      @foreach($contacts as $i => $c)
        <tr>
          <td>{{ $contacts->firstItem() + $i }}</td>
          <td>{{ $c->name }}</td>
          <td>{{ $c->available_days ?? '-' }}</td>

          <td>
            @if($c->available_time_start || $c->available_time_end)
              {{ substr($c->available_time_start,0,5) }} - {{ substr($c->available_time_end,0,5) }}
            @else
              -
            @endif
          </td>

          <td>{{ $c->whatsapp_number }}</td>

          <td>
            @if($c->is_active)
              <span class="badge bg-success">Aktif</span>
            @else
              <span class="badge bg-secondary">Nonaktif</span>
            @endif
          </td>

          <td>
            <a href="{{ route('emergency-contacts.edit', $c) }}" class="btn btn-sm btn-outline-primary">
              Edit
            </a>

            <form action="{{ route('emergency-contacts.destroy', $c) }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('Yakin hapus?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Hapus</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $contacts->links() }}
  </div>
@endsection
