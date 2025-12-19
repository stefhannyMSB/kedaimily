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
                html: `Selamat datang, <strong>{{ $userName }}</strong>!`,
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: false,
                customClass: { title: 'swal-title', htmlContainer: 'swal-html' }
            });
        </script>
        <style>
            .swal-title { font-weight: 700; font-size: 1.8rem; margin-bottom: 0.5em; }
            .swal-html { color: #444; font-size: 1.1rem; }
        </style>
    @endif

    {{-- =======================
     SUMMARY DASHBOARD (TABEL)
======================= --}}
<div class="container mb-5">
    <h2 class="fw-bold mb-4 text-center">Data Terbaru</h2>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center mb-0">
                    <thead class="table-success">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 60%;">Kategori Data</th>
                            <th style="width: 35%;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="fw-semibold text-start">
                                <i class="bi bi-egg-fried text-success me-2"></i> Jumlah Menu
                            </td>
                            <td>
                                <span class="badge bg-success fs-5 px-3 py-2">{{ $jumlahMenu }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td class="fw-semibold text-start">
                                <i class="bi bi-receipt text-info me-2"></i> Jumlah Transaksi
                            </td>
                            <td>
                                <span class="badge bg-info fs-5 px-3 py-2">{{ $totalTransaksi }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    {{-- =======================
        GRAFIK PENJUALAN TAHUNAN
    ======================== --}}
    <div class="card shadow-lg border-0 rounded-4 mb-5">
        <div class="card-header bg-gradient text-dark text-center" style="background: linear-gradient(90deg, #007bff, #00c6ff);">
            <h4 class="fw-bold mb-0">{{ $chartTitle ?? 'Grafik Penjualan Tahunan' }}</h4>
        </div>
        <div class="card-body p-4" style="max-height: 450px;">
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="penjualanChart"></canvas>
            </div>

            {{-- Fallback bila data kosong --}}
            @if(collect($chartData ?? [])->sum() == 0)
                <div class="text-center text-muted mt-3">
                    Belum ada data penjualan pada tahun ini.
                </div>
            @endif
        </div>
    </div>

    {{-- =======================
        TOMBOL LOGOUT
    ======================== --}}
    <div class="text-center mt-5">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
        </form>
    </div>
</div>

{{-- =======================
    SCRIPT: CHART JS + LOGOUT ALERT
======================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // === Konfirmasi Logout ===
    document.getElementById('logout-form').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            text: "Kamu akan keluar dari sistem Kedai Mily.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });

    // === GRAFIK PENJUALAN TAHUNAN (Chart.js) ===
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($chartLabels ?? []);   // ['Jan','Feb',...,'Des']
        const data   = @json($chartData ?? []);     // [totJan, totFeb, ...]
        const tahun  = @json($tahunAktif ?? null);  // mis. 2025

        if (!labels.length || !data.length) return;

        const ctx = document.getElementById('penjualanChart').getContext('2d');

        // Gradasi hijau
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(0, 180, 100, 0.9)');
        gradient.addColorStop(1, 'rgba(0, 180, 100, 0.2)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: `Total Penjualan ${tahun || ''}`,
                    data,
                    backgroundColor: gradient,
                    borderColor: 'rgba(0, 180, 100, 1)',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    maxBarThickness: 48
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255,255,255,0.95)',
                        titleColor: '#111',
                        bodyColor: '#333',
                        borderColor: '#ccc',
                        borderWidth: 1,
                        callbacks: { label: (ctx) => ` ${ctx.parsed.y} porsi` }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        ticks: { stepSize: 1 }
                    }
                },
                animation: { duration: 800, easing: 'easeOutQuart' }
            }
        });
    });
</script>
@endsection
