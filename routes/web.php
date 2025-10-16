<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PeramalanController;


// Route::get('/', function () {
//    return view('welcome');
// });

Route::get('/', function () {
   return view('dashboard');
});

Route::resource('menu', MenuController::class);
Route::resource('penjualan', PenjualanController::class);
Route::resource('peramalan', PeramalanController::class);
//Route::get('/daftar-menu', [MenuController::class, 'index']);

Route::get('report-penjualan', [PenjualanController::class,'report'])->name('penjualan.report');
Route::match(['get', 'post'], '/import-menu', [MenuController::class, 'import'])->name('menu.import');
Route::match(['get', 'post'], '/import-penjualan', [PenjualanController::class, 'import'])->name('penjualan.import');