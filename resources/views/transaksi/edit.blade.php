@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">UBAH DATA TRANSAKSI</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <form action="{{ route('transaksi.update', $transaksi->id_transaksi) }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="row mx-2 my-2">
                            {{-- Tanggal --}}
                            <div class="table mb-3">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" name="tanggal"
                                    class="form-control @error('tanggal') is-invalid @enderror"
                                    value="{{ old('tanggal', $transaksi->tanggal) }}">
                                @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nama Barang --}}
                            <div class="table mb-3">
                                <label for="id_menu">Nama Barang</label>
                                <select class="form-control @error('id_menu') is-invalid @enderror" name="id_menu"
                                    id="id_menu">
                                    <option disabled {{ old('id_menu', $transaksi->id_menu) ? '' : 'selected' }}>
                                        Pilih menu</option>
                                    @foreach ($menu as $item)
                                    <option value="{{ $item->id_menu }}" data-harga="{{ $item->harga }}"
                                        {{ old('id_menu', $transaksi->id_menu) == $item->id_menu ? 'selected' : '' }}>
                                        {{ $item->nama_menu }} (Rp {{ number_format($item->harga, 2) }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('id_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jumlah --}}
                            <div class="table mb-3">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" name="jumlah"
                                    class="form-control @error('jumlah') is-invalid @enderror"
                                    value="{{ old('jumlah', $transaksi->jumlah) }}" placeholder="Masukkan jumlah">
                                @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Total --}}
                            <div class="table">
                                <label for="total">Total</label>
                                <p id="totalDisplay" class="form-control-static">Rp
                                    {{ number_format($transaksi->total, 2) }}</p>
                                <input type="hidden" name="total" id="totalInput"
                                    value="{{ old('total', $transaksi->total) }}">
                            </div>
                        </div>
                        <div class="table">
                            <button type="submit" class="btn btn-dark" name="save">simpan</button>
                            <a href="{{ route('transaksi.index') }}" class="btn btn-dark">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk menghitung total
function calculateTotal() {
    var jumlah = document.getElementById('jumlah').value; // Mengambil jumlah
    var idmenu = document.getElementById('id_menu').value; // Mengambil ID menu yang dipilih
    var totalDisplay = document.getElementById('totalDisplay'); // Menampilkan total
    var totalInput = document.getElementById('totalInput'); // Menyimpan total yang dihitung

    // Mengambil harga menu dari data atribut
    var menuHarga = document.querySelector("#id_menu option:checked").getAttribute('data-harga');

    if (!isNaN(jumlah) && jumlah != '') {
        var total = jumlah * menuHarga; // Menghitung total berdasarkan jumlah dan harga menu
        totalDisplay.textContent = "Rp " + total.toLocaleString(); // Menampilkan total dalam format Rupiah
        totalInput.value = total; // Menyimpan total di input hidden
    } else {
        totalDisplay.textContent = "Rp 0"; // Jika jumlah tidak valid, tampilkan Rp 0
        totalInput.value = 0; // Set total menjadi 0
    }
}

// Memanggil fungsi calculateTotal ketika input jumlah atau menu berubah
document.getElementById('jumlah').addEventListener('input', calculateTotal); // Ketika jumlah diubah
document.getElementById('id_menu').addEventListener('change', calculateTotal); // Ketika menu diubah
</script>
@endsection