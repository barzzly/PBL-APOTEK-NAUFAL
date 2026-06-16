<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_apotek_naufal.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - Apotek Naufal</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Kebijakan privasi Apotek Naufal. Pelajari cara kami menjaga, mengelola, dan melindungi data pribadi Anda demi keamanan transaksi.">
    
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
            <span class="text-text-main font-semibold text-xs">Kebijakan Privasi</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Sticky Sidebar (Table of Contents) -->
            <aside class="lg:col-span-4 sticky top-24 space-y-6 lg:block hidden">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.01)]">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 px-2">Daftar Isi</h3>
                    <nav class="space-y-1" id="toc-nav">
                        <a href="#section-1" class="block px-3 py-2 rounded-xl text-xs font-semibold text-primary bg-primary-light transition-all">
                            1. Informasi yang Dikumpulkan
                        </a>
                        <a href="#section-2" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary transition-all">
                            2. Tujuan Penggunaan Data
                        </a>
                        <a href="#section-3" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary transition-all">
                            3. Perlindungan & Rekam Medis
                        </a>
                        <a href="#section-4" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary transition-all">
                            4. Keamanan Akses WhatsApp OTP
                        </a>
                        <a href="#section-5" class="block px-3 py-2 rounded-xl text-xs font-medium text-text-muted hover:bg-gray-50 hover:text-primary transition-all">
                            5. Hak & Pilihan Pengguna
                        </a>
                    </nav>
                </div>

                <div class="p-6 rounded-2xl bg-primary-light border border-green-100 text-xs text-primary flex items-start gap-3">
                    <i class="fa-solid fa-shield-check text-base shrink-0 mt-0.5"></i>
                    <div>
                        <h4 class="font-bold mb-1">Privasi Anda Prioritas Kami</h4>
                        <p class="leading-relaxed text-gray-600 mb-3">Jika Anda ingin mengajukan permohonan penutupan akun atau penghapusan riwayat data sediaan medis, hubungi staf Apoteker kami.</p>
                        <a href="{{ route('tickets.consult.create') }}" class="inline-flex items-center gap-1.5 font-bold text-primary hover:underline">
                            Tanya Apoteker <i class="fa-solid fa-arrow-right-long"></i>
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Right Content Area -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-gray-100 p-8 md:p-12 shadow-[0_4px_30px_rgba(0,0,0,0.015)] space-y-10">
                <div class="border-b border-gray-100 pb-6">
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight heading-font mb-2">Kebijakan Privasi</h1>
                    <p class="text-xs text-text-muted">Terakhir diperbarui: 16 Juni 2026</p>
                </div>

                <!-- Callout -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs flex gap-3 leading-relaxed">
                    <i class="fa-solid fa-shield-halved text-emerald-600 text-base shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold">Komitmen Keamanan Data:</span> Kami berkomitmen tinggi melindungi kerahasiaan catatan medis dan data diri Anda. Halaman ini memaparkan dengan transparan mengenai metode pengumpulan, pengolahan, serta penjagaan privasi Anda.
                    </div>
                </div>

                <!-- Clauses -->
                <div class="space-y-10 text-sm text-gray-600 leading-relaxed">
                    
                    <section id="section-1" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-extrabold shrink-0">1</span>
                            Informasi yang Dikumpulkan
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Kami mengumpulkan beberapa tipe data pribadi demi kelancaran penyediaan obat:</p>
                            <ul class="list-disc list-inside space-y-1.5 text-xs text-gray-500 pl-2">
                                <li><strong>Data Identitas:</strong> Nama lengkap, alamat email aktif, nomor WhatsApp, dan koordinat alamat pengiriman.</li>
                                <li><strong>Dokumen Medis:</strong> Unggahan foto resep dokter untuk sediaan tebus obat keras.</li>
                                <li><strong>Informasi Transaksi:</strong> Rincian pembayaran, nama pengirim transfer, dan bukti transfer pembayaran.</li>
                            </ul>
                        </div>
                    </section>

                    <section id="section-2" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-extrabold shrink-0">2</span>
                            Tujuan Penggunaan Data
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Informasi pribadi yang terkumpul diolah untuk tujuan esensial berikut:</p>
                            <ul class="list-disc list-inside space-y-1.5 text-xs text-gray-500 pl-2">
                                <li>Mengirimkan pesan keamanan berupa kode OTP login ke WhatsApp Anda.</li>
                                <li>Melakukan peninjauan resep medis oleh Apoteker bersertifikat demi keabsahan regulasi sebelum obat disiapkan.</li>
                                <li>Mengirimkan detail riwayat pembelian, tagihan pesanan, serta pembaruan status kurir.</li>
                            </ul>
                        </div>
                    </section>

                    <section id="section-3" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-extrabold shrink-0">3</span>
                            Perlindungan & Rekam Medis
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Dokumen resep dokter dan riwayat diagnosa obat keras dienkripsi dan disimpan di server aman. Kami menjamin tidak akan menyebarluaskan, memperjualbelikan, atau memberikan akses rekam medis Anda kepada pihak luar tanpa izin tegas tertulis, kecuali diperintahkan oleh otoritas hukum kesehatan Indonesia.</p>
                        </div>
                    </section>

                    <section id="section-4" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-extrabold shrink-0">4</span>
                            Keamanan Akses WhatsApp OTP
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Aplikasi menggunakan otentikasi login OTP WhatsApp. Setiap kode bersifat unik dan kedaluwarsa setelah 5 menit. Jangan pernah memberikan kode OTP ini kepada siapapun demi mencegah penyalahgunaan akun oleh pihak luar.</p>
                        </div>
                    </section>

                    <section id="section-5" class="space-y-4 scroll-mt-24">
                        <h2 class="text-lg font-bold text-gray-900 heading-font flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-extrabold shrink-0">5</span>
                            Hak & Pilihan Pengguna
                        </h2>
                        <div class="space-y-3 pl-11">
                            <p>Pengguna berhak mengubah data profil seperti nama dan avatar kapanpun melalui halaman edit profil.</p>
                            <p>Jika pengguna berniat menghapus informasi rekam medis secara permanen dari server Apotek Naufal, silakan kirimkan permohonan melalui menu aduan tiket chat apoteker.</p>
                        </div>
                    </section>

                </div>

                <!-- Footer Action for mobile -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex sm:hidden flex-col gap-3">
                    <p class="text-xs text-text-muted text-center">Butuh bantuan konsultasi kebijakan privasi?</p>
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
                        <li><a href="{{ route('syarat_ketentuan') }}" class="text-sm text-text-muted hover:text-primary transition">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('kebijakan_privasi') }}" class="text-sm text-primary font-semibold transition">Kebijakan Privasi</a></li>
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
                            link.classList.remove('text-emerald-600', 'bg-emerald-500/10', 'font-semibold');
                            link.classList.add('text-text-muted', 'font-medium');
                            if (link.getAttribute('href') === '#' + sec.id) {
                                link.classList.remove('text-text-muted', 'font-medium');
                                link.classList.add('text-emerald-600', 'bg-emerald-500/10', 'font-semibold');
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
