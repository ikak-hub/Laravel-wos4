<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_google',    // Menambahkan id_google ke fillable agar bisa diisi saat membuat atau mengupdate user
        'otp',          // Menambahkan otp ke fillable agar bisa diisi saat membuat atau mengupdate user
        'otp_expires_at',   // Menambahkan otp_expires_at ke fillable agar bisa diisi saat membuat atau mengupdate user
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp', // Ini penting untuk menyembunyikan OTP agar tidak terlihat saat user data diserialisasi, misalnya saat dikirim sebagai response API
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime', // Pastikan OTP expires_at juga dicast ke datetime agar mudah digunakan dalam logika aplikasi
            'password' => 'hashed',
        ];
    }
}
