<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class penjualan extends Model
{
    /** @use HasFactory<\Database\Factories\PenjualanFactory> */
    use HasFactory;
    protected $primaryKey = 'id_penjualan'; // Jangan lupa kalau pakai primary key selain 'id'
    public $timestamps = false; // Kalau tabel kamu tidak ada created_at, updated_at

    protected $fillable = [
        'tanggal', 'id_menu', 'jumlah', 'total'
    ];

    // model penjualan
public function menu()
{
    return $this->belongsTo(menu::class, 'id_menu', 'id_menu');
}


}