<?php
namespace App\Http\Controllers;
use App\Models\Kategori;
use App\Models\Buku;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung statistik
        $totalKategori = Kategori::count();
        $totalBuku = Buku::count();
        $totalUser = User::count();
        
        // Ambil data terbaru
        $latestBukus = Buku::with('kategori')
                          ->orderBy('created_at', 'desc')
                          ->take(5)
                          ->get();
        
        // Hitung buku per kategori
        $bukuPerKategori = Kategori::withCount('bukus')
                                   ->orderBy('bukus_count', 'desc')
                                   ->get();
        
        return view('dashboard', compact(
            'totalKategori',
            'totalBuku',
            'totalUser',
            'latestBukus',
            'bukuPerKategori'
        ));
    }
}

