<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'bukus';
    protected $primaryKey = 'idbuku';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['kode', 'judul', 'pengarang', 'idkategori'];
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'idkategori');
    }

    public function index()
    {
        $totalBukus = Buku::count();
        $totalKategoris = Kategori::count();
        return view('pages.dashboard', compact('totalBukus', 'totalKategoris'));
    }
}
