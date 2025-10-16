@extends('layouts.app')

@section('content')
<div class="container-lg py-5 mx-auto" style="max-width: 1200px;">

    {{-- ALERT LOGIN SUCCESS --}}
    @if(session('login_success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil 🎉',
                html: `Selamat datang, <strong>{{ session('user_name') }}</strong>!`,
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: {
                    title: 'swal-title',
                    htmlContainer: 'swal-html'
                }
            });
        </script>
        <style>
            .swal-title {
                font-weight: 700;
                font-size: 1.8rem;
                margin-bottom: 0.5em;
            }
            .swal-html {
                color: #444;
                font-size: 1.1rem;
            }
        </style>
    @endif

    {{-- =======================
        SUMMARY DASHBOARD
    ======================== --}}
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-4">Data Terbaru</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow-lg h-100 rounded-4">
                    <div class="card-body text-center py-4">
                        <h5 class="text-primary fw-semibold mb-2">Jumlah Menu</h5>
                        <h1 class="fw-bold display-6 text-dark">
                            {{ 16 }}
                        </h1>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow-lg h-100 rounded-4">
                    <div class="card-body text-center py-4">
                        <h5 class="text-info fw-semibold mb-2">Jumlah Transaksi Terakhir</h5>
                        <h1 class="fw-bold display-6 text-dark">
                            {{-- Nanti isi jumlah transaksi disini --}}
                        </h1>
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    {{-- =======================
        GRAFIK PENJUALAN BULANAN
    ======================== --}}
    <div class="card shadow-lg border-0 rounded-4 mb-5">
        <div class="card-header bg-gradient text-white text-center" style="background: linear-gradient(90deg, #007bff, #00c6ff);">
            <h4 class="fw-bold mb-0">📈 Grafik Penjualan Menu Bulan Terbaru</h4>
        </div>
        <div class="card-body p-4">
            <canvas id="penjualanChart" height="100"></canvas>
        </div>
    </div>

    {{-- =======================
        GRAFIK PENJUALAN TAHUNAN
    ======================== --}}
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-gradient text-white text-center" style="background: linear-gradient(90deg, #17a2b8, #00d4ff);">
            <h4 class="fw-bold mb-0">📆 Grafik Penjualan Tahunan</h4>
        </div>
        <div class="card-body p-4">
            <canvas id="penjualanTahunanChart" height="100"></canvas>
        </div>
    </div>

</div>
@endsection
