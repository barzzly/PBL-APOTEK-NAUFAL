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

    <!-- Topbar -->
    <div class="bg-white border-b border-border-muted text-xs py-2 text-text-muted">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span><i class="fa-solid fa-truck"></i> Gratis Ongkir ke Seluruh Indonesia</span>
            </div>
            <div class="flex gap-4">
                <a href="#" class="hover:text-primary transition">Bantuan</a>
                <a href="#" class="hover:text-primary transition">Lacak Pesanan</a>
                <a href="#" class="hover:text-primary transition">Download App</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white py-4 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4 lg:gap-8">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-notes-medical text-3xl"></i> Apotek Naufal
            </a>

            <div class="flex-grow w-full lg:w-auto order-3 lg:order-none relative">
                <input type="text" placeholder="Cari obat, vitamin, atau suplemen..." 
                    class="w-full py-3 px-5 pr-12 border border-border-muted rounded-full text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition">
                <button class="absolute right-4 top-1/2 -translate-y-1/2 text-primary text-lg cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>

            <div class="flex items-center gap-5">
                <a href="{{ route('cart.index') }}" class="text-text-main hover:text-primary text-xl relative transition">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span id="cart-badge" class="absolute -top-2 -right-2.5 bg-secondary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ array_sum(array_column(session('cart', []), 'quantity')) }}
                    </span>
                </a>
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-primary hover:bg-primary-dark transition border border-transparent flex items-center gap-2"><i class="fa-solid fa-gauge-high"></i> Panel Admin</a>
                        @else
                            <a href="{{ route('orders.history') }}" class="px-3 py-2 text-xs font-semibold text-primary hover:underline flex items-center gap-1.5"><i class="fa-solid fa-receipt"></i> Pesanan Saya</a>
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
        <section class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-text-main">Produk Apotek Naufal</h3>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-5">
                
                @forelse($medicines as $medicine)
                <!-- Product Card -->
                <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition flex flex-col relative group border border-gray-100">
                    <div class="absolute top-2 left-2 bg-primary-light text-primary text-[10px] font-bold px-2 py-1 rounded z-10">{{ $medicine->category->name ?? 'Umum' }}</div>
                    <a href="{{ route('product.detail', $medicine->slug) }}" class="h-40 p-4 flex items-center justify-center bg-white">
                        @if($medicine->image)
                        <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="max-w-full max-h-full object-contain">
                        @else
                        <div class="text-gray-300 text-4xl"><i class="fa-solid fa-pills"></i></div>
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
                <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center text-text-muted py-8">Belum ada produk yang ditambahkan.</div>
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
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Tebus Resep</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Konsultasi Dokter</a></li>
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
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-location-dot w-5 text-center"></i> Jl. Kesehatan No. 123, Jakarta</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-envelope w-5 text-center"></i> cs@apoteknaufal.com</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-brands fa-whatsapp w-5 text-center"></i> +62 812-3456-7890</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-phone w-5 text-center"></i> (021) 1500-123</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center pt-6 border-t border-border-muted text-sm text-text-muted">
                <p>&copy; 2026 Apotek Naufal. All rights reserved. SIPA: 123/SIPA/2026.</p>
            </div>
        </div>
    </footer>

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
                                    <span style="display: inline-block; font-size: 11px; font-weight: 600; color: #00A651; background-color: #e6f6ec; border: 1px solid rgba(0,166,81,0.15); padding: 2px 8px; border-radius: 9999px; line-height: 1.2;">Kemasan: ${unit}</span>
                                </div>
                                <div style="font-size: 16px; font-weight: 800; color: #f26522; margin-top: 4px;">${price}</div>
                            </div>
                        </div>

                        <div style="margin-top: 14px; padding: 12px 14px; background-color: #f9fafb; border-radius: 10px; border: 1px dashed #e5e7eb; font-size: 12px; color: #4b5563; line-height: 1.5; text-align: left;">
                            <strong style="color: #1f2937; display: block; margin-bottom: 4px; font-size: 12px;"><i class="fa-solid fa-file-prescription" style="color: #00A651; margin-right: 4px;"></i>Deskripsi Obat:</strong>
                            ${description || 'Tidak ada deskripsi obat.'}
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#00A651',
                cancelButtonColor: '#f26522',
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
                                confirmButtonColor: '#00A651',
                                cancelButtonColor: '#f26522',
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
    </script>
</body>
</html>
