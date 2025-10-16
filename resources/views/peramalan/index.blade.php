@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-dark">📈 Peramalan Penjualan per Hari (Double Exponential Smoothing)</h3>

    <form method="GET" action="{{ route('peramalan.index') }}" class="row g-3 mb-4">
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
            <input type="number" step="0.1" min="0.1" max="0.9" name="alpha" value="{{ request('alpha', 0.1) }}" class="form-control" required>
        </div> 
        <!-- buat dropdown -->
         
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-dark mt-2">Hitung Peramalan</button>
        </div>
    </form>

    @if(!empty($results))
    <div class="card p-4 shadow-sm">
        <h5>Hasil Peramalan (α = {{ $results['alpha'] }})</h5>

        {{-- 🔹 Tabel Proses Perhitungan --}}
        <h6 class="mt-4 mb-2">📘 Proses Perhitungan Double Exponential Smoothing</h6>
        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Data Aktual (Xₜ)</th>
                    <th>S₁ (Single)</th>
                    <th>S₂ (Double)</th>
                    <th>Level (aₜ)</th>
                    <th>Trend (bₜ)</th>
                    <th>Forecast (Fₜ)</th>
                    <th>Error |Xₜ - Fₜ|</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results['tanggal'] as $i => $tgl)
                    <tr class="text-center">
                        <td>{{ $i+1 }}</td>
                        <td>{{ $tgl }}</td>
                        <td>{{ $results['aktual'][$i] ?? '-' }}</td>
                        <td>{{ isset($results['S1'][$i]) ? number_format($results['S1'][$i], 2) : '-' }}</td>
                        <td>{{ isset($results['S2'][$i]) ? number_format($results['S2'][$i], 2) : '-' }}</td>
                        <td>{{ isset($results['level'][$i]) ? number_format($results['level'][$i], 2) : '-' }}</td>
                        <td>{{ isset($results['trend'][$i]) ? number_format($results['trend'][$i], 2) : '-' }}</td>
                        <td>{{ isset($results['forecast'][$i]) ? number_format($results['forecast'][$i], 2) : '-' }}</td>
                        <td>{{ isset($results['error'][$i]) ? number_format($results['error'][$i], 2) : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- 🔹 Tabel Hasil Akhir --}}
        <h6 class="mt-4">📊 Hasil Peramalan dan Evaluasi</h6>
        <table class="table table-bordered mt-2">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Aktual</th>
                    <th>Peramalan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results['tanggal'] as $i => $tgl)
                <tr>
                    <td>{{ $tgl }}</td>
                    <td>{{ $results['aktual'][$i] }}</td>
                    <td>{{ isset($results['forecast'][$i]) ? number_format($results['forecast'][$i], 2) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h6 class="mt-4">🔮 Hasil Peramalan 2 Hari ke Depan:</h6>
        <ul>
            @foreach($forecastNextDays as $tgl => $nilai)
                <li>{{ $tgl }} → {{ number_format($nilai, 2) }}</li>
            @endforeach
        </ul>

        <h6 class="mt-4">📈 Evaluasi Alpha (0.1 - 0.9)</h6>
        <table class="table table-sm table-striped">
            <thead class="table-secondary">
                <tr>
                    <th>Alpha</th>
                    <th>MAE</th>
                    <th>MAPE (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alphaEvaluations as $e)
                <tr>
                    <td>{{ $e['alpha'] }}</td>
                    <td>{{ $e['mae'] }}</td>
                    <td>{{ $e['mape'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            <p><strong>Alpha Terbaik (MAE Terkecil):</strong> {{ $bestAlphaMAE['alpha'] ?? '-' }} ({{ $bestAlphaMAE['mae'] ?? '-' }})</p>
            <p><strong>Alpha Terbaik (MAPE Terkecil):</strong> {{ $bestAlphaMAPE['alpha'] ?? '-' }} ({{ $bestAlphaMAPE['mape'] ?? '-' }}%)</p>
        </div>

        <canvas id="chartPerbandingan" height="120"></canvas>
    </div>
    @endif
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
