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
use App\Http\Controllers\KantinController;
use App\Http\Controllers\VendorController;
use App\Http\Middleware\CheckVendorSession;
use App\Http\Controllers\AntrianController;




Route::get('/', fn() => redirect()->route('login'));

Auth::routes();
Route::get('/auth/google', [App\Http\Controllers\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');


Route::middleware('guest')->group(function () {
    Route::get('/otp', [App\Http\Controllers\GoogleController::class, 'showOtpForm'])->name('otp.show');
    Route::post('/otp/verify', [App\Http\Controllers\GoogleController::class, 'verifyOtp'])->name('otp.verify');
    Route::get('/otp/resend', [App\Http\Controllers\OtpController::class, 'resend'])->name('otp.resend');
});

Route::prefix('kantin')->name('kantin.')->group(function () {
    Route::get('/',                    [App\Http\Controllers\KantinController::class, 'index'])->name('index');
    Route::get('/menu/{idvendor}',     [App\Http\Controllers\KantinController::class, 'getMenus'])->name('menu');
    Route::post('/order',              [App\Http\Controllers\KantinController::class, 'createOrder'])->name('order');
    Route::get('/check/{idpesanan}',   [App\Http\Controllers\KantinController::class, 'checkPayment'])->name('check');
    // Halaman QR Code pesanan customer 
    Route::get('/pesanan',             [App\Http\Controllers\KantinController::class, 'pesananPage'])->name('pesanan');
});

// Midtrans Notification Webhook (exclude CSRF) 
Route::post('/midtrans/notification', [KantinController::class, 'notification'])->name('midtrans.notification');

// Vendor Panel 
Route::prefix('kantor')->name('kantor.')->group(function () {
    // Login (public)
    Route::get('/login',  [App\Http\Controllers\VendorController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\VendorController::class, 'login'])->name('login.post');
    Route::get('/logout', [App\Http\Controllers\VendorController::class, 'logout'])->name('logout');


    // Protected (cek session vendor_id)
    Route::middleware([CheckVendorSession::class])->group(function () {
        Route::get('/dashboard',        [App\Http\Controllers\VendorController::class, 'dashboard'])->name('dashboard');
        Route::get('/menu',             [App\Http\Controllers\VendorController::class, 'menuIndex'])->name('menu');
        Route::post('/menu',            [App\Http\Controllers\VendorController::class, 'menuStore'])->name('menu.store');
        Route::put('/menu/{id}',        [App\Http\Controllers\VendorController::class, 'menuUpdate'])->name('menu.update');
        Route::delete('/menu/{id}',     [App\Http\Controllers\VendorController::class, 'menuDestroy'])->name('menu.destroy');
        Route::get('/orders',           [App\Http\Controllers\VendorController::class, 'orders'])->name('orders');
        Route::get('/scan',                  [App\Http\Controllers\VendorController::class, 'scanPage'])->name('scan');
        Route::get('/scan-result/{orderId}', [App\Http\Controllers\VendorController::class, 'scanResult'])->name('scan.result');
    });
});
// Customer
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/',             [App\Http\Controllers\CustomerController::class, 'index'])->name('index');
    Route::get('/tambah-blob',  [App\Http\Controllers\CustomerController::class, 'createBlob'])->name('create.blob');
    Route::post('/tambah-blob', [App\Http\Controllers\CustomerController::class, 'storeBlob'])->name('store.blob');
    Route::get('/tambah-file',  [App\Http\Controllers\CustomerController::class, 'createFile'])->name('create.file');
    Route::post('/tambah-file', [App\Http\Controllers\CustomerController::class, 'storeFile'])->name('store.file');
});
// ── Halaman Guest (daftar antrian) ──────────────────────────
Route::get('/guest',            [AntrianController::class, 'guestPage'])->name('antrian.guest');
Route::post('/antrian/daftar',  [AntrianController::class, 'register'])->name('antrian.register');
Route::get('/antrian/tiket/{id}', [AntrianController::class, 'tiket'])->name('antrian.tiket');
 
// ── Halaman Admin ────────────────────────────────────────────
Route::get('/admin',  [AntrianController::class, 'adminPage'])->name('antrian.admin');
Route::post('/admin/panggil',                    [AntrianController::class, 'panggil'])->name('antrian.panggil');
Route::post('/admin/tandai-terlambat/{id}',      [AntrianController::class, 'tandaiTerlambat'])->name('antrian.tandaiTerlambat');
Route::post('/admin/panggil-terlambat/{id}',     [AntrianController::class, 'panggilTerlambat'])->name('antrian.panggilTerlambat');
Route::post('/admin/reset',                      [AntrianController::class, 'reset'])->name('antrian.reset');
 
