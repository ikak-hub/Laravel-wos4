<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table      = 'customers';
    protected $primaryKey = 'idcustomer';
    protected $fillable   = ['nama', 'email', 'foto_blob', 'foto_path'];
}
