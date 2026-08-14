<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_apotek_naufal.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metode Pembayaran - Apotek Naufal</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Informasi lengkap opsi metode pembayaran yang aman di Apotek Naufal: Transfer Bank, QRIS, dan Tunai.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, .heading-font { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex flex-col min-h-screen">

    <!-- Header -->
    <header class="ui-glass-nav py-3 sticky top-0 z-50 border-b">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4 lg:gap-8">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <img src="{{ asset('images/logo_apotek_naufal.png') }}" class="h-8 w-auto object-contain" alt="Logo Apotek Naufal"> Apotek Naufal
            </a>

            <form action="{{ route('home') }}" method="GET" class="flex-grow w-full lg:w-auto order-3 lg:order-none relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari obat, vitamin, atau suplemen..." 
                    class="w-full py-3 px-5 pr-12 ui-input rounded-full text-sm outline-none transition header-search-input">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-primary text-lg cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <div class="flex items-center gap-5">
                <a href="{{ route('cart.index') }}" class="text-text-main hover:text-primary text-xl relative transition">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span id="cart-badge" class="absolute -top-2 -right-2.5 bg-secondary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $cartCount ?? 0 }}
                    </span>
                </a>
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-primary hover:bg-primary-dark transition border border-transparent flex items-center gap-2"><i class="fa-solid fa-gauge-high"></i> Panel Admin</a>
                        @else
                            <a href="{{ route('orders.history') }}" class="px-3 py-2 text-xs font-semibold text-primary hover:underline flex items-center gap-1.5"><i class="fa-solid fa-receipt"></i> Pesanan Saya</a>
                            <a href="{{ route('tickets.history') }}" class="px-3 py-2 text-xs font-semibold text-primary hover:underline flex items-center gap-1.5"><i class="fa-solid fa-ticket"></i> Ticket Saya</a>
                            <a href="{{ route('profile.edit') }}" class="px-3 py-2 text-sm font-semibold text-text-main flex items-center gap-2 hover:text-primary transition">
                                <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center overflow-hidden border border-gray-100 shadow-sm">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ str_starts_with(auth()->user()->avatar, '/') ? auth()->user()->avatar : '/' . auth()->user()->avatar }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                {{ auth()->user()->name }}
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="ml-2">
                                @csrf
                                <button type="submit" class="p-2 text-text-muted hover:text-red-500 transition" title="Keluar"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
                            </form>
                        @endif
                    @else
                        <a href="/login" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-primary bg-white border border-primary hover:bg-primary-light transition">Masuk</a>
                        <a href="/register" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-primary hover:bg-primary-dark transition border border-transparent">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-3 mb-6">
            <a href="/" class="text-text-muted hover:text-primary transition text-xs"><i class="fa-solid fa-house"></i> Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-text-muted"></i>
            <span class="text-text-main font-semibold text-xs">Metode Pembayaran</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Sidebar -->
            <aside class="lg:col-span-4 sticky top-24 space-y-6 lg:block hidden">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 px-2">Pilihan Pembayaran</h3>
                    <nav class="space-y-1">
                        <a href="#transfer-bank" class="block px-3 py-2 rounded-xl text-xs font-semibold text-primary bg-primary-light">1. Transfer Bank</a>
                        <a href="#qris" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary">2. QRIS (Semua E-Wallet & M-Banking)</a>
                        <a href="#cash" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary">3. Tunai / Bayar di Kasir (Pickup)</a>
                        <a href="#konfirmasi" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary">4. Konfirmasi Pembayaran</a>
                    </nav>
                </div>
            </aside>

            <!-- Main Document -->
            <div class="lg:col-span-8 bg-white p-6 md:p-10 rounded-3xl border border-gray-100 shadow-sm space-y-8">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider mb-3">
                        <i class="fa-solid fa-credit-card"></i> Informasi Pembayaran
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 heading-font mb-3">Metode Pembayaran Resmi</h1>
                    <p class="text-sm text-gray-500 leading-relaxed">Apotek Naufal menyediakan berbagai opsi transaksi fleksibel dan aman untuk kenyamanan berbelanja obat Anda.</p>
                </div>

                <div class="space-y-8 text-sm text-gray-600 leading-relaxed">
                    
                    <section id="transfer-bank" class="space-y-3">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm font-extrabold shrink-0"><i class="fa-solid fa-building-columns"></i></span>
                            1. Transfer Bank (BCA / Mandiri / BRI / BNI)
                        </h2>
                        <p class="pl-11">Anda dapat melakukan transfer dari ATM, M-Banking, atau Internet Banking ke rekening resmi Apotek Naufal yang tertera pada halaman Checkout. Pastikan nominal pembayaran sesuai hingga angka terakhir.</p>
                    </section>

                    <section id="qris" class="space-y-3">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-sm font-extrabold shrink-0"><i class="fa-solid fa-qrcode"></i></span>
                            2. QRIS (Gopay, OVO, ShopeePay, Dana, LinkAja, BCA Mobile)
                        </h2>
                        <p class="pl-11">Pindai (scan) kode QRIS Apotek Naufal menggunakan aplikasi e-wallet atau m-banking kesayangan Anda untuk pembayaran serba praktis dan serba cepat dalam hitungan detik.</p>
                    </section>

                    <section id="cash" class="space-y-3">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-extrabold shrink-0"><i class="fa-solid fa-money-bill-wave"></i></span>
                            3. Tunai / Bayar di Kasir (Pickup / Ambil Sendiri)
                        </h2>
                        <p class="pl-11">Bagi Anda yang memilih opsi pengambilian obat sendiri di lokasi Apotek Naufal, pembayaran tunai dapat dilakukan secara langsung di konter kasir apotek.</p>
                    </section>

                    <section id="konfirmasi" class="space-y-3">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-gray-800 text-white flex items-center justify-center text-sm font-extrabold shrink-0"><i class="fa-solid fa-file-invoice"></i></span>
                            4. Unggah Bukti Pembayaran
                        </h2>
                        <p class="pl-11">Untuk metode Transfer Bank & QRIS, harap unggah tangkapan layar (screenshot) bukti transfer di menu <strong>"Pesanan Saya"</strong> agar verifikasi pesanan dapat diproses instan oleh admin kami.</p>
                    </section>

                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white/82 backdrop-blur border-t border-border-muted pt-16 pb-6 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-10">
                <div class="lg:col-span-2">
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
                            <img src="{{ asset('images/logo_apotek_naufal.png') }}" class="h-6 w-auto object-contain" alt="Logo Apotek Naufal"> Apotek Naufal
                        </h2>
                    </div>
                    <p class="text-sm text-text-muted mb-5 leading-relaxed pr-0 md:pr-10">
                        Apotek Naufal adalah platform kesehatan terpercaya yang menyediakan akses mudah untuk mendapatkan obat, vitamin, dan kebutuhan kesehatan lainnya dengan layanan konsultasi apoteker profesional.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Layanan</h3>
                    <ul class="flex flex-col gap-3">
                        <li><a href="{{ route('tickets.create') }}" class="text-sm text-text-muted hover:text-primary transition">Tebus Resep</a></li>
                        <li><a href="{{ route('tickets.consult.create') }}" class="text-sm text-text-muted hover:text-primary transition">Konsultasi Apoteker</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Bantuan & Panduan</h3>
                    <ul class="flex flex-col gap-3">
                        <li><a href="{{ route('cara_belanja') }}" class="text-sm text-text-muted hover:text-primary transition">Cara Belanja</a></li>
                        <li><a href="{{ route('metode_pembayaran') }}" class="text-sm text-primary font-semibold transition">Metode Pembayaran</a></li>
                        <li><a href="{{ route('pengiriman') }}" class="text-sm text-text-muted hover:text-primary transition">Pengiriman</a></li>
                        <li><a href="{{ route('syarat_ketentuan') }}" class="text-sm text-text-muted hover:text-primary transition">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('kebijakan_privasi') }}" class="text-sm text-text-muted hover:text-primary transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Hubungi Kami</h3>
                    <ul class="flex flex-col gap-3">
                        <li><span class="text-sm text-text-muted flex items-start gap-2"><i class="fa-solid fa-location-dot w-5 text-center mt-1 shrink-0"></i> Jl. Andalas raya No.125, Andalas, Kec. Padang Tim., Kota Padang, Sumatera Barat 25171</span></li>
                        <li><a href="mailto:kalafinnali@gmail.com" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-envelope w-5 text-center"></i> kalafinnali@gmail.com</a></li>
                        <li><a href="https://wa.me/628218417911" target="_blank" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-brands fa-whatsapp w-5 text-center text-emerald-500"></i> 08218417911</a></li>
                        <li><a href="tel:08218417911" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-phone w-5 text-center"></i> 08218417911</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center pt-6 border-t border-border-muted text-sm text-text-muted">
                <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
