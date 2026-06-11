<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Apotek Naufal</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white py-4 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4 lg:gap-8">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-notes-medical text-3xl"></i> Apotek Naufal
            </a>

            <div class="flex-grow w-full lg:w-auto order-3 lg:order-none relative max-w-2xl">
                <input type="text" placeholder="Cari obat, vitamin, atau suplemen..." 
                    class="w-full py-2.5 px-5 pr-12 border border-border-muted rounded-full text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition">
                <button class="absolute right-4 top-1/2 -translate-y-1/2 text-primary text-lg cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>

            <div class="flex items-center gap-5">
                <a href="#" class="text-text-main hover:text-primary text-xl relative transition">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="absolute -top-2 -right-2.5 bg-secondary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ session('cart') ? collect(session('cart'))->sum('quantity') : 0 }}
                    </span>
                </a>
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-primary hover:bg-primary-dark transition border border-transparent flex items-center gap-2"><i class="fa-solid fa-gauge-high"></i> Panel Admin</a>
                        @else
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
                <li><a href="/" class="text-text-main hover:text-primary font-medium text-sm transition">Beranda</a></li>
                @foreach($categories->take(5) as $navCat)
                <li>
                    <a href="{{ route('category.show', $navCat->slug) }}" class="{{ $category->id == $navCat->id ? 'text-primary font-semibold relative after:content-[\'\'] after:absolute after:-bottom-3 after:left-0 after:w-full after:h-0.5 after:bg-primary' : 'text-text-main hover:text-primary font-medium transition' }} text-sm">
                        {{ $navCat->name }}
                    </a>
                </li>
                @endforeach
                <li><a href="#" class="text-text-main hover:text-primary font-medium text-sm transition">Promo</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        <!-- Notification Area -->
        <div class="max-w-7xl mx-auto px-4 mt-4">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg flex items-center gap-3 shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <!-- Category Banner -->
        <section class="bg-primary-light py-8 border-b border-border-muted">
            <div class="max-w-7xl mx-auto px-4 flex items-center gap-6">
                @if($category->image)
                <div class="w-20 h-20 rounded-full overflow-hidden flex items-center justify-center bg-white shadow-sm shrink-0">
                    <img src="{{ str_starts_with($category->image, '/') ? $category->image : '/' . $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                </div>
                @else
                <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center text-primary text-3xl shadow-sm shrink-0"><i class="fa-solid fa-pills"></i></div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-text-main mb-1">{{ $category->name }}</h1>
                    <p class="text-sm text-text-muted">Menampilkan semua produk dalam kategori {{ $category->name }}</p>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <section class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <div class="text-sm text-text-muted">Ditemukan <span class="font-bold text-text-main">{{ $medicines->count() }}</span> produk</div>
                <select class="px-4 py-2 border border-border-muted rounded-lg text-sm outline-none focus:border-primary">
                    <option>Terbaru</option>
                    <option>Harga: Rendah ke Tinggi</option>
                    <option>Harga: Tinggi ke Rendah</option>
                </select>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-5">
                @forelse($medicines as $medicine)
                <!-- Product Card -->
                <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition flex flex-col relative group border border-gray-100">
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
                        <div class="text-xs text-text-muted mb-4">Sisa stok: {{ $medicine->stock }}</div>
                        
                        <form action="{{ route('cart.add', $medicine->id) }}" method="POST" class="mt-auto w-full">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full py-2 bg-white border border-primary text-primary text-xs font-bold rounded-lg hover:bg-primary hover:text-white transition">Tambah ke Keranjang</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center text-text-muted py-12 bg-white rounded-xl border border-gray-100">
                    <div class="text-5xl mb-4 text-gray-200"><i class="fa-solid fa-box-open"></i></div>
                    <p>Belum ada produk di kategori ini.</p>
                </div>
                @endforelse
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted pt-12 pb-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center text-sm text-text-muted">
                <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
