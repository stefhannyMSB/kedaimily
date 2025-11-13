@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-dark">Peramalan</h3>

    <form method="GET" action="{{ route('hasilperamalan.index') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Pilih Menu</label>
            <select class="form-control" name="id_menu" required>
                <option value="">-- Pilih Menu --</option>
                @foreach($menus as $menu)
                    <option value="{{ $menu->id_menu }}" {{ request('id_menu') == $menu->id_menu ? 'selected' : '' }}>
                        {{ $menu->nama_menu }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tanggal Awal</label>
            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Alpha (α)</label>
            <input type="number" step="0.1" min="0.1" max="0.3" name="alpha" value="{{ request('alpha', 0.1) }}" class="form-control" required>
        </div> 
        <!-- buat dropdown -->
         
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-dark mt-2">Hitung Peramalan</button>
        </div>
    </form>

    {{-- =======================
        TOMBOL LOGOUT
    ======================== --}}
    <div class="text-center mt-5">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <!-- <button type="submit" class="btn btn-outline-danger px-4 py-2 fw-semibold rounded-3">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </button> -->
        </form>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(!empty($results))
<script>
    const ctx = document.getElementById('chartPerbandingan');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($results['tanggal']) !!},
            datasets: [
                {
                    label: 'Data Aktual',
                    data: {!! json_encode($results['aktual']) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.3
                },
                {
                    label: 'Peramalan (α = {{ $results["alpha"] }})',
                    data: {!! json_encode($results['forecast']) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endif
@endsection
