<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScannerController extends Controller
{
    
    // Halaman Barcode Scanner (Praktikum 1).
    // Menampilkan view scanner dengan kamera untuk membaca label harga.
    public function barcode()
    {
        return view('scanner.barcode');
    }
}