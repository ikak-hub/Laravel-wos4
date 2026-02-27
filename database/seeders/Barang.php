<?php

namespace Database\Seeders;
use illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
class BarangSeeder extends Seeder
{
    /**
     * Seed minimal 10 baris barang.
     * INSERT tanpa id_barang → trigger PostgreSQL (fn_set_id_barang) akan mengisinya.
     * Setiap insert diberi jeda 1 detik agar COUNT per-hari bertambah benar.
     */
    public function run(): void
    {
        $items = [
            ['nama' => 'Pensil 2B',           'harga' => 3500],
            ['nama' => 'Buku Tulis 40 Lembar', 'harga' => 5000],
            ['nama' => 'Penghapus Faber',      'harga' => 2500],
            ['nama' => 'Pulpen Pilot Hitam',   'harga' => 8000],
            ['nama' => 'Stabilo Hijau',        'harga' => 6000],
            ['nama' => 'Tipe-X Kenko',         'harga' => 4500],
            ['nama' => 'Rautan Kecil',         'harga' => 3000],
            ['nama' => 'Penggaris 30 cm',      'harga' => 7000],
            ['nama' => 'Spidol Merah',         'harga' => 5500],
            ['nama' => 'Map Plastik Bening',   'harga' => 4000],
            ['nama' => 'Gunting Kecil',        'harga' => 9500],
            ['nama' => 'Lem Kertas Stick',     'harga' => 3500],
        ];

        foreach ($items as $item) {
            // Tidak menyertakan id_barang → trigger mengisinya
            DB::insert(
                'INSERT INTO barangs (nama, harga, "timestamp") VALUES (?, ?, NOW())',
                [$item['nama'], $item['harga']]
            );
        }
    }
}