<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Apotek Naufal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

            <div class="flex-grow w-full lg:w-auto order-3 lg:order-none relative max-w-2xl">
                <input type="text" placeholder="Cari obat, vitamin, atau suplemen..." 
                    class="w-full py-2.5 px-5 pr-12 border border-border-muted rounded-full text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition">
                <button class="absolute right-4 top-1/2 -translate-y-1/2 text-primary text-lg cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>

            <div class="flex items-center gap-5">
                <a href="{{ route('cart.index') }}" class="text-primary hover:text-primary-dark text-xl relative transition">
                    <i class="fa-solid fa-cart-shopping"></i>
                    @php $cartCount = collect(session('cart', []))->sum('quantity') @endphp
                    <span class="absolute -top-2 -right-2.5 bg-secondary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $cartCount }}</span>
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
                <li><a href="{{ route('category.show', $navCat->slug) }}" class="text-text-main hover:text-primary font-medium text-sm transition">{{ $navCat->name }}</a></li>
                @endforeach
                <li><a href="#" class="text-text-main hover:text-primary font-medium text-sm transition">Promo</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-8">
        
        <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-primary rounded-r-lg flex items-center justify-between text-green-800 shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-primary text-lg"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex items-center justify-between text-red-800 shadow-sm animate-fade-in">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <h1 class="text-2xl font-bold mb-8 flex items-center gap-3 text-text-main">
            <i class="fa-solid fa-cart-flatbed text-primary"></i> Keranjang Belanja
        </h1>

        @if(count($cart) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left: Cart Items List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <span class="text-sm font-semibold text-text-muted">Daftar Produk ({{ count($cart) }})</span>
                        
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan keranjang belanja?')">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700 transition flex items-center gap-1.5 bg-transparent border-0 cursor-pointer">
                                <i class="fa-solid fa-trash-can"></i> Kosongkan Keranjang
                            </button>
                        </form>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($cart as $id => $item)
                        <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4 hover:bg-gray-50/20 transition">
                            <!-- Product Image -->
                            <div class="w-20 h-20 bg-white rounded-lg border border-gray-100 p-2 flex items-center justify-center shrink-0">
                                @if($item['image'])
                                <img src="{{ str_starts_with($item['image'], '/') ? $item['image'] : '/' . $item['image'] }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain">
                                @else
                                <div class="text-gray-300 text-3xl"><i class="fa-solid fa-pills"></i></div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex-grow">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-base font-semibold text-text-main">{{ $item['name'] }}</h3>
                                    @if($item['requires_prescription'])
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#fff3e0] text-[#f26522] border border-[#ffe0b2] flex items-center gap-1">
                                        <i class="fa-solid fa-file-prescription"></i> Butuh Resep Dokter
                                    </span>
                                    @endif
                                </div>
                                <p class="text-xs text-text-muted mb-2">Satuan: {{ $item['unit'] }}</p>
                                <div class="text-sm font-bold text-secondary">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                            </div>

                            <!-- Quantity controls & Subtotal -->
                            <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                                <!-- Plus Minus Controls -->
                                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white">
                                    <!-- Minus Button -->
                                    <form action="{{ route('cart.update', $id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center hover:bg-gray-50 text-gray-500 hover:text-text-main transition border-0 bg-transparent cursor-pointer" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-minus text-[10px]"></i>
                                        </button>
                                    </form>

                                    <!-- Value -->
                                    <span class="w-10 text-center text-sm font-semibold select-none">{{ $item['quantity'] }}</span>

                                    <!-- Plus Button -->
                                    <form action="{{ route('cart.update', $id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center hover:bg-gray-50 text-gray-500 hover:text-text-main transition border-0 bg-transparent cursor-pointer" {{ $item['quantity'] >= $item['stock'] ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-plus text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Subtotal per item -->
                                <div class="text-right min-w-[100px]">
                                    <div class="text-xs text-text-muted mb-0.5">Subtotal</div>
                                    <div class="text-sm font-bold text-text-main">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>
                                </div>

                                <!-- Remove button -->
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 p-2 transition bg-transparent border-0 cursor-pointer" title="Hapus Produk">
                                        <i class="fa-solid fa-trash text-base"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <h2 class="text-lg font-bold text-text-main border-b border-gray-100 pb-4">Ringkasan Belanja</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-text-muted">Total Harga ({{ $cartCount }} barang)</span>
                            <span class="font-semibold text-text-main">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-text-muted">Ongkos Kirim</span>
                            @if($shipping == 0)
                            <span class="font-semibold text-primary">Gratis</span>
                            @else
                            <span class="font-semibold text-text-main">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        
                        @if($shipping > 0)
                        <div class="p-3 bg-primary-light/50 rounded-lg text-[11px] text-primary-dark flex gap-2">
                            <i class="fa-solid fa-circle-info shrink-0 mt-0.5 text-xs"></i>
                            <span>Tambahkan belanjaan senilai <strong>Rp {{ number_format(150000 - $subtotal, 0, ',', '.') }}</strong> lagi untuk mendapatkan <strong>Gratis Ongkir!</strong></span>
                        </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                        <span class="text-base font-bold text-text-main">Total Belanja</span>
                        <span class="text-xl font-bold text-secondary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <!-- Checkout button -->
                    <button class="w-full py-3.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl shadow-sm hover:shadow-md transition text-center flex items-center justify-center gap-2 cursor-pointer border-0">
                        Lanjut ke Pembayaran <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>

                <!-- Safe transaction badge -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex items-center gap-3">
                    <i class="fa-solid fa-shield-halved text-primary text-xl"></i>
                    <div>
                        <h4 class="text-xs font-bold text-text-main">Jaminan Belanja Aman</h4>
                        <p class="text-[10px] text-text-muted">Apotek Naufal menjamin produk 100% asli dan proses pembayaran aman.</p>
                    </div>
                </div>
            </div>

        </div>
        @else
        <!-- Empty Cart View -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center max-w-2xl mx-auto space-y-6">
            <div class="w-24 h-24 bg-primary-light text-primary rounded-full flex items-center justify-center text-4xl mx-auto">
                <i class="fa-solid fa-cart-arrow-down"></i>
            </div>
            <div class="space-y-2">
                <h2 class="text-xl font-bold text-text-main">Keranjang Belanja Anda Kosong</h2>
                <p class="text-sm text-text-muted max-w-sm mx-auto">Anda belum menambahkan produk apa pun ke dalam keranjang. Mulai jelajahi obat-obatan terbaik kami sekarang.</p>
            </div>
            <a href="/" class="inline-block px-6 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg shadow-sm transition">
                Kembali Belanja
            </a>
        </div>
        @endif

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
