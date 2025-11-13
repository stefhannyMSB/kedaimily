<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class penjualan extends Model
{
    use HasFactory;

    // >>> PENTING: samakan dengan nama tabel sebenarnya di DB
    protected $table = 'datapenjualans';

    // Primary key kustom
    protected $primaryKey = 'id_penjualan';
    public $incrementing = true;
    protected $keyType = 'int';

    // Jika tabel tidak punya created_at & updated_at
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'id_menu',
        'jumlah',
        'total',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah'  => 'integer',
        'total'   => 'decimal:2',
    ];

    // Relasi ke tabel menu
    public function menu()
    {
        return $this->belongsTo(menu::class, 'id_menu', 'id_menu');
    }
}
