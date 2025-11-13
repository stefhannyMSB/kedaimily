<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahMenu      = DB::table('menus')->count();
        $jumlahTransaksi = DB::table('transaksis')->count(); // <- ganti ini

        $deskripsi = "Kedai Mily adalah usaha kuliner di Banyuwa...";

        return view('user.dashboard', compact('jumlahMenu','jumlahTransaksi','deskripsi'));
    }
}
