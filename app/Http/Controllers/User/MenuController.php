<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = DB::table('menus')->orderBy('id_menu')->paginate(10);
        return view('user.menu.index', compact('menus'));
    }
}