<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

class KantinController extends Controller
{
    // ── Halaman utama customer ──────────────────────────────────────
    public function index()
    {
        $vendors = Vendor::all();
        return view('kantin.index', compact('vendors'));
    }

    // ── AJAX: Ambil menu berdasarkan vendor ─────────────────────────
    public function getMenus($idvendor)
    {
        $menus = Menu::where('idvendor', $idvendor)
            ->get(['idmenu', 'nama_menu', 'harga', 'path_gambar']);

        return response()->json([
            'status' => 'success',
            'data'   => $menus,
        ]);
    }

    // ── POST: Buat pesanan + dapatkan Snap Token Midtrans ───────────
    public function createOrder(Request $request)
    {
        $request->validate([
            'idvendor'          => 'required|exists:vendors,idvendor',
            'total'             => 'required|integer|min:1',
            'items'             => 'required|array|min:1',
            'items.*.idmenu'    => 'required|exists:menu,idmenu',
            'items.*.jumlah'    => 'required|integer|min:1',
            'items.*.harga'     => 'required|integer|min:0',
            'items.*.subtotal'  => 'required|integer|min:0',
        ]);

        // ── Generate nama guest (Guest_0000001, dst) ──────────────
        $lastGuest  = Pesanan::where('nama', 'like', 'Guest_%')
            ->orderBy('idpesanan', 'desc')
            ->value('nama');
        $nextNum    = 1;
        if ($lastGuest) {
            $nextNum = (int) substr($lastGuest, 6) + 1;
        }
        $guestName = 'Guest_' . str_pad($nextNum, 7, '0', STR_PAD_LEFT);
        $orderId   = 'KANTIN-' . time() . '-' . $nextNum;

        try {
            DB::beginTransaction();

            // Simpan pesanan
            $pesanan = Pesanan::create([
                'nama'               => $guestName,
                'total'              => $request->total,
                'status_bayar'       => 0,
                'idvendor'           => $request->idvendor,
                'midtrans_order_id'  => $orderId,
            ]);

            // Simpan detail
            foreach ($request->items as $item) {
                DetailPesanan::create([
                    'idmenu'    => $item['idmenu'],
                    'idpesanan' => $pesanan->idpesanan,
                    'jumlah'    => $item['jumlah'],
                    'harga'     => $item['harga'],
                    'subtotal'  => $item['subtotal'],
                    'catatan'   => $item['catatan'] ?? null,
                ]);
            }

            // ── Konfigurasi Midtrans ──────────────────────────────
            \Midtrans\Config::$serverKey    = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized  = true;
            \Midtrans\Config::$is3ds        = true;

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => (int) $request->total,
                ],
                'customer_details' => [
                    'first_name' => $guestName,
                ],
                'enabled_payments' => [
                    'bca_va',
                    'bni_va',
                    'bri_va',
                    'permata_va',
                    'other_va',
                    'gopay',
                    'qris',
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $pesanan->update(['snap_token' => $snapToken]);

            DB::commit();
            // Generate QR Code
            $qrCode = new QrCode($orderId);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Kirim base64 QR code ke frontend
            $qrBase64 = base64_encode($result->getString());

            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
                'guest_name' => $guestName,
                'idpesanan'  => $pesanan->idpesanan,
                'qr_code'    => 'data:image/png;base64,' . $qrBase64,  
            ]);

            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
                'guest_name' => $guestName,
                'idpesanan'  => $pesanan->idpesanan,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── POST: Webhook Midtrans (update status bayar) ────────────────

    public function notification(Request $request)
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        $notification = new \Midtrans\Notification();

        $orderId     = $notification->order_id;
        $transStatus = $notification->transaction_status;
        $fraudStatus = $notification->fraud_status;
        $paymentType = $notification->payment_type;

        // Ambil idpesanan dari order_id (format: KANTIN-{timestamp}-{idpesanan})
        $idPesanan = last(explode('-', $orderId));

        $pesanan = Pesanan::find($idPesanan);
        if (!$pesanan) return response('Not found', 404);

        if ($transStatus == 'capture' && $fraudStatus == 'accept') {
            $pesanan->status_bayar = 1;
            $pesanan->metode_bayar = $paymentType;
        } elseif ($transStatus == 'settlement') {
            $pesanan->status_bayar = 1;
            $pesanan->metode_bayar = $paymentType;
        } elseif (in_array($transStatus, ['cancel', 'deny', 'expire'])) {
            $pesanan->status_bayar = 0;
        }

        $pesanan->save();
        return response('OK', 200);
    }


    // ── GET: Cek status bayar (polling dari frontend) ───────────────
    public function checkPayment($idpesanan)
    {
        $pesanan = Pesanan::findOrFail($idpesanan);
        return response()->json([
            'status_bayar' => $pesanan->status_bayar,
            'nama'         => $pesanan->nama,
        ]);
    }
}
