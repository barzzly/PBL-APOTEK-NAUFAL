<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Apotek Naufal</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Apotek Naufal adalah apotek online terpercaya yang menyediakan berbagai macam obat, vitamin, dan alat kesehatan dengan harga terbaik dan pengiriman cepat.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                <li><a href="{{ route('category.show', $navCat->slug) }}" class="text-text-main hover:text-primary font-medium text-sm transition">{{ $navCat->name }}</a></li>
                @endforeach
                <li><a href="#" class="text-text-main hover:text-primary font-medium text-sm transition">Promo</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <div class="flex items-center gap-3 mb-6">
            <a href="/" class="text-text-muted hover:text-primary transition text-sm"><i class="fa-solid fa-house"></i> Beranda</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <span class="text-text-main font-semibold text-sm">Keranjang Belanja</span>
        </div>

        <h1 class="text-2xl font-bold text-text-main mb-6">Keranjang Belanja Anda</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <div id="cart-container" class="grid grid-cols-1 lg:grid-cols-3 gap-8 @if(empty($cart)) hidden @endif">
            <!-- Items Column -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 hidden md:grid grid-cols-12 text-xs font-semibold uppercase text-text-muted tracking-wider">
                        <div class="col-span-6">Produk</div>
                        <div class="col-span-2 text-center">Harga</div>
                        <div class="col-span-2 text-center">Jumlah</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>
                    
                    <div class="divide-y divide-gray-100 overflow-y-auto" id="cart-items-list" style="max-height: 450px;">
                        @foreach($cart as $item)
                        <!-- Item row -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-center" id="cart-item-row-{{ $item['id'] }}">
                            <div class="col-span-12 md:col-span-6 flex gap-4">
                                <div class="w-20 h-20 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center overflow-hidden shrink-0">
                                    @if($item['image'])
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="max-w-full max-h-full object-contain p-1">
                                    @else
                                        <div class="text-gray-300 text-3xl"><i class="fa-solid fa-pills"></i></div>
                                    @endif
                                </div>
                                <div class="flex flex-col justify-center">
                                    <h4 class="text-sm font-semibold text-text-main line-clamp-2">{{ $item['name'] }}</h4>
                                    <span class="text-xs text-text-muted mt-1">Satuan: {{ $item['unit'] ?? 'pcs' }}</span>
                                    @if($item['requires_prescription'])
                                        <span class="mt-2 inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 w-max"><i class="fa-solid fa-prescription"></i> Butuh Resep</span>
                                    @endif
                                    
                                    @if($item['stock'] == 0)
                                        <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 w-max">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Stok Habis (Out of Stock)
                                        </span>
                                    @elseif($item['quantity'] > $item['stock'])
                                        <span class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 w-max">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Sisa Stok: {{ $item['stock'] }} (Mohon kurangi jumlah)
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-span-6 md:col-span-2 flex md:flex-col justify-between md:justify-center md:items-center">
                                <span class="text-xs text-text-muted md:hidden font-medium">Harga</span>
                                <span class="text-sm font-medium text-text-main">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                            </div>

                            <div class="col-span-6 md:col-span-2 flex md:flex-col justify-between md:justify-center md:items-center">
                                <span class="text-xs text-text-muted md:hidden font-medium">Jumlah</span>
                                <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden w-28 bg-white">
                                    <button onclick="updateQty({{ $item['id'] }}, -1)" class="w-8 h-8 flex items-center justify-center hover:bg-gray-50 transition text-text-muted border-r border-gray-200">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                    <input type="text" id="qty-input-{{ $item['id'] }}" value="{{ $item['quantity'] }}" readonly class="w-12 h-8 text-center text-sm font-semibold text-text-main bg-white border-0 outline-none">
                                    <button onclick="updateQty({{ $item['id'] }}, 1)" class="w-8 h-8 flex items-center justify-center hover:bg-gray-50 transition text-text-muted border-l border-gray-200">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-span-12 md:col-span-2 flex justify-between md:flex-col items-center md:items-end">
                                <span class="text-xs text-text-muted md:hidden font-medium">Subtotal</span>
                                <div class="flex items-center gap-3 md:flex-col md:items-end">
                                    <span class="text-sm font-bold text-secondary" id="item-subtotal-{{ $item['id'] }}">
                                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </span>
                                    <button onclick="removeItem({{ $item['id'] }})" class="text-red-500 hover:text-red-700 text-xs flex items-center gap-1 transition">
                                        <i class="fa-solid fa-trash-can"></i> <span class="md:hidden">Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Summary Column -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <h3 class="text-base font-bold text-text-main">Ringkasan Belanja</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm text-text-muted">
                            <span>Subtotal</span>
                            <span id="cart-subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-text-muted pb-3 border-b border-gray-100">
                            <span>Estimasi Ongkir</span>
                            <span class="text-xs italic text-text-muted">Akan dihitung saat checkout</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-base font-bold text-text-main">Total Harga</span>
                            <span class="text-lg font-bold text-secondary" id="cart-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @php
                        $hasStockIssue = false;
                        foreach($cart as $item) {
                            if($item['stock'] == 0 || $item['quantity'] > $item['stock']) {
                                $hasStockIssue = true;
                                break;
                            }
                        }
                    @endphp

                    @if($hasStockIssue)
                        <button disabled class="w-full block py-3.5 bg-gray-300 text-gray-500 font-bold rounded-xl text-center text-sm cursor-not-allowed">
                            Lanjutkan ke Checkout
                        </button>
                        <p class="text-[11px] text-red-500 text-center mt-2">Ada produk dalam keranjang yang kehabisan stok atau melebihi stok tersedia. Mohon sesuaikan keranjang belanja Anda.</p>
                    @else
                        <a href="{{ route('checkout.index') }}" class="w-full block py-3.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl text-center text-sm shadow-sm transition-all hover:shadow-md">
                            Lanjutkan ke Checkout
                        </a>
                    @endif

                    <a href="/" class="w-full block mt-3 py-3 bg-white hover:bg-gray-50 border border-primary text-primary font-bold rounded-xl text-center text-sm transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Lanjutkan Berbelanja
                    </a>
                </div>
                
                <div class="p-4 rounded-xl bg-primary-light border border-green-100 text-xs text-primary flex items-start gap-3">
                    <i class="fa-solid fa-shield-halved text-base shrink-0 mt-0.5"></i>
                    <p class="leading-relaxed">Apotek Naufal menjamin keaslian obat 100%. Transaksi dilayani oleh tenaga kesehatan profesional bersertifikat.</p>
                </div>
            </div>
        </div>

        <!-- Empty Cart State -->
        <div id="empty-cart-state" class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm @if(!empty($cart)) hidden @endif">
            <div class="w-24 h-24 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto text-4xl mb-5 shadow-inner">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2">Keranjang Belanja Kosong</h3>
            <p class="text-sm text-text-muted mb-8 max-w-sm mx-auto">Anda belum menambahkan produk ke dalam keranjang belanja. Cari obat dan suplemen terbaik Anda sekarang!</p>
            <a href="/" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg text-sm transition">Mulai Belanja</a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted pt-16 pb-6 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-10 text-left">
                <div class="lg:col-span-2">
                    <div class="mb-4 text-left">
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
                    <ul class="flex flex-col gap-3 text-left">
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Tebus Resep</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Konsultasi Dokter</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Cek Lab</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Artikel Kesehatan</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Promo Menarik</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Bantuan & Panduan</h3>
                    <ul class="flex flex-col gap-3 text-left">
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Cara Belanja</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Metode Pembayaran</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Pengiriman</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Hubungi Kami</h3>
                    <ul class="flex flex-col gap-3 text-left">
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

    <!-- Ajax Script -->
    <script>
        function updateQty(medicineId, amount) {
            const input = document.getElementById(`qty-input-${medicineId}`);
            let newQty = parseInt(input.value) + amount;
            if (newQty < 1) return;

            // Show loading indicator
            Swal.fire({
                title: 'Memperbarui keranjang...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    medicine_id: medicineId,
                    quantity: newQty
                })
            })
            .then(res => res.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    input.value = data.quantity;
                    document.getElementById(`item-subtotal-${medicineId}`).innerText = `Rp ${data.item_subtotal}`;
                    document.getElementById('cart-subtotal').innerText = `Rp ${data.cart_subtotal}`;
                    document.getElementById('cart-total').innerText = `Rp ${data.cart_total}`;
                    document.getElementById('cart-badge').innerText = data.cart_count;

                    if (data.message.includes('disesuaikan')) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Penyesuaian Stok',
                            text: data.message,
                            confirmButtonColor: '#00A651',
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        window.location.reload();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal memperbarui',
                        text: data.message || 'Stok tidak mencukupi.'
                    });
                }
            })
            .catch(err => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Gagal terhubung ke server.'
                });
            });
        }

        function removeItem(medicineId) {
            Swal.fire({
                title: 'Hapus item?',
                text: 'Apakah Anda yakin ingin menghapus produk ini dari keranjang?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00A651',
                cancelButtonColor: '#757575',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus item...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route('cart.remove') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            medicine_id: medicineId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            // Remove item row
                            const row = document.getElementById(`cart-item-row-${medicineId}`);
                            row.remove();
                            
                            // Check if cart is empty
                            if (data.cart_count == 0) {
                                document.getElementById('cart-container').classList.add('hidden');
                                document.getElementById('empty-cart-state').classList.remove('hidden');
                            } else {
                                document.getElementById('cart-subtotal').innerText = `Rp ${data.cart_subtotal}`;
                                document.getElementById('cart-total').innerText = `Rp ${data.cart_total}`;
                            }
                            document.getElementById('cart-badge').innerText = data.cart_count;

                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus',
                                text: data.message,
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: 'Gagal menghapus item dari keranjang.'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>
