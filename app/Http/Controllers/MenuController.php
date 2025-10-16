<?php

namespace App\Http\Controllers;

use App\Models\menu;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\menuImport;

class MenuController extends Controller
{
    public function index()
    {
        {
            $menu = menu::orderBy('id_menu','ASC')->get();
            return view ('menu.index', compact('menu'));
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menu = menu::all(); // ambil data menu untuk select dropdown
    return view('menu.create', compact('menu')); // kirim ke view
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_menu' => 'required|string',
        'harga' => 'required|numeric',
    ]);

    menu::create($validated);
    return redirect()->route('menu.index')->with('success', 'menu berhasil ditambahkan!');
}


    /**
     * Display the specified resource.
     */
    public function show(menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(menu $menu)
    {
        return view('menu.edit', compact('menu'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, menu $menu)
    {
        // Validasi data
        $validated = $request->validate([
            'nama_menu' => 'required|string',
            'harga' => 'required|numeric',
        ]);

        // Debug: cek data yang diterima
        // dd($validated);

        // Update menu
        $menu->update($validated);

        return redirect()->route('menu.index')->with('success', 'Data Berhasil Diubah!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);


        Excel::import(new menuImport, $request->file('file'));

        return redirect()->route('menu.index')->with('success', 'Data berhasil diimport!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id_menu)
    {
        // Mencari menu berdasarkan id_menu
        $menu = menu::findOrFail($id_menu);  // menggunakan id_menu langsung
        $menu->delete(); // Hapus menu
    
        return redirect()->route('menu.index')->with('deleted', 'Data berhasil dihapus.');
    }
}
