<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\DatapenjualanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PeramalanController;

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\MenuController as UserMenuReadController;

/*
|--------------------------------------------------------------------------
| AUTH ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// (Opsional, lebih rapi) Lindungi logout dengan auth
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTE (login wajib)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ====== Tetap: Dashboard admin ======
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
        ->middleware('role:admin'); // <-- TAMBAH: agar user biasa tidak mendarat ke dashboard admin

    /*
     * ====== Khusus Admin (tambahkan middleware role:admin ke resource & utilitas) ======
     * Tambahan ini TIDAK menghapus route asli, hanya menambahkan pembatas akses.
     */
    Route::resource('menu', MenuController::class)->middleware('role:admin');                 // <-- TAMBAH
    Route::resource('datapenjualan', DatapenjualanController::class)->middleware('role:admin'); // <-- TAMBAH
    Route::resource('transaksi', TransaksiController::class)->middleware('role:admin');         // <-- TAMBAH
    Route::resource('peramalan', PeramalanController::class)->middleware('role:admin');         // <-- TAMBAH

    Route::get('report-datapenjualan', [DatapenjualanController::class, 'report'])
        ->name('datapenjualan.report')->middleware('role:admin'); // <-- TAMBAH
    Route::get('report-transaksi', [TransaksiController::class, 'report'])
        ->name('transaksi.report')->middleware('role:admin');     // <-- TAMBAH

    Route::match(['get', 'post'], '/import-menu', [MenuController::class, 'import'])
        ->name('menu.import')->middleware('role:admin');          // <-- TAMBAH
    Route::match(['get', 'post'], '/import-datapenjualan', [DatapenjualanController::class, 'import'])
        ->name('datapenjualan.import')->middleware('role:admin'); // <-- TAMBAH
    Route::match(['get', 'post'], '/import-transaksi', [TransaksiController::class, 'import'])
        ->name('transaksi.import')->middleware('role:admin');     // <-- TAMBAH

    // ====== AREA ADMIN (CRUD user, sudah benar) ======
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users',                [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create',         [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users',               [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',    [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',         [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',      [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset');
    });

    // ====== AREA USER (read-only) — sudah sesuai ======
    Route::middleware('role:user,admin')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/menu', [MenuController::class, 'userIndex'])->name('menu.index');
        //
        // Route::get('/peramalan', [PeramalanController::class, 'index'])->name('peramalan.index');
    });
});
