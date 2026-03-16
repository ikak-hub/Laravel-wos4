<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
 
class AjaxStudiController extends Controller
{
    //  SK 1 – Cascading Wilayah
    //  Source data: https://ibnux.github.io/data-indonesia/
 
    /** Halaman wilayah */
    public function wilayah()
    {
        return view('ajax.studi5_wilayah');
    }
 
    /**
     * GET /ajax/wilayah/provinsi
     * Kembalikan daftar provinsi dalam format JSON standar.
     */
    public function getProvinsi()
    {
        try {
            $resp = Http::timeout(10)->get('https://ibnux.github.io/data-indonesia/provinsi.json');
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Data provinsi berhasil diambil',
                'data'    => $resp->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }
 
    /**
     * GET /ajax/wilayah/kota/{provinsiId}
     */
    public function getKota($provinsiId)
    {
        try {
            $resp = Http::timeout(10)->get("https://ibnux.github.io/data-indonesia/kabupaten/{$provinsiId}.json");
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Data kota/kabupaten berhasil diambil',
                'data'    => $resp->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }
 
    /**
     * GET /ajax/wilayah/kecamatan/{kotaId}
     */
    public function getKecamatan($kotaId)
    {
        try {
            $resp = Http::timeout(10)->get("https://ibnux.github.io/data-indonesia/kecamatan/{$kotaId}.json");
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Data kecamatan berhasil diambil',
                'data'    => $resp->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }
 
    /**
     * GET /ajax/wilayah/kelurahan/{kecamatanId}
     */
    public function getKelurahan($kecId)
    {
        try {
            $resp = Http::timeout(10)->get("https://ibnux.github.io/data-indonesia/kelurahan/{$kecId}.json");
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Data kelurahan berhasil diambil',
                'data'    => $resp->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }
 
    // ════════════════════════════════════════════════════════════════
    //  SK 2 – Point of Sales (POS)
    // ════════════════════════════════════════════════════════════════
 
    /** Halaman POS */
    public function pos()
    {
        return view('ajax.studi6_pos');
    }
 
    /**
     * GET /ajax/pos/cari/{kode}
     * Cari barang berdasarkan id_barang (kode).
     * Response standar: { status, code, message, data }
     */
    public function cariBarang($kode)
    {
        $barang = Barang::find($kode);
 
        if (! $barang) {
            return response()->json([
                'status'  => 'error',
                'code'    => 404,
                'message' => 'Barang dengan kode "' . $kode . '" tidak ditemukan.',
                'data'    => null,
            ], 404);
        }
 
        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => 'Barang ditemukan.',
            'data'    => [
                'id_barang' => $barang->id_barang,
                'nama'      => $barang->nama,
                'harga'     => $barang->harga,
            ],
        ]);
    }
 
    /**
     * POST /ajax/pos/bayar
     * Simpan transaksi ke tabel penjualan dan penjualan_detail.
     *
     * Body JSON:
     * {
     *   "total": 50000,
     *   "items": [
     *     { "id_barang": "BRG0001", "jumlah": 2, "subtotal": 10000 },
     *     ...
     *   ]
     * }
     */
    public function bayar(Request $request)
    {
        $request->validate([
            'total'             => 'required|integer|min:0',
            'items'             => 'required|array|min:1',
            'items.*.id_barang' => 'required|string|exists:barang,id_barang',
            'items.*.jumlah'    => 'required|integer|min:1',
            'items.*.subtotal'  => 'required|integer|min:0',
        ]);
 
        try {
            DB::transaction(function () use ($request) {
                // Insert ke tabel penjualan
                $penjualan = Penjualan::create([
                    'timestamp' => now(),
                    'total'     => $request->total,
                ]);
 
                // Insert ke tabel penjualan_detail
                foreach ($request->items as $item) {
                    PenjualanDetail::create([
                        'id_penjualan' => $penjualan->id_penjualan,
                        'id_barang'    => $item['id_barang'],
                        'jumlah'       => $item['jumlah'],
                        'subtotal'     => $item['subtotal'],
                    ]);
                }
            });
 
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Transaksi berhasil disimpan!',
                'data'    => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }
}
