<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\BarangController;


Route::get('/', fn () => redirect()->route('login'));

Auth::routes();
Route::get('/auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware('guest')->group(function () {
    Route::get('/otp', [App\Http\Controllers\GoogleController::class, 'showOtpForm'])->name('otp.show');
    Route::post('/otp/verify', [App\Http\Controllers\GoogleController::class, 'verifyOtp'])->name('otp.verify');
    Route::get('/otp/resend', [App\Http\Controllers\OtpController::class, 'resend'])->name('otp.resend');   
});     

Route::middleware(['auth'])->group(function () {
   Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('kategori', KategoriController::class);
    Route::resource('buku', BukuController::class);

    Route::get('/barang/cetak', [App\Http\Controllers\BarangController::class, 'cetakForm'])->name('barang.cetak.form');
    Route::post('/barang/cetak', [App\Http\Controllers\BarangController::class, 'cetakPdf'])->name('barang.cetak.pdf');
    Route::resource('/barang', BarangController::class)->only(['index', 'store', 'update', 'destroy']);

    // PDF Routes
    Route::get('/pdf', [BukuController::class, 'generatePdf'])->name('pdf.index');
    Route::get('/pdf/sertifikat', [BukuController::class, 'previewSertifikat'])->name('pdf.sertifikat.preview');
    Route::get('/pdf/undangan', [BukuController::class, 'previewUndangan'])->name('pdf.undangan.preview');
    Route::get('/pdf/sertifikat', [BukuController::class, 'downloadSertifikat'])->name('pdf.sertifikat');
    Route::get('/pdf/undangan',   [BukuController::class, 'downloadUndangan'])->name('pdf.undangan');        
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
