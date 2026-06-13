<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Apotek Naufal</title>
    
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
                            <a href="{{ route('tickets.history') }}" class="px-3 py-2 text-xs font-semibold text-primary hover:underline flex items-center gap-1.5"><i class="fa-solid fa-ticket"></i> Ticket Saya</a>
                            
                            {{-- Clickable profile box pointing to profile edit page --}}
                            <a href="{{ route('profile.edit') }}" class="px-3 py-2 text-sm font-semibold text-primary border border-primary/20 rounded-xl bg-primary-light/30 hover:bg-primary-light/60 transition flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center overflow-hidden border border-white shadow-sm">
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
    <main class="flex-grow bg-gray-50/50 py-10">
        <div class="max-w-5xl mx-auto px-4">
            
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-3 mb-6 text-xs text-text-muted">
                <a href="/" class="hover:text-primary transition"><i class="fa-solid fa-house"></i> Beranda</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
                <span class="text-gray-600 font-bold">Edit Profil</span>
            </div>

            <!-- Page Title -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-gear text-primary"></i> Pengaturan Profil
                </h1>
                <p class="text-sm text-text-muted mt-1">Kelola data informasi akun dan kata sandi Anda.</p>
            </div>

            <!-- Profile Form Wrapper -->
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf
                
                <!-- Left Column: Avatar Upload Card -->
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                        <h2 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider text-left border-b border-gray-50 pb-2">Foto Profil</h2>
                        
                        <!-- Avatar Container with hover effect -->
                        <div class="relative w-36 h-36 mx-auto rounded-full overflow-hidden border-4 border-primary-light shadow-md bg-gray-50 group mb-4">
                            <img id="avatar-image-preview" 
                                 src="{{ $user->avatar ? (str_starts_with($user->avatar, '/') ? $user->avatar : '/' . $user->avatar) : '/images/default-avatar.png' }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=e6efe5&color=346739&bold=true&size=150'">
                            
                            <!-- Overlay Label to trigger file upload -->
                            <label for="avatar-input" class="absolute inset-0 bg-black/55 text-white flex flex-col items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <i class="fa-solid fa-camera text-lg"></i>
                                <span class="text-[10px] font-bold">Ubah Foto</span>
                            </label>
                        </div>

                        <!-- Real file input hidden -->
                        <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewAvatar(this)">

                        <div class="text-left mt-2">
                            <label for="avatar-input" class="mx-auto w-max px-4 py-2 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 transition flex items-center gap-1.5 justify-center cursor-pointer mb-3">
                                <i class="fa-solid fa-image text-gray-400"></i> Pilih File Gambar
                            </label>
                            <div class="text-[11px] text-text-muted leading-relaxed border-t border-gray-50 pt-3">
                                <p class="font-medium text-gray-700 mb-1"><i class="fa-solid fa-circle-info text-primary mr-1"></i>Ketentuan file:</p>
                                <ul class="list-disc pl-4 space-y-1">
                                    <li>Format: JPG, JPEG, PNG, GIF</li>
                                    <li>Ukuran berkas maksimal: 2MB</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Personal details & Password fields -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        
                        <!-- Tab Title 1: Personal Data -->
                        <h2 class="text-base font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                            <i class="fa-regular fa-id-card text-primary text-lg"></i> Informasi Pribadi
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                            <!-- Name input -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="name">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" required>
                            </div>

                            <!-- Email input -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="email">Alamat Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" required>
                            </div>

                            <!-- Phone input -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="phone">Nomor Telepon <span class="text-red-500">*</span></label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" required>
                            </div>

                            <!-- Address input -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="address">Alamat Lengkap</label>
                                <textarea id="address" name="address" rows="3" 
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" 
                                          placeholder="Masukkan alamat lengkap Anda...">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>

                        <!-- Tab Title 2: Password Change -->
                        <h2 class="text-base font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-3">
                            <i class="fa-solid fa-lock text-primary"></i> Ubah Kata Sandi (Opsional)
                        </h2>
                        
                        <!-- Alert Box for Password -->
                        <div class="p-4 bg-primary-light/30 border border-primary/15 rounded-xl flex gap-3 text-primary mb-6">
                            <i class="fa-solid fa-circle-info text-base shrink-0 mt-0.5"></i>
                            <p class="text-xs leading-normal">Kosongkan bagian kata sandi di bawah jika Anda tidak ingin merubah kata sandi lama Anda.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                            <!-- Old Password -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="old_password">Kata Sandi Lama</label>
                                <input type="password" id="old_password" name="old_password" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" 
                                       placeholder="Masukkan kata sandi lama Anda">
                            </div>

                            <!-- New Password -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="password">Kata Sandi Baru</label>
                                <input type="password" id="password" name="password" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" 
                                       placeholder="Minimal 8 karakter">
                            </div>

                            <!-- Confirm New Password -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-primary focus:ring-4 focus:ring-primary-light transition" 
                                       placeholder="Ulangi kata sandi baru">
                            </div>
                        </div>

                        <!-- Form Submission -->
                        <div class="flex justify-end gap-3 border-t border-gray-50 pt-6">
                            <a href="/" class="px-5 py-3 border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition text-center min-w-[100px]">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-3 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition shadow-md shadow-primary/20 min-w-[140px] flex items-center justify-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                            </button>
                        </div>

                    </div>
                </div>
            </form>

        </div>
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

    <!-- Scripts for Live Preview and SweetAlert alerts -->
    <script>
        // Preview selected avatar in the client immediately before upload
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-image-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Handle success/error feedback alerts via SweetAlert2
        document.addEventListener('DOMContentLoaded', function() {
            // Flash success message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Flash error validation messages
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
                    confirmButtonColor: '#346739'
                });
            @endif
        });
    </script>

</body>
</html>
