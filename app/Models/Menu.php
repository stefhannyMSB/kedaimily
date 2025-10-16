<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Menu extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_menu';
    protected $table = 'menus';
    public $timestamps = false;
    protected $fillable = ['nama_menu','harga'];
    
}
