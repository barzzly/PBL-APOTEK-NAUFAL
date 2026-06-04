<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Apotek Naufal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg-body text-text-main font-sans antialiased min-h-screen flex items-center justify-center p-5 py-10">

    <div class="w-full max-w-lg">
        <div class="bg-white rounded-xl shadow-md p-8 sm:p-10">
            <div class="text-center mb-8">
                <a href="/" class="text-primary text-3xl font-bold flex items-center justify-center gap-2 mb-3 hover:text-primary-dark transition">
                    <i class="fa-solid fa-notes-medical"></i> Apotek Naufal
                </a>
                <h2 class="text-xl font-bold text-text-main mb-1">Daftar Akun Baru</h2>
                <p class="text-sm text-text-muted">Bergabunglah untuk mulai belanja</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2" for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="Masukkan nama lengkap Anda" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="Masukkan email Anda" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2" for="phone">Nomor Telepon (WhatsApp)</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="Contoh: 081234567890" required>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-text-main mb-2" for="password">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="Buat password" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-text-main mb-2" for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="Ulangi password" required>
                    </div>
                </div>

                <div class="text-xs text-text-muted leading-relaxed py-2">
                    Dengan mendaftar, Anda menyetujui <a href="#" class="text-primary hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-primary hover:underline">Kebijakan Privasi</a> Apotek Naufal.
                </div>

                <button type="submit" class="w-full py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition mt-2">Daftar</button>
            </form>

            <div class="text-center mt-6 text-sm text-text-muted">
                Sudah punya akun? <a href="/login" class="text-primary font-semibold hover:text-primary-dark hover:underline transition">Masuk di sini</a>
            </div>
        </div>
    </div>

</body>
</html>
