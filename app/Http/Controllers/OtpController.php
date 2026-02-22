<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function show()
    {
       // Melihat user yang sedang mencoba login dengan OTP
        if (!session('otp_user_id')) {
            return redirect()->route('login')
                             ->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        return view('auth.otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus 6 karakter.',
        ]);

        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                             ->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')
                             ->with('error', 'User tidak ditemukan.');
        }

        // Cek apakah OTP sudah kadaluarsa
        if ($user->otp_expires_at && now()->isAfter($user->otp_expires_at)) {
            return back()->with('error', 'Kode OTP telah kadaluarsa. Silakan login kembali.');
        }

        // Verifikasi OTP (case-insensitive)
        if (strtoupper($request->otp) !== strtoupper($user->otp)) {
            return back()->with('error', 'Kode OTP tidak valid. Silakan coba lagi.');
        }

        // OTP valid — hapus OTP dari DB dan buat sesi login
        $user->update([
            'otp'            => null,
            'otp_expires_at' => null,
        ]);

        // Hapus session OTP
        session()->forget('otp_user_id');

        // Login user
        Auth::login($user);

        // Redirect ke dashboard
        return redirect()->route('dashboard')
                         ->with('success', 'Login berhasil! Selamat datang, ' . ($user->name ?? $user->email));
    }

    /**
     * Kirim ulang OTP
     */
    public function resend()
    {
        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                             ->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // Generate OTP baru
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $otp = '';
        for ($i = 0; $i < 6; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Kirim ulang email
        Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name ?? 'User')
                    ->subject('Kode OTP Login (Resend) - Aplikasi Perpustakaan');
        });

        return back()->with('info', 'Kode OTP baru telah dikirim ke email ' . $user->email);
    }
}