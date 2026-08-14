<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_apotek_naufal.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apotek Naufal - Beli Obat Online Terpercaya</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Apotek Naufal adalah apotek online terpercaya yang menyediakan berbagai macam obat, vitamin, dan alat kesehatan dengan harga terbaik dan pengiriman cepat.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    class="ui-input w-full py-3 px-5 pr-12 rounded-full text-sm outline-none transition header-search-input">
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
                            <a href="{{ route('admin.dashboard') }}" class="ui-btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2"><i class="fa-solid fa-gauge-high"></i> Panel Admin</a>
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
                        <a href="/login" class="ui-btn-ghost px-5 py-2.5 rounded-xl text-sm font-semibold transition">Masuk</a>
                        <a href="/register" class="ui-btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold transition">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white/70 backdrop-blur border-b border-border-muted">
        <div class="max-w-7xl mx-auto px-4">
            <ul class="flex gap-6 overflow-x-auto whitespace-nowrap scrollbar-hide py-3">
                <li><a href="/" class="text-primary font-semibold text-sm relative after:content-[''] after:absolute after:-bottom-3 after:left-0 after:w-full after:h-0.5 after:bg-primary">Beranda</a></li>
                @foreach($categories->take(5) as $navCat)
                <li><a href="{{ route('category.show', $navCat->slug) }}" class="text-text-main hover:text-primary font-medium text-sm transition">{{ $navCat->name }}</a></li>
                @endforeach
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        <!-- Alerts -->
        <div class="max-w-7xl mx-auto px-4 mt-5">
            @if(session('success'))
            <div class="p-4 bg-green-50 border-l-4 border-primary rounded-r-lg flex items-center justify-between text-green-800 shadow-sm animate-fade-in mb-4">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-primary text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex items-center justify-between text-red-800 shadow-sm animate-fade-in mb-4">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
            @endif
        </div>
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 py-5">
            <div class="w-full min-h-[420px] rounded-[1.75rem] overflow-hidden relative shadow-[0_24px_80px_rgba(15,77,55,0.16)] bg-primary-light">
                <img src="/images/hero.webp" alt="Promo Apotek Naufal" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-primary-dark/86 via-primary/48 to-transparent"></div>
                <div class="relative z-10 flex min-h-[420px] flex-col justify-center max-w-2xl p-6 md:p-12 text-white">
                    <span class="mb-4 inline-flex w-fit items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white ring-1 ring-white/20"><i class="fa-solid fa-shield-heart"></i> Apotek online tepercaya</span>
                    <h2 class="text-3xl md:text-5xl font-extrabold mb-4 leading-tight drop-shadow-md">Kesehatan Anda, Diurus Lebih Cepat</h2>
                    <p class="text-sm md:text-base mb-7 max-w-xl text-white/88 drop-shadow-md">Beli obat asli, tebus resep, dan konsultasi apoteker dalam pengalaman belanja yang rapi, cepat, dan aman.</p>
                    <a href="#products-section" class="ui-btn-primary px-6 py-3.5 font-semibold rounded-xl transition w-max inline-flex items-center gap-2">Belanja Sekarang <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </section>

        <!-- Prescription Banner Section -->
        <section class="max-w-7xl mx-auto px-4 py-2">
            <div class="bg-gradient-to-r from-primary-dark via-primary to-secondary text-white rounded-[1.25rem] p-6 md:p-8 shadow-[0_18px_50px_rgba(15,77,55,0.18)] flex flex-col lg:flex-row justify-between items-center gap-6 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 text-white/10 text-9xl font-bold select-none pointer-events-none group-hover:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div class="relative z-10 max-w-2xl">
                    <span class="bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase tracking-wider inline-block mb-3">Layanan Konsultasi & Resep</span>
                    <h3 class="text-xl md:text-2xl font-bold mb-2">Tanya Apoteker & Tebus Resep Dokter</h3>
                    <p class="text-sm text-white/95 leading-relaxed">Butuh saran obat untuk keluhan Anda atau ingin menebus resep dokter? Hubungi apoteker kami melalui live chat. Kami akan membantu mencarikan obat yang sesuai dan memasukkannya langsung ke keranjang belanja Anda.</p>
                </div>
                <div class="relative z-10 shrink-0 w-full lg:w-auto flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('tickets.create') }}" class="w-full sm:w-auto text-center px-6 py-3.5 bg-white text-primary font-bold rounded-xl hover:bg-bg-body hover:-translate-y-0.5 transition shadow-sm inline-block">
                        <i class="fa-solid fa-upload mr-2"></i> Unggah Resep Dokter
                    </a>
                    <a href="{{ route('tickets.consult.create') }}" class="w-full sm:w-auto text-center px-6 py-3.5 bg-secondary text-white font-bold rounded-xl hover:bg-[#d85517] hover:-translate-y-0.5 transition shadow-sm inline-block">
                        <i class="fa-solid fa-comments mr-2"></i> Konsultasi Chat (Tanya Apoteker)
                    </a>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-text-main">Kategori Obat</h3>
            </div>
            <div class="grid grid-cols-4 md:grid-cols-8 gap-4">
                @forelse($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" class="ui-card-soft p-4 flex flex-col items-center gap-3 hover:text-primary transition text-center group">
                    @if($category->image)
                    <div class="w-12 h-12 rounded-full overflow-hidden flex items-center justify-center bg-primary-light">
                        <img src="{{ str_starts_with($category->image, '/') ? $category->image : '/' . $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="w-12 h-12 bg-primary-light rounded-full flex items-center justify-center text-primary text-xl group-hover:scale-110 transition"><i class="fa-solid fa-pills"></i></div>
                    @endif
                    <span class="text-xs font-semibold text-text-main group-hover:text-primary">{{ $category->name }}</span>
                </a>
                @empty
                <div class="col-span-4 md:col-span-8 text-center text-text-muted py-4">Belum ada kategori</div>
                @endforelse
            </div>
        </section>

        <!-- Products Section -->
        <section id="products-section" class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-5">
                @if(request('search'))
                    <div class="flex flex-col gap-1">
                        <h3 class="text-xl font-bold text-text-main">Hasil Pencarian untuk: <span class="text-primary">"{{ request('search') }}"</span></h3>
                        <a href="{{ route('home') }}" class="text-xs text-secondary hover:underline flex items-center gap-1 w-fit"><i class="fa-solid fa-circle-xmark"></i> Hapus Pencarian</a>
                    </div>
                @else
                    <h3 class="text-xl font-bold text-text-main">Produk Apotek Naufal</h3>
                @endif
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-5">
                
                @forelse($medicines as $medicine)
                <!-- Product Card -->
                @php
                    $isInitiallyHidden = !request('search') && $loop->index >= 10;
                @endphp
                <div class="ui-product-card overflow-hidden flex flex-col relative group @if($isInitiallyHidden) hidden-medicine-card @endif"
                     @if($isInitiallyHidden) style="display: none;" @endif>
                    <div class="absolute top-2 left-2 bg-primary-light text-primary text-[10px] font-bold px-2 py-1 rounded z-10">{{ $medicine->category->name ?? 'Umum' }}</div>
                    <a href="{{ route('product.detail', $medicine->slug) }}" class="h-40 flex items-center justify-center bg-white w-full overflow-hidden">
                        @if($medicine->image)
                        <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="w-full h-full object-cover">
                        @else
                        <div class="text-gray-300 text-4xl flex items-center justify-center w-full h-full bg-gray-50"><i class="fa-solid fa-pills"></i></div>
                        @endif
                    </a>
                    <div class="p-4 flex flex-col flex-grow text-left">
                        <a href="{{ route('product.detail', $medicine->slug) }}" class="text-sm font-semibold text-text-main mb-2 line-clamp-2 h-10 group-hover:text-primary transition">{{ $medicine->name }}</a>
                        @if($medicine->price_before_discount)
                        <div class="text-xs text-text-muted line-through mb-0.5">Rp {{ number_format($medicine->price_before_discount, 0, ',', '.') }}</div>
                        @endif
                        <div class="text-base font-bold text-secondary mb-2 mt-auto">Rp {{ number_format($medicine->price, 0, ',', '.') }}</div>
                        @if($medicine->stock > 0)
                            <button onclick="addToCart(this)" 
                                    class="mt-auto w-full py-2.5 ui-btn-ghost text-xs font-semibold rounded-xl hover:bg-primary hover:text-white transition cursor-pointer"
                                    data-id="{{ $medicine->id }}"
                                    data-name="{{ $medicine->name }}"
                                    data-price="Rp {{ number_format($medicine->price, 0, ',', '.') }}"
                                    data-description="{{ $medicine->description ?? 'Tidak ada deskripsi obat.' }}"
                                    data-unit="{{ $medicine->unit ?? 'Pcs' }}"
                                    data-image="{{ $medicine->image ? (str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image) : '' }}">
                                Tambah ke Keranjang
                            </button>
                        @else
                            <button disabled class="mt-auto w-full py-2 bg-gray-150 border border-gray-200 text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed">Stok Habis</button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center text-text-muted py-12 bg-white rounded-xl border border-gray-100 shadow-sm w-full">
                    <div class="text-5xl mb-4 text-gray-200"><i class="fa-solid fa-prescription-bottle-medical"></i></div>
                    <p class="text-sm font-medium">
                        Tidak ditemukan obat dengan kata kunci "{{ request('search') }}".
                    </p>
                </div>
                @endforelse

            </div>

            @if(!request('search') && $medicines->count() > 10)
            <div class="text-center mt-12 mb-4" id="lihat-semua-container">
                <button onclick="showAllMedicines()" class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl transition-all duration-200 shadow-md shadow-primary/10 flex items-center gap-2 mx-auto cursor-pointer hover:-translate-y-0.5">
                    <i class="fa-solid fa-eye"></i> Lihat Semua Obat
                </button>
            </div>
            @endif
        </section>
        
        <!-- Trust Indicators Section -->
        <section class="max-w-7xl mx-auto px-4 mb-10">
            <div class="flex flex-wrap md:flex-nowrap justify-between gap-6 ui-card p-6 md:p-8">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <i class="fa-solid fa-shield-halved text-3xl text-primary shrink-0"></i>
                    <div>
                        <h4 class="text-base font-bold mb-1">Produk 100% Asli</h4>
                        <p class="text-sm text-text-muted">Langsung dari distributor resmi</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <i class="fa-solid fa-user-doctor text-3xl text-primary shrink-0"></i>
                    <div>
                        <h4 class="text-base font-bold mb-1">Apoteker Berlisensi</h4>
                        <p class="text-sm text-text-muted">Dilayani tenaga profesional</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <i class="fa-solid fa-motorcycle text-3xl text-primary shrink-0"></i>
                    <div>
                        <h4 class="text-base font-bold mb-1">Pengiriman Cepat</h4>
                        <p class="text-sm text-text-muted">Tersedia layanan instant</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <i class="fa-solid fa-lock text-3xl text-primary shrink-0"></i>
                    <div>
                        <h4 class="text-base font-bold mb-1">Transaksi Aman</h4>
                        <p class="text-sm text-text-muted">Berbagai metode pembayaran</p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white/82 backdrop-blur border-t border-border-muted pt-16 pb-6 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-10">
                <div class="lg:col-span-2">
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-primary flex items-center gap-2"><img src="{{ asset('images/logo_apotek_naufal.png') }}" class="h-6 w-auto object-contain" alt="Logo Apotek Naufal"> Apotek Naufal</h2>
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
                        <li><a href="{{ route('metode_pembayaran') }}" class="text-sm text-text-muted hover:text-primary transition">Metode Pembayaran</a></li>
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
                        <li><a href="https://wa.me/6281372869386" target="_blank" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-brands fa-whatsapp w-5 text-center text-emerald-500"></i> 081372869386</a></li>
                        <li><a href="tel:081372869386" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-phone w-5 text-center"></i> 081372869386</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center pt-6 border-t border-border-muted text-sm text-text-muted">
                <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Search Scripts -->
    <script src="/js/search-autocomplete.js"></script>

    <script>
        function addToCart(button) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const price = button.getAttribute('data-price');
            const unit = button.getAttribute('data-unit');
            const image = button.getAttribute('data-image');
            const description = button.getAttribute('data-description');

            // Format image HTML
            let imgHtml = '';
            if (image) {
                imgHtml = `<img src="${image}" alt="${name}" style="width: 64px; height: 64px; object-fit: contain; border-radius: 8px; border: 1px solid #e5e7eb; padding: 4px; background-color: #fff; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">`;
            } else {
                imgHtml = `<div style="width: 64px; height: 64px; border-radius: 8px; background-color: #f9fafb; color: #d1d5db; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"><i class="fa-solid fa-pills" style="font-size: 24px; color: #9ca3af;"></i></div>`;
            }

            Swal.fire({
                title: 'Tambah ke Keranjang?',
                html: `
                    <div style="font-family: 'Inter', sans-serif; text-align: left; margin-top: 12px;">
                        <p style="font-size: 13px; color: #6b7280; margin: 0 0 16px 0; line-height: 1.4;">Apakah Anda yakin ingin memasukkan obat ini ke keranjang belanja?</p>
                        
                        <div style="display: flex; align-items: center; gap: 16px; padding: 16px; background: linear-gradient(to right, #f9fafb, #fff); border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04); width: 100%; box-sizing: border-box;">
                            ${imgHtml}
                            <div style="flex-grow: 1; min-width: 0; text-align: left; display: flex; flex-direction: column; gap: 4px;">
                                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #1f2937; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${name}</h4>
                                <div style="margin-top: 2px;">
                                    <span style="display: inline-block; font-size: 11px; font-weight: 600; color: #346739; background-color: #e6efe5; border: 1px solid rgba(52,103,57,0.15); padding: 2px 8px; border-radius: 9999px; line-height: 1.2;">Kemasan: ${unit}</span>
                                </div>
                                <div style="font-size: 16px; font-weight: 800; color: #79AE6F; margin-top: 4px;">${price}</div>
                            </div>
                        </div>

                        <div style="margin-top: 14px; padding: 12px 14px; background-color: #f9fafb; border-radius: 10px; border: 1px dashed #e5e7eb; font-size: 12px; color: #4b5563; line-height: 1.5; text-align: left;">
                            <strong style="color: #1f2937; display: block; margin-bottom: 4px; font-size: 12px;"><i class="fa-solid fa-file-prescription" style="color: #346739; margin-right: 4px;"></i>Deskripsi Obat:</strong>
                            ${description || 'Tidak ada deskripsi obat.'}
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#346739',
                cancelButtonColor: '#79AE6F',
                confirmButtonText: 'Ya, Masukkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm shadow-primary/20',
                    cancelButton: 'px-5 py-2.5 rounded-xl text-xs font-bold transition-all'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menambahkan ke keranjang...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route('cart.add') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            medicine_id: id,
                            quantity: 1
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            // Update badge
                            const badge = document.getElementById('cart-badge');
                            if (badge) {
                                badge.innerText = data.cart_count;
                            }
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                showCancelButton: true,
                                confirmButtonColor: '#346739',
                                cancelButtonColor: '#79AE6F',
                                confirmButtonText: 'Lihat Keranjang',
                                cancelButtonText: 'Lanjut Belanja',
                                customClass: {
                                    popup: 'rounded-2xl'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '{{ route('cart.index') }}';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan.',
                                customClass: {
                                    popup: 'rounded-2xl'
                                }
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Gagal menghubungi server.',
                            customClass: {
                                popup: 'rounded-2xl'
                            }
                        });
                    });
                }
            });
        }

        function showAllMedicines() {
            const hiddenCards = document.querySelectorAll('.hidden-medicine-card');
            hiddenCards.forEach(card => {
                card.style.display = 'flex';
            });
            const btnContainer = document.getElementById('lihat-semua-container');
            if (btnContainer) {
                btnContainer.remove();
            }
        }
    </script>

    @if(request('search'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSection = document.getElementById('products-section');
            if (productSection) {
                setTimeout(() => {
                    productSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        });
    </script>
    @endif

</body>
</html>
