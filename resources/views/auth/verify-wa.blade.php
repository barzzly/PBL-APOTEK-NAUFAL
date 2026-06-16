<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_apotek_naufal.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP Login | Apotek Naufal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, .heading-font {
            font-family: 'Outfit', sans-serif;
        }
        .mesh-bg {
            background-color: #faf9f0;
            background-image: 
                radial-gradient(at 0% 0%, rgba(230, 239, 229, 0.4) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(121, 174, 111, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(242, 237, 194, 0.3) 0px, transparent 50%);
        }
    </style>
</head>
<body class="mesh-bg text-text-main antialiased min-h-screen flex items-center justify-center p-5">

    <div class="w-full max-w-md bg-white rounded-3xl border border-border-muted p-8 sm:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.03)] flex flex-col justify-center gap-6">
        
        <!-- Header -->
        <div class="text-center">
            <a href="/" class="text-primary text-3xl font-bold flex items-center justify-center gap-2 mb-6 hover:text-primary-dark transition relative z-10">
                <img src="{{ asset('images/logo_apotek_naufal.png') }}" class="h-6 w-auto object-contain" alt="Logo Apotek Naufal"> Apotek Naufal
            </a>
            
            <!-- Icon Lock -->
            <div class="w-16 h-16 bg-primary-light text-primary rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 border border-green-100 shadow-inner">
                <i class="fa-solid fa-key-skeleton"></i>
            </div>
            
            <h2 class="text-xl font-bold tracking-tight text-gray-900 heading-font">Verifikasi Masuk</h2>
            <p class="text-xs text-text-muted mt-1 leading-relaxed px-4">Masukkan 6 digit kode OTP WhatsApp yang dikirim ke nomor telepon Anda:</p>
            <div class="mt-2 text-sm font-extrabold text-primary tracking-wide">
                +{{ session('otp_phone') }}
            </div>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="p-4 bg-emerald-50/70 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-start gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-base mt-0.5 shrink-0 text-emerald-500"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-4 bg-red-50/70 border border-red-200 text-red-700 rounded-2xl text-xs font-semibold flex items-start gap-2.5 shadow-sm animate-shake">
                <i class="fa-solid fa-circle-exclamation text-base mt-0.5 shrink-0 text-red-500"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <!-- Form -->
        <form action="/login-wa/verify" method="POST" class="space-y-5">
            @csrf
            
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block text-center" for="otp">Kode Keamanan OTP</label>
                <input type="text" id="otp" name="otp" maxLength="6" 
                    class="w-full px-4 py-3 bg-gray-50/50 border border-border-muted rounded-xl text-2xl text-center tracking-[0.6em] font-extrabold outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary-light transition" 
                    placeholder="------" required autofocus autocomplete="one-time-code">
                <p class="text-[10px] text-text-muted text-center leading-normal">OTP aman Anda hanya berlaku selama 5 menit.</p>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-3.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl text-xs shadow-md shadow-primary/10 hover:shadow-lg hover:shadow-primary/20 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-shield-check text-sm"></i> Verifikasi & Masuk
            </button>
        </form>

        <!-- OTP Resend & Back -->
        <div class="space-y-4 pt-4 border-t border-border-muted text-center text-xs">
            <div class="text-text-muted font-medium">
                <span id="timer-text">Kirim ulang kode dalam <span id="countdown" class="font-bold text-primary">05:00</span></span>
                
                <form id="resend-form" action="/login-wa" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="phone" value="{{ session('otp_phone') }}">
                    <button type="submit" class="text-primary font-bold hover:underline transition cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-sm mr-1"></i> Kirim Ulang OTP via WhatsApp
                    </button>
                </form>
            </div>
            
            <div>
                <a href="/login-wa" class="text-primary font-bold hover:underline transition flex items-center justify-center gap-1.5 cursor-pointer">
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
