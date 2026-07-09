<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_apotek_naufal.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Apotek Naufal</title>
    
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
<body class="mesh-bg text-text-main antialiased min-h-screen flex items-stretch">

    <div class="w-full grid grid-cols-1 lg:grid-cols-12 min-h-screen">
        
        <!-- Left Pane: Branding & Features (Visible on large screens) -->
        <div class="hidden lg:flex lg:col-span-5 bg-gradient-to-br from-primary-dark via-primary to-secondary text-white p-12 flex-col justify-between relative overflow-hidden shadow-2xl">
            <!-- Background Deco -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <!-- Logo & Brand -->
            <a href="/" class="flex items-center gap-3 w-fit hover:opacity-90 transition relative z-10">
                <img src="{{ asset('images/logo_apotek_naufal.png') }}" class="w-12 h-12 object-contain rounded-2xl bg-white/10 border border-white/20 p-1.5 shadow-inner" alt="Logo Apotek Naufal">
                <span class="text-xl font-bold tracking-tight heading-font">Apotek Naufal</span>
            </a>

            <!-- Welcome Copy -->
            <div class="relative z-10 space-y-6 my-auto">
                <h1 class="text-4xl font-extrabold leading-tight tracking-tight heading-font">
                    Solusi Kesehatan Terpercaya dalam Satu Genggaman.
                </h1>
                <p class="text-white/80 text-sm leading-relaxed">
                    Apotek Naufal menyediakan obat-obatan berkualitas, tebus resep praktis, dan konsultasi apoteker profesional secara cepat, aman, dan berlisensi resmi.
                </p>

                <!-- Value Props List -->
                <div class="space-y-4 pt-6 border-t border-white/10">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-prescription text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold heading-font">Tebus Resep Online</h3>
                            <p class="text-xs text-white/70 mt-0.5">Unggah resep dokter Anda dan biarkan kami menyiapkannya.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-truck-fast text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold heading-font">Pengiriman Cepat & Instan</h3>
                            <p class="text-xs text-white/70 mt-0.5">Obat dikirim langsung ke alamat Anda dengan aman.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-shield-check text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold heading-font">100% Produk Original</h3>
                            <p class="text-xs text-white/70 mt-0.5">Seluruh persediaan obat terjamin keasliannya dan terdaftar resmi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer copy -->
            <div class="text-xs text-white/50 relative z-10">
                &copy; 2026 Apotek Naufal. Seluruh hak cipta dilindungi.
            </div>
        </div>

        <!-- Right Pane: Login Form Container -->
        <div class="col-span-1 lg:col-span-7 flex items-center justify-center p-6 sm:p-12 relative">
            <div class="w-full max-w-md bg-white rounded-3xl border border-border-muted p-8 sm:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.03)] flex flex-col justify-center gap-6">
                
                <!-- Heading -->
                <div class="text-center sm:text-left">
                    <!-- Mobile Logo only -->
                    <div class="flex lg:hidden justify-center mb-6">
                        <a href="/" class="flex items-center gap-2.5">
                            <img src="{{ asset('images/logo_apotek_naufal.png') }}" class="w-10 h-10 object-contain rounded-xl bg-primary/10 border border-primary/20 p-1.5 shadow-md" alt="Logo Apotek Naufal">
                            <span class="text-lg font-bold tracking-tight text-primary heading-font">Apotek Naufal</span>
                        </a>
                    </div>
                    
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 heading-font">Selamat Datang Kembali</h2>
                    <p class="text-xs text-text-muted mt-1 leading-normal">Silakan masuk menggunakan akun terdaftar Anda.</p>
                </div>

                <!-- Session / Errors Alert -->
                @if($errors->any())
                    <div class="p-4 bg-red-50/70 border border-red-200 text-red-700 rounded-2xl text-xs font-semibold flex items-start gap-2.5 shadow-sm animate-shake">
                        <i class="fa-solid fa-circle-exclamation text-base mt-0.5 shrink-0 text-red-500"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold flex items-start gap-2.5 shadow-sm">
                        <i class="fa-solid fa-circle-check text-base mt-0.5 shrink-0 text-emerald-500"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <!-- Form -->
                <form action="/login" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Username/Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block" for="email">Email / Nomor WhatsApp</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="text" id="email" name="email" value="{{ old('email') }}" 
                                class="w-full pl-10 pr-4 py-3 bg-gray-50/50 ui-input rounded-xl text-xs outline-none transition font-medium" 
                                placeholder="Masukkan email atau nomor WA Anda" required autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-bold text-gray-700 uppercase tracking-wider block" for="password">Password</label>
                            <a href="{{ route('login.wa') }}" class="text-xs font-semibold text-primary hover:underline">Lupa Password?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password" 
                                class="w-full pl-10 pr-10 py-3 bg-gray-50/50 ui-input rounded-xl text-xs outline-none transition font-medium" 
                                placeholder="Masukkan password Anda" required>
                            <!-- Password Toggle Eye -->
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                                <i class="fa-solid fa-eye" id="password-eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center pt-1">
                        <label class="flex items-center gap-2.5 text-xs text-text-muted font-medium cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="rounded border-border-muted text-primary focus:ring-primary cursor-pointer w-4 h-4 transition"> 
                            Ingat sesi masuk saya
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full py-3.5 mt-2 ui-btn-primary font-bold rounded-xl text-xs shadow-md shadow-primary/10 hover:shadow-lg hover:shadow-primary/20 transition-all duration-200 cursor-pointer">
                        Masuk ke Akun
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-border-muted"></div>
                    <span class="flex-shrink mx-3 text-[10px] text-text-muted font-bold uppercase tracking-wider">Atau Masuk Lebih Cepat</span>
                    <div class="flex-grow border-t border-border-muted"></div>
                </div>

                <!-- WA Login Button -->
                <a href="/login-wa" class="w-full py-3.5 border border-primary text-primary font-bold rounded-xl text-xs hover:bg-primary-light transition flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                    <i class="fa-brands fa-whatsapp text-sm"></i> Masuk Tanpa Password (OTP WA)
                </a>

                <!-- Register footer links -->
                <div class="text-center text-xs text-text-muted mt-2">
                    Belum memiliki akun Apotek Naufal? <a href="/register" class="text-primary font-bold hover:underline">Daftar Sekarang</a>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Password visibility toggle script -->
    <script>
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('password-eye-icon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
