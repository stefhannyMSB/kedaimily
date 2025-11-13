<?php

namespace App\Http\Controllers;

use App\Imports\TransaksiImport;
use App\Models\Transaksi;
use App\Models\Menu;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    /**
     * Tampilkan daftar data transaksi (dengan filter bulan & tahun)
     */
    public function index(Request $request)
    {
        $menu = Menu::all();
        $query = Transaksi::with('menu')->orderBy('id_transaksi', 'DESC');

        // 🔹 Filter berdasarkan bulan & tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $transaksi = $query->get();

        // 🔹 Ambil daftar tahun unik dari data transaksi
        $tahunList = Transaksi::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('transaksi.index', compact('transaksi', 'menu', 'tahunList'));
    }

    /**
     * Tampilkan form tambah data transakasi
     */
    public function create()
    {
        $menu = Menu::orderBy('nama_menu', 'ASC')->get();
        return view('transaksi.create', compact('menu'));
    }

    /**
     * Simpan data transaksi baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'id_menu' => 'required|exists:menus,id_menu',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $menu = Menu::findOrFail($validatedData['id_menu']);
        $total = $validatedData['jumlah'] * $menu->harga;

        Transaksi::create([
            'tanggal' => $validatedData['tanggal'],
            'id_menu' => $validatedData['id_menu'],
            'jumlah' => $validatedData['jumlah'],
            'total' => $total,
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Data Transaksi berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit data transaksi
     */
    public function edit($id)
    {
        $menu = Menu::all();
        $transaksi = Transaksi::findOrFail($id);
        return view('transaksi.edit', compact('transaksi', 'menu'));
    }

    /**
     * Update data transaksi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'id_menu' => 'required|exists:menus,id_menu',
            'jumlah' => 'required|numeric|min:1',
        ]);

        $menu = Menu::findOrFail($validated['id_menu']);
        $total = $validated['jumlah'] * $menu->harga;

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'tanggal' => $validated['tanggal'],
            'id_menu' => $validated['id_menu'],
            'jumlah' => $validated['jumlah'],
            'total' => $total,
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Data Transaksi berhasil diubah.');
    }

    /**
     * Hapus data transaksi
     */
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Data Transaksi berhasil dihapus.');
    }

    /**
     * Import data transaksi dari file Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        Excel::import(new TransaksiImport, $request->file('file'));

        return redirect()->route('transaksi.index')->with('success', 'Data Transaksi berhasil diimport!');
    }

    /**
     * Cetak laporan Transaksi berdasarkan periode (bulan & tahun)
     */
    public function report(Request $request)
    {
        $query = Transaksi::with('menu')->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $transaksi = $query->get();

        // Untuk menampilkan bulan/tahun di header laporan
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return view('transaksi.report', compact('transaksi', 'bulan', 'tahun'));
    }
}
