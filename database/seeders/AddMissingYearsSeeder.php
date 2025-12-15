<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AddMissingYearsSeeder extends Seeder
{
    /**
     * Jalankan seeder: clone semua baris tahun 2024 ke 2023 dan 2025
     * Hati-hati: ini menambahkan data duplikat bila tidak dicek.
     */
    public function run()
    {
        $fromYear = 2024;
        $targetYears = [2023, 2025];

        // Daftar tabel dan primary key mereka (jika ada)
        $tables = [
            'transaksis' => 'id_transaksi',
            'penjualans' => 'id_penjualan',
            'datapenjualans' => 'id_datapenjualan',
        ];

        foreach ($tables as $table => $pk) {
            if (!Schema::hasTable($table)) {
                $this->command->info("Tabel {$table} tidak ada, dilewati.");
                continue;
            }

            $rows = DB::table($table)->whereYear('tanggal', $fromYear)->get();
            $this->command->info("Menemukan {$rows->count()} baris di tabel {$table} untuk tahun {$fromYear}.");

            foreach ($rows as $row) {
                $rowArr = (array) $row;

                foreach ($targetYears as $y) {
                    // Ganti tahun pada kolom tanggal, tetap pakai hari & bulan yang sama
                    try {
                        $newTanggal = Carbon::parse($rowArr['tanggal'])->setYear($y)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Jika parsing gagal, skip
                        $this->command->warn("Gagal parse tanggal: " . ($rowArr['tanggal'] ?? 'null')); 
                        continue;
                    }

                    // Siapkan data baru tanpa primary key
                    $new = $rowArr;
                    if (isset($new[$pk])) {
                        unset($new[$pk]);
                    }

                    // Overwrite tanggal dan timestamps jika ada
                    $new['tanggal'] = $newTanggal;
                    if (Schema::hasColumn($table, 'created_at')) {
                        $new['created_at'] = Carbon::now();
                    }
                    if (Schema::hasColumn($table, 'updated_at')) {
                        $new['updated_at'] = Carbon::now();
                    }

                    // Cek duplikat (sederhana): sama tanggal, id_menu, jumlah, total
                    $dupQuery = DB::table($table)->where('tanggal', $new['tanggal']);
                    if (isset($new['id_menu'])) $dupQuery->where('id_menu', $new['id_menu']);
                    if (isset($new['jumlah'])) $dupQuery->where('jumlah', $new['jumlah']);
                    if (isset($new['total'])) $dupQuery->where('total', $new['total']);

                    $exists = $dupQuery->exists();
                    if ($exists) {
                        $this->command->info("Baris untuk tanggal {$new['tanggal']} sudah ada di {$table}, dilewati.");
                        continue;
                    }

                    DB::table($table)->insert($new);
                    $this->command->info("Menambahkan ke {$table}: tanggal={$new['tanggal']}.");
                }
            }
        }

        $this->command->info('Seeder AddMissingYearsSeeder selesai.');
    }
}
