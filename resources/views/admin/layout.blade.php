@inject('notificationService', 'App\Services\NotificationService')
@php
    $notifications = $notificationService->getNotifications();
    $totalNotifCount = $notifications->count();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Apotek Naufal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #sidebar {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            width: 256px; /* w-64 */
        }
        #sidebar.sidebar-hidden {
            width: 0;
        }
        #sidebar .sidebar-text {
            transition: opacity 0.15s ease;
            opacity: 1;
            white-space: nowrap;
        }
        #sidebar.sidebar-hidden .sidebar-text {
            opacity: 0;
        }
    </style>
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar" class="bg-white border-r border-gray-100 flex flex-col shrink-0 sticky top-0 h-screen shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-20">
        <div class="h-16 flex items-center px-10 border-b border-gray-100 shrink-0">
            <a href="/" class="text-primary text-xl font-bold flex items-center gap-3">
                <div class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center shadow-md shadow-primary/30">
                    <i class="fa-solid fa-notes-medical"></i>
                </div>
                <span class="sidebar-text">Apotek Naufal</span>
            </a>
        </div>
        
        <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto">
            <div class="sidebar-text text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 mt-2 px-3">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-chart-pie w-5 {{ request()->routeIs('admin.dashboard') ? 'text-white/90' : 'text-gray-400' }}"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
            <a href="{{ route('admin.categories') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.categories*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-list w-5 {{ request()->routeIs('admin.categories*') ? 'text-white/90' : 'text-gray-400' }}"></i>
                <span class="sidebar-text">Kategori Obat</span>
            </a>
            <a href="{{ route('admin.medicines') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.medicines*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-pills w-5 {{ request()->routeIs('admin.medicines*') ? 'text-white/90' : 'text-gray-400' }}"></i>
                <span class="sidebar-text">Data Obat</span>
            </a>
            <a href="{{ route('admin.orders') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.orders*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-receipt w-5 {{ request()->routeIs('admin.orders*') ? 'text-white/90' : 'text-gray-400' }}"></i>
                <span class="sidebar-text">Pesanan</span>
            </a>
            <a href="{{ route('admin.prescriptions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.prescriptions*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-file-prescription w-5 {{ request()->routeIs('admin.prescriptions*') ? 'text-white/90' : 'text-gray-400' }}"></i>
                <span class="sidebar-text">Resep Obat</span>
            </a>
            <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.laporan*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-chart-line w-5 {{ request()->routeIs('admin.laporan*') ? 'text-white/90' : 'text-gray-400' }}"></i>
                <span class="sidebar-text">Laporan Penjualan</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all text-left font-medium text-sm">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-red-400"></i>
                    <span class="sidebar-text">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col min-w-0 bg-gray-50/50">
        <!-- Header -->
        <header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between pl-6 pr-10 sticky top-0 z-10 shrink-0">
            <div class="flex items-center gap-4">
                <!-- Toggle sidebar button -->
                <button id="sidebarToggle"
                    class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-primary hover:bg-gray-100 transition-all"
                    title="Sembunyikan/Tampilkan Menu">
                    <i class="fa-solid fa-bars text-base"></i>
                </button>
                <h1 class="text-xl font-bold text-gray-800">@yield('header_title', 'Dashboard')</h1>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4">
                    {{-- Notifications Dropdown --}}
                    <div class="relative" id="notificationDropdownContainer">
                        <button id="notificationBellBtn" class="relative w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-primary hover:bg-gray-100 transition-all cursor-pointer focus:outline-none" title="Notifikasi">
                            <i class="fa-regular fa-bell text-xl"></i>
                            @if($totalNotifCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full border border-white flex items-center justify-center shadow-md animate-pulse">
                                    {{ $totalNotifCount }}
                                </span>
                            @endif
                        </button>
                        
                        {{-- Dropdown Panel --}}
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden transform origin-top-right transition-all">
                            <div class="px-5 py-4 bg-gradient-to-r from-primary/10 to-emerald-500/10 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-800">Notifikasi</span>
                                    <span class="text-xs bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-bold">
                                        {{ $totalNotifCount }} Baru
                                    </span>
                                </div>
                                <span class="text-xs font-semibold text-gray-400">Apotek Naufal</span>
                            </div>
                            
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                @forelse($notifications as $notif)
                                    <a href="{{ $notif['route'] }}" class="px-5 py-4 flex gap-4 hover:bg-gray-50/80 transition-colors block text-left">
                                        <div class="w-10 h-10 rounded-xl {{ $notif['icon_bg'] }} flex items-center justify-center text-base shrink-0 shadow-sm border border-gray-100">
                                            <i class="fa-solid {{ $notif['icon'] }}"></i>
                                        </div>
                                        <div class="flex-grow min-w-0">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <span class="text-xs font-bold {{ $notif['text_color'] }}">{{ $notif['title'] }}</span>
                                                <span class="text-[10px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full font-medium shrink-0">{{ $notif['time'] }}</span>
                                            </div>
                                            <p class="text-xs text-gray-600 leading-normal">{!! $notif['message'] !!}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="flex flex-col items-center justify-center py-10 text-center text-gray-400">
                                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg mb-3 shadow-inner">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                        <p class="text-xs font-bold text-gray-700">Semua aman!</p>
                                        <p class="text-[11px] text-gray-400 mt-0.5 px-6">Tidak ada stok obat yang hampir habis atau orderan pending.</p>
                                    </div>
                                @endforelse
                            </div>
                            
                            @if($totalNotifCount > 0)
                                <div class="px-5 py-3.5 bg-gray-50 border-t border-gray-100 text-center">
                                    <span class="text-xs font-semibold text-gray-500">Silakan klik notifikasi untuk merespons</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="h-8 w-px bg-gray-200"></div>

                    <div class="flex items-center gap-3 cursor-pointer group">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold text-gray-700 group-hover:text-primary transition">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-xs text-gray-500">Administrator</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold border-2 border-white shadow-sm overflow-hidden">
                            @if(auth()->user() && auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="p-8 bg-bg-body">
            @yield('content')
        </div>
    </main>

    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <style>
        /* SweetAlert2 Custom Theme - Apotek Naufal */
        .swal2-popup {
            font-family: 'Inter', sans-serif;
            border-radius: 1.25rem !important;
            padding: 2rem 2rem 1.75rem !important;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.04) !important;
        }
        .swal2-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            padding-top: 0.5rem !important;
        }
        .swal2-html-container, .swal2-content {
            font-size: 0.9rem !important;
            color: #6b7280 !important;
            line-height: 1.6 !important;
        }
        .swal2-icon {
            margin-bottom: 1rem !important;
            margin-top: 0 !important;
            width: 4.5rem !important;
            height: 4.5rem !important;
        }
        .swal2-icon .swal2-icon-content {
            font-size: 2.25rem !important;
        }
        .swal2-actions {
            gap: 0.75rem !important;
            margin-top: 1.75rem !important;
            padding: 0 !important;
            flex-wrap: nowrap !important;
        }
        .swal2-confirm {
            padding: 0.65rem 1.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            border-radius: 0.625rem !important;
            background-color: #00A651 !important;
            color: #fff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(0,166,81,0.3) !important;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s !important;
            min-width: 120px !important;
        }
        .swal2-confirm:hover {
            background-color: #008f45 !important;
            box-shadow: 0 6px 16px rgba(0,166,81,0.4) !important;
            transform: translateY(-1px) !important;
        }
        .swal2-cancel {
            padding: 0.65rem 1.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            border-radius: 0.625rem !important;
            background-color: #fff !important;
            color: #374151 !important;
            border: 1.5px solid #d1d5db !important;
            box-shadow: none !important;
            transition: background 0.2s, border-color 0.2s, transform 0.15s !important;
            min-width: 100px !important;
        }
        .swal2-cancel:hover {
            background-color: #f9fafb !important;
            border-color: #9ca3af !important;
            transform: translateY(-1px) !important;
        }
        /* Progress bar for success */
        .swal2-timer-progress-bar {
            background: #00A651 !important;
        }
        /* Error icon */
        .swal2-icon.swal2-error {
            border-color: #fca5a5 !important;
        }
        .swal2-icon.swal2-error .swal2-x-mark-line-left,
        .swal2-icon.swal2-error .swal2-x-mark-line-right {
            background-color: #ef4444 !important;
        }
    </style>
    <script>
        // Sidebar toggle
        (function () {
            var sidebar = document.getElementById('sidebar');
            var btn = document.getElementById('sidebarToggle');

            // Restore saved state
            if (localStorage.getItem('sidebar_hidden') === '1') {
                sidebar.classList.add('sidebar-hidden');
            }

            btn.addEventListener('click', function () {
                sidebar.classList.toggle('sidebar-hidden');
                localStorage.setItem('sidebar_hidden', sidebar.classList.contains('sidebar-hidden') ? '1' : '0');
            });
        })();

        // Global pagination handler
        function changePerPage(select) {
            var u = new URL(window.location.href);
            u.searchParams.set('per_page', select.value);
            u.searchParams.set('page', 1);
            window.location.href = u.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Toggle notification dropdown
            const bellBtn = document.getElementById('notificationBellBtn');
            const dropdown = document.getElementById('notificationDropdown');
            if (bellBtn && dropdown) {
                bellBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && e.target !== bellBtn && !bellBtn.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            // Confirm delete kustom
            document.addEventListener('submit', function(e) {
                const form = e.target.closest('.confirm-delete');
                if (form) {
                    e.preventDefault();
                    const message = form.getAttribute('data-message') || 'Apakah Anda yakin ingin menghapus data ini?';
                    
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa-solid fa-trash-can" style="margin-right:6px"></i>Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        buttonsStyling: true,
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });

            // Flash success alert
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    buttonsStyling: true
                });
            @endif

            // Flash error / validation alert
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    html: `<ul class="text-left list-disc pl-5 text-sm space-y-1 text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>`,
                    confirmButtonText: 'Tutup',
                    buttonsStyling: true
                });
            @endif
        });
    </script>

</body>
</html>
