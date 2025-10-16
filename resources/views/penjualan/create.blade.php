@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">TAMBAH DATA PENJUALAN</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('penjualan.store') }}" method="POST">
                    @csrf
                    <div class="row mx-2 my-2">

                        {{-- Tanggal --}}
                        <div class="table mb-3">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal"
                                class="form-control @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal') }}">
                            @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Barang --}}
                        <div class="table mb-3">
                            <label for="id_menu">Nama Barang</label>
                            <select class="form-control @error('id_menu') is-invalid @enderror" name="id_menu"
                                id="id_menu">
                                <option disabled selected value="">Pilih Menu</option>
                                @foreach ($menu as $item)
                                <option value="{{ $item->id_menu }}" data-harga="{{ $item->harga }}"
                                    {{ old('id_menu') == $item->id_menu ? 'selected' : '' }}>
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
                                class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}"
                                placeholder="Masukkan jumlah">
                            @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Total --}}
                        <div class="table mb-3">
                            <label for="total">Total</label>
                            <p id="totalDisplay" class="form-control-static">Rp 0</p>
                            <input type="hidden" name="total" id="totalInput" value="{{ old('total', 0) }}">
                        </div>

                    </div>

                    <div class="table">
                        <button type="submit" class="btn btn-success" name="save">Simpan</button>
                        <a href="{{ route('penjualan.index') }}" class="btn btn-success">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi untuk menghitung total
function calculateTotal() {
    var jumlah = document.getElementById('jumlah').value;
    var menuHarga = null;
    var totalDisplay = document.getElementById('totalDisplay');
    var totalInput = document.getElementById('totalInput');

    var selectedOption = document.querySelector("#id_menu option:checked");
    if (selectedOption) {
        menuHarga = selectedOption.getAttribute('data-harga');
    }

    if (!isNaN(jumlah) && jumlah != '' && menuHarga !== null) {
        var total = jumlah * menuHarga;
        totalDisplay.textContent = "Rp " + total.toLocaleString();
        totalInput.value = total;
    } else {
        totalDisplay.textContent = "Rp 0";
        totalInput.value = 0;
    }
}

// Event listener untuk input jumlah dan perubahan menu
document.getElementById('jumlah').addEventListener('input', calculateTotal);
document.getElementById('id_menu').addEventListener('change', calculateTotal);

// Panggil sekali saat load untuk inisialisasi total
window.onload = calculateTotal;
</script>
@endsection