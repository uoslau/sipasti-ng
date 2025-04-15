<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\PetugasKegiatanController;

Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('auth');

Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index')->middleware('auth');
Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store')->middleware('auth');
Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show')->middleware('auth');
Route::get('/kegiatan/{kegiatan}/edit-kegiatan', [KegiatanController::class, 'edit'])->name('kegiatan.edit')->middleware('auth');
Route::put('/kegiatan/{kegiatan}', [KegiatanController::class, 'update'])->name('kegiatan.update')->middleware('auth');
Route::delete('/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy')->middleware('auth');

Route::delete('/kegiatan/{kegiatan}/{petugasKegiatan}', [PetugasKegiatanController::class, 'destroy'])->name('petugas.destroy')->middleware('auth');
Route::get('search-mitra', [PetugasKegiatanController::class, 'search'])->name('petugas.search')->middleware('auth');
Route::post('/kegiatan/{kegiatan}', [PetugasKegiatanController::class, 'store'])->name('petugas.store')->middleware('auth');
Route::get('/kegiatan/{kegiatan}/{petugasKegiatan}/edit-petugas', [PetugasKegiatanController::class, 'edit'])->name('petugas.edit')->middleware('auth');
Route::put('/kegiatan/{kegiatan}/{petugasKegiatan}', [PetugasKegiatanController::class, 'update'])->name('petugas.update')->middleware('auth');
Route::post('/kegiatan/{kegiatan}/petugas-import', [PetugasKegiatanController::class, 'import'])->name('petugas.import')->middleware('auth');

Route::get('/mitra', [MitraController::class, 'index'])->name('mitra.index')->middleware('auth');

Route::get('/kegiatan/download/{kegiatan}', [DownloadController::class, 'downloadBAST'])->name('kegiatan.download')->middleware('auth');
Route::get('/kontrak/{slug}', [DownloadController::class, 'downloadKontrak'])->name('kontrak.download')->middleware('auth');

Route::get('/download/{nama_file}', function ($nama_file) {
    $filePath = storage_path("app/public/template/{$nama_file}");

    if (!file_exists($filePath)) {
        abort(404, 'File tidak ditemukan');
    }
    return Response::download($filePath, $nama_file);
})->name('file.download');
