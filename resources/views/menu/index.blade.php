@extends('layouts.app')

@section('content')
<div class="row mx-2 my-1">
    <h2 class="text-dark">Data Menu</h2>
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
@if (Session::has('deleted'))
<script>
Swal.fire({
    title: 'Dihapus!',
    text: '{{ Session::get("deleted") }}',
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
          {{-- Tambah Data --}}
          <a href="{{ route('menu.create') }}" class="btn btn-success btn-sm d-flex align-items-center action-btn">
            <i class="bi bi-plus-circle me-1"></i> Tambah Data
          </a>

          {{-- Import Data --}}
          <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center action-btn"
                  data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-1"></i> Import Data
          </button>
        </div>
      </div>
    </div>

    {{-- =======================
         TABEL DATA MENU
    ======================== --}}
    <div class="card border-0 shadow-sm rounded-4 mx-3 mb-4">
      <div class="card-body">
        @if($menu->isEmpty())
          <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            Belum ada data menu yang ditampilkan.
          </div>
        @else
          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table id="datatables" class="table table-hover table-bordered table-sm align-middle mb-0 w-100">
              <thead class="table-success text-center" style="position: sticky; top: 0; z-index: 1020;">
                <tr>
                  <th class="text-start" style="width:80px;">No</th>
                  <th class="text-start">Nama Menu</th>
                  <th class="text-end" style="width:180px;">Harga</th>
                  <th class="text-center" style="width:140px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($menu as $item)
                  <tr>
                    <td class="text-start">{{ $loop->iteration }}</td>
                    <td class="text-start text-truncate" style="max-width: 320px;">{{ $item->nama_menu }}</td>
                    <td class="text-end">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('menu.edit', $item->id_menu) }}" class="btn btn-secondary btn-sm">
                          Edit
                        </a>
                        <form id="delete-form-{{ $item->id_menu }}"
                              action="{{ route('menu.destroy', $item->id_menu) }}"
                              method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="button"
                                  class="btn btn-danger btn-sm delete-btn"
                                  data-id="{{ $item->id_menu }}">
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
    {{-- =======================
        TOMBOL LOGOUT
    ======================== --}}
    <div class="text-center mt-5">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <!-- <button type="submit" class="btn btn-outline-danger px-4 py-2 fw-semibold rounded-3">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button> -->
        </form>
    </div>
</div>
<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('menu.import') }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="importModalLabel">Import Data menu</h5>
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

@push('styles')
<style>
thead th {
    position: sticky;
    top: 0;
    background-color: #343a40;
    color: white;
    z-index: 1020;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#datatables').DataTable({
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