// ── Halaman Papan Antrian (layar publik) ─────────────────────
Route::get('/papan', [AntrianController::class, 'papanPage'])->name('antrian.papan');
 
// ── SSE Stream & API State ───────────────────────────────────
Route::get('/sse/antrian',   [AntrianController::class, 'stream'])->name('antrian.stream');
Route::get('/antrian/state', [AntrianController::class, 'getState'])->name('antrian.state');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('kategori', KategoriController::class);
    Route::resource('buku', BukuController::class);

    Route::get('/barang/cetak', [App\Http\Controllers\BarangController::class, 'cetakForm'])->name('barang.cetak.form');
    Route::post('/barang/cetak', [App\Http\Controllers\BarangController::class, 'cetakPdf'])->name('barang.cetak.pdf');
    Route::resource('barang', BarangController::class)->only(['index', 'store', 'update', 'destroy', 'edit']);
    Route::resource('barang', BarangController::class);

    // PDF Routes
    Route::get('/pdf', [BukuController::class, 'generatePdf'])->name('pdf.index');
    Route::get('/pdf/sertifikat', [BukuController::class, 'previewSertifikat'])->name('pdf.sertifikat.preview');
    Route::get('/pdf/undangan', [BukuController::class, 'previewUndangan'])->name('pdf.undangan.preview');
    Route::get('/pdf/sertifikat', [BukuController::class, 'downloadSertifikat'])->name('pdf.sertifikat');
    Route::get('/pdf/undangan',   [BukuController::class, 'downloadUndangan'])->name('pdf.undangan');

    // Javascript studi kasus
    Route::get('/js/studi1', [App\Http\Controllers\JsStudiController::class, 'studi1'])->name('js.studi1');
    Route::get('/js/studi2', [App\Http\Controllers\JsStudiController::class, 'studi2Plain'])->name('js.studi2_plain');
    Route::get('/js/studi3', [App\Http\Controllers\JsStudiController::class, 'studi3Dt'])->name('js.studi3_dt');
    Route::get('/js/studi4', [App\Http\Controllers\JsStudiController::class, 'studi4'])->name('js.studi4');

    // AJAX Studi Kasus 
    // SK 1 – Cascading Wilayah
    Route::get('/ajax/wilayah',                     [App\Http\Controllers\AjaxStudiController::class, 'wilayah'])->name('ajax.wilayah');
    Route::get('/ajax/wilayah/provinsi',             [App\Http\Controllers\AjaxStudiController::class, 'getProvinsi'])->name('ajax.wilayah.provinsi');
    Route::get('/ajax/wilayah/kota/{provinsiId}',    [App\Http\Controllers\AjaxStudiController::class, 'getKota'])->name('ajax.wilayah.kota');
    Route::get('/ajax/wilayah/kecamatan/{kotaId}',   [App\Http\Controllers\AjaxStudiController::class, 'getKecamatan'])->name('ajax.wilayah.kecamatan');
    Route::get('/ajax/wilayah/kelurahan/{kecId}',    [App\Http\Controllers\AjaxStudiController::class, 'getKelurahan'])->name('ajax.wilayah.kelurahan');

    // SK 2 – Point of Sales
    Route::get('/ajax/pos',              [App\Http\Controllers\AjaxStudiController::class, 'pos'])->name('ajax.pos');
    Route::get('/ajax/pos/cari/{kode}',  [App\Http\Controllers\AjaxStudiController::class, 'cariBarang'])->name('ajax.pos.cari');
    Route::post('/ajax/pos/bayar',       [App\Http\Controllers\AjaxStudiController::class, 'bayar'])->name('ajax.pos.bayar');

    // Barcode Scanner
    Route::get('/scanner/barcode', [App\Http\Controllers\ScannerController::class, 'barcode'])->name('scanner.barcode');

    // Kunjungan Toko
    Route::get('/kunjungan', [App\Http\Controllers\KunjunganTokoController::class, 'index'])->name('kunjungan.index');
    Route::post('/kunjungan/toko', [App\Http\Controllers\KunjunganTokoController::class, 'storeToko'])->name('kunjungan.toko.store');
    Route::get('/kunjungan/toko/{barcode}', [App\Http\Controllers\KunjunganTokoController::class, 'getToko'])->name('kunjungan.toko.get');
    Route::get('/kunjungan/qrcode/{barcode}', [App\Http\Controllers\KunjunganTokoController::class, 'qrCode'])->name('kunjungan.qrcode');
    Route::delete('/kunjungan/toko/{barcode}', [App\Http\Controllers\KunjunganTokoController::class, 'deleteToko'])->name('kunjungan.toko.delete'); 
    
});
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
