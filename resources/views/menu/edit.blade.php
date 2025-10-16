@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">UBAH DATA MENU</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <form action="{{ route('menu.update', $menu->id_menu) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row mx-2 my-2">

                            {{-- Nama Barang --}}
                            <div class="table mb-3">
                                <label for="nama_menu">Nama Barang</label>
                                <input type="text" name="nama_menu"
                                    class="form-control @error('nama_menu') is-invalid @enderror"
                                    value="{{ old('nama_menu', $menu->nama_menu) }}"
                                    placeholder="Masukkan nama barang">
                                @error('nama_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Harga --}}
                            <div class="table mb-3">
                                <label for="harga">Harga</label>
                                <input type="number" name="harga"
                                    class="form-control @error('harga') is-invalid @enderror"
                                    value="{{ old('harga', $menu->harga) }}" placeholder="Masukkan harga">
                                @error('harga')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <button type="submit" class="btn btn-dark">Simpan</button>
                        <a href="{{ route('menu.index') }}" class="btn btn-dark">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection