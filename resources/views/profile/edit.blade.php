<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_apotek_naufal.png') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Apotek Naufal</title>
    
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
    <style>
        .avatar-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }
        .avatar-option {
            width: 68px;
            height: 68px;
            padding: 4px;
            border: 2px solid #e2e5e2; /* --color-border-muted */
            background-color: #ffffff;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            outline: none;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        .avatar-option:hover {
            border-color: #79AE6F; /* --color-secondary */
            transform: translateY(-2px);
        }
        .avatar-option.selected {
            border-color: #346739; /* --color-primary */
            background-color: #e6efe5; /* --color-primary-light */
            box-shadow: 0 0 0 3px rgba(52, 103, 57, 0.25);
        }
        .avatar-option img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
        .profile-header-container {
            display: flex;
            align-items: center;
            gap: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e2e5e2; /* border-gray-100 */
        }
        .profile-meta-info {
            text-align: left;
        }
        .profile-name {
            font-size: 20px;
            font-weight: 700;
            color: #333333;
            margin: 0;
            line-height: 1.2;
        }
        .profile-badges-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }
        @media (max-width: 640px) {
            .profile-header-container {
                flex-direction: column;
                text-align: center;
            }
            .profile-meta-info {
                text-align: center;
            }
            .profile-badges-row {
                justify-content: center;
            }
        }
    </style>
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex flex-col min-h-screen">

    <!-- Header -->
    <header class="ui-glass-nav py-3 sticky top-0 z-50 border-b">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4 lg:gap-8">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <img src="{{ asset('images/logo_apotek_naufal.png') }}" class="h-8 w-auto object-contain" alt="Logo Apotek Naufal"> Apotek Naufal
            </a>

            <div class="flex-grow w-full lg:w-auto order-3 lg:order-none relative max-w-2xl">
                <form action="{{ route('home') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Cari obat, vitamin, atau suplemen..." 
                        class="w-full py-2.5 px-5 pr-12 ui-input rounded-full text-sm outline-none transition header-search-input">
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-primary text-lg cursor-pointer">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
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
                            <a href="{{ route('tickets.history') }}" class="px-3 py-2 text-xs font-semibold text-primary hover:underline flex items-center gap-1.5"><i class="fa-solid fa-ticket"></i> Ticket Saya</a>
                            
                            {{-- Clickable profile box --}}
                            <a href="{{ route('profile.edit') }}" class="px-3 py-2 text-sm font-semibold text-primary border border-primary/20 rounded-xl bg-primary-light/30 hover:bg-primary-light/60 transition flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center overflow-hidden border border-white shadow-sm shrink-0">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ str_starts_with(auth()->user()->avatar, '/') ? auth()->user()->avatar : '/' . auth()->user()->avatar }}" class="w-full h-full object-cover" id="header-avatar-preview">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                <span class="max-w-[120px] truncate">{{ auth()->user()->name }}</span>
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

    <!-- Navigation -->
    <nav class="bg-white/70 backdrop-blur border-b border-border-muted">
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
    <main class="flex-grow bg-gray-50/50 py-10">
        <div class="max-w-4xl mx-auto px-4">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-3 mb-6 text-xs text-text-muted">
                <a href="/" class="hover:text-primary transition"><i class="fa-solid fa-house"></i> Beranda</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
                <span class="text-gray-600 font-bold" id="breadcrumb-title">Profil Saya</span>
            </div>

            <!-- Page Title Header -->
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-text-main">
                        <span id="page-title">Profil Saya</span>
                    </h1>
                </div>
                <!-- Toggle Edit Button -->
                <button type="button" id="btn-toggle-edit" onclick="toggleEditMode()" class="px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-sm shrink-0 cursor-pointer">
                    <i class="fa-solid fa-user-pen"></i> Edit Profil
                </button>
            </div>

            <!-- UNIFIED PROFILE CARD CONTAINER -->
            <div class="ui-card p-6 md:p-8">
                
                <!-- Profile Header Section -->
                <div class="profile-header-container">
                    <!-- Avatar Circle -->
                    <div class="relative w-24 h-24 rounded-full overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center shrink-0">
                        <img id="avatar-image-preview" 
                             src="{{ $user->avatar ? (str_starts_with($user->avatar, '/') ? $user->avatar : '/' . $user->avatar) : '/images/avatars/avatar1.png' }}" 
                             class="w-full h-full object-cover">
                    </div>
                    
                    <!-- User Meta Info -->
                    <div class="profile-meta-info flex-grow">
                        <h2 class="profile-name">{{ $user->name }}</h2>
                        <div class="profile-badges-row">
                            <span class="text-xs font-semibold text-primary bg-primary-light px-2.5 py-0.5 rounded-full">
                                {{ auth()->user()->role === 'admin' ? 'Administrator' : 'Customer' }}
                            </span>
                            <span class="text-xs text-text-muted flex items-center gap-1">
                                <i class="fa-regular fa-calendar-days text-primary"></i> Bergabung {{ $user->created_at->isoFormat('D MMMM YYYY') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- 3. Dynamic Details Panel (View/Edit) -->
                <div class="mt-6">
                    
                    <!-- VIEW MODE PANEL -->
                    <div id="view-mode-panel" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name info -->
                            <div>
                                <span class="text-xs font-semibold text-text-muted block mb-1">Nama Lengkap</span>
                                <span class="text-sm font-medium text-text-main block bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">{{ $user->name }}</span>
                            </div>
                            <!-- Email info -->
                            <div>
                                <span class="text-xs font-semibold text-text-muted block mb-1">Alamat Email</span>
                                <span class="text-sm font-medium text-text-main block bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">{{ $user->email }}</span>
                            </div>
                            <!-- Phone info -->
                            <div>
                                <span class="text-xs font-semibold text-text-muted block mb-1">Nomor Telepon</span>
                                <span class="text-sm font-medium text-text-main block bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">{{ $user->phone }}</span>
                            </div>
                            <!-- Role/Status info -->
                            <div>
                                <span class="text-xs font-semibold text-text-muted block mb-1">Status Hak Akses</span>
                                <span class="text-sm font-medium text-text-main block bg-gray-50 px-4 py-3 rounded-xl border border-gray-100">{{ auth()->user()->role === 'admin' ? 'Akses Admin' : 'Akses Pelanggan' }}</span>
                            </div>
                            <!-- Address info -->
                            <div class="md:col-span-2">
                                <span class="text-xs font-semibold text-text-muted block mb-1">Alamat Lengkap</span>
                                <div class="text-sm font-medium text-text-main block bg-gray-50 px-4 py-3 rounded-xl border border-gray-100 min-h-[80px] leading-relaxed">
                                    {{ $user->address ?? 'Alamat pengiriman belum ditambahkan.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- EDIT MODE PANEL (HIDDEN BY DEFAULT) -->
                    <div id="edit-mode-panel" class="hidden">
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <!-- Avatar Selection Grid -->
                            <div class="space-y-3 p-4 bg-gray-50/50 border border-gray-100 rounded-xl">
                                <label class="block text-xs font-bold text-text-muted uppercase tracking-wider mb-1">
                                    Pilih Gambar Avatar Baru
                                </label>
                                <div class="avatar-grid">
                                    @for($i = 1; $i <= 10; $i++)
                                    <button type="button" onclick="selectAvatar('avatar{{ $i }}')" id="opt-avatar{{ $i }}" class="avatar-option">
                                        <img src="/images/avatars/avatar{{ $i }}.png">
                                    </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="avatar" id="selected-avatar" value="">
                            </div>
                            
                            <!-- Personal Details inputs -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2" for="name">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" required>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2" for="email">Alamat Email <span class="text-red-500">*</span></label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" required>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2" for="phone">Nomor Telepon <span class="text-red-500">*</span></label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" required>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2" for="address">Alamat Lengkap</label>
                                    <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" placeholder="Masukkan alamat lengkap Anda...">{{ old('address', $user->address) }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Password updates -->
                            <div class="p-4 bg-gray-50/50 rounded-xl border border-gray-100 space-y-4 pt-5 mt-4">
                                <h4 class="text-sm font-bold text-text-main pb-2 border-b border-gray-200">
                                    Keamanan Kata Sandi (Opsional)
                                </h4>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2" for="old_password">Kata Sandi Lama</label>
                                        <input type="password" id="old_password" name="old_password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-xs outline-none focus:border-primary focus:ring-2 focus:ring-primary-light transition" placeholder="Sandi saat ini">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2" for="password">Kata Sandi Baru</label>
                                        <input type="password" id="password" name="password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-xs outline-none focus:border-primary focus:ring-2 focus:ring-primary-light transition" placeholder="Min 8 karakter">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2" for="password_confirmation">Konfirmasi Sandi Baru</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-xs outline-none focus:border-primary focus:ring-2 focus:ring-primary-light transition" placeholder="Ketik ulang sandi">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-3 border-t border-gray-100 pt-6">
                                <button type="button" onclick="toggleViewMode()" class="px-5 py-3 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition text-center min-w-[100px] cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-6 py-3 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition shadow-sm min-w-[140px] flex items-center justify-center gap-2 cursor-pointer">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white/82 backdrop-blur border-t border-border-muted pt-16 pb-6 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-10">
                <div class="lg:col-span-2">
                    <div class="mb-4">
                        <h2 class="text-2xl font-bold text-primary flex items-center gap-2"><img src="{{ asset('images/logo_apotek_naufal.png') }}" class="h-6 w-auto object-contain" alt="Logo Apotek Naufal"> Apotek Naufal</h2>
                    </div>
                    <p class="text-sm text-text-muted mb-5 leading-relaxed pr-0 md:pr-10">
                        Apotek Naufal adalah platform kesehatan terpercaya yang menyediakan akses mudah untuk mendapatkan obat, vitamin, dan kebutuhan kesehatan lainnya dengan layanan konsultasi apoteker profesional.
                    </p>
                    
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
                        <li><a href="{{ route('syarat_ketentuan') }}" class="text-sm text-text-muted hover:text-primary transition">Syarat & Ketentuan</a></li>
                        <li><a href="{{ route('kebijakan_privasi') }}" class="text-sm text-text-muted hover:text-primary transition">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-text-main mb-5">Hubungi Kami</h3>
                    <ul class="flex flex-col gap-3">
                        <li><span class="text-sm text-text-muted flex items-start gap-2"><i class="fa-solid fa-location-dot w-5 text-center mt-1 shrink-0"></i> Jl. Andalas raya No.125, Andalas, Kec. Padang Tim., Kota Padang, Sumatera Barat 25171</span></li>
                        <li><a href="mailto:kalafinnali@gmail.com" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-envelope w-5 text-center"></i> kalafinnali@gmail.com</a></li>
                        <li><a href="https://wa.me/6281372869386" target="_blank" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-brands fa-whatsapp w-5 text-center text-emerald-500"></i> 081372869386</a></li>
                        <li><a href="tel:081372869386" class="text-sm text-text-muted hover:text-primary transition flex items-center gap-2"><i class="fa-solid fa-phone w-5 text-center"></i> 081372869386</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="text-center pt-6 border-t border-border-muted text-sm text-text-muted">
                <p>&copy; 2026 Apotek Naufal. All rights reserved. SIPA: 123/SIPA/2026.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts for Toggling Mode and Avatar Choice -->
    <script>
        let mode = 'view';

        // Select pre-defined avatar function
        function selectAvatar(avatarName) {
            // Remove selection class from all avatar choices
            document.querySelectorAll('.avatar-option').forEach(opt => {
                opt.classList.remove('selected');
            });

            // Add selection class to the chosen avatar choice
            const selectedOpt = document.getElementById('opt-' + avatarName);
            if (selectedOpt) {
                selectedOpt.classList.add('selected');
            }

            // Set hidden field value & preview source
            document.getElementById('selected-avatar').value = avatarName;
            document.getElementById('avatar-image-preview').src = '/images/avatars/' + avatarName + '.png';
        }

        // Toggle edit mode
        function toggleEditMode() {
            mode = 'edit';
            updatePageLayout();
        }

        // Toggle back to view mode
        function toggleViewMode() {
            mode = 'view';
            updatePageLayout();
        }

        // Update visual components based on the active mode
        function updatePageLayout() {
            const viewPanel = document.getElementById('view-mode-panel');
            const editPanel = document.getElementById('edit-mode-panel');
            const editBtn = document.getElementById('btn-toggle-edit');
            
            const breadcrumb = document.getElementById('breadcrumb-title');
            const pageTitle = document.getElementById('page-title');

            if (mode === 'edit') {
                viewPanel.classList.add('hidden');
                editPanel.classList.remove('hidden');
                editBtn.classList.add('hidden'); // Hide the edit profile button

                breadcrumb.innerText = 'Edit Profil';
                pageTitle.innerText = 'Pengaturan Profil';
            } else {
                viewPanel.classList.remove('hidden');
                editPanel.classList.add('hidden');
                editBtn.classList.remove('hidden'); // Show the edit profile button

                breadcrumb.innerText = 'Profil Saya';
                pageTitle.innerText = 'Profil Saya';
            }
        }

        // Initialize user's current avatar selection
        document.addEventListener('DOMContentLoaded', function() {
            const currentAvatar = "{{ $user->avatar }}";
            let defaultSelect = 'avatar1';

            // Find current avatar match
            for (let i = 1; i <= 10; i++) {
                if (currentAvatar.includes('avatar' + i)) {
                    defaultSelect = 'avatar' + i;
                    break;
                }
            }

            selectAvatar(defaultSelect);

            // Automatically open edit mode if there are validation errors
            @if($errors->any())
                toggleEditMode();
            @endif

            // Flash success message using SweetAlert2
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
            @endif

            // Flash error validation messages using SweetAlert2
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    html: `<ul class="text-left list-disc pl-5 text-sm space-y-1 text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>`,
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#346739',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'px-5 py-2.5 rounded-xl text-xs font-bold'
                    }
                });
            @endif
        });
    </script>
</body>
</html>
