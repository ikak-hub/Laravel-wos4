<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // ─────────────────────────────────────────────
    //  HELPER: state default (kosong)
    // ─────────────────────────────────────────────
    private function defaultState(): array
    {
        return [
            'menunggu'         => [],   // [{id, nomor, nama, waktu_daftar}]
            'dipanggil'        => null, // {id, nomor, nama, loket, waktu, is_terlambat?}
            'terlambat'        => [],   // [{id, nomor, nama}]
            'riwayat'          => [],   // 5 panggilan terakhir
            'next_counter'     => 1,
            'total_terdaftar'  => 0,
        ];
    }

    // ─────────────────────────────────────────────
    //  GET /guest  →  halaman daftar antrian
    // ─────────────────────────────────────────────
    public function guestPage()
    {
        return view('antrian.guest');
    }

    // ─────────────────────────────────────────────
    //  POST /antrian/daftar  →  simpan & return JSON
    //  (dipanggil via fetch dari guest.blade)
    // ─────────────────────────────────────────────
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $state   = Cache::get('antrian_state', $this->defaultState());
        $counter = $state['next_counter'];
        $nomor   = 'A' . str_pad($counter, 3, '0', STR_PAD_LEFT);

        // Simpan ke database
        $antrian = Antrian::create([
            'nomor_antrian' => $nomor,
            'nama'          => $validated['nama'],
            'status'        => Antrian::STATUS_MENUNGGU,
        ]);

        // Update cache
        $state['menunggu'][] = [
            'id'           => $antrian->idantrian,
            'nomor'        => $nomor,
            'nama'         => $validated['nama'],
            'waktu_daftar' => now()->format('H:i:s'),
        ];
        $state['next_counter']    = $counter + 1;
        $state['total_terdaftar'] = $state['total_terdaftar'] + 1;

        Cache::put('antrian_state', $state, now()->addHours(24));

        return response()->json([
            'success'   => true,
            'tiket_url' => route('antrian.tiket', $antrian->idantrian),
            'nomor'     => $nomor,
            'nama'      => $validated['nama'],
            'posisi'    => count($state['menunggu']),
        ]);
    }

    // ─────────────────────────────────────────────
    //  GET /antrian/tiket/{id}  →  halaman tiket cetak
    // ─────────────────────────────────────────────
    public function tiket($id)
    {
        $antrian = Antrian::findOrFail($id);
        // Hitung posisi dalam antrian saat ini
        $state  = Cache::get('antrian_state', $this->defaultState());
        $posisi = null;
        foreach ($state['menunggu'] as $i => $item) {
            if ($item['id'] == $antrian->idantrian) {
                $posisi = $i + 1;
                break;
            }
        }
        return view('antrian.tiket', compact('antrian', 'posisi'));
    }

    // ─────────────────────────────────────────────
    //  GET /admin  →  halaman admin
    // ─────────────────────────────────────────────
    public function adminPage()
    {
        return view('antrian.admin');
    }

    // ─────────────────────────────────────────────
    //  POST /admin/panggil  →  panggil nomor berikutnya
    //  Body: { loket: int }
    // ─────────────────────────────────────────────
    public function panggil(Request $request)
    {
        $request->validate([
            'loket' => 'required|integer|min:1|max:20',
        ]);

        $state = Cache::get('antrian_state', $this->defaultState());

        if (empty($state['menunggu'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada antrian yang menunggu.',
            ], 422);
        }

        // Ambil antrian pertama
        $next  = array_shift($state['menunggu']);
        $loket = (int) $request->loket;

        // Simpan panggilan sebelumnya ke riwayat
        if ($state['dipanggil'] !== null) {
            array_unshift($state['riwayat'], $state['dipanggil']);
            $state['riwayat'] = array_slice($state['riwayat'], 0, 5);
        }

        $panggilan = [
            'id'           => $next['id'],
            'nomor'        => $next['nomor'],
            'nama'         => $next['nama'],
            'loket'        => $loket,
            'waktu'        => now()->format('H:i:s'),
            'is_terlambat' => false,
        ];

        $state['dipanggil'] = $panggilan;

        // Update DB
        Antrian::where('idantrian', $next['id'])
            ->update(['status' => Antrian::STATUS_DIPANGGIL, 'loket' => $loket]);

        Cache::put('antrian_state', $state, now()->addHours(24));

        return response()->json(['success' => true, 'data' => $panggilan]);
    }

    // ─────────────────────────────────────────────
    //  POST /admin/tandai-terlambat/{id}
    //  Pindahkan dari menunggu → terlambat
    // ─────────────────────────────────────────────
    public function tandaiTerlambat($id)
    {
        $state = Cache::get('antrian_state', $this->defaultState());

        $found = null;
        foreach ($state['menunggu'] as $key => $item) {
            if ($item['id'] == $id) {
                $found = $item;
                unset($state['menunggu'][$key]);
                $state['menunggu'] = array_values($state['menunggu']);
                break;
            }
        }

        if (!$found) {
            return response()->json(['success' => false, 'message' => 'Tidak ditemukan.'], 404);
        }

        $state['terlambat'][] = $found;
        Antrian::where('idantrian', $id)->update(['status' => Antrian::STATUS_TERLAMBAT]);
        Cache::put('antrian_state', $state, now()->addHours(24));

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────
    //  POST /admin/panggil-terlambat/{id}
    //  Panggil ulang dari daftar terlambat
    //  Body: { loket: int }
    // ─────────────────────────────────────────────
    public function panggilTerlambat(Request $request, $id)
    {
        $request->validate([
            'loket' => 'required|integer|min:1|max:20',
        ]);

        $state = Cache::get('antrian_state', $this->defaultState());

        $found = null;
        foreach ($state['terlambat'] as $key => $item) {
            if ($item['id'] == $id) {
                $found = $item;
                unset($state['terlambat'][$key]);
                $state['terlambat'] = array_values($state['terlambat']);
                break;
            }
        }

        if (!$found) {
            return response()->json(['success' => false, 'message' => 'Tidak ditemukan.'], 404);
        }

        $loket = (int) $request->loket;

        if ($state['dipanggil'] !== null) {
            array_unshift($state['riwayat'], $state['dipanggil']);
            $state['riwayat'] = array_slice($state['riwayat'], 0, 5);
        }

        $panggilan = [
            'id'           => $found['id'],
            'nomor'        => $found['nomor'],
            'nama'         => $found['nama'],
            'loket'        => $loket,
            'waktu'        => now()->format('H:i:s'),
            'is_terlambat' => true,
        ];

        $state['dipanggil'] = $panggilan;
        Antrian::where('idantrian', $id)
            ->update(['status' => Antrian::STATUS_DIPANGGIL, 'loket' => $loket]);
        Cache::put('antrian_state', $state, now()->addHours(24));

        return response()->json(['success' => true, 'data' => $panggilan]);
    }

    // ─────────────────────────────────────────────
    //  GET /papan  →  layar antrian publik
    // ─────────────────────────────────────────────
    public function papanPage()
    {
        return view('antrian.papan');
    }

    // ─────────────────────────────────────────────
    //  GET /sse/antrian  →  SSE stream
    // ─────────────────────────────────────────────
    public function stream(Request $request)
    {
        return response()->stream(function () {
            // Cegah PHP timeout pada koneksi SSE yang panjang
            set_time_limit(0);

            $lastHash = null;

            while (true) {
                if (connection_aborted()) {
                    break;
                }

                $state     = Cache::get('antrian_state', $this->defaultState());
                $hash      = md5(json_encode($state));

                if ($hash !== $lastHash) {
                    // Ada perubahan data → kirim event
                    echo 'event: queue-update' . PHP_EOL;
                    echo 'data: ' . json_encode($state) . PHP_EOL;
                    echo PHP_EOL; // baris kosong = akhir satu pesan SSE
                    ob_flush();
                    flush();
                    $lastHash = $hash;
                } else {
                    // Tidak ada perubahan → kirim keep-alive agar koneksi tidak drop
                    echo ': keep-alive ' . time() . PHP_EOL . PHP_EOL;
                    ob_flush();
                    flush();
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',   // penting untuk Nginx
            'Connection'        => 'keep-alive',
        ]);
    }

    // ─────────────────────────────────────────────
    //  POST /admin/reset  →  hapus semua state
    // ─────────────────────────────────────────────
    public function reset()
    {
        Cache::forget('antrian_state');
        return response()->json(['success' => true, 'message' => 'State antrian direset.']);
    }

    // ─────────────────────────────────────────────
    //  GET /antrian/state  →  ambil state saat ini (JSON)
    //  Untuk initial load sebelum SSE terhubung
    // ─────────────────────────────────────────────
    public function getState()
    {
        $state = Cache::get('antrian_state', $this->defaultState());
        return response()->json($state);
    }
}