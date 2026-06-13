<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Apotek Naufal</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Apotek Naufal adalah apotek online terpercaya yang menyediakan berbagai macam obat, vitamin, dan alat kesehatan dengan harga terbaik dan pengiriman cepat.">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet.js Map Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex flex-col min-h-screen">



    <!-- Header -->
    <header class="bg-white py-4 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4 lg:gap-8">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-notes-medical text-3xl"></i> Apotek Naufal
            </a>

            <div class="flex items-center gap-5 ml-auto">
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

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <div class="flex items-center gap-3 mb-6">
            <a href="/" class="text-text-muted hover:text-primary transition text-sm"><i class="fa-solid fa-house"></i> Beranda</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <a href="{{ route('cart.index') }}" class="text-text-muted hover:text-primary transition text-sm">Keranjang Belanja</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <span class="text-text-main font-semibold text-sm">Checkout</span>
        </div>

        <h1 class="text-2xl font-bold text-text-main mb-6">Checkout</h1>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                <h4 class="font-bold mb-2 flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Terjadi Kesalahan Input:</h4>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" id="checkout-form">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Inputs Section -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- 1. Tipe Pengiriman -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs">1</span>
                            Tipe Pengiriman
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Option Pickup -->
                            <label class="border-2 border-primary rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="label-pickup">
                                <input type="radio" name="order_type" value="pickup" checked onchange="toggleOrderType('pickup')" class="mt-1 accent-primary">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-sm text-text-main">Ambil di Apotek (Pickup)</span>
                                    <span class="text-xs text-text-muted mt-1">Tanpa Biaya Pengiriman</span>
                                    <span class="text-[10px] text-primary font-semibold mt-2"><i class="fa-solid fa-location-dot"></i> Jl. Andalas Raya No. 125, Padang</span>
                                </div>
                            </label>
                            
                            <!-- Option Delivery -->
                            <label class="border border-gray-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="label-delivery">
                                <input type="radio" name="order_type" value="delivery" onchange="toggleOrderType('delivery')" class="mt-1 accent-primary">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-sm text-text-main">Kirim ke Alamat (Delivery)</span>
                                    <span class="text-xs text-text-muted mt-1">Tarif Jarak: Rp 2.500/km (Min. Rp 10.000)</span>
                                    <span class="text-[10px] text-secondary font-semibold mt-2"><i class="fa-solid fa-motorcycle"></i> Pengiriman Instant / Reguler</span>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Shipping Address Container -->
                        <div id="address-container" class="hidden space-y-4 mt-4 pt-2 border-t border-gray-100">
                            <label class="text-xs font-semibold text-text-main block">Pilih Titik Antar di Peta <span class="text-red-500">*</span></label>
                            
                            <!-- Leaflet Map Container -->
                            <div class="relative w-full rounded-xl border border-gray-200 overflow-hidden" style="height: 320px;">
                                <div id="map" class="w-full h-full" style="z-index: 1;"></div>
                                <!-- Overlay indicator when loading geocode -->
                                <div id="map-loader" class="absolute inset-0 bg-white/70 z-[1000] flex items-center justify-center hidden">
                                    <div class="flex items-center gap-2 text-xs font-semibold text-primary">
                                        <i class="fa-solid fa-spinner fa-spin text-lg"></i> Mencari alamat...
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden Fields for Location Coords and Distance -->
                            <input type="hidden" name="delivery_latitude" id="delivery_latitude" value="{{ old('delivery_latitude') }}">
                            <input type="hidden" name="delivery_longitude" id="delivery_longitude" value="{{ old('delivery_longitude') }}">
                            <input type="hidden" name="delivery_distance" id="delivery_distance" value="{{ old('delivery_distance') }}">

                            <!-- Delivery Info Badge -->
                            <div id="delivery-info-badge" class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs flex flex-wrap gap-4 items-center justify-between hidden">
                                <div class="flex items-center gap-1.5 text-text-main">
                                    <i class="fa-solid fa-route text-primary text-sm"></i>
                                    <span>Jarak Pengiriman: <strong id="display-distance">0.0 km</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5 text-text-main">
                                    <i class="fa-solid fa-money-bill-wave text-secondary text-sm"></i>
                                    <span>Ongkir Terhitung: <strong id="display-shipping" class="text-secondary">Rp 0</strong></span>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-text-main block">Alamat Pengiriman Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="shipping_address" id="shipping_address" placeholder="Pilih lokasi pada peta di atas untuk mengisi alamat secara otomatis, lalu tambahkan detail (No. Rumah, RT/RW, Patokan)..." class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition h-24">{{ old('shipping_address') }}</textarea>
                                <p class="text-[10px] text-text-muted">Alamat di atas akan terisi otomatis saat Anda mengklik peta. Anda dapat menyuntingnya jika diperlukan.</p>
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-text-main block">Detail Lokasi seperti warna pagar dan cat rumah <span class="text-red-500">*</span></label>
                                <input type="text" name="location_details" id="location_details" placeholder="Contoh: Pagar besi hitam, cat rumah kuning, depan warung..." value="{{ old('location_details') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition">
                                <p class="text-[10px] text-text-muted">Membantu kurir menemukan lokasi Anda dengan lebih mudah.</p>
                            </div>
                        </div>

                        <!-- Catatan Tambahan -->
                        <div class="space-y-1 mt-4 pt-4 border-t border-gray-100">
                            <label class="text-xs font-semibold text-text-main block">Catatan Tambahan</label>
                            <textarea name="notes" placeholder="Tuliskan catatan khusus untuk apoteker atau kurir jika diperlukan..." class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition h-20">{{ old('notes') }}</textarea>
                        </div>
                    </div>



                    <!-- 3. Metode Pembayaran -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs">2</span>
                            Metode Pembayaran
                        </h3>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Tunai -->
                            <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="pay-cash">
                                <input type="radio" name="payment_method" value="cash" checked onchange="togglePaymentMethod('cash')" class="absolute top-3 right-3 accent-primary">
                                <div class="text-xl text-primary mt-2"><i class="fa-solid fa-money-bill-wave"></i></div>
                                <span class="text-xs font-semibold text-text-main mt-1">Tunai</span>
                            </label>
                            
                            <!-- Transfer Bank -->
                            <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="pay-transfer">
                                <input type="radio" name="payment_method" value="transfer" onchange="togglePaymentMethod('transfer')" class="absolute top-3 right-3 accent-primary">
                                <div class="text-xl text-primary mt-2"><i class="fa-solid fa-building-columns"></i></div>
                                <span class="text-xs font-semibold text-text-main mt-1">Transfer Bank</span>
                            </label>
                            
                            <!-- QRIS -->
                            <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="pay-qris">
                                <input type="radio" name="payment_method" value="qris" onchange="togglePaymentMethod('qris')" class="absolute top-3 right-3 accent-primary">
                                <div class="text-xl text-primary mt-2"><i class="fa-solid fa-qrcode"></i></div>
                                <span class="text-xs font-semibold text-text-main mt-1">QRIS / E-Wallet</span>
                            </label>

                            <!-- BPJS -->
                            <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="pay-bpjs">
                                <input type="radio" name="payment_method" value="bpjs" onchange="togglePaymentMethod('bpjs')" class="absolute top-3 right-3 accent-primary">
                                <div class="text-xl text-primary mt-2"><i class="fa-solid fa-id-card"></i></div>
                                <span class="text-xs font-semibold text-text-main mt-1">BPJS</span>
                            </label>
                        </div>

                        <!-- Transfer Instruction Info -->
                        <div id="transfer-info" class="hidden p-4 rounded-xl bg-gray-50 border border-gray-200 space-y-3 mt-4 text-xs text-text-main">
                            <h4 class="font-bold text-sm text-primary">Informasi Rekening Bank Apotek Naufal</h4>
                            <p>Silakan lakukan pembayaran ke nomor rekening di bawah ini:</p>
                            <div class="p-3 bg-white border border-gray-100 rounded-lg font-mono flex justify-between items-center text-sm">
                                <div>
                                    <span class="text-text-muted text-[10px] font-sans block uppercase">Bank Mandiri</span>
                                    <strong>123-000-456789-0</strong>
                                    <span class="text-xs font-sans text-text-muted block">a/n Apotek Naufal Jaya</span>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2">
                                <label class="text-xs font-semibold block">Unggah Bukti Transfer (Opsional)</label>
                                <input type="file" name="payment_proof" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                                <p class="text-[10px] text-text-muted">Format file: JPG, PNG, JPEG. Ukuran maksimum: 2MB. Anda juga dapat mengunggah bukti setelah pemesanan dilakukan.</p>
                            </div>
                        </div>

                        <!-- QRIS Instruction Info -->
                        <div id="qris-info" class="hidden p-4 rounded-xl bg-gray-50 border border-gray-200 space-y-3 mt-4 text-xs text-text-main flex flex-col items-center">
                            <h4 class="font-bold text-sm text-primary w-full text-left">Pembayaran QRIS Apotek Naufal</h4>
                            <p class="w-full">Pindai kode QR di bawah ini menggunakan aplikasi e-wallet Anda (Gopay, OVO, Dana, LinkAja) atau m-Banking:</p>
                            
                            <!-- Mock QR Code Design -->
                            <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col items-center shadow-inner mt-2">
                                <div class="w-40 h-40 bg-gray-200 flex items-center justify-center border-4 border-primary relative">
                                    <i class="fa-solid fa-qrcode text-8xl text-gray-800"></i>
                                    <div class="absolute inset-0 bg-black/5 flex items-center justify-center">
                                        <span class="bg-primary text-white font-bold text-[10px] px-2 py-1 rounded shadow-sm">APOTEK NAUFAL</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold tracking-widest mt-2 uppercase">NMID: ID10304958102</span>
                            </div>

                            <div class="space-y-2 pt-2 w-full">
                                <label class="text-xs font-semibold block">Unggah Bukti Bayar QRIS (Opsional)</label>
                                <input type="file" name="payment_proof_qris" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                                <p class="text-[10px] text-text-muted">Format file: JPG, PNG, JPEG. Ukuran maksimum: 2MB.</p>
                            </div>
                        </div>

                        <!-- BPJS Info -->
                        <div id="bpjs-info" class="hidden p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 leading-relaxed mt-4">
                            <h4 class="font-bold mb-1"><i class="fa-solid fa-circle-info"></i> Ketentuan Klaim BPJS</h4>
                            <p>Pembayaran menggunakan BPJS memerlukan verifikasi manual oleh staf apotek kami. Pastikan kartu BPJS Anda aktif dan unggah resep dokter BPJS resmi pada form resep dokter di atas.</p>
                        </div>
                    </div>
                </div>

                <!-- Summary Column -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <h3 class="text-base font-bold text-text-main">Ringkasan Pesanan</h3>
                        
                        <!-- Mini items list -->
                        <div class="divide-y divide-gray-100 max-h-60 overflow-y-auto pr-1">
                            @foreach($cart as $item)
                            <div class="py-3 flex justify-between gap-3 text-xs">
                                <div class="flex-grow">
                                    <span class="font-medium text-text-main block">{{ $item['name'] }}</span>
                                    <span class="text-text-muted text-[10px]">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }} ({{ $item['unit'] ?? 'pcs' }})</span>
                                </div>
                                <span class="font-semibold text-text-main shrink-0">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="space-y-3 pt-4 border-t border-gray-100">
                            <div class="flex justify-between text-sm text-text-muted">
                                <span>Subtotal Produk</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-text-muted">
                                <span>Biaya Pengiriman</span>
                                <span id="shipping-cost-label">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <span class="text-base font-bold text-text-main">Total Bayar</span>
                                <span class="text-lg font-bold text-secondary" id="total-amount-label">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl text-center text-sm shadow-sm transition-all hover:shadow-md cursor-pointer">
                            Buat Pesanan Sekarang
                        </button>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 space-y-3">
                        <h4 class="text-xs font-bold text-text-main uppercase tracking-wider"><i class="fa-solid fa-shield-halved text-primary mr-1"></i> Keamanan & Privasi</h4>
                        <p class="text-[10px] text-text-muted leading-relaxed">Seluruh transaksi Anda dienkripsi secara aman. Data resep dan rekam medis dilindungi sesuai undang-undang kesehatan Republik Indonesia.</p>
                    </div>
                </div>
            </div>
        </form>
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
                        <li><a href="#" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-location-dot w-5 text-center"></i> Jl. Andalas Raya No. 125, Padang</a></li>
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

    <!-- Interactive script for dynamic forms -->
    <script>
        const subtotal = {{ $subtotal }};
        
        let currentShippingFee = 15000; // default initial fee
        
        function calculateShippingFee(distance) {
            if (distance <= 0) return 0;
            // Rp 2.500 per km, minimal Rp 10.000
            let fee = Math.ceil(distance) * 2500;
            return Math.max(fee, 10000);
        }

        function updateTotals() {
            const shippingCostLabel = document.getElementById('shipping-cost-label');
            const totalAmountLabel = document.getElementById('total-amount-label');
            const submitBtn = document.querySelector('button[type="submit"]');
            
            const isDelivery = document.querySelector('input[name="order_type"]:checked').value === 'delivery';
            
            if (isDelivery && currentDistance > 50) {
                shippingCostLabel.innerHTML = `<span class="text-red-500 font-semibold">Jarak Terlalu Jauh</span>`;
                totalAmountLabel.innerHTML = `<span class="text-red-500 font-bold">Jarak Terlalu Jauh</span>`;
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerText = 'Jarak Pengiriman Terlalu Jauh';
                }
            } else {
                const fee = isDelivery ? currentShippingFee : 0;
                shippingCostLabel.innerText = fee > 0 ? formatRupiah(fee) : "Rp 0";
                totalAmountLabel.innerText = formatRupiah(subtotal + fee);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerText = 'Buat Pesanan Sekarang';
                }
            }
        }

        function toggleOrderType(type) {
            const addressContainer = document.getElementById('address-container');
            const inputAddress = document.getElementById('shipping_address');
            const inputDetails = document.getElementById('location_details');

            const labelPickup = document.getElementById('label-pickup');
            const labelDelivery = document.getElementById('label-delivery');

            if (type === 'delivery') {
                addressContainer.classList.remove('hidden');
                inputAddress.setAttribute('required', 'required');
                if (inputDetails) inputDetails.setAttribute('required', 'required');
                
                // Styling labels
                labelDelivery.className = "border-2 border-primary rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
                labelPickup.className = "border border-gray-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
                
                // Initialize Leaflet map
                initMap();
            } else {
                addressContainer.classList.add('hidden');
                inputAddress.removeAttribute('required');
                if (inputDetails) inputDetails.removeAttribute('required');
                
                // Styling labels
                labelPickup.className = "border-2 border-primary rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
                labelDelivery.className = "border border-gray-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
            }
            updateTotals();
        }

        // --- Leaflet Map Logic ---
        let map, pharmacyMarker, userMarker, routeLayer = null, currentDistance = 0;
        const pharmacyCoords = [-0.937722, 100.3878982]; // Apotek Naufal coordinates
        let mapInitialized = false;

        function initMap() {
            if (mapInitialized) {
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
                return;
            }

            // Create Leaflet map centered at Apotek Naufal
            map = L.map('map').setView(pharmacyCoords, 15);

            // Load OSM tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add Pharmacy Marker (Red Icon)
            pharmacyMarker = L.marker(pharmacyCoords).addTo(map)
                .bindPopup('<strong>Apotek Naufal</strong><br>Jl. Andalas Raya No. 125')
                .openPopup();

            // Default position of delivery marker is Apotek Naufal initially
            let initialUserCoords = pharmacyCoords;
            
            const oldLat = document.getElementById('delivery_latitude').value;
            const oldLng = document.getElementById('delivery_longitude').value;
            if (oldLat && oldLng) {
                initialUserCoords = [parseFloat(oldLat), parseFloat(oldLng)];
            }

            // Create Draggable Delivery Marker
            userMarker = L.marker(initialUserCoords, {
                draggable: true
            }).addTo(map);

            userMarker.bindPopup('<strong>Titik Pengantaran Anda</strong><br>Geser/Klik peta untuk memilih titik.').openPopup();

            // On drag end: recalculate distance and address
            userMarker.on('dragend', function (e) {
                const position = userMarker.getLatLng();
                updateDeliveryLocation(position.lat, position.lng);
            });

            // On map click: move marker and recalculate
            map.on('click', function (e) {
                userMarker.setLatLng(e.latlng);
                updateDeliveryLocation(e.latlng.lat, e.latlng.lng);
            });

            mapInitialized = true;

            // If we have old values, calculate initial distance
            if (oldLat && oldLng) {
                updateDeliveryLocation(parseFloat(oldLat), parseFloat(oldLng), false);
            }
        }

        // Calculate Haversine distance in km
        function getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of earth in km
            const dLat = deg2rad(lat2 - lat1);
            const dLon = deg2rad(lon2 - lon1);
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c; // Distance in km
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180);
        }

        // Update location, calculate distance, and geocode address via OpenStreetMap Nominatim
        function updateDeliveryLocation(lat, lng, doGeocode = true) {
            document.getElementById('delivery_latitude').value = lat;
            document.getElementById('delivery_longitude').value = lng;
            document.getElementById('delivery-info-badge').classList.remove('hidden');

            const loader = document.getElementById('map-loader');
            if (loader && doGeocode) {
                loader.classList.remove('hidden');
            }

            // Fetch from local calculation route
            fetch(`/checkout/calculate-distance?latitude=${lat}&longitude=${lng}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        applyDistanceAndShipping(data.distance, data.shipping_cost, data.formatted_shipping_cost);

                        // Draw routing geometry
                        if (data.geometry) {
                            if (routeLayer) {
                                map.removeLayer(routeLayer);
                            }
                            routeLayer = L.geoJSON(data.geometry, {
                                style: {
                                    color: '#2563eb', // Premium blue color
                                    weight: 4,
                                    opacity: 0.8
                                }
                            }).addTo(map);

                            // Fit map bounds to show route
                            const bounds = routeLayer.getBounds();
                            map.fitBounds(bounds, { padding: [30, 30] });
                        }
                    } else {
                        fallbackDistanceCalculation(lat, lng);
                    }
                })
                .catch(err => {
                    console.error("Local route calculation error: ", err);
                    fallbackDistanceCalculation(lat, lng);
                })
                .then(() => {
                    if (doGeocode) {
                        return fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.display_name) {
                                    document.getElementById('shipping_address').value = data.display_name;
                                }
                            });
                    }
                })
                .catch(err => {
                    console.error("Geocoding error: ", err);
                })
                .finally(() => {
                    if (loader && doGeocode) {
                        loader.classList.add('hidden');
                    }
                });
        }

        function fallbackDistanceCalculation(lat, lng) {
            const distance = getDistance(pharmacyCoords[0], pharmacyCoords[1], lat, lng);
            const calculatedFee = calculateShippingFee(distance);
            applyDistanceAndShipping(distance, calculatedFee, formatRupiah(calculatedFee));

            // Draw straight line fallback route
            const fallbackGeometry = {
                type: 'LineString',
                coordinates: [
                    [pharmacyCoords[1], pharmacyCoords[0]],
                    [lng, lat]
                ]
            };
            if (routeLayer) {
                map.removeLayer(routeLayer);
            }
            routeLayer = L.geoJSON(fallbackGeometry, {
                style: {
                    color: '#9ca3af', // gray color for fallback
                    weight: 3,
                    opacity: 0.6,
                    dashArray: '5, 5'
                }
            }).addTo(map);
        }

        function applyDistanceAndShipping(distance, shippingCost, formattedCost) {
            document.getElementById('delivery_distance').value = distance.toFixed(2);
            
            // Update global distance state
            currentDistance = distance;

            const displayDistance = document.getElementById('display-distance');
            const displayShipping = document.getElementById('display-shipping');
            const deliveryLabel = document.querySelector('#label-delivery .text-text-muted');

            if (distance > 50) {
                displayDistance.innerHTML = `<span class="text-red-500 font-bold">${distance.toFixed(2)} km (Terlalu Jauh)</span>`;
                displayShipping.innerHTML = `<span class="text-red-500 font-bold">Jarak Terlalu Jauh</span>`;
                
                if (deliveryLabel) {
                    deliveryLabel.innerHTML = `<span class="text-red-500 font-semibold">Jarak Terlalu Jauh (Maks. 50 km)</span>`;
                }
                
                currentShippingFee = 0;
            } else {
                displayDistance.innerText = distance.toFixed(2) + " km";
                displayShipping.innerText = formattedCost;
                
                if (deliveryLabel) {
                    deliveryLabel.innerText = "Ongkir Jarak: " + formattedCost + " (" + distance.toFixed(2) + " km)";
                }
                
                currentShippingFee = shippingCost;
            }

            updateTotals();
        }

        function togglePaymentMethod(method) {
            const transferInfo = document.getElementById('transfer-info');
            const qrisInfo = document.getElementById('qris-info');
            const bpjsInfo = document.getElementById('bpjs-info');

            // Reset borders
            document.getElementById('pay-cash').className = "border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
            document.getElementById('pay-transfer').className = "border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
            document.getElementById('pay-qris').className = "border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
            document.getElementById('pay-bpjs').className = "border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";

            // Set primary border for active payment method
            document.getElementById(`pay-${method}`).className = "border-2 border-primary rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";

            // Show instruction
            transferInfo.classList.add('hidden');
            qrisInfo.classList.add('hidden');
            bpjsInfo.classList.add('hidden');

            if (method === 'transfer') {
                transferInfo.classList.remove('hidden');
            } else if (method === 'qris') {
                qrisInfo.classList.remove('hidden');
            } else if (method === 'bpjs') {
                bpjsInfo.classList.remove('hidden');
            }
        }

        // Initialize active class
        togglePaymentMethod('cash');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(number).replace("Rp", "Rp ");
        }

        // Form submission loading
        document.getElementById('checkout-form').addEventListener('submit', function () {
            Swal.fire({
                title: 'Sedang memproses pesanan...',
                text: 'Mohon tunggu beberapa saat.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>
</body>
</html>
