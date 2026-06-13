<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $medicine->name }} - Apotek Naufal</title>
    
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

            <form action="{{ route('home') }}" method="GET" class="flex-grow w-full lg:w-auto order-3 lg:order-none relative max-w-2xl">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari obat, vitamin, atau suplemen..." 
                    class="w-full py-2.5 px-5 pr-12 border border-border-muted rounded-full text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition header-search-input">
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
                    <a href="{{ route('category.show', $navCat->slug) }}" class="{{ $medicine->category_id == $navCat->id ? 'text-primary font-semibold relative after:content-[\'\'] after:absolute after:-bottom-3 after:left-0 after:w-full after:h-0.5 after:bg-primary' : 'text-text-main hover:text-primary font-medium transition' }} text-sm">
                        {{ $navCat->name }}
                    </a>
                </li>
                @endforeach
                <li><a href="#" class="text-text-main hover:text-primary font-medium text-sm transition">Promo</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <!-- Notification Area -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg flex items-center gap-3 shadow-sm animate-fade-in">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-text-muted mb-6">
            <a href="/" class="hover:text-primary">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('category.show', $medicine->category->slug) }}" class="hover:text-primary">{{ $medicine->category->name }}</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-text-main font-medium">{{ $medicine->name }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left/Middle: Product Info & Image -->
            <div class="lg:col-span-9 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_10px_30px_rgba(0,0,0,0.03)] flex flex-col md:flex-row justify-between items-start gap-8">
                    <!-- Product Details -->
                    <div class="flex-grow space-y-4">
                        <span class="text-[10px] tracking-wider uppercase font-bold text-primary bg-primary/10 border border-primary/20 px-3 py-1 rounded-full w-fit block">{{ $medicine->category->name }}</span>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-tight">{{ $medicine->name }}</h1>
                        
                        <!-- Rating and Sales Dynamic -->
                        <div class="flex items-center gap-3 text-xs text-text-muted">
                            <span class="bg-gray-100 px-2.5 py-1 rounded-lg text-gray-700 font-semibold">Terjual <span class="font-bold">{{ $medicine->sold_count }} obat</span></span>
                            <span class="text-gray-200">|</span>
                            <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-100 px-2.5 py-1 rounded-lg text-amber-800 font-bold">
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <span>{{ number_format($medicine->average_rating, 1) }}</span>
                                <span class="text-amber-600/70 font-normal text-[10px] ml-0.5">({{ $medicine->reviews_count }} rating)</span>
                            </div>
                        </div>

                        @if($medicine->brand)
                            <p class="text-xs text-text-muted">Merek: <span class="font-bold text-gray-800">{{ $medicine->brand }}</span></p>
                        @endif

                        <div class="pt-2">
                            @if($medicine->price_before_discount)
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-xs text-gray-400 line-through">Rp{{ number_format($medicine->price_before_discount, 0, ',', '.') }}</span>
                                    <span class="text-[10px] font-extrabold bg-rose-50 text-rose-600 border border-rose-100 px-2 py-0.5 rounded-md">
                                        DISKON {{ round((($medicine->price_before_discount - $medicine->price) / $medicine->price_before_discount) * 100) }}%
                                    </span>
                                </div>
                            @endif
                            <span class="text-4xl font-extrabold text-primary tracking-tight">Rp{{ number_format($medicine->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Product Image -->
                    <div class="flex items-center justify-center bg-gray-50 border border-gray-100 rounded-2xl relative overflow-hidden mx-auto md:mx-0 shrink-0 w-auto h-auto" style="max-width: 280px; max-height: 280px;">
                        @if($medicine->requires_prescription)
                            <div class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-rose-600 text-white text-[9px] font-bold px-2 py-1.5 rounded-md shadow-sm flex items-center gap-1 z-10 animate-pulse">
                                <i class="fa-solid fa-prescription-bottle-medical"></i> Resep Dokter
                            </div>
                        @endif
                        <div class="flex items-center justify-center w-full h-full">
                            @if($medicine->image)
                                <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="object-contain hover:scale-105 transition-transform duration-300" style="max-width: 280px; max-height: 280px; width: auto; height: auto; display: block;">
                            @else
                                <div class="flex flex-col items-center justify-center text-center p-6 h-40 w-56">
                                    <div class="w-12 h-12 rounded-full bg-emerald-100/80 text-primary flex items-center justify-center text-2xl mb-2 shadow-sm">
                                        <i class="fa-solid fa-pills"></i>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700">Gambar Belum Tersedia</span>
                                    <span class="text-[10px] text-gray-400 mt-0.5">Sedang disiapkan</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description Only -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_10px_30px_rgba(0,0,0,0.03)]">
                    <h3 class="text-base font-bold text-gray-900 mb-3">Deskripsi</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $medicine->description ?? 'Tidak ada deskripsi untuk obat ini.' }}</p>
                </div>

                <!-- Reviews Section -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_10px_30px_rgba(0,0,0,0.03)] space-y-6">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h3 class="text-base font-bold text-gray-900">Ulasan Pengguna</h3>
                        <div class="flex items-center gap-1.5 text-sm font-bold text-amber-800">
                            <i class="fa-solid fa-star text-amber-500"></i>
                            <span>{{ number_format($medicine->average_rating, 1) }} / 5.0</span>
                            <span class="text-gray-400 font-normal text-xs">({{ $medicine->reviews_count }} Ulasan)</span>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @forelse($ratings as $rating)
                            <div class="p-4 bg-gray-50 rounded-xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($rating->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-800">{{ $rating->user->name }}</h4>
                                            <span class="text-[10px] text-gray-400">{{ $rating->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-0.5 text-xs text-amber-500">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating)
                                                <i class="fa-solid fa-star"></i>
                                            @else
                                                <i class="fa-regular fa-star text-gray-300"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if($rating->review)
                                    <p class="text-xs text-gray-600 leading-relaxed">{{ $rating->review }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 text-xs">
                                <i class="fa-solid fa-comments text-2xl mb-2 text-gray-300 block"></i>
                                Belum ada ulasan untuk obat ini.
                            </div>
                        @endforelse
                    </div>

                    <!-- Review Form -->
                    @auth
                        @php
                            $userHasReviewed = $ratings->contains('user_id', auth()->id());
                        @endphp
                        
                        @if(!$userHasReviewed)
                            <div class="border-t border-gray-100 pt-4">
                                <h4 class="text-xs font-bold text-gray-800 mb-3"><i class="fa-solid fa-pen-to-square text-primary mr-1.5"></i>Tulis Ulasan Anda</h4>
                                <form action="{{ route('medicine.review.store', $medicine->slug) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <!-- Rating Input (Stars) -->
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[11px] font-semibold text-gray-500">Rating Obat:</span>
                                        <div class="flex items-center gap-1.5" id="star-selector">
                                            @for($i = 1; $i <= 5; $i++)
                                                <button type="button" onclick="setRating({{ $i }})" class="text-xl text-gray-300 hover:text-amber-500 star-btn transition cursor-pointer font-bold" style="background: none; border: none; padding: 0;" data-value="{{ $i }}">
                                                    <i class="fa-solid fa-star"></i>
                                                </button>
                                            @endfor
                                            <input type="hidden" name="rating" id="ratingValue" value="" required>
                                        </div>
                                    </div>

                                    <!-- Review text input -->
                                    <div class="space-y-1.5">
                                        <label for="review" class="text-[11px] font-semibold text-gray-500 block">Ulasan Anda (Opsional):</label>
                                        <textarea name="review" id="review" placeholder="Tuliskan pengalaman Anda menggunakan obat ini..." 
                                                  class="w-full border border-gray-200 rounded-xl p-3 text-xs outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none h-20 bg-white hover:border-gray-300"></textarea>
                                    </div>

                                    <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-lg text-xs transition duration-200 cursor-pointer shadow-sm">
                                        Kirim Ulasan
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="border-t border-gray-100 pt-4 text-center py-2 bg-emerald-50/50 rounded-xl border border-dashed border-emerald-100">
                                <span class="text-xs font-semibold text-emerald-800"><i class="fa-solid fa-circle-check text-emerald-500 mr-1.5"></i>Anda sudah memberikan ulasan untuk obat ini. Terima kasih!</span>
                            </div>
                        @endif
                    @else
                        <div class="border-t border-gray-100 pt-4 text-center py-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <span class="text-xs text-gray-500">Silakan <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Masuk</a> untuk menulis ulasan untuk obat ini.</span>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Right: Purchase Card (Sticky) -->
            <div class="lg:col-span-3 lg:sticky lg:top-24">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_8px_30px_rgba(0,0,0,0.04)] space-y-4">
                    <h3 class="text-sm font-bold text-gray-800">Atur Jumlah & Catatan</h3>
                    
                    <form action="{{ route('cart.add') }}" method="POST" id="purchaseForm" class="space-y-4">
                        @csrf
                        <input type="hidden" name="medicine_id" value="{{ $medicine->id }}">

                        <!-- Quantity Selector & Stock -->
                        <div class="flex items-center justify-between gap-2 p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center bg-white border border-gray-200 rounded-lg p-0.5 shadow-sm">
                                <button type="button" onclick="adjustQty(-1)" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded transition cursor-pointer font-bold text-sm">
                                    <i class="fa-solid fa-minus text-[10px]"></i>
                                </button>
                                <input type="number" id="qtyInput" name="quantity" value="1" min="1" max="{{ $medicine->stock }}" oninput="handleManualQty(this)" class="w-12 text-center font-bold text-gray-800 bg-transparent border-0 outline-none text-sm focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button type="button" onclick="adjustQty(1)" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded transition cursor-pointer font-bold text-sm">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-text-muted block">Stok Tersedia</span>
                                <span class="text-xs font-semibold text-gray-800">{{ $medicine->stock }} {{ $medicine->unit ?? 'Pcs' }}</span>
                            </div>
                        </div>

                        <!-- Catatan Pembelian -->
                        <div class="space-y-1.5">
                            <label for="notes" class="text-xs font-semibold text-gray-700 block">Catatan Pembelian (Opsional)</label>
                            <textarea name="notes" id="notes" placeholder="Contoh: Sendok takar, dsb." 
                                      class="w-full border border-gray-200 rounded-xl p-3 text-xs outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none h-14 bg-white hover:border-gray-300"></textarea>
                        </div>

                        <!-- Subtotal -->
                        <div class="flex justify-between items-center py-1.5 border-t border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Subtotal</span>
                            <span class="text-xl font-bold text-primary tracking-tight" id="subtotalDisplay">Rp{{ number_format($medicine->price, 0, ',', '.') }}</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-2">
                            <button type="submit" class="w-full py-3 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl text-xs transition duration-200 flex items-center justify-center gap-2 cursor-pointer shadow-sm shadow-primary/20">
                                <i class="fa-solid fa-cart-plus"></i> + Keranjang
                            </button>
                            <button type="submit" name="buy_now" value="1" class="w-full py-3 bg-secondary hover:bg-[#d85517] text-white font-bold rounded-xl text-xs transition duration-200 flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                                <i class="fa-solid fa-wallet"></i> Beli Langsung
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        @php
            $relatedMedicines = \App\Models\Medicine::where('category_id', $medicine->category_id)
                ->where('id', '!=', $medicine->id)
                ->where('is_active', true)
                ->take(5)
                ->get();
        @endphp
        
        @if($relatedMedicines->count() > 0)
            <div class="mt-12">
                <h3 class="text-xl font-bold text-text-main mb-6">Produk Terkait</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-5">
                    @foreach($relatedMedicines as $relMed)
                        <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition flex flex-col relative group border border-gray-100">
                            <a href="{{ route('product.detail', $relMed->slug) }}" class="h-40 flex items-center justify-center bg-white w-full overflow-hidden">
                                @if($relMed->image)
                                    <img src="{{ str_starts_with($relMed->image, '/') ? $relMed->image : '/' . $relMed->image }}" alt="{{ $relMed->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="text-gray-300 text-4xl flex items-center justify-center w-full h-full bg-gray-50"><i class="fa-solid fa-pills"></i></div>
                                @endif
                            </a>
                            <div class="p-4 flex flex-col flex-grow">
                                <a href="{{ route('product.detail', $relMed->slug) }}" class="text-sm font-medium text-text-main mb-2 line-clamp-2 h-10 group-hover:text-primary transition">{{ $relMed->name }}</a>
                                @if($relMed->price_before_discount)
                                    <div class="text-xs text-text-muted line-through mb-0.5">Rp {{ number_format($relMed->price_before_discount, 0, ',', '.') }}</div>
                                @endif
                                <div class="text-base font-bold text-secondary mb-2 mt-auto">Rp {{ number_format($relMed->price, 0, ',', '.') }}</div>
                                <div class="text-xs text-text-muted mb-4">Sisa stok: {{ $relMed->stock }}</div>
                                <a href="{{ route('product.detail', $relMed->slug) }}" class="mt-auto w-full py-2 bg-white border border-primary text-primary text-xs font-semibold rounded-lg hover:bg-primary hover:text-white text-center block transition">Lihat Detail</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

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

    <!-- Scripts -->
    <script>
        const unitPrice = {{ $medicine->price }};
        const maxStock = {{ $medicine->stock }};
        const qtyInput = document.getElementById('qtyInput');
        const subtotalDisplay = document.getElementById('subtotalDisplay');

        function adjustQty(amount) {
            let currentQty = parseInt(qtyInput.value);
            if (isNaN(currentQty)) currentQty = 1;
            let newQty = currentQty + amount;

            if (newQty >= 1 && newQty <= maxStock) {
                qtyInput.value = newQty;
                let subtotal = unitPrice * newQty;
                subtotalDisplay.innerText = 'Rp' + subtotal.toLocaleString('id-ID');
            }
        }

        function handleManualQty(input) {
            let val = parseInt(input.value);
            if (isNaN(val)) {
                subtotalDisplay.innerText = 'Rp0';
                return;
            }
            if (val < 1) {
                val = 1;
            } else if (val > maxStock) {
                val = maxStock;
            }
            input.value = val;
            let subtotal = unitPrice * val;
            subtotalDisplay.innerText = 'Rp' + subtotal.toLocaleString('id-ID');
        }

        qtyInput.addEventListener('blur', function() {
            let val = parseInt(qtyInput.value);
            if (isNaN(val) || val < 1) {
                qtyInput.value = 1;
                let subtotal = unitPrice * 1;
                subtotalDisplay.innerText = 'Rp' + subtotal.toLocaleString('id-ID');
            }
        });
    </script>

    <!-- Search Scripts -->
    <script src="/js/search-autocomplete.js"></script>
</body>
</html>
