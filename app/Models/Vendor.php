<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table      = 'vendors';
    protected $primaryKey = 'idvendor';
    protected $fillable   = ['nama_vendor', 'username', 'password'];
    protected $hidden     = ['password'];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'idvendor', 'idvendor');
    }
}
