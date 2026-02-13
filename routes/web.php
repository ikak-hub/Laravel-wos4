<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

   Route::get('/home', function () {
       return view('home');
   })->name('home');

   Route::resource('kategori', KategoriController::class);
   Route::resource('buku', BukuController::class);
});

