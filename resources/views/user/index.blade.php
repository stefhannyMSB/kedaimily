{{-- resources/views/user/menu/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Daftar Menu (Read-only)</h3>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-white">
                    <tr>
                        <th>#</th>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $m)
                    <tr>
                        <td>{{ $m->id_menu }}</td>
                        <td>{{ $m->nama_menu }}</td>
                        <td>{{ isset($m->harga) ? 'Rp '.number_format($m->harga,0,',','.') : '-' }}</td>
                        <td>{{ $m->kategori ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $menus->links() }}</div>
    </div>
</div>
@endsection
