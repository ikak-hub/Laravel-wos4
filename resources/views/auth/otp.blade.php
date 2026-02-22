<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; width: 350px; }
        input { padding: 10px; font-size: 18px; width: 80%; letter-spacing: 5px; text-align: center; margin: 10px 0; }
        button { padding: 10px 20px; background-color: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #1d4ed8; }
        .resend { margin-top: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Verifikasi OTP</h2>
        <p>Masukkan kode OTP yang dikirim ke email Anda</p>
        
        @if(session('info'))
            <p style="color: green;">{{ session('info') }}</p>
        @endif
        
        <form action="{{ route('otp.verify') }}" method="POST">
            @csrf
            <input type="text" name="otp" placeholder="XXXXXX" maxlength="6" required autofocus>
            <br>
            <button type="submit">Verifikasi</button>
        </form>
        
        <div class="resend">
            <a href="{{ route('login') }}">Kirim Ulang OTP</a>
        </div>
    </div>
</body>
</html>