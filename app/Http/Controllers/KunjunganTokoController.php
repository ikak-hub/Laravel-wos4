<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Toko;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class KunjunganTokoController extends Controller
{
    // Halaman utama
    public function index()
    {
        $tokos = Toko::latest()->get();
        return view('kunjungan.index', compact('tokos'));
    }

    // POST /kunjungan/toko untuk menyimpan toko baru 
    public function storeToko(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:200',
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy'  => 'required|numeric|min:0',
        ]);

        // Generate barcode: TOKO00001, TOKO00002, …
        $last    = Toko::all()->map(fn($t) => (int) substr($t->barcode, 4))->max();
        $nextNum = $last ? $last + 1 : 1;
        $barcode = 'TOKO' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

        Toko::create([
            'barcode'   => $barcode,
            'nama_toko' => $request->nama_toko,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy'  => $request->accuracy,
        ]);

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Toko berhasil disimpan.',
            'data'    => ['barcode' => $barcode],
        ]);
    }

    // get kunjungan/toko/{barcode}  →  data toko (AJAX)
    public function getToko($barcode)
    {
        $toko = Toko::find($barcode);

        if (! $toko) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => "Toko dengan barcode \"{$barcode}\" tidak ditemukan.",
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Toko ditemukan.',
            'data'    => $toko,
        ]);
    }

    // get kunjungan/qrcode/{barcode}  →  gambar QR code
    public function qrCode($barcode)
    {
        $toko = Toko::findOrFail($barcode);

        $qrCode = new QrCode($toko->barcode);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString())
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    // DELETE kunjungan/toko/{barcode}  →  hapus toko
    public function deleteToko($barcode)
    {
        $toko = Toko::findOrFail($barcode);
        $toko->delete();

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Toko berhasil dihapus.',
            'data'    => null,
        ]);
    }
}
