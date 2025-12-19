<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MenuImport;

class MenuController extends Controller
{
    public function index()
    {
        {
            $menu = Menu::orderBy('id_menu','ASC')->get();
            return view ('menu.index', compact('menu'));
        }
    }

    /**
     * >>> ADD: Halaman MENU untuk USER (read-only) dengan paginate
     * Route yang memanggil: /user/menu  -> name: user.menu.index
     * View: resources/views/user/menu/index.blade.php
     */
    public function userIndex(Request $request)
    {
        $menus = Menu::select('id_menu','nama_menu','harga')
            ->orderBy('id_menu','ASC')
            ->paginate(10)
            ->withQueryString();

        return view('user.menu.index', compact('menus'));
    }
    // <<< END ADD

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $menu = Menu::all(); // ambil data menu untuk select dropdown
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

        Menu::create($validated);
        return redirect()->route('menu.index')->with('success', 'menu berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        return view('menu.edit', compact('menu'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        // Validasi data
        $validated = $request->validate([
            'nama_menu' => 'required|string',
            'harga' => 'required|numeric',
        ]);

        // Update menu
        $menu->update($validated);

        return redirect()->route('menu.index')->with('success', 'Data Berhasil Diubah!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        Excel::import(new MenuImport, $request->file('file'));

        return redirect()->route('menu.index')->with('success', 'Data berhasil diimport!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id_menu)
    {
        // Mencari menu berdasarkan id_menu
        $menu = Menu::findOrFail($id_menu);  // menggunakan id_menu langsung
        $menu->delete(); // Hapus menu
    
        return redirect()->route('menu.index')->with('deleted', 'Data berhasil dihapus.');
    }
}
