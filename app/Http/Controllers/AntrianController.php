<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    // ─────────────────────────────────────────────
    //  HELPER: state default
    // ─────────────────────────────────────────────
    private function defaultState(): array
    {
        return [
            'menunggu'        => [],
            'dipanggil'       => null,
            'terlambat'       => [],
            'selesai'         => [],
            'riwayat'         => [],
            'next_counter'    => 1,
            'total_terdaftar' => 0,
        ];
    }

    // ─────────────────────────────────────────────
    //  HELPER: ambil state, rebuild dari DB jika kosong
    // ─────────────────────────────────────────────
    private function getState(): array
    {
        $state = Cache::get('antrian_state');

        if ($state !== null) {
            if (!isset($state['selesai']))   $state['selesai']   = [];
            if (!isset($state['terlambat'])) $state['terlambat'] = [];
            return $state;
        }

        // Rebuild dari DB
        $state   = $this->defaultState();
        $today   = now()->toDateString();
        $records = Antrian::whereDate('created_at', $today)
            ->orderBy('idantrian')
            ->get();

        $maxCounter = 0;

        foreach ($records as $a) {
            $num = (int) ltrim(substr($a->nomor_antrian, 1), '0') ?: 1;
            if ($num > $maxCounter) $maxCounter = $num;

            $base = [
                'id'           => $a->idantrian,
                'nomor'        => $a->nomor_antrian,
                'nama'         => $a->nama,
                'layanan'      => $a->layanan ?? 'Umum',
                'waktu_daftar' => $a->created_at->format('H:i'),
            ];

            match ($a->status) {
                Antrian::STATUS_MENUNGGU  => $state['menunggu'][]  = $base,
                Antrian::STATUS_DIPANGGIL => $state['menunggu'][]  = $base, // restore ke menunggu
                Antrian::STATUS_TERLAMBAT => $state['terlambat'][] = $base,
                Antrian::STATUS_SELESAI   => $state['selesai'][]   = array_merge($base, [
                    'loket' => $a->loket,
                    'waktu' => $a->updated_at->format('H:i'),
                ]),
                default => null,
            };
        }

        $state['next_counter']    = $maxCounter + 1;
        $state['total_terdaftar'] = $records->count();

        $this->putState($state);
        return $state;
    }

    private function putState(array $state): void
    {
        Cache::put('antrian_state', $state, now()->addHours(24));
    }

    // ─────────────────────────────────────────────
    //  GET /guest
    // ─────────────────────────────────────────────
    public function guestPage()
    {
        $layananList = Antrian::DAFTAR_LAYANAN;
        return view('antrian.guest', compact('layananList'));
    }

    // ─────────────────────────────────────────────
    //  POST /antrian/daftar
    // ─────────────────────────────────────────────
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama'    => 'required|string|max:255',
            'layanan' => 'required|string|max:100',
        ]);

        $state   = $this->getState();
        $counter = $state['next_counter'];
        $nomor   = 'A' . str_pad($counter, 3, '0', STR_PAD_LEFT);

        $antrian = Antrian::create([
            'nomor_antrian' => $nomor,
            'nama'          => $validated['nama'],
            'layanan'       => $validated['layanan'],
            'status'        => Antrian::STATUS_MENUNGGU,
        ]);

        $state['menunggu'][] = [
            'id'           => $antrian->idantrian,
            'nomor'        => $nomor,
            'nama'         => $validated['nama'],
            'layanan'      => $validated['layanan'],
            'waktu_daftar' => now()->format('H:i'),
        ];
        $state['next_counter']    = $counter + 1;
        $state['total_terdaftar'] = $state['total_terdaftar'] + 1;

        $this->putState($state);

        return response()->json([
            'success'   => true,
            'tiket_url' => route('antrian.tiket', $antrian->idantrian),
            'nomor'     => $nomor,
            'nama'      => $validated['nama'],
            'layanan'   => $validated['layanan'],
            'posisi'    => count($state['menunggu']),
        ]);
    }

    // ─────────────────────────────────────────────
    //  GET /antrian/tiket/{id}
    // ─────────────────────────────────────────────
    public function tiket($id)
    {
        $antrian = Antrian::findOrFail($id);
        $state   = $this->getState();
        $posisi  = null;
        foreach ($state['menunggu'] as $i => $item) {
            if ($item['id'] == $antrian->idantrian) {
                $posisi = $i + 1;
                break;
            }
        }
        return view('antrian.tiket', compact('antrian', 'posisi'));
    }

    // ─────────────────────────────────────────────
    //  GET /admin
    // ─────────────────────────────────────────────
    public function adminPage()
    {
        return view('antrian.admin');
    }

    // ─────────────────────────────────────────────
    //  POST /admin/panggil  — panggil antrian pertama
    // ─────────────────────────────────────────────
    public function panggil(Request $request)
    {
        $request->validate(['loket' => 'required|integer|min:1|max:20']);

        $state = $this->getState();

        if (empty($state['menunggu'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada antrian yang menunggu.',
            ], 422);
        }

        return $this->doCall(array_shift($state['menunggu']), $state, (int) $request->loket, false);
    }

    // ─────────────────────────────────────────────
    //  POST /admin/panggil-langsung/{id}
    //  Panggil pasien tertentu dari daftar menunggu
    // ─────────────────────────────────────────────
    public function panggilLangsung(Request $request, $id)
    {
        $request->validate(['loket' => 'required|integer|min:1|max:20']);

        $state = $this->getState();
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

        return $this->doCall($found, $state, (int) $request->loket, false);
    }

    // ─────────────────────────────────────────────
    //  POST /admin/selesai/{id}
    // ─────────────────────────────────────────────
    public function selesai($id)
    {
        $state = $this->getState();

        if (!$state['dipanggil'] || $state['dipanggil']['id'] != $id) {
            return response()->json(['success' => false, 'message' => 'Bukan antrian yang dipanggil.'], 422);
        }

        $done = $state['dipanggil'];
        $state['selesai'][] = array_merge($done, ['waktu' => now()->format('H:i')]);

        array_unshift($state['riwayat'], $done);
        $state['riwayat']  = array_slice($state['riwayat'], 0, 5);
        $state['dipanggil'] = null;

        Antrian::where('idantrian', $id)->update(['status' => Antrian::STATUS_SELESAI]);
        $this->putState($state);

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────
    //  POST /admin/tandai-terlambat/{id}
    // ─────────────────────────────────────────────
    public function tandaiTerlambat($id)
    {
        $state = $this->getState();
        $found = null;

        // Cari di menunggu
        foreach ($state['menunggu'] as $key => $item) {
            if ($item['id'] == $id) {
                $found = $item;
                unset($state['menunggu'][$key]);
                $state['menunggu'] = array_values($state['menunggu']);
                break;
            }
        }

        // Cari di dipanggil
        if (!$found && $state['dipanggil'] && $state['dipanggil']['id'] == $id) {
            $found = $state['dipanggil'];
            $state['dipanggil'] = null;
        }

        if (!$found) {
            return response()->json(['success' => false, 'message' => 'Tidak ditemukan.'], 404);
        }

        $state['terlambat'][] = $found;
        Antrian::where('idantrian', $id)->update(['status' => Antrian::STATUS_TERLAMBAT]);
        $this->putState($state);

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────
    //  POST /admin/panggil-terlambat/{id}
    // ─────────────────────────────────────────────
    public function panggilTerlambat(Request $request, $id)
    {
        $request->validate(['loket' => 'required|integer|min:1|max:20']);

        $state = $this->getState();
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

        return $this->doCall($found, $state, (int) $request->loket, true);
    }

    // ─────────────────────────────────────────────
    //  INTERNAL: eksekusi panggilan
    // ─────────────────────────────────────────────
    private function doCall(array $item, array $state, int $loket, bool $isTerlambat)
    {
        if ($state['dipanggil'] !== null) {
            array_unshift($state['riwayat'], $state['dipanggil']);
            $state['riwayat'] = array_slice($state['riwayat'], 0, 5);
        }

        $panggilan = [
            'id'           => $item['id'],
            'nomor'        => $item['nomor'],
            'nama'         => $item['nama'],
            'layanan'      => $item['layanan'] ?? 'Umum',
            'loket'        => $loket,
            'waktu'        => now()->format('H:i:s'),
            'ts'            => now()->timestamp,
            'waktu_daftar' => $item['waktu_daftar'] ?? '',
            'is_terlambat' => $isTerlambat,
        ];

        $state['dipanggil'] = $panggilan;

        Antrian::where('idantrian', $item['id'])
            ->update(['status' => Antrian::STATUS_DIPANGGIL, 'loket' => $loket]);

        $this->putState($state);

        return response()->json(['success' => true, 'data' => $panggilan]);
    }

    // ─────────────────────────────────────────────
    //  GET /papan
    // ─────────────────────────────────────────────
    public function papanPage()
    {
        return view('antrian.papan');
    }

    // ─────────────────────────────────────────────
    //  GET /sse/antrian
    // ─────────────────────────────────────────────
    public function stream(Request $request)
    {
        session()->save();

        return response()->stream(function () {
            set_time_limit(0);
            ignore_user_abort(true);

            $lastHash = null;

            while (true) {
                if (connection_aborted()) break;

                $state = $this->getState();
                $hash  = md5(json_encode($state));

                if ($hash !== $lastHash) {
                    // Event 1: queue-update (untuk admin — format lengkap)
                    echo 'event: queue-update' . PHP_EOL;
                    echo 'data: ' . json_encode($state) . PHP_EOL;
                    echo PHP_EOL;
                    // Event 2: antrian-update (untuk papan — format berbeda)
                    $papanData = [
                        'menunggu'  => $state['menunggu'] ?? [],
                        'dipanggil' => $state['dipanggil'] ?? null,
                    ];
                    echo 'event: antrian-update' . PHP_EOL;
                    echo 'data: ' . json_encode($papanData) . PHP_EOL;
                    echo PHP_EOL;

                    // Event 3: panggil — trigger suara di papan
                    if ($state['dipanggil']) {
                        $dp = $state['dipanggil'];
                        echo 'event: panggil' . PHP_EOL;
                        echo 'data: ' . json_encode([
                            'nomor_antrian' => $dp['nomor'],
                            'nama'          => $dp['nama'],
                            'loket'         => $dp['loket'],
                            'ts'            => $dp['waktu'] ?? time(),
                        ]) . PHP_EOL;
                        echo PHP_EOL;
                    }

                    ob_flush();
                    flush();
                    $lastHash = $hash;
                } else {
                    echo ': keep-alive ' . time() . PHP_EOL . PHP_EOL;
                    ob_flush();
                    flush();
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    //  POST /admin/reset
    public function reset()
    {
        Cache::forget('antrian_state');
        // Hapus semua data antrian hari ini dari DB
        Antrian::whereDate('created_at', now()->toDateString())->delete();
        return response()->json(['success' => true, 'message' => 'State antrian direset.']);
    }

    //  GET /antrian/state
    public function getStateJson(Request $request)
    {
        return response()->json($this->getState());
    }
}
