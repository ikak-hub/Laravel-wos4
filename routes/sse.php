<?php

use App\Http\Controllers\AntrianController;
use Illuminate\Support\Facades\Route;

// Route ini TIDAK kena middleware web (tidak ada session lock)
Route::get('/antrian/state', [AntrianController::class, 'getStateJson'])
    ->name('antrian.state');

Route::get('/sse/antrian', [AntrianController::class, 'stream'])
    ->name('antrian.stream');