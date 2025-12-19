<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Kartu ringkas
        $jumlahMenu = Menu::count();

        // Jumlah transaksi (sesuai kode kamu sekarang)
        $totalTransaksi = DB::table('transaksis')->count();
        // Jika sebenarnya di 'datapenjualans', pakai:
        // $totalTransaksi = DB::table('datapenjualans')->count();

        // Info user
        $user     = auth()->user();
        $userName = $user->username ?? $user->name ?? 'Pengguna';
        $userRole = $user->role ?? 'user';

        // ====== PARAM TAHUN ======
        $tahunAktif = (int)($request->get('tahun') ?: Carbon::now('Asia/Jakarta')->year);

        // ====== DATA GRAFIK TAHUNAN: total per bulan (Jan–Des) ======
        // --- Jika data penjualan di 'datapenjualans' ---
        $rows = DB::table('datapenjualans')
            ->whereYear('tanggal', $tahunAktif)
            ->selectRaw('MONTH(tanggal) as bln, SUM(jumlah) as total')
            ->groupBy('bln')
            ->orderBy('bln')
            ->get();

        // --- Jika datanya ada di 'transaksis', ganti query di atas menjadi:
        // $rows = DB::table('transaksis')
        //     ->whereYear('tanggal', $tahunAktif)
        //     ->selectRaw('MONTH(tanggal) as bln, SUM(jumlah) as total')
        //     ->groupBy('bln')
        //     ->orderBy('bln')
        //     ->get();

        // Siapkan 12 bulan (1..12) dengan default 0
        $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $dataPerBulan = array_fill(1, 12, 0);

        foreach ($rows as $r) {
            $dataPerBulan[(int)$r->bln] = (int)$r->total;
        }

        // Kirim ke view
        $chartLabels = $bulanLabels;                    // ['Jan','Feb',...,'Des']
        $chartData   = array_values($dataPerBulan);     // [0, 12, 5, ...]
        $chartTitle  = "📈 Grafik Penjualan Tahunan ($tahunAktif)";

        return view('dashboard', compact(
            'jumlahMenu',
            'totalTransaksi',
            'userName',
            'userRole',
            'chartLabels',
            'chartData',
            'chartTitle',
            'tahunAktif'
        ));
    }
}
