<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP</title>
    {{-- Gunakan asset yang sudah ada di project --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .otp-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-align: center;
        }

        .otp-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .otp-icon i {
            font-size: 32px;
            color: white;
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #718096;
            font-size: 14px;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        /* OTP Input Container */
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .otp-input {
            width: 52px;
            height: 56px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            color: #2d3748;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #f7fafc;
        }

        .otp-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.2);
            background: #fff;
        }

        .otp-input.filled {
            border-color: #667eea;
            background: #eef2ff;
        }

        /* Hidden actual input */
        #otp-hidden {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .btn-verify {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            letter-spacing: 0.5px;
        }

        .btn-verify:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-verify:active { transform: translateY(0); }
        .btn-verify:disabled { opacity: 0.6; cursor: not-allowed; }

        .resend-section {
            margin-top: 20px;
            font-size: 14px;
            color: #718096;
        }

        .resend-section a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        .resend-section a:hover { text-decoration: underline; }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: left;
        }
        .alert-danger { background: #fff5f5; border: 1px solid #fed7d7; color: #c53030; }
        .alert-info   { background: #ebf8ff; border: 1px solid #bee3f8; color: #2b6cb0; }
        .alert-success{ background: #f0fff4; border: 1px solid #c6f6d5; color: #276749; }

        .timer {
            font-size: 13px;
            color: #e53e3e;
            margin-top: 6px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="otp-card">
        {{-- Icon --}}
        <div class="otp-icon">
            <i class="mdi mdi-shield-key-outline"></i>
        </div>

        <h2>Verifikasi OTP</h2>
        <p class="subtitle">
            Kode OTP 6 karakter telah dikirim ke email Anda.<br>
            Masukkan kode tersebut di bawah ini.
        </p>

        {{-- Alert Messages --}}
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="mdi mdi-alert-circle-outline"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                <i class="mdi mdi-information-outline"></i> {{ session('info') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="mdi mdi-check-circle-outline"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="mdi mdi-alert-circle-outline"></i> {{ $errors->first() }}
            </div>
        @endif

        {{-- OTP Form --}}
        <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
            @csrf
            {{-- Hidden input yang menyimpan nilai OTP gabungan --}}
            <input type="hidden" name="otp" id="otp-hidden">

            {{-- 6 Kotak Input OTP --}}
            <div class="otp-inputs">
                @for($i = 1; $i <= 6; $i++)
                    <input type="text"
                           class="otp-input"
                           id="otp-box-{{ $i }}"
                           maxlength="1"
                           pattern="[A-Za-z0-9]"
                           autocomplete="off"
                           inputmode="text">
                @endfor
            </div>

            <button type="submit" class="btn-verify" id="btn-verify" disabled>
                <i class="mdi mdi-check-circle-outline"></i> Verifikasi OTP
            </button>
        </form>

        {{-- Countdown Timer --}}
        <div class="timer" id="timer">OTP berlaku selama: <span id="countdown">10:00</span></div>

        {{-- Resend OTP --}}
        <div class="resend-section">
            Tidak menerima kode?
            <a href="{{ route('otp.resend') }}">Kirim ulang OTP</a>
        </div>
    </div>

    <script>
        // === OTP Input Logic ===
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('otp-hidden');
        const btnVerify = document.getElementById('btn-verify');

        function updateHiddenInput() {
            let otp = '';
            inputs.forEach(inp => otp += inp.value.toUpperCase());
            hiddenInput.value = otp;
            btnVerify.disabled = otp.length < 6;
        }

        inputs.forEach((input, index) => {
            input.addEventListener('input', function (e) {
                // Hanya izinkan huruf dan angka
                this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(-1);

                if (this.value) {
                    this.classList.add('filled');
                    // Fokus ke input berikutnya
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else {
                    this.classList.remove('filled');
                }
                updateHiddenInput();
            });

            input.addEventListener('keydown', function (e) {
                // Backspace: kosongkan dan kembali ke input sebelumnya
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = '';
                    inputs[index - 1].classList.remove('filled');
                    updateHiddenInput();
                }
                // Arrow keys navigation
                if (e.key === 'ArrowLeft' && index > 0) inputs[index - 1].focus();
                if (e.key === 'ArrowRight' && index < inputs.length - 1) inputs[index + 1].focus();
            });

            // Handle paste — distribusikan karakter ke masing-masing box
            input.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData)
                    .getData('text')
                    .replace(/[^A-Za-z0-9]/g, '')
                    .toUpperCase()
                    .slice(0, 6);

                paste.split('').forEach((char, i) => {
                    if (inputs[index + i]) {
                        inputs[index + i].value = char;
                        inputs[index + i].classList.add('filled');
                    }
                });

                const nextEmpty = index + paste.length;
                if (nextEmpty < inputs.length) inputs[nextEmpty].focus();
                updateHiddenInput();
            });
        });

        // Fokus ke box pertama saat halaman dimuat
        inputs[0].focus();

        // === Countdown Timer (10 menit) ===
        let totalSeconds = 10 * 60;
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(() => {
            totalSeconds--;
            const minutes = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
            const seconds = (totalSeconds % 60).toString().padStart(2, '0');
            countdownEl.textContent = `${minutes}:${seconds}`;
   
            if (totalSeconds <= 0) {
                clearInterval(timer);
                countdownEl.textContent = 'Kadaluarsa';
                btnVerify.disabled = true;
                document.getElementById('timer').style.color = '#718096';
            }
        }, 1000);

        // Auto submit ketika 6 karakter sudah terisi
        document.getElementById('otpForm').addEventListener('submit', function (e) {
            const otp = hiddenInput.value;
            if (otp.length < 6) {
                e.preventDefault();
                alert('Masukkan 6 karakter OTP terlebih dahulu!');
            }
        });
    </script>
</body>
</html>