<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_apotek_naufal.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - Apotek Naufal</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Syarat dan ketentuan penggunaan layanan Apotek Naufal. Pelajari aturan belanja obat, tebus resep dokter, dan kebijakan transaksi kami.">
    
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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, .heading-font {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white py-4 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4 lg:gap-8">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <img src="{{ asset('images/logo_apotek_naufal.png') }}" class="h-8 w-auto object-contain" alt="Logo Apotek Naufal"> Apotek Naufal
            </a>

            <form action="{{ route('home') }}" method="GET" class="flex-grow w-full lg:w-auto order-3 lg:order-none relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari obat, vitamin, atau suplemen..." 
                    class="w-full py-3 px-5 pr-12 border border-border-muted rounded-full text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition header-search-input">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-primary text-lg cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <div class="flex items-center gap-5">
                <a href="{{ route('cart.index') }}" class="text-text-main hover:text-primary text-xl relative transition">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span id="cart-badge" class="absolute -top-2 -right-2.5 bg-secondary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $cartCount }}
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

    <!-- Navigation -->
    <nav class="bg-white border-b border-border-muted shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <ul class="flex gap-6 overflow-x-auto whitespace-nowrap scrollbar-hide py-3">
                <li><a href="/" class="text-text-main hover:text-primary font-medium text-sm transition">Beranda</a></li>
                @foreach($categories->take(5) as $navCat)
                <li><a href="{{ route('category.show', $navCat->slug) }}" class="text-text-main hover:text-primary font-medium text-sm transition">{{ $navCat->name }}</a></li>
                @endforeach
                <li><a href="#" class="text-text-main hover:text-primary font-medium text-sm transition">Promo</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-3 mb-6">
            <a href="/" class="text-text-muted hover:text-primary transition text-xs"><i class="fa-solid fa-house"></i> Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-text-muted"></i>
            <span class="text-text-main font-semibold text-xs">Syarat & Ketentuan</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Sticky Sidebar (Table of Contents) -->
            <aside class="lg:col-span-4 sticky top-24 space-y-6 lg:block hidden">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.01)]">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 px-2">Daftar Isi</h3>
                    <nav class="space-y-1" id="toc-nav">
                        <a href="#section-1" class="block px-3 py-2 rounded-xl text-xs font-semibold text-primary bg-primary-light transition-all">
                            1. Ketentuan Umum Akun
                        </a>
                        <a href="#section-2" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary transition-all">
                            2. Pembelian & Regulasi Obat
                        </a>
                        <a href="#section-3" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary transition-all">
                            3. Metode Pembayaran & Verifikasi
                        </a>
                        <a href="#section-4" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary transition-all">
                            4. Ketentuan Pengiriman Kurir
                        </a>
                        <a href="#section-5" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary transition-all">
                            5. Kebijakan Retur & Refund
                        </a>
                    </nav>
                </div>

                <div class="p-6 rounded-2xl bg-primary-light border border-green-100 text-xs text-primary flex items-start gap-3">
                    <i class="fa-solid fa-circle-question text-base shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="font-bold mb-1">Butuh bantuan?</h4>
                        <p class="leading-relaxed text-gray-600 mb-3">Jika Anda memiliki pertanyaan mengenai Syarat & Ketentuan kami, silakan hubungi Apoteker melalui chat.</p>
                        <a href="{{ route('tickets.consult.create') }}" class="inline-flex items-center gap-1.5 font-bold text-primary hover:underline">
                            Tanya Apoteker <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Right Content Area -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-gray-100 p-8 md:p-12 shadow-[0_4px_30px_rgba(0,0,0,0.015)] space-y-10">
                <div class="border-b border-gray-100 pb-6">
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight heading-font mb-2">Syarat & Ketentuan</h1>
                    <p class="text-xs text-text-muted">Terakhir diperbarui: 16 Juni 2026</p>
                </div>

                <!-- Callout -->
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex gap-3 leading-relaxed">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold">Pernyataan Persetujuan:</span> Harap membaca syarat dan ketentuan ini dengan saksama sebelum bertransaksi. Dengan menggunakan platform e-commerce Apotek Naufal, Anda secara otomatis menyetujui seluruh aturan yang berlaku di bawah ini.
                    </div>
                </div>

                <!-- Clauses -->
                <div class="space-y-10 text-sm text-gray-600 leading-relaxed">
                    
                    <section id="section-1" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-extrabold shrink-0">1</span>
                            Ketentuan Umum Akun
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Untuk menggunakan layanan e-commerce Apotek Naufal secara lengkap, pengguna diwajibkan melakukan pendaftaran akun menggunakan data asli yang dapat dipertanggungjawabkan (Nama, Email, dan WhatsApp).</p>
                            <p>Otentikasi login pada platform ini memanfaatkan teknologi nirkabel OTP (One-Time Password) ke nomor WhatsApp Anda. Keamanan akun merupakan tanggung jawab pribadi masing-masing pemilik nomor.</p>
                        </div>
                    </section>

                    <section id="section-2" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-extrabold shrink-0">2</span>
                            Pembelian & Regulasi Obat
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Seluruh produk farmasi, suplemen, obat bebas, dan alat kesehatan terjamin 100% Asli dan bersertifikat BPOM dari distributor resmi.</p>
                            <div class="p-4 bg-primary-light border border-green-150 rounded-2xl text-xs text-primary leading-relaxed my-2">
                                <span class="font-bold"><i class="fa-solid fa-file-prescription mr-1.5"></i>Wajib Resep Dokter:</span> Pembelian sediaan obat keras (logo lingkaran merah huruf K) wajib melampirkan foto resep dokter yang sah pada kolom unggah berkas yang disediakan sebelum pembayaran.
                            </div>
                        </div>
                    </section>

                    <section id="section-3" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-extrabold shrink-0">3</span>
                            Metode Pembayaran & Verifikasi
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Sistem menerima transfer antar bank dan e-wallet resmi. Pesanan akan diproses segera setelah tim finance kami melakukan verifikasi bukti transfer pembayaran Anda.</p>
                            <p>Batas maksimal pembayaran dan pengunggahan bukti transfer adalah 1x24 jam. Jika terlampaui, pesanan batal secara otomatis oleh sistem.</p>
                        </div>
                    </section>

                    <section id="section-4" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-extrabold shrink-0">4</span>
                            Ketentuan Pengiriman Kurir
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Biaya pengiriman ditentukan secara otomatis berdasarkan jarak tempuh dari Apotek Naufal ke koordinat pengantaran yang diinput oleh pengguna.</p>
                            <p>Pengiriman instan dilakukan selama jam operasional apotek (08:00 - 21:00 WIB). Pesanan di luar jam tersebut akan dikirimkan keesokan harinya.</p>
                        </div>
                    </section>

                    <section id="section-5" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-sm font-extrabold shrink-0">5</span>
                            Kebijakan Retur & Refund
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Demi menjaga mutu sediaan obat, produk farmasi yang sudah dibeli tidak dapat ditukar atau dikembalikan kecuali jika terjadi kesalahan pengiriman dari pihak Apotek Naufal atau obat telah kedaluwarsa.</p>
                            <p>Segala pengajuan retur harus disertai video unboxing penuh tanpa jeda dalam waktu 1x24 jam sejak paket diterima.</p>
                        </div>
                    </section>

                </div>

                <!-- Footer Action for mobile -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex sm:hidden flex-col gap-3">
                    <p class="text-xs text-text-muted text-center">Butuh bantuan konsultasi syarat & ketentuan?</p>
                    <a href="{{ route('tickets.consult.create') }}" class="w-full text-center py-3 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl text-xs transition">
                        <i class="fa-solid fa-comments mr-1.5"></i> Hubungi Apoteker
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted pt-16 pb-6 mt-12">
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
                    <div class="flex gap-4">
                        <a href="#" class="w-9 h-9 rounded-full bg-bg-body text-text-main flex items-center justify-center hover:bg-primary hover:text-white transition"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-9 h-9 rounded-full bg-bg-body text-text-main flex items-center justify-center hover:bg-primary hover:text-white transition"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-9 h-9 rounded-full bg-bg-body text-text-main flex items-center justify-center hover:bg-primary hover:text-white transition"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="w-9 h-9 rounded-full bg-bg-body text-text-main flex items-center justify-center hover:bg-primary hover:text-white transition"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Layanan</h3>
                    <ul class="flex flex-col gap-3">
                        <li><a href="{{ route('tickets.create') }}" class="text-sm text-text-muted hover:text-primary transition">Tebus Resep</a></li>
                        <li><a href="{{ route('tickets.consult.create') }}" class="text-sm text-text-muted hover:text-primary transition">Konsultasi Apoteker</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Cek Lab</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Artikel Kesehatan</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Promo Menarik</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Bantuan & Panduan</h3>
                    <ul class="flex flex-col gap-3">
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Cara Belanja</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Metode Pembayaran</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Pengiriman</a></li>
                        <li><a href="{{ route('syarat_ketentuan') }}" class="text-sm text-primary font-semibold transition">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('kebijakan_privasi') }}" class="text-sm text-text-muted hover:text-primary transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Hubungi Kami</h3>
                    <ul class="flex flex-col gap-3">
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-location-dot w-5 text-center"></i> Jl. Andalas</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-envelope w-5 text-center"></i> cs@apoteknaufal.com</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-brands fa-whatsapp w-5 text-center"></i> +62 812-3456-7890</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-phone w-5 text-center"></i> (021) 1500-123</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center pt-6 border-t border-border-muted text-sm text-text-muted">
                <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Autocomplete search -->
    <script src="/js/search-autocomplete.js"></script>

    <!-- Interactive Scroll Highlighting -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('section[id^="section-"]');
            const navLinks = document.querySelectorAll('#toc-nav a');

            function makeActive() {
                let fromTop = window.scrollY + 120;
                sections.forEach(sec => {
                    if (sec.offsetTop <= fromTop && sec.offsetTop + sec.offsetHeight > fromTop) {
                        navLinks.forEach(link => {
                            link.classList.remove('text-primary', 'bg-primary-light', 'font-semibold');
                            link.classList.add('text-text-muted', 'font-medium');
                            if (link.getAttribute('href') === '#' + sec.id) {
                                link.classList.remove('text-text-muted', 'font-medium');
                                link.classList.add('text-primary', 'bg-primary-light', 'font-semibold');
                            }
                        });
                    }
                });
            }

            window.addEventListener('scroll', makeActive);
            makeActive();
        });
    </script>
</body>
</html>
