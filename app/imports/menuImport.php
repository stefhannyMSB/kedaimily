<?php

namespace App\Imports;

use App\Models\Menu;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;

class menuImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2; // Lewati baris header
    }

    public function model(array $row)
    {
        $namamenuExcel = trim($row[0]); // Ambil nama menu dari Excel
        $harga = $row[1]; // Ambil harga dari Excel

        // Buat slug dari nama menu Excel
        $slug = Str::slug($namamenuExcel);

        // Cek apakah menu dengan slug yang sama sudah ada
        $existing = menu::all()->first(function ($item) use ($slug) {
            return Str::slug($item->nama_menu) === $slug;
        });

        if ($existing) {
            // menu sudah ada, lewati (atau bisa di-update jika diperlukan)
            return null;
        }

        return new menu([
            'nama_menu' => $namamenuExcel,
            'harga' => $harga,
        ]);
    }
}
