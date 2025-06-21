<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// rak
Route::get('rak', [AuthController::class, 'Rak']);
Route::post('storerak', [AuthController::class, 'storerak']);
Route::delete('/deleterak/{id}', [AuthController::class, 'deleterak']);
Route::put('/updaterak/{id}', [AuthController::class, 'updaterak']);

// obat
Route::get('obat', [AuthController::class, 'Obat']);
Route::post('storeobat', [AuthController::class, 'storeobat']);
Route::put('/updateobat/{id}', [AuthController::class, 'updateObat']);
Route::delete('/deleteobat/{id}', [AuthController::class, 'deleteObat']);

// transaksi
Route::get('obat/transaksi/{id}', [AuthController::class, 'Transaksi']);
Route::post('/tambahtransaksi', [AuthController::class, 'tambahtransaksi']);
Route::put('/edittransaksi/{id}', [AuthController::class, 'edittransaksi']);
Route::delete('/hapustransaksi/{id}', [AuthController::class, 'hapustransaksi']);


Route::get('kadaluarsa', [AuthController::class, 'getObatKadaluarsaPerBulan']);
Route::get('prediksi', [AuthController::class, 'prediksiObatKadaluarsaBulanDepan']);

Route::get('notifikasi', [AuthController::class, 'notifikasi']);

// riwayat
Route::get('riwayat-kadaluarsa', [AuthController::class, 'riwayat']);

// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
//     Route::get('/admin', function () {
//         return 'This is an admin route';
//     });
// });
