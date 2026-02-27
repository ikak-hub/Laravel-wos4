<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    
    protected $primaryKey = 'id_barang';
    public    $incrementing = false;
    protected $keyType      = 'string';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'harga',
    ];

    protected $casts = [
        'harga'     => 'integer',
        'timestamp' => 'datetime',
    ];
}
