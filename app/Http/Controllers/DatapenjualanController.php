<?php

namespace App\Http\Controllers;

use App\Imports\DatapenjualanImport;
use App\Models\Datapenjualan;
use App\Models\Menu;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DatapenjualanController extends Controller
{
    /**
     * Tampilkan daftar data penjualan (dengan filter bulan & tahun)
     */
    public function index(Request $request)
    {
        $menu = Menu::all();
        $query = Datapenjualan::with('menu')->orderBy('id_datapenjualan', 'DESC');

        // 🔹 Filter berdasarkan bulan & tahun
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $datapenjualan = $query->get();

        // 🔹 Ambil daftar tahun unik dari data penjualan
        $tahunList = Datapenjualan::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('datapenjualan.index', compact('datapenjualan', 'menu', 'tahunList'));
    }

    /**
     * Tampilkan form tambah data penjualan
     */
    public function create()
    {
        $menu = Menu::orderBy('nama_menu', 'ASC')->get();
        return view('datapenjualan.create', compact('menu'));
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
        // $total = $validatedData['jumlah'] * $menu->harga;

        Datapenjualan::create([
            'tanggal' => $validatedData['tanggal'],
            'id_menu' => $validatedData['id_menu'],
            'jumlah' => $validatedData['jumlah'],
            
        ]);

        return redirect()->route('datapenjualan.index')->with('success', 'Data Penjualan berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit data penjualan
     */
    public function edit($id)
    {
        $menu = Menu::all();
        $datapenjualan = Datapenjualan::findOrFail($id);
        return view('datapenjualan.edit', compact('datapenjualan', 'menu'));
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
        // $total = $validated['jumlah'] * $menu->harga;

        $datapenjualan = Datapenjualan::findOrFail($id);
        $datapenjualan->update([
            'tanggal' => $validated['tanggal'],
            'id_menu' => $validated['id_menu'],
            'jumlah' => $validated['jumlah'],
            
        ]);

        return redirect()->route('datapenjualan.index')->with('success', 'Data Penjualan berhasil diubah.');
    }

    /**
     * Hapus data penjualan
     */
    public function destroy($id)
    {
        $datapenjualan = Datapenjualan::findOrFail($id);
        $datapenjualan->delete();

        return redirect()->route('datapenjualan.index')->with('success', 'Data Penjualan berhasil dihapus.');
    }

    /**
     * Import data penjualan dari file Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        Excel::import(new DatapenjualanImport, $request->file('file'));

        return redirect()->route('datapenjualan.index')->with('success', 'Data Penjualan berhasil diimport!');
    }

    /**
     * Cetak laporan penjualan berdasarkan periode (bulan & tahun)
     */
    public function report(Request $request)
    {
        $query = Datapenjualan::with('menu')->orderBy('tanggal', 'asc');

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $datapenjualan = $query->get();

        // Untuk menampilkan bulan/tahun di header laporan
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        return view('datapenjualan.report', compact('datapenjualan', 'bulan', 'tahun'));
    }
}
