<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Apotek Naufal</title>
    
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
                <h2 class="text-xl font-bold text-text-main mb-1">Selamat Datang Kembali</h2>
                <p class="text-sm text-text-muted">Silakan masuk ke akun Anda</p>
            </div>

            <form action="/login" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2" for="email">Email / Username</label>
                    <input type="text" id="email" name="email" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="Masukkan email atau username" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2" for="password">Password</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="Masukkan password Anda" required>
                </div>

                <div class="flex justify-between items-center text-sm pt-1 pb-2">
                    <label class="flex items-center gap-2 text-text-muted cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-primary focus:ring-primary cursor-pointer w-4 h-4"> Ingat Saya
                    </label>
                    <a href="#" class="text-primary font-medium hover:text-primary-dark hover:underline transition">Lupa Password?</a>
                </div>

                <button type="submit" class="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition">Masuk</button>
            </form>

            <div class="text-center mt-6 text-sm text-text-muted">
                Belum punya akun? <a href="/register" class="text-primary font-semibold hover:text-primary-dark hover:underline transition">Daftar Sekarang</a>
            </div>
        </div>
    </div>

</body>
</html>
