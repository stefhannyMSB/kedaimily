@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-success">Peramalan</h3>

    {{-- FORM FILTER --}}
<div class="card border-0 shadow-lg rounded-4 mb-4">
    <div class="card-header d-flex justify-content-between align-items-center rounded-top-4"
         style="background: linear-gradient(90deg,#0f9b0f,#00b09b);">
        <h6 class="mb-0 text-white fw-semibold">Peramalan DES</h6>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('peramalan.index') }}" class="row g-3 align-items-end">
            {{-- Menu --}}
            <div class="col-lg-3 col-md-6">
                <label class="form-label mb-1">Pilih Menu</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-list-ul"></i></span>
                    <select class="form-select form-select-sm" name="id_menu" required>
                        <option value="">-- Pilih Menu --</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id_menu }}" {{ request('id_menu') == $menu->id_menu ? 'selected' : '' }}>
                                {{ $menu->nama_menu }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tanggal Awal --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label mb-1">Tanggal Awal</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                    <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}"
                           class="form-control form-control-sm" required>
                </div>
            </div>

            {{-- Tanggal Akhir --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label mb-1">Tanggal Akhir</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-calendar2-check"></i></span>
                    <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                           class="form-control form-control-sm" required>
                </div>
            </div>

            {{-- Alpha --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label mb-1">Alpha (α)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-sliders"></i></span>
                    <input type="number" step="0.1" min="0.1" max="0.9" name="alpha"
                           value="{{ request('alpha', 0.1) }}"
                           class="form-control form-control-sm" required>
                </div>
            </div>

            {{-- Periode --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label mb-1">Periode</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                    @php $periodeReq = request('periode'); @endphp
                    <select name="periode" class="form-select form-select-sm" required>
                        <option value="" {{ $periodeReq == '' ? 'selected' : '' }}>-- Pilih Periode --</option>
                        <option value="harian"   {{ $periodeReq === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="mingguan" {{ $periodeReq === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan"  {{ $periodeReq === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="col-lg-1 col-md-6 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-success btn-sm px-3 w-1000">
                     Hitung
                </button>
            </div>
        </form>
    </div>
</div>

    {{-- HASIL --}}
    @if(!empty($results))
    <div class="card p-4 shadow-sm">
        <h5>Hasil Peramalan (α = {{ $results['alpha'] }})</h5>

        @php
            // Periode aktif & unit tampilan
            $periodeAktif = $results['periode'] ?? request('periode','harian');
            $unitPeriode  = $periodeAktif === 'mingguan' ? 'Minggu' : 'Hari';

            // Pagination sederhana 10 baris
            $totalRows   = isset($results['tanggal']) ? count($results['tanggal']) : 0;
            $showAll     = request('show') === 'all';
            $limit       = $showAll ? $totalRows : 10;

            // URL helper
            $qAll = array_merge(request()->query(), ['show' => 'all']);
            $seeAllUrl = url()->current() . '?' . http_build_query($qAll);
            $qTen = request()->query(); unset($qTen['show']);
            $showTenUrl = url()->current() . (count($qTen) ? ('?' . http_build_query($qTen)) : '');

            // Label chart rapi (format tanggal)
            $labelsFormatted = [];
            if (!empty($results['tanggal'])) {
                foreach ($results['tanggal'] as $lbl) {
                    try {
                        $labelsFormatted[] = \Carbon\Carbon::parse($lbl)->format('d M Y');
                    } catch (\Exception $e) {
                        $labelsFormatted[] = $lbl;
                    }
                }
            }
        @endphp

        <div class="d-flex align-items-center justify-content-between mt-3 mb-2">
            <h6 class="mb-0">Tabel Perhitungan Double Exponential Smoothing ({{ ucfirst($periodeAktif) }})</h6>
            <div class="text-end">
                @if(!$showAll && $totalRows > 10)
                    <small class="text-muted me-2">Menampilkan 10 dari {{ $totalRows }} baris</small>
                    <a href="{{ $seeAllUrl }}" class="btn btn-outline-secondary btn-sm">Lihat semua ({{ $totalRows }})</a>
                @elseif($showAll && $totalRows > 10)
                    <small class="text-muted me-2">Menampilkan semua ({{ $totalRows }})</small>
                    <a href="{{ $showTenUrl }}" class="btn btn-outline-secondary btn-sm">Tampilkan 10 saja</a>
                @endif
            </div>
        </div>

        {{-- TABEL PROSES --}}
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-success">
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
                    @for($i = 0; $i < $limit; $i++)
                        @php
                            $tglRaw = $results['tanggal'][$i] ?? null;
                            if ($tglRaw === null) break;
                            try {
                                $tglTampil = \Carbon\Carbon::parse($tglRaw)->format('d M Y');
                            } catch (\Exception $e) {
                                $tglTampil = $tglRaw;
                            }
                        @endphp
                        <tr class="text-center">
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $tglTampil }}</td>
                            <td>{{ $results['aktual'][$i] ?? '-' }}</td>
                            <td>{{ isset($results['S1'][$i]) ? number_format($results['S1'][$i], 2) : '-' }}</td>
                            <td>{{ isset($results['S2'][$i]) ? number_format($results['S2'][$i], 2) : '-' }}</td>
                            <td>{{ isset($results['level'][$i]) ? number_format($results['level'][$i], 2) : '-' }}</td>
                            <td>{{ isset($results['trend'][$i]) ? number_format($results['trend'][$i], 2) : '-' }}</td>
                            <td>{{ isset($results['forecast'][$i]) ? number_format($results['forecast'][$i], 2) : '-' }}</td>
                            <td>{{ isset($results['error'][$i]) ? number_format($results['error'][$i], 2) : '-' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- RAMALAN KE DEPAN --}}
        <h6 class="mt-4">Hasil Peramalan 2 {{ $unitPeriode }} ke Depan:</h6>
        <ul>
            @foreach($forecastNextDays as $tgl => $nilai)
                @php
                    try { $tglList = \Carbon\Carbon::parse($tgl)->format('d M Y'); }
                    catch (\Exception $e) { $tglList = $tgl; }
                @endphp
                <li>{{ $tglList }} → {{ number_format($nilai, 2) }}</li>
            @endforeach
        </ul>

        {{-- EVALUASI ALPHA --}}
        <h6 class="mt-4">Evaluasi Alpha (0.1 - 0.9)</h6>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Alpha</th>
                        <!-- <th>MAE</th> -->
                        <th>MAPE (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alphaEvaluations as $e)
                    <tr>
                        <td>{{ $e['alpha'] }}</td>
                        <!-- <td>{{ $e['mae'] }}</td> -->
                        <td>{{ $e['mape'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <!-- <p><strong>Alpha Terbaik (MAE Terkecil):</strong> {{ $bestAlphaMAE['alpha'] ?? '-' }} ({{ $bestAlphaMAE['mae'] ?? '-' }})</p> -->
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
    (function() {
        const labels = {!! json_encode($labelsFormatted ?? ($results['tanggal'] ?? [])) !!};

        new Chart(document.getElementById('chartPerbandingan'), {
            type: 'line',
            data: {
                labels,
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
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: (items) => items[0]?.label || ''
                        }
                    }
                }
            }
        });
    })();
</script>
@endif

{{-- Tinggi input & tombol diseragamkan --}}
<style>
    .form-control-sm, .btn-sm { height: 38px !important; font-size: 0.9rem; }
</style>
@endsection
