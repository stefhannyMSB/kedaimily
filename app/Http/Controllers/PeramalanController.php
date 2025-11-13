<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeramalanController extends Controller
{
    public function index(Request $request)
    {
        $menus = DB::table('menus')->get();

        $results = [];
        $alphaEvaluations = [];
        $bestAlphaMAE = null;
        $bestAlphaMAPE = null;
        $forecastNextDays = [];
        $tanggalList = [];
        $dataAktual = [];

        if ($request->filled(['id_menu', 'tanggal_awal', 'tanggal_akhir'])) {
            $id_menu       = $request->id_menu;
            $tanggal_awal  = $request->tanggal_awal;
            $tanggal_akhir = $request->tanggal_akhir;

            // Periode ('harian' | 'mingguan' | 'bulanan'), default harian
            $periode       = $request->get('periode', 'harian');
            $selectedAlpha = $request->filled('alpha') ? (float)$request->alpha : 0.1;

            // === Ambil data transaksi per-periode ===
            if ($periode === 'mingguan') {
                // Mingguan (ISO: minggu mulai Senin)
                $transaksi = DB::table('transaksis')
                    ->selectRaw("
                        YEARWEEK(tanggal, 1) as yw,
                        MIN(DATE(tanggal)) as start_week,
                        SUM(jumlah) as total_jual
                    ")
                    ->where('id_menu', $id_menu)
                    ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
                    ->groupBy('yw')
                    ->orderBy('start_week', 'asc')
                    ->get();

                if ($transaksi->isNotEmpty()) {
                    $tanggalList = $transaksi->pluck('start_week')->toArray();  // label = tanggal awal minggu (Senin)
                    $dataAktual  = $transaksi->pluck('total_jual')->toArray();
                }

            } elseif ($periode === 'bulanan') {
                // Bulanan: label = tanggal hari pertama di bulan tsb (YYYY-MM-01)
                $transaksi = DB::table('transaksis')
                    ->selectRaw("
                        YEAR(tanggal) as yy,
                        MONTH(tanggal) as mm,
                        DATE_FORMAT(DATE(tanggal), '%Y-%m-01') as start_month,
                        SUM(jumlah) as total_jual
                    ")
                    ->where('id_menu', $id_menu)
                    ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
                    ->groupBy('yy','mm','start_month')
                    ->orderBy('start_month', 'asc')
                    ->get();

                if ($transaksi->isNotEmpty()) {
                    $tanggalList = $transaksi->pluck('start_month')->toArray(); // label = 1 tgl tiap bulan
                    $dataAktual  = $transaksi->pluck('total_jual')->toArray();
                }

            } else {
                // Harian (default)
                $transaksi = DB::table('transaksis')
                    ->selectRaw('DATE(tanggal) as tanggal, SUM(jumlah) as total_jual')
                    ->where('id_menu', $id_menu)
                    ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
                    ->groupBy('tanggal')
                    ->orderBy('tanggal', 'asc')
                    ->get();

                if ($transaksi->isNotEmpty()) {
                    $tanggalList = $transaksi->pluck('tanggal')->toArray();
                    $dataAktual  = $transaksi->pluck('total_jual')->toArray();
                }
            }

            // === Perhitungan DES bila ada data ===
            if (!empty($dataAktual)) {
                for ($alpha = 0.1; $alpha <= 0.9; $alpha += 0.1) {
                    $alpha = round($alpha, 1);

                    $S1 = [];
                    $S2 = [];
                    $level = [];
                    $trend = [];
                    $forecast = [];
                    $errors = [];

                    foreach ($dataAktual as $t => $x) {
                        if ($t === 0) {
                            // inisialisasi
                            $S1[$t] = $x;
                            $S2[$t] = $x;
                            $level[$t] = $x;
                            $trend[$t] = 0;
                            $forecast[$t] = null;
                            $errors[$t] = null;
                        } else {
                            // smoothing
                            $S1[$t] = $alpha * $x + (1 - $alpha) * $S1[$t - 1];
                            $S2[$t] = $alpha * $S1[$t] + (1 - $alpha) * $S2[$t - 1];

                            // level & trend
                            $level[$t] = 2 * $S1[$t] - $S2[$t];
                            $trend[$t] = ($alpha / (1 - $alpha)) * ($S1[$t] - $S2[$t]);

                            // forecast periode t pakai level & trend periode sebelumnya
                            $forecast[$t] = $level[$t - 1] + $trend[$t - 1];

                            // error absolut
                            $errors[$t] = abs($x - $forecast[$t]);
                        }
                    }

                    // MAE
                    $validErrors = array_filter($errors, fn($e) => $e !== null);
                    $mae = count($validErrors) ? array_sum($validErrors) / count($validErrors) : 0;

                    // MAPE
                    $mapeSum = 0;
                    $validMAPE = 0;
                    foreach ($dataAktual as $i => $val) {
                        if ($i > 0 && $val != 0 && isset($forecast[$i])) {
                            $mapeSum += abs(($val - $forecast[$i]) / $val);
                            $validMAPE++;
                        }
                    }
                    $mape = $validMAPE ? ($mapeSum / $validMAPE) * 100 : 0;

                    $alphaEvaluations[] = [
                        'alpha'   => $alpha,
                        'mae'     => round($mae, 3),
                        'mape'    => round($mape, 3),
                        'periode' => $periode,
                    ];

                    // simpan detail untuk alpha terpilih
                    if (abs($alpha - $selectedAlpha) < 0.001) {
                        $results = [
                            'alpha'    => $selectedAlpha,
                            'periode'  => $periode,
                            'tanggal'  => $tanggalList,
                            'aktual'   => $dataAktual,
                            'S1'       => $S1,
                            'S2'       => $S2,
                            'level'    => $level,
                            'trend'    => $trend,
                            'forecast' => $forecast,
                            'error'    => $errors
                        ];
                    }
                }

                // Alpha terbaik
                $bestAlphaMAE  = collect($alphaEvaluations)->sortBy('mae')->first();
                $bestAlphaMAPE = collect($alphaEvaluations)->sortBy('mape')->first();

                // === Forecast ke depan (2 periode) ===
                // Periode = hari (harian), minggu (mingguan), bulan (bulanan)
                if (!empty($results['level']) && !empty($results['trend'])) {
                    $lastLevel = end($results['level']);
                    $lastTrend = end($results['trend']);

                    $lastLabel = end($tanggalList);
                    $lastDate  = Carbon::parse($lastLabel);

                    if ($periode === 'mingguan') {
                        // label = tanggal awal minggu berikutnya
                        for ($i = 1; $i <= 2; $i++) {
                            $nextStartOfWeek = $lastDate->copy()->startOfWeek(Carbon::MONDAY)->addWeeks($i);
                            $forecastNextDays[$nextStartOfWeek->format('Y-m-d')] = $lastLevel + $i * $lastTrend;
                        }
                    } elseif ($periode === 'bulanan') {
                        // label = tanggal awal bulan berikutnya
                        for ($i = 1; $i <= 2; $i++) {
                            $nextStartOfMonth = $lastDate->copy()->startOfMonth()->addMonths($i);
                            $forecastNextDays[$nextStartOfMonth->format('Y-m-d')] = $lastLevel + $i * $lastTrend;
                        }
                    } else {
                        // harian
                        for ($i = 1; $i <= 2; $i++) {
                            $nextDate = $lastDate->copy()->addDays($i)->format('Y-m-d');
                            $forecastNextDays[$nextDate] = $lastLevel + $i * $lastTrend;
                        }
                    }
                }
            }
        }

        return view('peramalan.index', compact(
            'menus',
            'results',
            'alphaEvaluations',
            'bestAlphaMAE',
            'bestAlphaMAPE',
            'forecastNextDays'
        ));
    }
}
