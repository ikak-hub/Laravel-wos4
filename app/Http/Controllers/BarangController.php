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
        $barang = Barang::orderBy('timestamp', 'desc')->get();
        return view('barang.index', compact('barang'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);
        DB::insert("INSERT INTO barang (nama, harga, timestamp) VALUES (?, ?, NOW())", [
            $request->nama,
            $request->harga,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
    }
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama'  => 'required|string|max:50',
            'harga' => 'required|integer|min:0',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update([
            'nama'  => $request->nama,
            'harga' => $request->harga,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui!');
    }
    public function destroy(string $id)
    {
        Barang::findOrFail($id)->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function cetakForm()
    {
        $barang = Barang::orderBy('timestamp', 'desc')->get();
        return view('barang.cetak', compact('barang'));
    }


    // Generate PDF for label printing on TnJ No 108 paper (5 cols x 8 rows = 40 labels)
    public function cetakPdf(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:barang,id_barang',
            'start_x' => 'required|integer|min:1|max:5',
            'start_y' => 'required|integer|min:1|max:8',
        ]);

        $selectedIds = $request->ids;
        $barang = Barang::whereIn('id_barang', $selectedIds)->get()->keyBy('id_barang');

        // Reorder by selected order
        $selectedBarang = collect($selectedIds)->map(fn($id) => $barang[$id])->filter();

        $startX = (int) $request->start_x; // column 1-5
        $startY = (int) $request->start_y; // row 1-8

        // Convert to 0-based linear index
        $startIndex = ($startY - 1) * 5 + ($startX - 1);

        $pdf = Pdf::loadView('barang.pdf_label', [
            'barang'    => $selectedBarang,
            'startIndex' => $startIndex,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('tag_harga_' . now()->format('YmdHis') . '.pdf');
    }
}
