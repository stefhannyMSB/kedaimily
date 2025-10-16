<?php

namespace App\Http\Controllers;

use App\Imports\PenjualanImport;
use App\Models\Penjualan;
use App\Models\Menu;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PenjualanController extends Controller
{
    /**
     * Tampilkan daftar data penjualan (dengan filter bulan & tahun)
     */
    public function index(Request $request)
    {
        $menu = Menu::all();
        $query = Penjualan::with('menu')->orderBy('id_penjualan', 'DESC');

        // 🔹 Filter berdasarkan bulan & tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $penjualan = $query->get();

        // 🔹 Ambil daftar tahun unik dari data penjualan
        $tahunList = Penjualan::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('penjualan.index', compact('penjualan', 'menu', 'tahunList'));
    }

    /**
     * Tampilkan form tambah data penjualan
     */
    public function create()
    {
        $menu = Menu::orderBy('nama_menu', 'ASC')->get();
        return view('penjualan.create', compact('menu'));
    }

    /**
     * Simpan data penjualan baru
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

        Penjualan::create([
            'tanggal' => $validatedData['tanggal'],
            'id_menu' => $validatedData['id_menu'],
            'jumlah' => $validatedData['jumlah'],
            'total' => $total,
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Data Penjualan berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit data penjualan
     */
    public function edit($id)
    {
        $menu = Menu::all();
        $penjualan = Penjualan::findOrFail($id);
        return view('penjualan.edit', compact('penjualan', 'menu'));
    }

    /**
     * Update data penjualan
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

        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update([
            'tanggal' => $validated['tanggal'],
            'id_menu' => $validated['id_menu'],
            'jumlah' => $validated['jumlah'],
            'total' => $total,
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Data Penjualan berhasil diubah.');
    }

    /**
     * Hapus data penjualan
     */
    public function destroy($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $penjualan->delete();

        return redirect()->route('penjualan.index')->with('success', 'Data Penjualan berhasil dihapus.');
    }

    /**
     * Import data penjualan dari file Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        Excel::import(new PenjualanImport, $request->file('file'));

        return redirect()->route('penjualan.index')->with('success', 'Data Penjualan berhasil diimport!');
    }

    /**
     * Cetak laporan penjualan berdasarkan periode (bulan & tahun)
     */
    public function report(Request $request)
    {
        $query = Penjualan::with('menu')->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $penjualan = $query->get();

        // Untuk menampilkan bulan/tahun di header laporan
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return view('penjualan.report', compact('penjualan', 'bulan', 'tahun'));
    }
}
