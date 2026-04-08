<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vendors')->insert([
            [
                'nama_vendor' => 'Kantin Bu Sari',
                'username'    => 'busari',
                'password'    => Hash::make('password123'),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nama_vendor' => 'Kantin Pak Budi',
                'username'    => 'pakbudi',
                'password'    => Hash::make('password123'),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}