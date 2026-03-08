<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class JsStudiController extends Controller
{
   /** Studi Kasus 1 – Button spinner pada form Tambah Buku */
    public function studi1()
    {
        $kategoris = Kategori::all();
        return view('js.studi1', compact('kategoris'));
    }

    /** Studi Kasus 2 & 3 – Tabel HTML biasa (no DB) */
    public function studi2Plain()
    {
        return view('js.studi2_plain');
    }

    /** Studi Kasus 2 & 3 – DataTables (no DB) */
    public function studi3Dt()
    {
        return view('js.studi3_dt');
    }

    /** Studi Kasus 4 – Select & Select2 */
    public function studi4()
    {
        return view('js.studi4');
    }
}
