<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $table      = 'antrian';
    protected $primaryKey = 'idantrian';

    protected $fillable = [
        'nomor_antrian',
        'nama',
        'layanan',
        'status',
        'loket',
    ];

    protected $casts = [
        'status' => 'integer',
        'loket'  => 'integer',
    ];

    const STATUS_MENUNGGU  = 0;
    const STATUS_DIPANGGIL = 1;
    const STATUS_TERLAMBAT = 2;
    const STATUS_SELESAI   = 3;

    const DAFTAR_LAYANAN = [
        'Umum',
        'Poli Jantung',
        'Poli Gigi',
        'Poli Mata',
        'Poli Anak',
        'Poli Kandungan',
        'Poli Bedah',
        'Poli THT',
        'Poli Saraf',
        'Poli Kulit & Kelamin',
        'IGD',
    ];
}