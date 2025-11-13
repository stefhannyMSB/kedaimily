@extends('layouts.app')
@section('title', 'Dashboard Pengguna')

@section('content')
<div class="container-lg py-5 mx-auto" style="max-width: 1200px;">
    <h2 class="fw-bold mb-4">SELAMAT DATANG DI KEDAI MILY</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 rounded-4">
                <div class="fw-semibold text-muted">Jumlah Menu</div>
                <div class="fs-2 fw-bold text-success">{{ $jumlahMenu ?? 0 }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 rounded-4">
                <div class="fw-semibold text-muted">Role</div>
                <div class="fs-4 fw-bold text-success">{{ auth()->user()->role ?? 'user' }}</div>
            </div>
        </div>
    </div>

    {{-- =========================
        TENTANG KEDAI MILY (Expandable)
    ========================== --}}
    <div class="card mt-4 border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-start justify-content-between mb-2">
                <h5 class="fw-bold mb-0">Tentang Kedai Mily</h5>
                {{-- Tombol expand/collapse --}}
                <button class="btn btn-sm btn-outline-success" type="button"
                        data-bs-toggle="collapse" data-bs-target="#aboutMore"
                        aria-expanded="false" aria-controls="aboutMore" id="toggleAboutBtn">
                    Lihat selengkapnya
                </button>
            </div>

            {{-- Ringkas (selalu tampil) --}}
            <p class="text-secondary mb-3" style="white-space: normal; overflow: visible; text-overflow: unset; display: block;">
                <p class="text-secondary mb-3">
                    Kedai Mily kuliner di Banyuwangi yang memiliki cita rasa khas, harga terjangkau, serta variasi menu yang menarik mulai dari makanan berat hingga camilan ringan. 
                    Dengan pelayanan yang cepat dan kualitas rasa yang konsisten, Kedai Mily menjadi pilihan banyak konsumen dari berbagai kalangan. 
            </p>

            {{-- Detail (ditampilkan saat di-expand) --}}
            <div class="collapse" id="aboutMore">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <h6 class="fw-semibold mb-2">Layanan</h6>
                            <ul class="mb-0 text-secondary">
                                <li>Dine-in & Take-away.</li>
                                <li>Pemesanan via WA (0812345678).</li>
                                <li>Menerima pesanan arisan, hajatan, dan lainnya</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <h6 class="fw-semibold mb-2">Informasi Umum</h6>
                            <ul class="mb-0 text-secondary">
                                <li>Lokasi: Banyuwangi (Gg.Lombok sukowidi,no 001).</li>
                                <li>Jam operasional: buka setiap hari (06.00-16.00).</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <h6 class="fw-semibold mb-2">Menu Populer</h6>
                            <ul class="mb-0 text-secondary">
                                <li>Nasi ayam geprek / sego sambel ayam suwir.</li>
                                <li>Nasi gembira</li>
                                <li>Camilan: donat mentul, tahu walik, risol, dll.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script kecil untuk toggle label tombol --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('toggleAboutBtn');
    const target = document.getElementById('aboutMore');
    if (!btn || !target) return;

    target.addEventListener('shown.bs.collapse', () => btn.textContent = 'Sembunyikan');
    target.addEventListener('hidden.bs.collapse', () => btn.textContent = 'Lihat selengkapnya');
});
</script>
@endpush
@endsection
