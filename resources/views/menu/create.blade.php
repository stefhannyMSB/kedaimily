@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">TAMBAH DATA MENU</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <form action="{{ route('menu.store') }}" method="POST">
                        @csrf
                        <div class="row mx-2 my-2">
                            <div class="table">
                                <label for="nama_menu">Nama Menu</label>
                                <input type="text" name="nama_menu" class="form-control" placeholder=""
                                    aria-label="First" value="{{ old('nama_menu') }}">
                                @error('nama_menu')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="table">
                                <label for="harga">Harga</label>
                                <input type="text" name="harga" class="form-control" placeholder="" aria-label="First"
                                    value="{{ old('harga') }}">
                                @error('harga')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="table">
                                <button type="submit" class="btn btn-success" name="save">simpan</button>
                                <a href="{{ route('menu.index') }}" class="btn btn-success">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection