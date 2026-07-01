<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\UserController;
use App\Http\Controllers\Owner\KategoriMenuController;
use App\Http\Controllers\Owner\MenuController;
use App\Http\Controllers\Owner\BahanBakuController;
use App\Http\Controllers\Owner\ResepProdukController;
use App\Http\Controllers\Owner\StokController;
use App\Http\Controllers\Owner\RiwayatStokController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\Owner\StokDashboardController;
use App\Http\Controllers\Kasir\POSController;
use App\Http\Controllers\Kasir\TransaksiController;

// AUTH
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// OWNER
Route::middleware(['auth', 'role:owner,admin'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-stok', [StokDashboardController::class, 'index'])->name('dashboard-stok');
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::resource('kategori', KategoriMenuController::class);
    Route::resource('menu', MenuController::class);
    Route::patch('menu/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menu.toggle-status');
    Route::resource('bahan-baku', BahanBakuController::class);
    Route::get('stok/{bahan}/masuk',   [StokController::class, 'masuk'])->name('stok.masuk');
    Route::post('stok/{bahan}/masuk',  [StokController::class, 'simpanMasuk'])->name('stok.simpan-masuk');
    Route::get('stok/{bahan}/keluar',  [StokController::class, 'keluar'])->name('stok.keluar');
    Route::post('stok/{bahan}/keluar', [StokController::class, 'simpanKeluar'])->name('stok.simpan-keluar');
    Route::resource('resep', ResepProdukController::class);
    Route::get('resep/menu/{menu}', [ResepProdukController::class, 'byMenu'])->name('resep.by-menu');
    Route::get('riwayat-stok', [RiwayatStokController::class, 'index'])->name('riwayat-stok.index');
    Route::get('riwayat-stok/{bahan}', [RiwayatStokController::class, 'byBahan'])->name('riwayat-stok.by-bahan');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
    Route::get('laporan/bulanan/export-excel', [LaporanController::class, 'exportBulananExcel'])->name('laporan.bulanan.export-excel');
    Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('transaksi', [TransaksiController::class, 'indexOwner'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
});

// KASIR
Route::middleware(['auth', 'role:kasir,owner'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/pos',  [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos', [POSController::class, 'store'])->name('pos.store');
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::get('/transaksi/{transaksi}/struk', [TransaksiController::class, 'struk'])
     ->name('transaksi.struk');
    Route::post('/transaksi/{transaksi}/cancel', [TransaksiController::class, 'cancel'])->name('transaksi.cancel');
    Route::get('/laporan', [TransaksiController::class, 'laporan'])->name('laporan.index');
});

// Dashboard - owner only
Route::get('/dashboard', [DashboardController::class, 'index'])
     ->name('dashboard')
     ->middleware('role:owner');

// Users - owner only
Route::resource('users', UserController::class)
     ->middleware('role:owner');
Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])
     ->name('users.toggle-active')
     ->middleware('role:owner');

// Laporan - owner only
Route::get('laporan', [LaporanController::class, 'index'])
     ->name('laporan.index')
     ->middleware('role:owner');
Route::get('laporan/bulanan', [LaporanController::class, 'bulanan'])
     ->name('laporan.bulanan')
     ->middleware('role:owner');
Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])
     ->name('laporan.export-pdf')
     ->middleware('role:owner');
Route::get('laporan/export-excel', [LaporanController::class, 'exportExcel'])
     ->name('laporan.export-excel')
     ->middleware('role:owner');

// Owner & Admin bisa akses
Route::resource('kategori', KategoriMenuController::class);
Route::resource('menu', MenuController::class);
Route::patch('menu/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])
     ->name('menu.toggle-status');
Route::resource('bahan-baku', BahanBakuController::class);
Route::get('stok/{bahan}/masuk', [StokController::class, 'masuk'])->name('stok.masuk');
Route::post('stok/{bahan}/masuk', [StokController::class, 'simpanMasuk'])->name('stok.simpan-masuk');
Route::get('stok/{bahan}/keluar', [StokController::class, 'keluar'])->name('stok.keluar');
Route::post('stok/{bahan}/keluar', [StokController::class, 'simpanKeluar'])->name('stok.simpan-keluar');
Route::resource('resep', ResepProdukController::class);
Route::get('resep/menu/{menu}', [ResepProdukController::class, 'byMenu'])->name('resep.by-menu');
Route::get('riwayat-stok', [RiwayatStokController::class, 'index'])->name('riwayat-stok.index');
Route::get('riwayat-stok/{bahan}', [RiwayatStokController::class, 'byBahan'])->name('riwayat-stok.by-bahan');
Route::get('transaksi', [TransaksiController::class, 'indexOwner'])->name('transaksi.index');
Route::get('transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');

// ROOT
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return auth()->user()->isOwner()
        ? redirect()->route('owner.dashboard')
        : redirect()->route('kasir.pos.index');
});