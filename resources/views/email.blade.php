<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Login</title>
</head>
<body>
    <h2>Kode OTP Login Anda</h2>
    <p>Halo, {{ $user->name }}</p>
    <p>Kode OTP Anda adalah: <strong>{{ $otp }}</strong></p>
    <p>Kode ini akan berlaku selama 10 menit.</p>
</body>
</html>