<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\MenuApiController;
use App\Http\Controllers\Api\StokApiController;
use App\Http\Controllers\Api\TransaksiApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SIM Coffee
|--------------------------------------------------------------------------
| Semua route di sini otomatis dapat prefix "/api" (bawaan Laravel).
| Contoh: POST /api/v1/login
|
| Role yang ada: owner, admin, kasir
*/

// ============================================================
// AUTH — tidak butuh token
// ============================================================
Route::post('/v1/login', [AuthApiController::class, 'login']);

// ============================================================
// SEMUA ROUTE DI BAWAH INI BUTUH TOKEN (Bearer Token / Sanctum)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth ---
    Route::post('/v1/logout', [AuthApiController::class, 'logout']);
    Route::get('/v1/me', [AuthApiController::class, 'me']);

    // --- Menu ---
    Route::get('/v1/menus', [MenuApiController::class, 'index']);
    Route::get('/v1/menus/{id}', [MenuApiController::class, 'show']);

    Route::middleware('role.api:admin,owner')->group(function () {
        Route::post('/v1/menus', [MenuApiController::class, 'store']);
        Route::put('/v1/menus/{id}', [MenuApiController::class, 'update']);
        Route::delete('/v1/menus/{id}', [MenuApiController::class, 'destroy']);
    });

    // --- Bahan Baku & Riwayat Stok ---
    Route::get('/v1/bahan-baku', [StokApiController::class, 'index']);
    Route::get('/v1/bahan-baku/menipis', [StokApiController::class, 'menipis']);
    Route::get('/v1/bahan-baku/fast-moving', [StokApiController::class, 'fastMoving']);
    Route::get('/v1/bahan-baku/nilai-total', [StokApiController::class, 'nilaiTotal']);
    Route::get('/v1/bahan-baku/{id}', [StokApiController::class, 'show']);
    Route::get('/v1/riwayat-stok', [StokApiController::class, 'riwayat']);
    Route::get('/v1/riwayat-stok/bahan/{bahanId}', [StokApiController::class, 'riwayatByBahan']);

    Route::middleware('role.api:owner,admin')->group(function () {
        Route::post('/v1/bahan-baku', [StokApiController::class, 'store']);
        Route::put('/v1/bahan-baku/{id}', [StokApiController::class, 'update']);
        Route::delete('/v1/bahan-baku/{id}', [StokApiController::class, 'destroy']);
        Route::post('/v1/bahan-baku/{id}/masuk', [StokApiController::class, 'stokMasuk']);
        Route::post('/v1/bahan-baku/{id}/keluar', [StokApiController::class, 'stokKeluar']);
    });

    // --- Transaksi ---
    Route::get('/v1/transaksi', [TransaksiApiController::class, 'index']);
    Route::get('/v1/transaksi/{id}', [TransaksiApiController::class, 'show']);

    Route::middleware('role.api:kasir,owner')->group(function () {
        Route::post('/v1/transaksi', [TransaksiApiController::class, 'store']);
        Route::post('/v1/transaksi/{id}/cancel', [TransaksiApiController::class, 'cancel']);
    });
});
