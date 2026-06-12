<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resep Saya - Apotek Naufal</title>
    
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
                    <a href="{{ route('prescriptions.history') }}" class="px-3 py-2 text-xs font-semibold text-primary hover:underline flex items-center gap-1.5"><i class="fa-solid fa-file-prescription"></i> Resep Saya</a>
                    <div class="px-3 py-2 text-sm font-semibold text-text-main flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center"><i class="fa-solid fa-user"></i></div>
                        {{ auth()->user()->name }}
                    </div>
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
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <a href="/" class="text-text-muted hover:text-primary transition text-sm"><i class="fa-solid fa-house"></i> Beranda</a>
                <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
                <span class="text-text-main font-semibold text-sm">Resep Saya</span>
            </div>
            
            <a href="{{ route('prescriptions.create') }}" class="px-5 py-3 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-xl shadow-md shadow-primary/20 flex items-center justify-center gap-2 transition">
                <i class="fa-solid fa-plus"></i> Tebus Resep Baru
            </a>
        </div>

        <h1 class="text-2xl font-bold text-text-main mb-6">Tiket Konsultasi Resep Saya</h1>

        @if(session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-primary rounded-r-lg flex items-center justify-between text-green-800 shadow-sm mb-6">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-primary text-lg"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if($prescriptions->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="w-24 h-24 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto text-4xl mb-5 shadow-inner">
                <i class="fa-solid fa-file-prescription"></i>
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2">Belum Ada Tiket Resep</h3>
            <p class="text-sm text-text-muted mb-8 max-w-sm mx-auto">Anda belum pernah mengunggah resep dokter atau membuat tiket konsultasi apoteker.</p>
            <a href="{{ route('prescriptions.create') }}" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg text-sm transition">Unggah Resep Sekarang</a>
        </div>
        @else
        <div class="space-y-4">
            @foreach($prescriptions as $p)
            <!-- Ticket Card -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col md:flex-row justify-between gap-6 hover:shadow-md transition">
                <div class="space-y-3 flex-grow">
                    <!-- Ticket Header -->
                    <div class="flex flex-wrap items-center gap-3 text-xs text-text-muted">
                        <span><i class="fa-regular fa-calendar"></i> Diunggah: <strong>{{ $p->created_at->isoFormat('D MMMM YYYY HH:mm') }}</strong></span>
                        <span class="hidden md:inline">|</span>
                        <span>Nomor Tiket: <strong class="text-text-main">{{ $p->prescription_number }}</strong></span>
                    </div>

                    <!-- Details Summary -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 py-2 text-sm">
                        <div>
                            <span class="text-xs text-text-muted block">Nama Pasien:</span>
                            <strong class="text-text-main">{{ $p->patient_name }} ({{ $p->patient_age ?? '-' }} th)</strong>
                        </div>
                        <div>
                            <span class="text-xs text-text-muted block">Dokter Penulis:</span>
                            <strong class="text-text-main">{{ $p->doctor_name }}</strong>
                        </div>
                        <div>
                            <span class="text-xs text-text-muted block">Tanggal Resep:</span>
                            <strong class="text-text-main">{{ $p->prescription_date->isoFormat('D MMMM YYYY') }}</strong>
                        </div>
                    </div>

                    @if($p->customer_notes)
                    <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-xs text-text-muted">
                        <span class="font-bold text-gray-700 block mb-0.5"><i class="fa-solid fa-comment-dots mr-1 text-primary"></i> Catatan Pasien:</span>
                        <p class="italic">"{{ $p->customer_notes }}"</p>
                    </div>
                    @endif
                </div>

                <!-- Action & Status -->
                <div class="flex md:flex-col justify-between md:justify-center md:items-end shrink-0 gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100">
                    <div class="flex items-center gap-2 md:mb-1">
                        @php
                            $badgeColors = [
                                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                'verified' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'completed' => 'bg-green-50 text-green-700 border-green-200',
                                'rejected' => 'bg-red-50 text-red-700 border-red-200',
                            ];
                            $colorClass = $badgeColors[$p->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $colorClass }}">
                            <i class="fa-solid fa-circle-notch animate-spin mr-1 {{ $p->status === 'pending' || $p->status === 'processing' ? '' : 'hidden' }}"></i>
                            {{ $p->status_label }}
                        </span>
                    </div>

                    <a href="{{ route('prescriptions.show', $p->id) }}" class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl text-xs transition shadow-sm text-center flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-comments"></i> Konsultasi Chat
                    </a>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-6">
                {{ $prescriptions->links() }}
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
