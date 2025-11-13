{{-- resources/views/user/menu/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Daftar Menu</h3>

    {{-- =======================
         TABEL MENU (STYLE SEPERTI PENJUALAN)
    ======================== --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            @if(collect($menus)->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Belum ada data.
                </div>
            @else
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover table-bordered table-sm align-middle mb-0 w-100">
                        <thead class="table-success" style="position: sticky; top: 0; z-index: 1020;">
                            <tr class="text-center">
                                <th class="text-start" style="width:80px;">No</th>
                                <th class="text-start">Nama Menu</th>
                                <th class="text-end" style="width:180px;">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $item)
                                <tr>
                                    <td class="text-start">{{ $loop->iteration }}</td>
                                    <td class="text-start text-truncate" style="max-width: 360px;">{{ $item->nama_menu }}</td>
                                    <td class="text-end">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Pagination (jika pakai paginator) --}}
        @if(method_exists($menus, 'links'))
            <div class="card-footer bg-white">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</div>

{{-- STYLE TAMBAHAN (selaras dengan halaman penjualan) --}}
<style>
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,.025); }
</style>

@endsection
