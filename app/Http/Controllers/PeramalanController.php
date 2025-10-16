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
            $id_menu = $request->id_menu;
            $tanggal_awal = $request->tanggal_awal;
            $tanggal_akhir = $request->tanggal_akhir;
            $selectedAlpha = $request->filled('alpha') ? (float)$request->alpha : 0.1;

            // 🔹 Ambil data penjualan per hari
            $penjualan = DB::table('penjualans')
                ->selectRaw('DATE(tanggal) as tanggal, SUM(jumlah) as total_jual')
                ->where('id_menu', $id_menu)
                ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get();

            if ($penjualan->isNotEmpty()) {
                $tanggalList = $penjualan->pluck('tanggal')->toArray();
                $dataAktual = $penjualan->pluck('total_jual')->toArray();

                // 🔹 Evaluasi alpha 0.1 - 0.9
                for ($alpha = 0.1; $alpha <= 0.9; $alpha += 0.1) {
                    $S1 = [];
                    $S2 = [];
                    $level = [];
                    $trend = [];
                    $forecast = [];
                    $errors = [];

                    foreach ($dataAktual as $t => $x) {
                        if ($t == 0) {
                            // Inisialisasi nilai pertama
                            $S1[$t] = $x;
                            $S2[$t] = $x;
                            $level[$t] = $x;
                            $trend[$t] = 0;
                            $forecast[$t] = null;
                            $errors[$t] = null;
                        } else {
                            // Hitung Single dan Double Exponential Smoothing
                            $S1[$t] = $alpha * $x + (1 - $alpha) * $S1[$t - 1];
                            $S2[$t] = $alpha * $S1[$t] + (1 - $alpha) * $S2[$t - 1];

                            // Hitung Level dan Trend
                            $level[$t] = 2 * $S1[$t] - $S2[$t];
                            $trend[$t] = ($alpha / (1 - $alpha)) * ($S1[$t] - $S2[$t]);

                            // Forecast untuk periode t (menggunakan nilai sebelumnya)
                            $forecast[$t] = $level[$t - 1] + $trend[$t - 1];

                            // Error absolut
                            $errors[$t] = abs($x - $forecast[$t]);
                        }
                    }

                    // Hitung MAE dan MAPE
                    $validErrors = array_filter($errors);
                    $mae = count($validErrors) ? array_sum($validErrors) / count($validErrors) : 0;

                    $mape = 0;
                    $validMAPE = 0;
                    foreach ($dataAktual as $i => $val) {
                        if ($i > 0 && $val != 0 && isset($forecast[$i])) {
                            $mape += abs(($val - $forecast[$i]) / $val);
                            $validMAPE++;
                        }
                    }
                    $mape = $validMAPE ? ($mape / $validMAPE) * 100 : 0;

                    $alphaEvaluations[] = [
                        'alpha' => round($alpha, 1),
                        'mae' => round($mae, 3),
                        'mape' => round($mape, 3)
                    ];

                    // Simpan hasil untuk alpha terpilih
                    if (abs($alpha - $selectedAlpha) < 0.001) {
                        $results = [
                            'alpha' => $selectedAlpha,
                            'tanggal' => $tanggalList,
                            'aktual' => $dataAktual,
                            'S1' => $S1,
                            'S2' => $S2,
                            'level' => $level,
                            'trend' => $trend,
                            'forecast' => $forecast,
                            'error' => $errors
                        ];
                    }
                }

                // 🔹 Cari alpha terbaik
                $bestAlphaMAE = collect($alphaEvaluations)->sortBy('mae')->first();
                $bestAlphaMAPE = collect($alphaEvaluations)->sortBy('mape')->first();

                // 🔹 Forecast 2 hari ke depan
                if (!empty($level) && !empty($trend)) {
                    $lastLevel = end($level);
                    $lastTrend = end($trend);
                    $lastDate = Carbon::parse(end($tanggalList));

                    for ($i = 1; $i <= 2; $i++) {
                        $nextDate = $lastDate->copy()->addDays($i)->format('Y-m-d');
                        $forecastNextDays[$nextDate] = $lastLevel + $i * $lastTrend;
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
