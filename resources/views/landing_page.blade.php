<!DOCTYPE html>
<html lang="id">
<head>
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
    <header class="bg-white py-4 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4 lg:gap-8">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-notes-medical text-3xl"></i> Apotek Naufal
            </a>

            <form action="{{ route('home') }}" method="GET" class="flex-grow w-full lg:w-auto order-3 lg:order-none relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari obat, vitamin, atau suplemen..." 
                    class="w-full py-3 px-5 pr-12 border border-border-muted rounded-full text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition">
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
                            <div class="px-3 py-2 text-sm font-semibold text-text-main flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center"><i class="fa-solid fa-user"></i></div>
                                {{ auth()->user()->name }}
                            </div>
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
                <li><a href="/" class="text-primary font-semibold text-sm relative after:content-[''] after:absolute after:-bottom-3 after:left-0 after:w-full after:h-0.5 after:bg-primary">Beranda</a></li>
                @foreach($categories->take(5) as $navCat)
                <li><a href="{{ route('category.show', $navCat->slug) }}" class="text-text-main hover:text-primary font-medium text-sm transition">{{ $navCat->name }}</a></li>
                @endforeach
                <li><a href="#" class="text-text-main hover:text-primary font-medium text-sm transition">Promo</a></li>
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
            <div class="w-full h-48 md:h-[350px] rounded-2xl overflow-hidden relative shadow-md bg-primary-light">
                <img src="/images/hero.png" alt="Promo Apotek Naufal" class="w-full h-full object-cover">
                <div class="absolute inset-y-0 left-0 flex flex-col justify-center max-w-md p-6 md:p-12 text-white bg-black/30 backdrop-blur-sm rounded-l-2xl">
                    <h2 class="text-2xl md:text-4xl font-bold mb-4 leading-tight drop-shadow-md">Kesehatan Anda Adalah Prioritas Kami</h2>
                    <p class="text-sm md:text-base mb-6 drop-shadow-md hidden md:block">Beli obat asli, lengkap, dan terpercaya secara online dengan pengiriman cepat.</p>
                    <a href="#" class="px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition w-max">Belanja Sekarang</a>
                </div>
            </div>
        </section>

        <!-- Prescription Banner Section -->
        <section class="max-w-7xl mx-auto px-4 py-2">
            <div class="bg-gradient-to-r from-primary to-[#008f45] text-white rounded-2xl p-6 md:p-8 shadow-sm flex flex-col lg:flex-row justify-between items-center gap-6 relative overflow-hidden group">
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
                <a href="{{ route('category.show', $category->slug) }}" class="bg-white rounded-xl p-4 flex flex-col items-center gap-3 shadow-sm hover:-translate-y-1 hover:shadow-md hover:text-primary transition text-center group">
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

        <!-- Promo Banners -->
        <section class="max-w-7xl mx-auto px-4 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="rounded-xl overflow-hidden h-48 relative flex items-center p-8 bg-gradient-to-br from-primary-light to-[#ccecd8]">
                <div class="z-10 w-2/3">
                    <h3 class="text-xl font-bold text-text-main mb-2">Diskon Spesial Vitamin</h3>
                    <p class="text-sm text-text-muted mb-4 hidden sm:block">Jaga daya tahan tubuh dengan vitamin lengkap. Diskon hingga 30%!</p>
                    <a href="#" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:bg-primary-dark transition inline-block">Lihat Promo</a>
                </div>
            </div>
            <div class="rounded-xl overflow-hidden h-48 relative flex items-center p-8 bg-gradient-to-br from-[#fff3e0] to-[#ffe0b2]">
                <div class="z-10 w-2/3">
                    <h3 class="text-xl font-bold text-text-main mb-2">Kebutuhan Si Kecil</h3>
                    <p class="text-sm text-text-muted mb-4 hidden sm:block">Belanja produk ibu & anak sekarang lebih hemat. Gratis Ongkir!</p>
                    <a href="#" class="px-4 py-2 bg-secondary text-white text-xs font-semibold rounded-lg hover:bg-[#d85517] transition inline-block">Cek Sekarang</a>
                </div>
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
                <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition flex flex-col relative group border border-gray-100">
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
                                    class="mt-auto w-full py-2 bg-white border border-primary text-primary text-xs font-semibold rounded-lg hover:bg-primary hover:text-white transition cursor-pointer"
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
        </section>
        
        <!-- Trust Indicators Section -->
        <section class="max-w-7xl mx-auto px-4 mb-10">
            <div class="flex flex-wrap md:flex-nowrap justify-between gap-6 bg-white p-6 md:p-8 rounded-xl shadow-sm">
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
    <footer class="bg-white border-t border-border-muted pt-16 pb-6 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-10">
                <div class="lg:col-span-2">
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-primary flex items-center gap-2"><i class="fa-solid fa-notes-medical"></i> Apotek Naufal</h2>
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
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Kebijakan Privasi</a></li>
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

    <!-- Search Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInputs = document.querySelectorAll('input[name="search"]');
        
        searchInputs.forEach(input => {
            const form = input.closest('form');
            if (!form) return;
            
            // Ensure parent form has relative positioning
            form.classList.add('relative');
            
            // Create suggestions container
            const dropdown = document.createElement('div');
            dropdown.className = 'absolute left-0 right-0 mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden z-50 max-h-80 overflow-y-auto divide-y divide-gray-50 hidden';
            form.appendChild(dropdown);
            
            let debounceTimer;
            
            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = input.value.trim();
                
                if (query.length < 2) {
                    dropdown.innerHTML = '';
                    dropdown.classList.add('hidden');
                    return;
                }
                
                debounceTimer = setTimeout(() => {
                    fetch(`/search-suggestions?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            dropdown.innerHTML = '';
                            
                            if (data.length === 0) {
                                const emptyItem = document.createElement('div');
                                emptyItem.className = 'p-4 text-center text-xs text-text-muted';
                                emptyItem.textContent = 'Tidak ditemukan obat yang cocok';
                                dropdown.appendChild(emptyItem);
                            } else {
                                data.forEach(item => {
                                    const a = document.createElement('a');
                                    a.href = `/obat/${item.slug}`;
                                    a.className = 'flex items-center gap-3 p-3 hover:bg-gray-50 transition text-left';
                                    
                                    const imgWrapper = document.createElement('div');
                                    imgWrapper.className = 'w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden shrink-0 border border-gray-100';
                                    
                                    if (item.image) {
                                        const img = document.createElement('img');
                                        img.src = item.image;
                                        img.alt = item.name;
                                        img.className = 'w-full h-full object-contain';
                                        imgWrapper.appendChild(img);
                                    } else {
                                        imgWrapper.innerHTML = '<i class="fa-solid fa-pills text-gray-300 text-sm"></i>';
                                    }
                                    
                                    const infoWrapper = document.createElement('div');
                                    infoWrapper.className = 'flex-grow min-w-0';
                                    
                                    const nameSpan = document.createElement('span');
                                    nameSpan.className = 'text-sm font-semibold text-text-main block truncate';
                                    nameSpan.textContent = item.name;
                                    
                                    const catSpan = document.createElement('span');
                                    catSpan.className = 'text-[10px] text-text-muted block mt-0.5';
                                    catSpan.textContent = item.category_name;
                                    
                                    infoWrapper.appendChild(nameSpan);
                                    infoWrapper.appendChild(catSpan);
                                    
                                    const priceSpan = document.createElement('span');
                                    priceSpan.className = 'text-xs font-bold text-secondary text-right shrink-0';
                                    priceSpan.textContent = `Rp ${item.price}`;
                                    
                                    a.appendChild(imgWrapper);
                                    a.appendChild(infoWrapper);
                                    a.appendChild(priceSpan);
                                    
                                    dropdown.appendChild(a);
                                });
                            }
                            dropdown.classList.remove('hidden');
                        })
                        .catch(err => console.error('Error fetching suggestions:', err));
                }, 300);
            });
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!form.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Show dropdown again when input is focused and has text
            input.addEventListener('focus', function() {
                if (input.value.trim().length >= 2 && dropdown.children.length > 0) {
                    dropdown.classList.remove('hidden');
                }
            });
        });
    });
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
