<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table      = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    public    $incrementing = true;
    protected $keyType    = 'int';
    public    $timestamps = false; // kolom timestamp dikelola manual
 
    protected $fillable = ['timestamp', 'total'];
 
    protected $casts = [
        'timestamp' => 'datetime',
        'total'     => 'integer',
    ];
 
    public function details()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan', 'id_penjualan');
    }
}
