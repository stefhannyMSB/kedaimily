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
        <div class="mx-4 mb-3 d-flex gap-2">
            <a href="{{ route('menu.create') }}" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i>&nbsp;Tambah Data
            </a>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fa fa-upload"></i>&nbsp;Import Data
            </button>
        </div>

        <div class="card shadow py-2 px-2 mx-3">
            <div class="card-body">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table id="datatables" class="table table-striped table-bordered table-sm w-100">
                        <thead class="table-success text-center">
                            <tr>
                                <th class="text-start">No</th>
                                <th class="text-start">Nama Menu</th>
                                <th class="text-start">Harga</th>
                                <th class="text-start">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menu as $item)
                            <tr>
                                <td class="text-start align-middle">{{ $loop->iteration }}</td>
                                <td class="text-start align-middle">{{ $item->nama_menu }}</td>
                                <td class="text-start align-middle">{{ $item->harga }}</td>
                                <td class="text-start align-middle">
                                    <a href="{{ route('menu.edit', $item->id_menu) }}"
                                        class="btn btn-sm btn-secondary me-1">
                                        Edit
                                    </a>
                                    <form id="delete-form-{{ $item->id_menu }}"
                                        action="{{ route('menu.destroy', $item->id_menu) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('delete')
                                        <button type="button" class="btn btn-sm btn-danger delete-btn"
                                            data-id="{{ $item->id_menu }}">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Data menu Tidak Ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
    $('.delete-btn').on('click', function() {
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
