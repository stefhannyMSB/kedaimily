@extends('layouts.app')

@section('content')
<div class="row mx-2 my-1">
    <h2 class="text-dark">DATA TRANSAKSI</h2>
</div>

{{-- SweetAlert success --}}
@if (Session::has('success'))
<script>
Swal.fire({
    title: 'Sukses!',
    text: '{{ Session::get("success") }}',
    icon: 'success',
    confirmButtonText: 'OK'
});
</script>
@endif

<div class="row">
  <div class="col-md">

    {{-- =======================
         ACTION TOOLBAR
    ======================== --}}
    <div class="card border-0 shadow-sm rounded-4 mx-3 mt-3 mb-3">
      <div class="card-body d-flex flex-wrap gap-2 align-items-center">
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('transaksi.create') }}" class="btn btn-success btn-sm d-flex align-items-center action-btn">
            <i class="bi bi-plus-circle me-1"></i> Tambah Data
          </a>

          <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center action-btn"
                  data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-1"></i> Import Data
          </button>

          <a href="{{ route('transaksi.report', ['bulan' => request('bulan'), 'tahun' => request('tahun')]) }}"
             class="btn btn-outline-secondary btn-sm d-flex align-items-center action-btn">
            <i class="bi bi-printer me-1"></i> Cetak Data
          </a>
        </div>
      </div>
    </div>

    {{-- =======================
         FILTER BULAN & TAHUN
    ======================== --}}
    <div class="card border-0 shadow-sm rounded-4 mx-3 mb-3">
      <div class="card-body">
        <form action="{{ route('transaksi.index') }}" method="GET" class="row g-3 align-items-end">
          <div class="col-lg-3 col-md-6">
            <label for="bulan" class="form-label mb-1">Pilih Bulan</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
              <select name="bulan" id="bulan" class="form-select form-select-sm">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                  <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-lg-3 col-md-6">
            <label for="tahun" class="form-label mb-1">Pilih Tahun</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light"><i class="bi bi-calendar2-week"></i></span>
              <select name="tahun" id="tahun" class="form-select form-select-sm">
                <option value="">Semua Tahun</option>
                @foreach($tahunList as $tahun)
                  <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                    {{ $tahun }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-lg-2 col-md-4">
            <button type="submit" class="btn btn-success btn-sm w-100">
              <i class="bi bi-funnel me-1"></i> Filter
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- =======================
         TABEL TRANSAKSI
    ======================== --}}
    <div class="card border-0 shadow-sm rounded-4 mx-3 mb-4">
      <div class="card-body">
        @if($transaksi->isEmpty())
          <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            Belum ada data transaksi yang ditampilkan.
          </div>
        @else
          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table id="example" class="table table-hover table-bordered table-sm align-middle mb-0">
              <thead class="table-success" style="position: sticky; top: 0; z-index: 1020;">
                <tr class="text-center">
                  <th class="text-start">No</th>
                  <th class="text-start">Tanggal</th>
                  <th class="text-start">Nama Menu</th>
                  <th class="text-center">Jumlah</th>
                  <th class="text-end">Total</th>
                  <th class="text-center" style="width:120px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($transaksi as $item)
                  <tr>
                    <td class="text-start">{{ $loop->iteration }}</td>
                    <td class="text-start">
                      {{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}
                    </td>
                    <td class="text-start text-truncate" style="max-width: 220px;">
                      {{ optional($item->menu)->nama_menu ?? '-' }}
                    </td>
                    <td class="text-center">
                      <span class="badge bg-light text-dark border">{{ $item->jumlah }}</span>
                    </td>
                    <td class="text-end">
                      <strong>Rp {{ number_format($item->total, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('transaksi.edit', $item->id_transaksi) }}"
                           class="btn btn-secondary btn-sm">
                          Ubah
                        </a>
                        <form id="delete-form-{{ $item->id_transaksi }}"
                              action="{{ route('transaksi.destroy', $item->id_transaksi) }}"
                              method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="button"
                                  class="btn btn-danger btn-sm delete-btn"
                                  data-id="{{ $item->id_transaksi }}">
                            Hapus
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

  </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('transaksi.import') }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importModalLabel">Import Data Transaksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih file Excel (.xlsx)</label>
                        <input type="file" name="file" id="file" class="form-control" accept=".xlsx" required>
                        <small class="text-muted">Pastikan file dalam format .xlsx</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#example').DataTable({
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                previous: "Sebelumnya",
                next: "Selanjutnya"
            }
        }
    });

    // Konfirmasi sebelum hapus data
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Data ini akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endpush

@endsection
