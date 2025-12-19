<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Transaksi extends Model
{
    /** @use HasFactory<\Database\Factories\TransaksiFactory> */
    use HasFactory;
    protected $primaryKey = 'id_transaksi'; // Jangan lupa kalau pakai primary key selain 'id'
    public $timestamps = false; // Kalau tabel kamu tidak ada created_at, updated_at
    

    protected $fillable = [
        'tanggal', 'id_menu', 'jumlah', 'total'
    ];

    // model transaksi
public function menu()
{
    return $this->belongsTo(menu::class, 'id_menu', 'id_menu');
}


}