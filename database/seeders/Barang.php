<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Seed barang dengan ID yang mudah diingat untuk demo.
     *
     * CARA PAKAI:
     *   php artisan db:seed --class=BarangSeeder
     *
     * ATAU tambahkan di DatabaseSeeder.php:
     *   $this->call(BarangSeeder::class);
     * lalu jalankan: php artisan db:seed
     *
     * CATATAN:
     * Tabel barang menggunakan id_barang varchar(8) tanpa auto-increment di Laravel,
     * tapi di-generate oleh trigger PostgreSQL. Karena itu kita pakai INSERT manual
     * dengan ID yang kita tentukan sendiri agar mudah diingat saat demo.
     *
     * Jika error duplicate key, jalankan dulu:
     *   php artisan db:seed --class=BarangSeeder
     * atau hapus data lama dulu:
     *   DB::table('barang')->whereIn('id_barang', ['BRG00001', 'BRG00002', ...])->delete();
     */
    public function run(): void
    {
        // Cek dulu apakah data sudah ada (hindari duplicate)
        $existing = DB::table('barang')->whereIn('id_barang', [
            'BRG00001', 'BRG00002', 'BRG00003', 'BRG00004', 'BRG00005',
        ])->count();

        if ($existing > 0) {
            $this->command->info('Data barang demo sudah ada, skip seeder.');
            return;
        }

        $barangs = [
            ['id_barang' => 'BRG00001', 'nama' => 'Pensil 2B Faber',      'harga' => 3500],
            ['id_barang' => 'BRG00002', 'nama' => 'Pulpen Pilot G2',       'harga' => 8000],
            ['id_barang' => 'BRG00003', 'nama' => 'Buku Tulis 58 Lembar',  'harga' => 5000],
            ['id_barang' => 'BRG00004', 'nama' => 'Penghapus Staedtler',   'harga' => 4500],
            ['id_barang' => 'BRG00005', 'nama' => 'Stabilo Boss Kuning',   'harga' => 12000],
            ['id_barang' => 'BRG00006', 'nama' => 'Lem Kertas UHU',        'harga' => 7500],
            ['id_barang' => 'BRG00007', 'nama' => 'Penggaris 30cm',        'harga' => 6000],
        ];

        foreach ($barangs as $b) {
            DB::insert(
                'INSERT INTO barang (id_barang, nama, harga, "timestamp") VALUES (?, ?, ?, NOW())',
                [$b['id_barang'], $b['nama'], $b['harga']]
            );
        }

        $this->command->info('Berhasil menambahkan ' . count($barangs) . ' barang demo.');
        $this->command->table(
            ['ID Barang', 'Nama', 'Harga'],
            array_map(fn($b) => [$b['id_barang'], $b['nama'], 'Rp ' . number_format($b['harga'], 0, ',', '.')], $barangs)
        );
    }
}