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




Route::get('/', fn () => redirect()->route('login'));

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
});

// Midtrans Notification Webhook (exclude CSRF) 
Route::post('/midtrans/notification',[KantinController::class, 'notification'])->name('midtrans.notification');

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

    });
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
