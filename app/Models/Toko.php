<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $table      = 'toko';
    protected $primaryKey = 'barcode';
    public    $incrementing = false;
    protected $keyType    = 'string';
 
    protected $fillable = [
        'barcode',
        'nama_toko',
        'latitude',
        'longitude',
        'accuracy',
    ];
}
