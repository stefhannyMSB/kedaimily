<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilperamalanController extends Controller
{
    public function index(Request $request)
    {
        $menus = DB::table('menus')->get();
         return view('hasilperamalan.index', compact(
            'menus',
        ));
    }
}
