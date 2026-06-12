<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Konsultasi Resep - Apotek Naufal</title>
    
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
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-3 mb-6">
            <a href="/" class="text-text-muted hover:text-primary transition text-sm"><i class="fa-solid fa-house"></i> Beranda</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <a href="{{ route('prescriptions.history') }}" class="text-text-muted hover:text-primary transition text-sm">Resep Saya</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <span class="text-text-main font-semibold text-sm">Ruang Obrolan Tiket</span>
        </div>

        @if(session('success') && session('success') !== 'Pesan terkirim.')
        <div class="p-4 bg-green-50 border-l-4 border-primary rounded-r-lg flex items-center justify-between text-green-800 shadow-sm mb-6">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-primary text-lg"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Left Panel: Prescription Details -->
            <div class="space-y-6">
                <!-- Prescription Details Card -->
                <div class="bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-150 flex justify-between items-center">
                        <h2 class="text-sm font-bold text-gray-800">Detail Resep Dokter</h2>
                        <span class="text-[11px] font-bold px-2 py-0.5 bg-gray-200 text-gray-700 rounded-md">
                            {{ $prescription->prescription_number }}
                        </span>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-gray-400 block mb-0.5">Nama Pasien</span>
                                <strong class="text-gray-700 text-sm">{{ $prescription->patient_name }} ({{ $prescription->patient_age ?? '-' }} th)</strong>
                            </div>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Tanggal Resep</span>
                                <strong class="text-gray-700 text-sm">{{ $prescription->prescription_date->isoFormat('D MMMM YYYY') }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-400 block mb-0.5">Dokter Penulis</span>
                                <strong class="text-gray-700 text-sm">{{ $prescription->doctor_name }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-400 block mb-0.5">RS / Klinik</span>
                                <strong class="text-gray-700 text-sm">{{ $prescription->hospital_clinic ?? '-' }}</strong>
                            </div>
                        </div>

                        @if($prescription->customer_notes)
                        <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-xs text-text-muted">
                            <span class="font-bold text-gray-700 block mb-0.5"><i class="fa-solid fa-comment-dots mr-1 text-primary"></i> Keluhan / Catatan Anda:</span>
                            <p class="italic">"{{ $prescription->customer_notes }}"</p>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-400 font-medium">Status Tiket:</span>
                            @php
                                $badgeColors = [
                                    'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                    'verified' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'completed' => 'bg-green-50 text-green-700 border-green-200',
                                    'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                ];
                                $colorClass = $badgeColors[$prescription->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $colorClass }}">
                                {{ $prescription->status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Prescription Image Preview Card -->
                <div class="bg-white rounded-2xl border border-gray-150 shadow-sm p-4 text-center">
                    <h3 class="text-xs font-bold text-gray-500 mb-3 text-left">Foto / Scan Resep Anda</h3>
                    <div class="relative rounded-xl overflow-hidden bg-gray-50 border border-gray-100 max-h-96 flex items-center justify-center p-2 group">
                        <img src="{{ route('prescriptions.view', ['filename' => basename($prescription->image)]) }}" 
                             alt="Resep Dokter" 
                             class="max-h-80 object-contain w-full rounded-lg hover:scale-[1.03] transition-transform duration-300 cursor-zoom-in"
                             onclick="showFullImage(this.src)">
                        <div class="absolute bottom-3 right-3 bg-black/60 text-white rounded-lg p-2 text-xs font-semibold flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass-plus"></i> Klik untuk perbesar
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Chat Consultation Interface -->
            <div class="lg:col-span-2 flex flex-col bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden h-[600px]">
                
                <!-- Chat Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-150 flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-primary-light text-primary flex items-center justify-center text-lg font-bold">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">Apoteker Profesional</h2>
                        <span class="text-xs text-green-500 flex items-center gap-1 font-medium">
                            <span class="inline-block w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Online
                        </span>
                    </div>
                    
                    <button onclick="window.location.reload()" class="ml-auto w-8 h-8 rounded-lg text-gray-400 hover:text-primary hover:bg-gray-100 flex items-center justify-center transition" title="Segarkan Chat">
                        <i class="fa-solid fa-rotate"></i>
                    </button>
                </div>

                <!-- Chat Box Area -->
                <div id="chat-messages" class="flex-grow p-6 overflow-y-auto bg-gray-50/50 space-y-4">
                    
                    <!-- Welcome / Information System Bubble -->
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-900 leading-relaxed shadow-sm">
                        <strong class="font-bold flex items-center gap-1.5 mb-1"><i class="fa-solid fa-info-circle"></i> Selamat Datang di Ruang Obrolan Resep</strong>
                        Disini Anda dapat berkonsultasi langsung dengan apoteker kami terkait obat resep yang diunggah. Apoteker kami sedang memeriksa resep Anda. Anda akan menerima pesan baru apabila ada obat yang perlu disesuaikan atau siap dimasukkan ke keranjang belanja Anda.
                    </div>

                    @foreach($prescription->messages as $msg)
                        @php
                            $isMe = ($msg->user_id === auth()->id());
                        @endphp
                        
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} gap-3">
                            @if(!$isMe)
                                <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 self-end mb-1">
                                    <i class="fa-solid fa-user-doctor"></i>
                                </div>
                            @endif

                            <div class="max-w-[75%]">
                                <div class="px-4 py-3 rounded-2xl shadow-sm leading-relaxed text-sm {{ $isMe ? 'bg-primary text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-150 rounded-bl-none' }}">
                                    {!! nl2br(e($msg->message)) !!}
                                </div>
                                <span class="text-[10px] text-gray-400 block mt-1 {{ $isMe ? 'text-right' : 'text-left' }} px-1 font-medium">
                                    {{ $msg->created_at->isoFormat('D MMMM HH:mm') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Call to Action Banner when Completed -->
                @if($prescription->status === 'completed')
                <div class="px-6 py-4 bg-emerald-50 border-t border-b border-emerald-100 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0 shadow-inner">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center text-lg shadow-md shrink-0">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="text-xs font-bold text-emerald-900">Verifikasi Resep Selesai & Obat Ditambahkan!</h4>
                            <p class="text-[11px] text-emerald-700 leading-normal mt-0.5">Obat hasil resep dokter Anda telah dimasukkan ke dalam keranjang. Silakan buka halaman keranjang belanja Anda untuk memesan.</p>
                        </div>
                    </div>
                    <a href="{{ route('cart.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 shrink-0 transition flex items-center justify-center gap-1.5">
                        Buka Keranjang <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
                @endif

                <!-- Chat Input Form -->
                @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
                <form action="{{ route('prescriptions.message', $prescription->id) }}" method="POST" class="p-4 bg-white border-t border-gray-150 flex gap-3 shrink-0 items-center">
                    @csrf
                    <input type="text" name="message" required autocomplete="off"
                        class="flex-grow px-4 py-3 bg-gray-50 border border-gray-250 rounded-xl text-sm outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition"
                        placeholder="Tulis pesan konsultasi Anda disini...">
                    <button type="submit" class="w-12 h-12 rounded-xl bg-primary hover:bg-primary-dark text-white flex items-center justify-center text-lg transition shadow-md shadow-primary/15 shrink-0 cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
                @else
                <div class="p-4 bg-gray-50 border-t border-gray-150 text-center text-xs text-gray-500 font-semibold shrink-0">
                    <i class="fa-solid fa-lock mr-1"></i> Tiket konsultasi resep ini telah ditutup karena status sudah {{ $prescription->status_label }}.
                </div>
                @endif

            </div>

        </div>
    </main>

    <!-- Modal Image Viewer -->
    <div id="image-modal" class="fixed inset-0 z-[100] bg-black/90 hidden items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
        <button onclick="closeFullImage()" class="absolute top-6 right-6 text-white hover:text-gray-300 text-3xl cursor-pointer transition focus:outline-none" title="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="max-w-4xl max-h-[90vh] flex flex-col items-center">
            <img id="modal-img" src="#" alt="Resep Full Screen" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl">
            <div class="mt-4 text-center">
                <a id="modal-download" href="#" download class="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl text-xs font-semibold flex items-center gap-2 backdrop-blur-md transition">
                    <i class="fa-solid fa-download"></i> Unduh Gambar Resep
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted py-8 mt-12 text-center text-xs text-text-muted">
        <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
    </footer>

    <script>
        // Scroll to the bottom of the chat box on page load
        document.addEventListener('DOMContentLoaded', () => {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });

        // Image full viewer modal
        const modal = document.getElementById('image-modal');
        const modalImg = document.getElementById('modal-img');
        const modalDownload = document.getElementById('modal-download');

        function showFullImage(src) {
            modalImg.src = src;
            modalDownload.href = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // prevent page scroll
        }

        function closeFullImage() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // restore page scroll
        }

        // Close modal when clicked outside the image
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.closest('#image-modal') && !e.target.closest('#modal-img') && !e.target.closest('#modal-download') && !e.target.closest('button')) {
                closeFullImage();
            }
        });
    </script>
</body>
</html>
