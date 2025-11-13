<?php

namespace App\Imports;

use App\Models\Datapenjualan;
use App\Models\Menu;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DatapenjualanImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2; // Abaikan header Excel
    }

    public function model(array $row)
    {
        try {
            // 🚫 Lewati baris kosong
            if (empty(array_filter($row))) {
                return null;
            }

            $tanggalExcel = $row[0];
            $namaProdukExcel = trim($row[1]);
            $jumlah = $row[2];
            $total = $row[3];

            // 🧩 Parsing tanggal Excel (dd/mm/yyyy → Y-m-d)
            if (is_numeric($tanggalExcel)) {
                // Format Excel number
                $carbonDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalExcel);
                $tanggal = Carbon::instance($carbonDate)->format('Y-m-d');
            } else {
                // Format string (misal: 02/01/2025)
                try {
                    $tanggal = Carbon::createFromFormat('d/m/Y', $tanggalExcel)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggal = Carbon::parse($tanggalExcel)->format('Y-m-d');
                }
            }

            // Cek tanggal valid
            if (!$tanggal) {
                Log::warning('Tanggal tidak valid, baris dilewati: ' . json_encode($row));
                return null;
            }

            // Cek nama produk kosong
            if (empty($namaProdukExcel)) {
                Log::warning('Nama produk kosong, baris dilewati: ' . json_encode($row));
                return null;
            }

            // 🔍 Cari menu berdasarkan nama (slug)
            $slug = Str::slug($namaProdukExcel);
            $menu = Menu::all()->first(function ($item) use ($slug) {
                return Str::slug($item->nama_menu) === $slug;
            });

            if (!$menu) {
                Log::warning('Menu tidak ditemukan, baris dilewati: ' . json_encode($row));
                return null;
            }

            // ✅ Simpan data penjualan
            return new Datapenjualan([
                'tanggal' => $tanggal, // format Y-m-d
                'id_menu' => $menu->id_menu,
                'nama_menu' => $namaProdukExcel,
                'jumlah' => $jumlah,
                
            ]);
        } catch (\Exception $e) {
            // 🧾 Log error tanpa hentikan import
            Log::error('Gagal impor baris: ' . json_encode($row) . ' | Error: ' . $e->getMessage());
            return null; // skip baris error
        }
    }
}
