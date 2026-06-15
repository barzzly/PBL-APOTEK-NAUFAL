<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Apotek Naufal</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white py-4 sticky top-0 z-50 shadow-sm border-b border-border-muted">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4 lg:gap-8">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-notes-medical text-3xl"></i> Apotek Naufal
            </a>

            <div class="flex items-center gap-5 ml-auto">
                <a href="{{ route('cart.index') }}" class="text-text-main hover:text-primary text-xl relative transition">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="absolute -top-2 -right-2.5 bg-secondary text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $cartCount }}
                    </span>
                </a>
                <div class="flex items-center gap-3">
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
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <div class="flex items-center gap-3 mb-6">
            <a href="/" class="text-text-muted hover:text-primary transition text-sm"><i class="fa-solid fa-house"></i> Beranda</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <span class="text-text-main font-semibold text-sm">Pesanan Saya</span>
        </div>

        <h1 class="text-2xl font-bold text-text-main mb-6">Riwayat Pesanan Anda</h1>

        @if($orders->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="w-24 h-24 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto text-4xl mb-5 shadow-inner">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2">Belum Ada Pesanan</h3>
            <p class="text-sm text-text-muted mb-8 max-w-sm mx-auto">Anda belum pernah melakukan pemesanan obat di platform kami.</p>
            <a href="/" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg text-sm transition">Cari Obat Sekarang</a>
        </div>
        @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <!-- Order Card -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between gap-6 hover:shadow-md transition">
                <div class="space-y-3 flex-grow">
                    <!-- Top header of card -->
                    <div class="flex flex-wrap items-center gap-3 text-xs text-text-muted">
                        <span><i class="fa-regular fa-calendar"></i> {{ $order->created_at->isoFormat('D MMMM YYYY HH:mm') }}</span>
                        <span class="hidden md:inline">|</span>
                        <span>No. Order: <strong class="text-text-main">{{ $order->order_number }}</strong></span>
                        <span class="hidden md:inline">|</span>
                        <span>Tipe: <strong>{{ $order->order_type === 'delivery' ? 'Pengiriman' : 'Ambil Sendiri' }}</strong></span>
                    </div>

                    <!-- Items Preview -->
                    <div class="flex items-center gap-4 py-2">
                        <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center text-primary text-xl shrink-0">
                            <i class="fa-solid fa-prescription-bottle-medical"></i>
                        </div>
                        <div>
                            @php
                                $firstItem = $order->items->first();
                                $extraCount = $order->items->count() - 1;
                            @endphp
                            <h4 class="text-sm font-semibold text-text-main">
                                {{ $firstItem->medicine_name ?? 'Produk Obat' }} 
                                @if($extraCount > 0)
                                <span class="text-xs text-text-muted font-normal">dan {{ $extraCount }} produk lainnya</span>
                                @endif
                            </h4>
                            <p class="text-xs text-text-muted mt-0.5">Jumlah: {{ $order->items->sum('quantity') }} item</p>
                        </div>
                    </div>
                </div>

                <!-- Price and Status info -->
                <div class="flex md:flex-col justify-between md:justify-center md:items-end shrink-0 gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100">
                    <div class="text-left md:text-right">
                        <span class="text-[10px] text-text-muted block uppercase tracking-wider">Total Belanja</span>
                        <span class="text-base font-bold text-secondary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <!-- Payment Status Badge -->
                        @if($order->payment_status === 'paid')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                <i class="fa-solid fa-circle-check"></i> Lunas
                            </span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                Dikembalikan
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                Belum Bayar
                            </span>
                        @endif

                        <!-- Order Status Badge -->
                        @php
                            $badgeColors = [
                                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'ready_for_pickup' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'shipped' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                'delivered' => 'bg-green-50 text-green-700 border-green-200',
                                'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                            ];
                            $colorClass = $badgeColors[$order->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $colorClass }}">
                            {{ $order->status_label }}
                        </span>
                    </div>

                    <a href="{{ route('orders.show', $order->id) }}" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg text-xs transition shadow-sm text-center">
                        Detail Pesanan
                    </a>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-6">
                {{ $orders->links() }}
            </div>
        </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted py-8 mt-12 text-center text-xs text-text-muted">
        <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
    </footer>

</body>
</html>
