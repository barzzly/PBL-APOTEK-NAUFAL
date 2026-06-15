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
            <a href="{{ route('admin.tickets.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.tickets*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} transition-all font-medium text-sm">
                <i class="fa-solid fa-ticket w-5 {{ request()->routeIs('admin.tickets*') ? 'text-white/90' : 'text-gray-400' }}"></i>
                <span class="sidebar-text">Ticket</span>
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
                                <span id="notificationCountBadge" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full border border-white flex items-center justify-center shadow-md animate-pulse">
                                    {{ $totalNotifCount }}
                                </span>
                            @endif
                        </button>
                        
                        {{-- Dropdown Panel --}}
                        <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden transform origin-top-right transition-all">
                            <div class="px-5 py-4 bg-gradient-to-r from-primary/10 to-emerald-500/10 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-800">Notifikasi</span>
                                    <span id="notificationDropdownBadge" class="text-xs bg-primary/10 text-primary px-2.5 py-0.5 rounded-full font-bold">
                                        {{ $totalNotifCount }} Baru
                                    </span>
                                </div>
                                <span class="text-xs font-semibold text-gray-400">Apotek Naufal</span>
                            </div>
                            
                            <div id="notificationItemsContainer" class="max-h-80 overflow-y-auto divide-y divide-gray-50">
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
                            
                            <div id="notificationDropdownFooter" class="px-5 py-3.5 bg-gray-50 border-t border-gray-100 text-center {{ $totalNotifCount > 0 ? '' : 'hidden' }}">
                                <span class="text-xs font-semibold text-gray-500">Silakan klik notifikasi untuk merespons</span>
                            </div>
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
        .swal2-modal {
            font-family: 'Inter', sans-serif !important;
            border-radius: 1.25rem !important;
            padding: 2rem 2rem 1.75rem !important;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.04) !important;
        }
        .swal2-modal .swal2-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            padding-top: 0.5rem !important;
        }
        .swal2-modal .swal2-html-container, 
        .swal2-modal .swal2-content {
            font-size: 0.9rem !important;
            color: #6b7280 !important;
            line-height: 1.6 !important;
        }
        .swal2-modal .swal2-icon {
            margin-bottom: 1rem !important;
            margin-top: 0 !important;
            width: 4.5rem !important;
            height: 4.5rem !important;
        }
        .swal2-modal .swal2-icon .swal2-icon-content {
            font-size: 2.25rem !important;
        }
        .swal2-modal .swal2-actions {
            gap: 0.75rem !important;
            margin-top: 1.75rem !important;
            padding: 0 !important;
            flex-wrap: nowrap !important;
        }
        .swal2-modal .swal2-confirm {
            padding: 0.65rem 1.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            border-radius: 0.625rem !important;
            background-color: #346739 !important;
            color: #fff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(52,103,57,0.3) !important;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s !important;
            min-width: 120px !important;
        }
        .swal2-modal .swal2-confirm:hover {
            background-color: #274E2B !important;
            box-shadow: 0 6px 16px rgba(52,103,57,0.4) !important;
            transform: translateY(-1px) !important;
        }
        .swal2-modal .swal2-cancel {
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
        .swal2-modal .swal2-cancel:hover {
            background-color: #f9fafb !important;
            border-color: #9ca3af !important;
            transform: translateY(-1px) !important;
        }
        /* Progress bar for success */
        .swal2-modal .swal2-timer-progress-bar {
            background: #346739 !important;
        }
        /* Error icon */
        .swal2-modal .swal2-icon.swal2-error {
            border-color: #fca5a5 !important;
        }
        .swal2-modal .swal2-icon.swal2-error .swal2-x-mark-line-left,
        .swal2-modal .swal2-icon.swal2-error .swal2-x-mark-line-right {
            background-color: #ef4444 !important;
        }

        /* Custom SweetAlert2 Toast Styles */
        .swal2-popup.swal2-toast {
            font-family: 'Inter', sans-serif !important;
            padding: 0.85rem 1rem !important;
            border-radius: 1rem !important;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.08) !important;
            border: 1px solid #e5e7eb !important;
            align-items: center !important;
        }
        .swal2-popup.swal2-toast .swal2-icon {
            margin: 0 0.75rem 0 0 !important;
            width: 2rem !important;
            height: 2rem !important;
            min-width: 2rem !important;
        }
        .swal2-popup.swal2-toast .swal2-icon .swal2-icon-content {
            font-size: 1.15rem !important;
        }
        .swal2-popup.swal2-toast .swal2-title {
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            margin: 0 !important;
            padding: 0 !important;
            text-align: left !important;
        }
        .swal2-popup.swal2-toast .swal2-html-container {
            font-size: 0.75rem !important;
            color: #4b5563 !important;
            margin: 0.25rem 0 0 0 !important;
            text-align: left !important;
            line-height: 1.4 !important;
        }
        .swal2-popup.swal2-toast .swal2-actions {
            margin: 0.5rem 0 0 0 !important;
            padding: 0 !important;
            gap: 0.5rem !important;
            justify-content: flex-start !important;
            width: 100% !important;
        }
        .swal2-popup.swal2-toast .swal2-confirm {
            padding: 0.35rem 0.85rem !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            border-radius: 0.5rem !important;
            min-width: 70px !important;
            background-color: #346739 !important;
            box-shadow: none !important;
        }
        .swal2-popup.swal2-toast .swal2-cancel {
            padding: 0.35rem 0.85rem !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            border-radius: 0.5rem !important;
            min-width: 60px !important;
            background-color: #fff !important;
            color: #4b5563 !important;
            border: 1px solid #d1d5db !important;
            box-shadow: none !important;
        }
        .swal2-popup.swal2-toast .swal2-timer-progress-bar {
            background: #346739 !important;
            height: 3px !important;
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

            let lastSeenNotificationKeys = null;

            // Poll for notifications every 10 seconds
            function pollNotifications() {
                fetch('{{ route('admin.notifications.fetch') }}')
                    .then(res => res.json())
                    .then(data => {
                        updateNotificationUI(data.notifications, data.count);
                    })
                    .catch(err => console.error('Error fetching notifications:', err));
            }

            function playNotificationSound() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const playChimeNote = (freq, startTime, duration) => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(freq, startTime);
                        
                        gain.gain.setValueAtTime(0, startTime);
                        gain.gain.linearRampToValueAtTime(0.2, startTime + 0.05);
                        gain.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);
                        
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        
                        osc.start(startTime);
                        osc.stop(startTime + duration);
                    };
                    
                    const now = audioCtx.currentTime;
                    // Pleasant two-tone chime (E5 followed by A5)
                    playChimeNote(659.25, now, 0.4);
                    playChimeNote(880.00, now + 0.12, 0.6);
                } catch (e) {
                    console.warn("Audio Context playback failed or was blocked by browser autoplay policy:", e);
                }
            }

            function showNewNotificationPopup(notif) {
                // Play notification sound
                playNotificationSound();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: (notif.type === 'new_order' || notif.type === 'new_ticket') ? 'info' : 'warning',
                    title: notif.title,
                    html: notif.message,
                    showConfirmButton: true,
                    confirmButtonText: 'Lihat',
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#346739',
                    cancelButtonColor: '#757575',
                    timer: 10000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-gray-100 text-left',
                        confirmButton: 'px-3 py-1.5 rounded-lg text-xs font-bold text-white',
                        cancelButton: 'px-3 py-1.5 rounded-lg text-xs font-semibold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = notif.route;
                    }
                });
            }

            function updateNotificationUI(notifications, count) {
                // 1. Update count badge on bell icon
                let badge = document.getElementById('notificationCountBadge');
                const bell = document.getElementById('notificationBellBtn');
                if (count > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.id = 'notificationCountBadge';
                        badge.className = 'absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full border border-white flex items-center justify-center shadow-md animate-pulse';
                        bell.appendChild(badge);
                    }
                    badge.innerText = count;
                } else {
                    if (badge) badge.remove();
                }

                // 2. Update badge inside dropdown
                const dropdownBadge = document.getElementById('notificationDropdownBadge');
                if (dropdownBadge) {
                    dropdownBadge.innerText = `${count} Baru`;
                }

                // 3. Update items list
                const container = document.getElementById('notificationItemsContainer');
                if (container) {
                    container.innerHTML = '';
                    if (notifications.length === 0) {
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center py-10 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg mb-3 shadow-inner">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-700">Semua aman!</p>
                                <p class="text-[11px] text-gray-400 mt-0.5 px-6">Tidak ada stok obat yang hampir habis atau orderan pending.</p>
                            </div>
                        `;
                    } else {
                        notifications.forEach(notif => {
                            const item = document.createElement('a');
                            item.href = notif.route;
                            item.className = 'px-5 py-4 flex gap-4 hover:bg-gray-50/80 transition-colors block text-left';
                            item.innerHTML = `
                                <div class="w-10 h-10 rounded-xl ${notif.icon_bg} flex items-center justify-center text-base shrink-0 shadow-sm border border-gray-100">
                                    <i class="fa-solid ${notif.icon}"></i>
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <span class="text-xs font-bold ${notif.text_color}">${notif.title}</span>
                                        <span class="text-[10px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full font-medium shrink-0">${notif.time}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-normal">${notif.message}</p>
                                </div>
                            `;
                            container.appendChild(item);
                        });
                    }
                }

                // 4. Update footer
                const footer = document.getElementById('notificationDropdownFooter');
                if (footer) {
                    if (count > 0) {
                        footer.classList.remove('hidden');
                    } else {
                        footer.classList.add('hidden');
                    }
                }

                // 5. Check for new notifications
                const currentKeys = [];
                notifications.forEach(notif => {
                    const key = notif.type + '_' + notif.route;
                    currentKeys.push(key);
                });

                if (lastSeenNotificationKeys !== null) {
                    notifications.forEach(notif => {
                        const key = notif.type + '_' + notif.route;
                        if (!lastSeenNotificationKeys.includes(key)) {
                            showNewNotificationPopup(notif);
                        }
                    });
                }
                lastSeenNotificationKeys = currentKeys;
            }

            // Run polling every 10 seconds
            setInterval(pollNotifications, 10000);

            // Trigger initial state mapping on load so lastSeenNotificationKeys is populated
            const initialNotifs = [];
            document.querySelectorAll('#notificationItemsContainer a').forEach(el => {
                const href = el.getAttribute('href');
                // infer type from icon class or background color if possible
                let type = 'unknown';
                if (el.querySelector('.text-red-500') || el.querySelector('.bg-red-50')) {
                    type = 'stock_empty';
                } else if (el.querySelector('.text-amber-500') || el.querySelector('.bg-amber-50')) {
                    type = 'stock_low';
                } else if (el.querySelector('.text-blue-500') || el.querySelector('.bg-blue-50')) {
                    type = 'new_order';
                } else if (el.querySelector('.text-indigo-500') || el.querySelector('.bg-indigo-50') || el.querySelector('.text-purple-500') || el.querySelector('.bg-purple-50')) {
                    type = 'new_ticket';
                }
                initialNotifs.push(type + '_' + href);
            });
            lastSeenNotificationKeys = initialNotifs;

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
