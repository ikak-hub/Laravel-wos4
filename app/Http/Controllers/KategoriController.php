<?php

namespace App\Http\Controllers;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{

    public function index()
    {
        $kategoris = Kategori::orderBy('idkategori', 'desc')->get();
        return view('kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:200',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('kategori.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('kategori.show', compact('kategori'));
    }

    public function edit(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:200',
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('kategori.index')
                         ->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy(string $id)
    {
        try {
            $kategori = Kategori::findOrFail($id);
            
            // Check if kategori has related books
            if ($kategori->bukus()->count() > 0) {
                return redirect()->route('kategori.index')
                                 ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki buku!');
            }
            
            $kategori->delete();
            
            return redirect()->route('kategori.index')
                             ->with('success', 'Kategori berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('kategori.index')
                             ->with('error', 'Terjadi kesalahan saat menghapus kategori!');
        }
    }
}