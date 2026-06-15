<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk dengan WhatsApp | Apotek Naufal</title>
    
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
                <h2 class="text-xl font-bold text-text-main mb-1">Masuk dengan WhatsApp</h2>
                <p class="text-sm text-text-muted">Kami akan mengirimkan kode OTP ke WhatsApp Anda</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-lg text-sm mb-5">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/login-wa" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2" for="phone">Nomor WhatsApp</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-text-muted text-sm font-semibold border-r border-border-muted pr-2 my-2">
                            +62
                        </span>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="w-full pl-16 pr-4 py-3 border border-border-muted rounded-lg text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="8123456789" required autofocus>
                    </div>
                    <p class="text-xs text-text-muted mt-2">Masukkan nomor WhatsApp aktif Anda tanpa angka 0 di depan.</p>
                </div>

                <button type="submit" class="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Kode OTP
                </button>
            </form>

            <div class="text-center mt-6 text-sm text-text-muted">
                Belum punya akun? <a href="/register" class="text-primary font-semibold hover:text-primary-dark hover:underline transition">Daftar Sekarang</a>
            </div>
            
            <div class="text-center mt-4 text-sm">
                <a href="/login" class="text-primary font-semibold hover:text-primary-dark hover:underline transition flex items-center justify-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Masuk dengan Email & Password
                </a>
            </div>
        </div>
    </div>

</body>
</html>
