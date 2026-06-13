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
                    <a href="{{ route('tickets.history') }}" class="px-3 py-2 text-xs font-semibold text-primary hover:underline flex items-center gap-1.5"><i class="fa-solid fa-ticket"></i> Ticket Saya</a>
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
            <a href="{{ route('tickets.history') }}" class="text-text-muted hover:text-primary transition text-sm">Ticket Saya</a>
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
            <div id="left-panel" class="space-y-6">
                <!-- Prescription Details Card -->
                <div class="bg-white rounded-2xl border border-border-muted shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-border-muted flex justify-between items-center">
                        <h2 class="text-sm font-bold text-gray-800">
                            @if($prescription->type === 'consultation')
                                Detail Tiket Konsultasi
                            @else
                                Detail Resep Dokter
                            @endif
                        </h2>
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
                                <span class="text-gray-400 block mb-0.5">Tipe Tiket</span>
                                <strong class="text-gray-700 text-sm">
                                    @if($prescription->type === 'consultation')
                                        Konsultasi Umum
                                    @else
                                        Tebus Resep
                                    @endif
                                </strong>
                            </div>
                            @if($prescription->type === 'prescription')
                            <div>
                                <span class="text-gray-400 block mb-0.5">Dokter Penulis</span>
                                <strong class="text-gray-700 text-sm">{{ $prescription->doctor_name }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-400 block mb-0.5">RS / Klinik</span>
                                <strong class="text-gray-700 text-sm">{{ $prescription->hospital_clinic ?? '-' }}</strong>
                            </div>
                            @endif
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

                        @if($prescription->status === 'completed' || $prescription->status === 'rejected')
                        <div class="mt-4 p-3 bg-red-50/60 border border-red-100 text-red-700 rounded-xl text-[10px] leading-relaxed">
                            <span class="font-bold block mb-0.5"><i class="fa-solid fa-clock-rotate-left mr-1"></i> Penghapusan Otomatis</span>
                            Tiket ini telah ditutup dan akan dihapus secara permanen beserta berkasnya dalam waktu 7 hari (sebelum <strong>{{ $prescription->updated_at->addDays(7)->isoFormat('D MMMM YYYY HH:mm') }}</strong>).
                        </div>
                        @endif
                    </div>
                </div>

                @if($prescription->image)
                <!-- Prescription Image Preview Card -->
                <div class="bg-white rounded-2xl border border-border-muted shadow-sm p-4 text-center">
                    <h3 class="text-xs font-bold text-gray-500 mb-3 text-left">Foto Pendukung / Resep Dokter</h3>
                    <div class="relative rounded-xl overflow-hidden bg-gray-50 border border-border-muted max-h-96 flex items-center justify-center p-2 group">
                        <img id="prescription-img" src="{{ route('tickets.view', ['filename' => basename($prescription->image)]) }}" 
                             alt="Resep Dokter" 
                             class="max-h-80 object-contain w-full rounded-lg hover:scale-[1.03] transition-transform duration-300 cursor-zoom-in"
                             onclick="showFullImage(this.src)">
                        <div style="position: absolute; bottom: 0.75rem; right: 0.75rem; background-color: rgba(0,0,0,0.65); color: white; padding: 0.35rem 0.6rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;">
                            <i class="fa-solid fa-magnifying-glass-plus"></i> Klik untuk perbesar
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Panel: Chat Consultation Interface -->
            <div id="chat-container" class="lg:col-span-2 flex flex-col bg-white rounded-2xl border border-border-muted shadow-sm overflow-hidden" style="min-height: 550px;">
                
                <!-- Chat Header -->
                <div class="px-6 py-4 bg-gray-50 border-b border-border-muted flex items-center gap-3 shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-primary-light text-primary flex items-center justify-center text-lg font-bold shrink-0">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-800">Apoteker Apotik Naufal</h2>
                    </div>
                    
                    <button onclick="window.location.reload()" class="ml-auto w-8 h-8 rounded-lg text-gray-400 hover:text-primary hover:bg-gray-100 flex items-center justify-center transition" title="Segarkan Chat">
                        <i class="fa-solid fa-rotate"></i>
                    </button>
                </div>

                <!-- Chat Box Area -->
                <div id="chat-messages" class="flex-grow p-6 overflow-y-auto bg-gray-50/50 space-y-4">
                    
                    <!-- Welcome / Information System Bubble -->
                    <div class="bg-white border border-border-muted rounded-xl p-4 text-xs text-text-main leading-relaxed shadow-sm">
                        <div class="font-bold flex items-center gap-1.5 mb-1.5 text-primary text-sm">
                            <i class="fa-solid fa-circle-info"></i> Layanan Konsultasi Apoteker
                        </div>
                        <p class="text-text-muted">Selamat datang di Ruang Obrolan Tiket Apotek Naufal. Apoteker kami sedang meninjau keluhan atau resep Anda. Anda dapat berdiskusi secara langsung di sini. Apabila ada obat yang disiapkan, apoteker akan menambahkannya ke keranjang belanja Anda agar dapat langsung ditebus.</p>
                    </div>

                    @foreach($prescription->messages as $msg)
                        @php
                            $isMe = ($msg->user_id === auth()->id());
                            $isSystem = str_starts_with($msg->message, '[SISTEM APOTEK]');
                        @endphp
                        
                        @if($isSystem)
                            <!-- System log bubble -->
                            <div class="flex justify-center my-3 w-full">
                                <div class="px-6 py-2.5 bg-white border border-border-muted rounded-xl text-[10px] font-semibold text-text-muted max-w-[85%] text-center shadow-sm flex items-center justify-center gap-3">
                                    <i class="fa-solid fa-bell text-secondary text-xs" style="margin-right: 8px;"></i>
                                    <span>{{ trim(str_replace('[SISTEM APOTEK]:', '', $msg->message)) }}</span>
                                </div>
                            </div>
                        @else
                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} gap-3">
                                @if(!$isMe)
                                    <div class="w-8 h-8 rounded-xl bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 self-end mb-1">
                                        <i class="fa-solid fa-user-doctor"></i>
                                    </div>
                                @endif

                                <div class="max-w-[75%]">
                                    <div class="px-4 py-3 rounded-2xl shadow-sm leading-relaxed text-sm break-words {{ $isMe ? 'bg-primary text-white' : 'bg-white text-text-main border border-border-muted' }}" style="word-break: break-word; overflow-wrap: break-word;">
                                        {!! nl2br(e($msg->message)) !!}
                                    </div>
                                    <span class="text-[10px] text-gray-400 block mt-1 {{ $isMe ? 'text-right' : 'text-left' }} px-1 font-medium">
                                        {{ $msg->created_at->isoFormat('D MMMM HH:mm') }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Call to Action Banner when Completed -->
                @if($prescription->status === 'completed')
                <div class="px-6 py-4 bg-primary-light border-t border-b border-primary/20 shrink-0 shadow-inner"
                     style="display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-w: 250px;">
                        <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center text-lg shadow-md shrink-0">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div style="flex: 1; min-w: 0; text-align: left;">
                            <h4 class="text-xs font-bold text-primary-dark">Verifikasi Selesai & Obat Ditambahkan!</h4>
                            <p class="text-[11px] text-primary-dark leading-normal mt-0.5">Obat hasil tiket konsultasi Anda telah dimasukkan ke dalam keranjang. Silakan buka halaman keranjang belanja Anda untuk memesan.</p>
                        </div>
                    </div>
                    <a href="{{ route('cart.index') }}" 
                       class="text-center px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-xl shadow-md shadow-primary/20 transition flex items-center justify-center gap-1.5"
                       style="flex-shrink: 0; min-width: 140px;">
                        Buka Keranjang <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
                @endif

                <!-- Chat Input Form -->
                @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
                <form id="chat-form" action="{{ route('tickets.message', $prescription->id) }}" method="POST" class="p-4 bg-white border-t border-border-muted flex gap-3 shrink-0 items-center">
                    @csrf
                    <input id="chat-input" type="text" name="message" required autocomplete="off"
                        class="flex-grow px-4 py-3 bg-gray-50 border border-border-muted rounded-xl text-sm outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition"
                        placeholder="Tulis pesan konsultasi Anda disini...">
                    <button type="submit" class="w-12 h-12 rounded-xl bg-primary hover:bg-primary-dark text-white flex items-center justify-center text-lg transition shadow-md shadow-primary/15 shrink-0 cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
                @else
                <div class="p-4 bg-gray-50 border-t border-border-muted text-center text-xs text-gray-500 font-semibold shrink-0">
                    <i class="fa-solid fa-lock mr-1"></i> Tiket konsultasi ini telah ditutup karena status sudah {{ $prescription->status_label }}.
                </div>
                @endif

            </div>

        </div>
    </main>

    <!-- Modal Image Viewer -->
    <div id="image-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; background-color: rgba(0, 0, 0, 0.9); align-items: center; justify-content: center; padding: 1rem; backdrop-filter: blur(4px); transition: all 0.3s ease;">
        <button onclick="closeFullImage()" style="position: absolute; top: 1.5rem; right: 1.5rem; color: white; background: none; border: none; font-size: 2rem; cursor: pointer; transition: color 0.2s;" title="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div style="max-width: 56rem; max-height: 90vh; display: flex; flex-direction: column; align-items: center;">
            <img id="modal-img" src="#" alt="Resep Full Screen" style="max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 0.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.55);">
            <div class="mt-4 text-center">
                <a id="modal-download" href="#" download style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; background-color: rgba(255, 255, 255, 0.2); color: white; border-radius: 0.75rem; font-size: 0.75rem; font-weight: 600; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.3)'" onmouseout="this.style.backgroundColor='rgba(255, 255, 255, 0.2)'">
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
        // Chat Polling and AJAX Submission
        document.addEventListener('DOMContentLoaded', () => {
            const currentUserId = {{ auth()->id() }};
            const prescriptionId = {{ $prescription->id }};
            const messagesUrl = "{{ route('tickets.messages', $prescription->id) }}";
            let currentStatus = "{{ $prescription->status }}";
            let lastMessageCount = {{ $prescription->messages->count() }};

            const chatMessages = document.getElementById('chat-messages');
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');

            // Function to match height dynamically
            function matchChatHeight() {
                const leftPanel = document.getElementById('left-panel');
                const chatContainer = document.getElementById('chat-container');
                if (leftPanel && chatContainer) {
                    if (window.innerWidth >= 1024) {
                        const leftHeight = leftPanel.offsetHeight;
                        const targetHeight = Math.max(leftHeight, 550);
                        chatContainer.style.height = `${targetHeight}px`;
                    } else {
                        chatContainer.style.height = '500px';
                    }
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                }
            }

            // Run height matching on load and window resize
            matchChatHeight();
            window.addEventListener('resize', matchChatHeight);

            // Also run when image loads to get actual height
            const rxImg = document.getElementById('prescription-img');
            if (rxImg) {
                if (rxImg.complete) {
                    matchChatHeight();
                } else {
                    rxImg.addEventListener('load', matchChatHeight);
                }
            }

            // Scroll to the bottom of the chat box on page load
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            if (chatForm && chatInput) {
                chatForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const message = chatInput.value.trim();
                    if (!message) return;

                    chatInput.value = '';
                    chatInput.focus();

                    fetch(chatForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({ message: message })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fetchMessages();
                        }
                    })
                    .catch(err => {
                        console.error('Error sending message:', err);
                    });
                });
            }

            function fetchMessages() {
                fetch(messagesUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== currentStatus) {
                            window.location.reload();
                            return;
                        }

                        if (data.messages && data.messages.length !== lastMessageCount) {
                            renderMessages(data.messages);
                            lastMessageCount = data.messages.length;
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching messages:', err);
                    });
            }

            function renderMessages(messages) {
                const welcomeBubble = chatMessages.firstElementChild;
                chatMessages.innerHTML = '';
                if (welcomeBubble) {
                    chatMessages.appendChild(welcomeBubble);
                }

                messages.forEach(msg => {
                    const isSystem = msg.message.startsWith('[SISTEM APOTEK]');
                    const isMe = (msg.user_id === currentUserId);

                    if (isSystem) {
                        const cleanMsg = msg.message.replace('[SISTEM APOTEK]:', '');
                        const systemDiv = document.createElement('div');
                        systemDiv.className = 'flex justify-center my-3 w-full';
                        systemDiv.innerHTML = `
                            <div class="px-6 py-2.5 bg-white border border-border-muted rounded-xl text-[10px] font-semibold text-text-muted max-w-[85%] text-center shadow-sm flex items-center justify-center gap-3">
                                <i class="fa-solid fa-bell text-secondary text-xs" style="margin-right: 8px;"></i>
                                <span>${escapeHtml(cleanMsg.trim())}</span>
                            </div>
                        `;
                        chatMessages.appendChild(systemDiv);
                    } else {
                        const flexDiv = document.createElement('div');
                        flexDiv.className = `flex ${isMe ? 'justify-end' : 'justify-start'} gap-3`;
                        
                        let doctorAvatar = '';
                        if (!isMe) {
                            doctorAvatar = `
                                <div class="w-8 h-8 rounded-xl bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 self-end mb-1">
                                    <i class="fa-solid fa-user-doctor"></i>
                                </div>
                            `;
                        }

                        const formattedMsg = escapeHtml(msg.message).replace(/\n/g, '<br>');

                        flexDiv.innerHTML = `
                            ${doctorAvatar}
                            <div class="max-w-[75%]">
                                <div class="px-4 py-3 rounded-2xl shadow-sm leading-relaxed text-sm break-words ${isMe ? 'bg-primary text-white' : 'bg-white text-text-main border border-border-muted'}" style="word-break: break-word; overflow-wrap: break-word;">
                                    ${formattedMsg}
                                </div>
                                <span class="text-[10px] text-gray-400 block mt-1 ${isMe ? 'text-right' : 'text-left'} px-1 font-medium">
                                    ${msg.created_at}
                                </span>
                            </div>
                        `;
                        chatMessages.appendChild(flexDiv);
                    }
                });

                chatMessages.scrollTop = chatMessages.scrollHeight;
                matchChatHeight();
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            // Poll every 3 seconds
            setInterval(fetchMessages, 3000);
        });

        // Image full viewer modal
        const modal = document.getElementById('image-modal');
        const modalImg = document.getElementById('modal-img');
        const modalDownload = document.getElementById('modal-download');

        function showFullImage(src) {
            modalImg.src = src;
            modalDownload.href = src;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // prevent page scroll
        }

        function closeFullImage() {
            modal.style.display = 'none';
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
