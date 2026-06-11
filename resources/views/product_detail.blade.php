<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $medicine->name }} - Apotek Naufal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tailwind CSS CDN Fallback (For environments without Node.js/NPM) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                primary: {
                  DEFAULT: '#00A651',
                  dark: '#008f45',
                  light: '#e6f6ec',
                },
                secondary: '#f26522',
                'text-main': '#333333',
                'text-muted': '#757575',
                'bg-body': '#f4f7f6',
                'border-muted': '#e0e0e0',
              }
            }
          }
        }
    </script>
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
            <!-- Left: Product Image (Sticky) -->
            <div class="lg:col-span-4 lg:sticky lg:top-24">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.06)] hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center aspect-square relative overflow-hidden">
                    @if($medicine->requires_prescription)
                        <div class="absolute top-4 left-4 bg-gradient-to-r from-red-500 to-rose-600 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg shadow-sm flex items-center gap-1.5 z-10 animate-pulse">
                            <i class="fa-solid fa-prescription-bottle-medical"></i> Resep Dokter
                        </div>
                    @endif
                    <div class="w-full h-full flex items-center justify-center p-2">
                        @if($medicine->image)
                            <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="max-w-full max-h-full object-contain hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="text-gray-300 text-6xl"><i class="fa-solid fa-pills"></i></div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Middle: Product Info & Specifications -->
            <div class="lg:col-span-5 space-y-5">
                <!-- Name & Category (Flat) -->
                <div class="px-2">
                    <span class="text-[10px] tracking-wider uppercase font-bold text-primary bg-primary/10 border border-primary/20 px-3 py-1 rounded-full w-fit mb-4 block">{{ $medicine->category->name }}</span>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2.5 leading-tight">{{ $medicine->name }}</h1>
                    
                    <!-- Rating and Sales Mock -->
                    <div class="flex items-center gap-3 text-xs text-text-muted mb-4">
                        <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-700 font-medium">Terjual <span class="font-bold">100+ obat</span></span>
                        <span class="text-gray-200">|</span>
                        <div class="flex items-center gap-1 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded text-amber-800 font-semibold">
                            <i class="fa-solid fa-star text-amber-500"></i>
                            <span>4.9</span>
                            <span class="text-amber-600/70 font-normal text-[10px] ml-0.5">(80+ rating)</span>
                        </div>
                    </div>

                    @if($medicine->brand)
                        <p class="text-xs text-text-muted mb-4">Merek: <a href="#" class="font-bold text-primary hover:text-primary-dark hover:underline transition">{{ $medicine->brand }}</a></p>
                    @endif

                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <span class="text-4xl font-extrabold text-gray-900 tracking-tight">Rp{{ number_format($medicine->price, 0, ',', '.') }}</span>
                        @if($medicine->price_before_discount)
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-[10px] font-extrabold bg-rose-50 text-rose-600 border border-rose-100 px-2 py-0.5 rounded-md">
                                    DISKON {{ round((($medicine->price_before_discount - $medicine->price) / $medicine->price_before_discount) * 100) }}%
                                </span>
                                <span class="text-sm text-gray-400 line-through">Rp{{ number_format($medicine->price_before_discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Purchase Card (Sticky) -->
            <div class="lg:col-span-3 lg:sticky lg:top-24">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_15px_35px_rgba(0,0,0,0.04)] space-y-5">
                    <h3 class="text-xs tracking-wider uppercase font-bold text-gray-500 mb-1">Atur jumlah & catatan</h3>
                    
                    <form action="{{ route('cart.add', $medicine->id) }}" method="POST" id="purchaseForm">
                        @csrf
                        <!-- Selected variant/product mini card -->
                        <div class="flex items-center gap-3 p-3 bg-gray-50/70 rounded-xl border border-gray-100">
                            @if($medicine->image)
                                <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="w-10 h-10 object-contain rounded-lg border border-gray-200/60 p-1 bg-white">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-white text-gray-400 flex items-center justify-center border border-gray-200/60"><i class="fa-solid fa-pills text-sm"></i></div>
                            @endif
                            <div class="flex-grow min-w-0">
                                <span class="text-xs text-gray-800 font-bold block truncate">{{ $medicine->name }}</span>
                                <span class="text-[10px] text-text-muted block mt-0.5">Kemasan: {{ $medicine->unit ?? 'Pcs' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 mb-4">
                            <div class="flex items-center border border-gray-200 rounded-xl bg-white p-0.5 shadow-sm hover:border-gray-300 transition-colors">
                                <button type="button" onclick="adjustQty(-1)" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition cursor-pointer">
                                    <i class="fa-solid fa-minus text-[10px]"></i>
                                </button>
                                <input type="number" id="qtyInput" name="quantity" value="1" min="1" max="{{ $medicine->stock }}" class="w-10 text-center font-extrabold text-gray-800 bg-transparent outline-none pointer-events-none text-sm" readonly>
                                <button type="button" onclick="adjustQty(1)" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition cursor-pointer">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                </button>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-text-muted block">Sisa Stok</span>
                                <span class="text-xs font-bold text-gray-800">{{ $medicine->stock }} {{ $medicine->unit ?? 'Pcs' }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-end border-t border-b border-gray-50 py-4 my-2">
                            <span class="text-xs text-gray-500 font-medium">Subtotal</span>
                            <span class="text-xl font-black text-gray-900 tracking-tight" id="subtotalDisplay">Rp{{ number_format($medicine->price, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex flex-col gap-2.5 mt-4">
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-[#00A651] to-[#008f45] hover:brightness-105 active:scale-[0.98] text-white font-bold rounded-xl text-xs transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer shadow-sm shadow-primary/20">
                                <i class="fa-solid fa-cart-plus"></i> + Keranjang
                            </button>
                            <button type="submit" name="buy_now" value="1" class="w-full py-3 bg-white border-2 border-primary/95 text-primary hover:bg-primary/5 active:scale-[0.98] font-bold rounded-xl text-xs transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-wallet"></i> Beli Langsung
                            </button>
                        </div>
                    </form>

                    <!-- Footer actions: Chat, Wishlist, Share -->
                    <div class="flex items-center justify-around pt-4 border-t border-gray-100 text-[11px] text-gray-500 font-bold mt-2">
                        <a href="#" class="flex items-center gap-1.5 hover:text-primary transition-transform hover:-translate-y-0.5 duration-200"><i class="fa-regular fa-comment-dots text-sm"></i> Chat</a>
                        <div class="w-px h-3.5 bg-gray-200"></div>
                        <a href="#" class="flex items-center gap-1.5 hover:text-rose-500 transition-transform hover:-translate-y-0.5 duration-200"><i class="fa-regular fa-heart text-sm"></i> Wishlist</a>
                        <div class="w-px h-3.5 bg-gray-200"></div>
                        <a href="#" class="flex items-center gap-1.5 hover:text-primary transition-transform hover:-translate-y-0.5 duration-200"><i class="fa-solid fa-share-nodes text-sm"></i> Share</a>
                    </div>
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
                            <a href="{{ route('product.detail', $relMed->slug) }}" class="h-40 p-4 flex items-center justify-center bg-white">
                                @if($relMed->image)
                                    <img src="{{ str_starts_with($relMed->image, '/') ? $relMed->image : '/' . $relMed->image }}" alt="{{ $relMed->name }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <div class="text-gray-300 text-4xl"><i class="fa-solid fa-pills"></i></div>
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
    <footer class="bg-white border-t border-border-muted pt-12 pb-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center text-sm text-text-muted">
                <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
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
            let newQty = currentQty + amount;

            if (newQty >= 1 && newQty <= maxStock) {
                qtyInput.value = newQty;
                let subtotal = unitPrice * newQty;
                subtotalDisplay.innerText = 'Rp' + subtotal.toLocaleString('id-ID');
            }
        }


    </script>
</body>
</html>
