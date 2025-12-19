<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datapenjualan extends Model
{
    /** @use HasFactory<\Database\Factories\DatapenjualanFactory> */
    use HasFactory;
    protected $primaryKey = 'id_datapenjualan'; // Jangan lupa kalau pakai primary key selain 'id'
    public $timestamps = false; // Kalau tabel kamu tidak ada created_at, updated_at

    protected $fillable = [
        'tanggal', 'id_menu', 'jumlah'
    ];

    // model penjualan
public function menu()
{
    return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
}


}