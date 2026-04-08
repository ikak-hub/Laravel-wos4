<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;

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
            'items.*.idmenu'    => 'required|exists:menus,idmenu',
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
                    'bca_va', 'bni_va', 'bri_va', 'permata_va',
                    'other_va', 'gopay', 'qris',
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $pesanan->update(['snap_token' => $snapToken]);

            DB::commit();

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
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $notification        = new \Midtrans\Notification();
            $transactionStatus   = $notification->transaction_status;
            $orderId             = $notification->order_id;
            $paymentType         = $notification->payment_type;
            $fraudStatus         = $notification->fraud_status ?? '';

            $pesanan = Pesanan::where('midtrans_order_id', $orderId)->first();
            if (!$pesanan) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                if ($fraudStatus === 'accept' || empty($fraudStatus)) {
                    $pesanan->update([
                        'status_bayar' => 1,            // LUNAS
                        'metode_bayar' => $paymentType,
                    ]);
                }
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $pesanan->update(['status_bayar' => 2]);  // Batal
            }

            return response()->json(['status' => 'OK']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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
