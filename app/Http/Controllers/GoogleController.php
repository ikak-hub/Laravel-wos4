<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                             ->with('error', 'Login Google gagal. Silakan coba lagi.');
        }

        // Cari user berdasarkan id_google atau email
        $user = User::where('id_google', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if (!$user) {
            // Buat user baru jika belum ada
            $user = User::create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'id_google' => $googleUser->getId(),
                'password'  => Hash::make(uniqid()),
            ]);
        } else {
            // Update id_google jika login pertama kali via Google
            if (!$user->id_google) {
                $user->update(['id_google' => $googleUser->getId()]);
            }
        }

        // Generate OTP dan simpan ke database
        $otp = $this->generateAndSaveOtp($user);

        // Kirim OTP ke email user
        $this->sendOtpEmail($user, $otp);

        // Simpan user ID di session untuk verifikasi OTP
        session(['otp_user_id' => $user->id]);

        // Redirect ke halaman input OTP
        return redirect()->route('otp.show')
                         ->with('info', 'Kode OTP telah dikirim ke email ' . $user->email);
    }

    // Generate OTP 6 karakter (huruf dan angka) dan simpan ke DB
    private function generateAndSaveOtp(User $user): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $otp = '';
        for ($i = 0; $i < 6; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $user->update([
            'otp'            => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        return $otp;
    }

    // Kirim email OTP ke user
    private function sendOtpEmail(User $user, string $otp): void
    {
        Mail::send('email', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name ?? 'User')
                    ->subject('Kode OTP Login - Aplikasi Perpustakaan');
        });
    }

    // Show OTP Form
    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.otp');
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Session expired. Silakan login lagi.');
        }

        // Check OTP
        if ($user->otp !== $request->otp) {
            return back()->with('error', 'Kode OTP salah!');
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->with('error', 'Kode OTP sudah expired. Silakan login lagi.');
        }

        // OTP valid, hapus OTP dari DB dan login user
        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        Auth::login($user);
        session()->forget('otp_user_id');

        return redirect()->intended('/dashboard')->with('success', 'Login berhasil!');
    }
}