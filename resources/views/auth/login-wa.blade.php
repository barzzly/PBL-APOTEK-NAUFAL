<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_apotek_naufal.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk dengan WhatsApp | Apotek Naufal</title>
    
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
            
            <!-- WhatsApp Bubble Icon -->
            <div class="w-16 h-16 bg-[#25d366]/10 text-[#25d366] rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 border border-green-50 shadow-inner">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            
            <h2 class="text-xl font-bold tracking-tight text-gray-900 heading-font">Masuk WhatsApp</h2>
            <p class="text-xs text-text-muted mt-1 leading-relaxed px-4">Kami akan mengirimkan kode keamanan OTP ke nomor WhatsApp terdaftar Anda.</p>
        </div>

        <!-- Errors Alert -->
        @if ($errors->any())
            <div class="p-4 bg-red-50/70 border border-red-200 text-red-700 rounded-2xl text-xs font-semibold flex items-start gap-2.5 shadow-sm animate-shake">
                <i class="fa-solid fa-circle-exclamation text-base mt-0.5 shrink-0 text-red-500"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <!-- Form -->
        <form action="/login-wa" method="POST" class="space-y-5">
            @csrf
            
            <!-- Phone input -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block" for="phone">Nomor WhatsApp</label>
                <div class="relative flex">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-gray-400 border-r border-border-muted my-2 pr-2.5 select-none">
                        +62
                    </span>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                        class="w-full pl-16 pr-4 py-3 bg-gray-50/50 border border-border-muted rounded-xl text-xs outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary-light transition font-semibold" 
                        placeholder="8123456789" required autofocus>
                </div>
                <p class="text-[10px] text-text-muted mt-1.5 leading-normal">Masukkan nomor WhatsApp aktif Anda tanpa angka 0 di depan.</p>
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-3.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl text-xs shadow-md shadow-primary/10 hover:shadow-lg hover:shadow-primary/20 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-paper-plane text-xs"></i> Kirim Kode Keamanan OTP
            </button>
        </form>

        <!-- Footer links -->
        <div class="space-y-4 pt-4 border-t border-border-muted text-center text-xs">
            <div class="text-text-muted">
                Belum memiliki akun Apotek Naufal? <a href="/register" class="text-primary font-bold hover:underline">Daftar Sekarang</a>
            </div>
            
            <div>
                <a href="/login" class="text-primary font-bold hover:underline transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-arrow-left"></i> Masuk dengan Email & Password
                </a>
            </div>
        </div>
        
    </div>

</body>
</html>
