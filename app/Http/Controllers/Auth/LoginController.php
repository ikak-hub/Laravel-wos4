<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Socialite\Socialite;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '../dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    protected function authenticated(Request $request, $user)
    {
        return redirect('/dashboard');
    }
    public function google_redirect()
    {
        return Socialite::driver('google')->redirect();
    }
    public function google_callback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Cari user berdasarkan email
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt('google-login'),
            ]
        );

        // Login user sementara
        Auth::login($user);

        // 🔥 Generate OTP hanya untuk Google login
        $otp = $this->generateAndSaveOtp($user);
        $this->sendOtpEmail($user, $otp);

        Auth::logout(); // logout dulu sampai OTP benar

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.show')
            ->with('info', 'Kode OTP telah dikirim ke email ' . $user->email);
    }
}
