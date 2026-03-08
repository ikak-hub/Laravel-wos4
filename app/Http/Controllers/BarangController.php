<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::latest('timestamp')->get();
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        // Tanpa id_barang → trigger PostgreSQL mengisi otomatis
        DB::insert(
            'INSERT INTO barang (nama, harga, "timestamp") VALUES (?, ?, NOW())',
            [$request->nama, $request->harga]
        );

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        $barang->update(['nama' => $request->nama, 'harga' => $request->harga]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diupdate!');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function cetakForm()
    {
        $barang = Barang::latest('timestamp')->get();
        return view('barang.cetak', compact('barang'));
    }

    public function cetakPdf(Request $request)
    {
        $validated = $request->validate([
            'start_x' => 'required|integer|min:1|max:5',
            'start_y' => 'required|integer|min:1|max:8',
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'required|string|exists:barang,id_barang',
        ]);

        $map   = Barang::whereIn('id_barang', $validated['ids'])->get()->keyBy('id_barang');
        $items = collect($validated['ids'])
            ->map(fn($id) => $map[$id] ?? null)->filter()
            ->map(fn($barang)  => [
                'id_barang' => $barang->id_barang,
                'nama'      => $barang->nama,
                'harga'     => $barang->harga,
                'timestamp' => $barang->timestamp,
            ])->values()->toArray();

        $start_x = (int) $validated['start_x'];
        $start_y = (int) $validated['start_y'];

        // Ukuran kertas TnJ No. 108: 222mm × 185mm → dalam point
        $pdf = Pdf::loadView('barang.pdf_label', compact('items', 'start_x', 'start_y'))
                  ->setPaper([0, 0, 629.29, 524.41])
                  ->setWarnings(false);

        return $pdf->stream('tag_harga_' . now()->format('YmdHis') . '.pdf');
    }
}