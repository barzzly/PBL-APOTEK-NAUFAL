<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP | Apotek Naufal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg-body text-text-main font-sans antialiased min-h-screen flex items-center justify-center p-5">

    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-md p-8 sm:p-10">
            <div class="text-center mb-8">
                <a href="/" class="text-primary text-3xl font-bold flex items-center justify-center gap-2 mb-3 hover:text-primary-dark transition">
                    <i class="fa-solid fa-notes-medical"></i> Apotek Naufal
                </a>
                <h2 class="text-xl font-bold text-text-main mb-1">Verifikasi Kode OTP</h2>
                <p class="text-sm text-text-muted">Masukkan 6 digit kode yang dikirim ke WhatsApp Anda</p>
                <div class="mt-2 text-sm font-semibold text-primary">
                    +{{ session('otp_phone') }}
                </div>
            </div>

            @if (session('status'))
                <div class="bg-green-50 text-green-700 p-4 rounded-lg text-sm mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-lg text-sm mb-5">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/login-wa/verify" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2 text-center" for="otp">Kode OTP</label>
                    <input type="text" id="otp" name="otp" maxLength="6" class="w-full px-4 py-3 border border-border-muted rounded-lg text-lg text-center tracking-widest font-bold outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="------" required autofocus autocomplete="one-time-code">
                    <p class="text-xs text-text-muted mt-2 text-center">Kode hanya berlaku selama 5 menit.</p>
                </div>

                <button type="submit" class="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-shield-check text-lg"></i> Verifikasi & Masuk
                </button>
            </form>

            <div class="text-center mt-6 text-sm text-text-muted">
                <span id="timer-text">Kirim ulang kode dalam <span id="countdown" class="font-bold text-primary">05:00</span></span>
                <form id="resend-form" action="/login-wa" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="phone" value="{{ session('otp_phone') }}">
                    <button type="submit" class="text-primary font-semibold hover:text-primary-dark hover:underline transition">
                        Kirim Ulang OTP via WhatsApp
                    </button>
                </form>
            </div>
            
            <div class="text-center mt-4 text-sm">
                <a href="/login-wa" class="text-primary font-semibold hover:text-primary-dark hover:underline transition flex items-center justify-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Ganti Nomor Telepon
                </a>
            </div>
        </div>
    </div>

    <!-- Countdown Javascript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let secondsLeft = 300; // 5 minutes
            const countdownEl = document.getElementById('countdown');
            const timerTextEl = document.getElementById('timer-text');
            const resendFormEl = document.getElementById('resend-form');

            const timer = setInterval(() => {
                secondsLeft--;
                if (secondsLeft <= 0) {
                    clearInterval(timer);
                    timerTextEl.classList.add('hidden');
                    resendFormEl.classList.remove('hidden');
                } else {
                    const minutes = Math.floor(secondsLeft / 60);
                    const seconds = secondsLeft % 60;
                    countdownEl.textContent = 
                        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }
            }, 1000);
        });
    </script>

</body>
</html>
