<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    protected $primaryKey = 'idantrian';
 
    protected $fillable = [
        'nomor_antrian',
        'nama',
        'status',
        'loket',
    ];
 
    protected $casts = [
        'status' => 'integer',
        'loket'  => 'integer',
    ];
 
    // Status constants
    const STATUS_MENUNGGU   = 0;
    const STATUS_DIPANGGIL  = 1;
    const STATUS_TERLAMBAT  = 2;
}
