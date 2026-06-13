@extends('admin.layout')
@section('header_title', 'Workspace Pelayanan Tiket')

@section('content')
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <div class="text-sm font-semibold">{{ session('success') }}</div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-lg text-red-500"></i>
        <div class="text-sm font-semibold">{{ session('error') }}</div>
    </div>
@endif

<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.tickets.index') }}" class="text-gray-400 hover:text-primary transition">Tiket</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Workspace Pelayanan ({{ $prescription->prescription_number }})</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    
    <!-- Left Column: Ticket & Patient Details -->
    <div id="left-panel" class="space-y-6">
        <!-- Patient metadata -->
        <div class="bg-white rounded-2xl border border-border-muted p-5 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Tiket</h3>
            
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block">Nama Pasien:</span>
                    <strong class="text-gray-700 text-sm block mt-0.5">{{ $prescription->patient_name }} ({{ $prescription->patient_age ?? '-' }} th)</strong>
                </div>
                <div>
                    <span class="text-gray-400 block">Tipe Tiket:</span>
                    <strong class="text-sm block mt-0.5">
                        @if($prescription->type === 'consultation')
                            <span class="text-primary font-bold"><i class="fa-solid fa-comments"></i> Konsultasi Umum</span>
                        @else
                            <span class="text-emerald-600 font-bold"><i class="fa-solid fa-file-prescription"></i> Tebus Resep</span>
                        @endif
                    </strong>
                </div>
                @if($prescription->type === 'prescription')
                <div>
                    <span class="text-gray-400 block">Dokter Penulis:</span>
                    <strong class="text-gray-700 text-sm block mt-0.5">{{ $prescription->doctor_name ?? '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block">Rumah Sakit / Klinik:</span>
                    <strong class="text-gray-700 text-sm block mt-0.5">{{ $prescription->hospital_clinic ?? '-' }}</strong>
                </div>
                @endif
                <div>
                    <span class="text-gray-400 block">Pelanggan Pengunggah:</span>
                    <strong class="text-gray-700 text-sm block mt-0.5">{{ $prescription->user->name }}</strong>
                </div>
            </div>

            @if($prescription->customer_notes)
            <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-[11px] text-gray-600">
                <span class="font-bold text-gray-700 block mb-0.5"><i class="fa-solid fa-comment-dots text-primary mr-1"></i> Catatan Pasien:</span>
                <p class="italic">"{{ $prescription->customer_notes }}"</p>
            </div>
            @endif

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-400">Status:</span>
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
                <span class="px-2 py-0.5 rounded-full border font-bold text-[10px] uppercase tracking-wider {{ $colorClass }}">
                    {{ $prescription->status_label }}
                </span>
            </div>
        </div>

        @if($prescription->image)
        <!-- Prescription Image Viewer -->
        <div class="bg-white rounded-2xl border border-border-muted p-4 shadow-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Berkas Foto Resep</h3>
            <div class="relative rounded-xl overflow-hidden bg-gray-50 border border-border-muted max-h-72 flex items-center justify-center p-2 group">
                <img id="prescription-img" src="{{ route('tickets.view', ['filename' => basename($prescription->image)]) }}" 
                     alt="Resep Dokter" 
                     class="max-h-60 object-contain w-full rounded-lg hover:scale-[1.03] transition-transform duration-300 cursor-zoom-in"
                     onclick="showFullImage(this.src)">
                <div style="position: absolute; bottom: 0.5rem; right: 0.5rem; background-color: rgba(0,0,0,0.65); color: white; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 10px; font-weight: 600; display: flex; align-items: center; gap: 0.25rem; pointer-events: none;">
                    <i class="fa-solid fa-magnifying-glass-plus"></i> Klik perbesar
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('tickets.view', ['filename' => basename($prescription->image)]) }}" target="_blank" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                    <i class="fa-solid fa-up-right-from-square"></i> Buka di Tab Baru
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Middle Column: Chat Consultation Thread -->
    <div id="chat-container" class="flex flex-col bg-white rounded-2xl border border-border-muted shadow-sm overflow-hidden" style="height: 620px; min-height: 550px;">
        <!-- Chat Header -->
        <div class="px-5 py-4 bg-gray-50 border-b border-border-muted flex items-center gap-3 shrink-0">
            <div class="w-10 h-10 rounded-xl bg-primary-light text-primary flex items-center justify-center font-bold shrink-0">
                {{ substr($prescription->user->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800">{{ $prescription->user->name }}</h2>
                <span class="text-xs text-text-muted">Chat Konsultasi Pelanggan</span>
            </div>
            <button onclick="window.location.reload()" class="ml-auto w-8 h-8 rounded-lg text-gray-400 hover:text-primary hover:bg-gray-100 flex items-center justify-center transition" title="Segarkan Chat">
                <i class="fa-solid fa-rotate"></i>
            </button>
        </div>

        <!-- Chat Area -->
        <div id="chat-messages" class="flex-grow p-5 overflow-y-auto bg-gray-50/50 space-y-4">
            
            <div class="bg-white border border-border-muted rounded-xl p-4 text-xs text-text-main leading-relaxed shadow-sm">
                <div class="font-bold flex items-center gap-1.5 mb-1.5 text-primary text-sm">
                    <i class="fa-solid fa-circle-info text-primary"></i> Chat Konsultasi Aktif
                </div>
                <p class="text-text-muted">Gunakan ruang obrolan ini untuk berkomunikasi secara langsung dengan pelanggan. Anda dapat mengonfirmasi keluhan, menawarkan obat alternatif, atau menanyakan dosis. Obat yang ditambahkan ke keranjang belanja pelanggan akan tercatat di log obrolan secara otomatis.</p>
            </div>

            @foreach($prescription->messages as $msg)
                @php
                    // Message is sent by Admin if it is NOT the customer
                    $isAdminMessage = ($msg->user_id !== $prescription->user_id);
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
                    <div class="flex {{ $isAdminMessage ? 'justify-end' : 'justify-start' }} gap-3">
                        @if(!$isAdminMessage)
                            <div class="w-8 h-8 rounded-xl bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 self-end mb-1">
                                {{ substr($prescription->user->name, 0, 1) }}
                            </div>
                        @endif

                        <div class="max-w-[75%]">
                            <div class="px-4 py-3 rounded-2xl shadow-sm text-sm leading-relaxed break-words {{ $isAdminMessage ? 'bg-primary text-white' : 'bg-white text-text-main border border-border-muted' }}" style="word-break: break-word; overflow-wrap: break-word;">
                                {!! nl2br(e($msg->message)) !!}
                            </div>
                            <span class="text-[10px] text-gray-400 block mt-1 {{ $isAdminMessage ? 'text-right' : 'text-left' }} px-1 font-medium">
                                {{ $msg->created_at->isoFormat('D MMMM HH:mm') }}
                            </span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Chat Input Form -->
        @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
        <form id="chat-form" action="{{ route('admin.tickets.message', $prescription->id) }}" method="POST" class="p-4 bg-white border-t border-border-muted flex gap-3 shrink-0 items-center">
            @csrf
            <input id="chat-input" type="text" name="message" required autocomplete="off"
                class="flex-grow px-4 py-3 bg-gray-50 border border-border-muted rounded-xl text-sm outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition"
                placeholder="Balas konsultasi pelanggan...">
            <button type="submit" class="w-12 h-12 rounded-xl bg-primary hover:bg-primary-dark text-white flex items-center justify-center text-lg transition shadow-md shrink-0 cursor-pointer">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
        @else
        <div class="p-3 bg-gray-100 text-center text-xs text-gray-500 font-semibold shrink-0 border-t border-border-muted">
            <i class="fa-solid fa-lock mr-1"></i> Tiket konsultasi ini telah ditutup.
        </div>
        @endif
    </div>

    <!-- Right Column: Customer Live Cart & Action Panel -->
    <div id="right-panel" class="space-y-6">
        
        <!-- Live Cart Manager -->
        <div class="bg-white rounded-2xl border border-border-muted shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Keranjang Belanja Pelanggan</h3>
                <span class="text-[10px] bg-primary-light text-primary px-2 py-0.5 rounded-full font-bold">Live Sync</span>
            </div>

            <!-- Cart Items List -->
            <div class="space-y-3 overflow-y-auto" style="max-height: 280px;">
                @forelse($cartItems as $item)
                <div class="flex items-center justify-between p-3 bg-white border border-border-muted rounded-xl hover:shadow-sm transition duration-200">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-lg bg-gray-50 border border-border-muted flex items-center justify-center overflow-hidden shrink-0">
                            @if($item->medicine && $item->medicine->image)
                            <img src="{{ $item->medicine->image }}" alt="{{ $item->medicine->name }}" class="w-full h-full object-cover">
                            @else
                            <i class="fa-solid fa-pills text-gray-300 text-lg"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-gray-800 truncate mb-1">{{ $item->medicine->name ?? 'Obat' }}</h4>
                            <span class="text-[10px] text-gray-500 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">
                                {{ $item->quantity }}x @ Rp {{ number_format($item->medicine->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2.5 shrink-0">
                        <span class="font-bold text-secondary text-xs">Rp {{ number_format($item->medicine->price * $item->quantity, 0, ',', '.') }}</span>
                        
                        @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
                        <form action="{{ route('admin.tickets.remove_medicine', ['id' => $prescription->id, 'itemId' => $item->id]) }}" method="POST" class="shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition cursor-pointer" title="Hapus Obat">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-gray-400 text-xs">
                    <i class="fa-solid fa-basket-shopping text-2xl block mb-2 opacity-35"></i>
                    Keranjang belanja pelanggan masih kosong.
                </div>
                @endforelse
            </div>

            <!-- Add Medicine Form -->
            @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
            <div class="pt-4 border-t border-border-muted space-y-4">
                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider"><i class="fa-solid fa-cart-plus text-primary mr-1"></i> Tambah Obat ke Keranjang</h4>
                
                <form action="{{ route('admin.tickets.add_medicine', $prescription->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Pilih Obat</label>
                        <select name="medicine_id" required 
                            class="w-full h-10 px-3 border border-border-muted rounded-xl bg-gray-50/50 outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition text-xs font-medium"
                            style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23757575%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%226 9 12 15 18 9%22></polyline></svg>'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem; padding-right: 2.5rem;">
                            <option value="">-- Cari/Pilih Obat --</option>
                            @foreach($medicines as $med)
                            <option value="{{ $med->id }}">
                                {{ $med->name }} (Stok: {{ $med->stock }} - Rp {{ number_format($med->price, 0, ',', '.') }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 0.75rem; align-items: end;">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Kuantitas</label>
                            <input type="number" name="quantity" value="1" min="1" required 
                                class="w-full h-10 px-3 border border-border-muted rounded-xl bg-gray-50/50 outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition text-sm font-semibold text-center">
                        </div>
                        <div>
                            <button type="submit" 
                                class="w-full h-10 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl transition shadow-md shadow-primary/10 flex items-center justify-center gap-1.5 cursor-pointer text-xs">
                                <i class="fa-solid fa-cart-plus"></i> Masukkan Obat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Action Center: Status update actions -->
        <div class="bg-white rounded-2xl border border-border-muted p-5 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi Pelayanan</h3>

            @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
            <div class="flex flex-col gap-2.5">
                
                <!-- If status is pending, show Start Processing button -->
                @if($prescription->status === 'pending')
                <form action="{{ route('admin.tickets.status', $prescription->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="w-full py-3 bg-secondary hover:bg-[#d85517] text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer shadow-md shadow-secondary/15">
                        <i class="fa-solid fa-play"></i> Mulai Memproses Tiket
                    </button>
                </form>
                @endif

                <!-- Complete / Close ticket button -->
                @if($prescription->status === 'processing' || $prescription->status === 'pending' || $prescription->status === 'verified')
                <form action="{{ route('admin.tickets.status', $prescription->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="w-full py-3 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer shadow-md shadow-primary/15">
                        <i class="fa-solid fa-circle-check"></i> Tandai Selesai & Kirim Obat
                    </button>
                </form>
                @endif

                <!-- Reject prescription button -->
                <form action="{{ route('admin.tickets.status', $prescription->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak tiket ini?')" class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer border border-red-200">
                        <i class="fa-solid fa-circle-xmark"></i> Tolak Tiket
                    </button>
                </form>

            </div>
            @else
            <div class="p-4 bg-gray-50 border border-border-muted rounded-xl text-center text-xs text-gray-500 font-semibold">
                <i class="fa-solid fa-circle-info text-primary mr-1 text-sm block mb-1"></i>
                Pelayanan tiket ini telah selesai diproses. Status saat ini: 
                <span class="block mt-1 font-bold text-gray-700 uppercase tracking-wide">{{ $prescription->status_label }}</span>
            </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Full Image View -->
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

<script>
    // Chat Polling and AJAX Submission
    document.addEventListener('DOMContentLoaded', () => {
        const customerUserId = {{ $prescription->user_id }};
        const prescriptionId = {{ $prescription->id }};
        const messagesUrl = "{{ route('admin.tickets.messages', $prescription->id) }}";
        let currentStatus = "{{ $prescription->status }}";
        let lastMessageCount = {{ $prescription->messages->count() }};

        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');

        // Function to match height dynamically
        function matchChatHeight() {
            const leftPanel = document.getElementById('left-panel');
            const rightPanel = document.getElementById('right-panel');
            const chatContainer = document.getElementById('chat-container');
            if (chatContainer) {
                if (window.innerWidth >= 1024) {
                    const leftHeight = leftPanel ? leftPanel.offsetHeight : 0;
                    const rightHeight = rightPanel ? rightPanel.offsetHeight : 0;
                    const targetHeight = Math.max(leftHeight, rightHeight, 620);
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
                const isAdminMessage = (msg.user_id !== customerUserId);

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
                    flexDiv.className = `flex ${isAdminMessage ? 'justify-end' : 'justify-start'} gap-3`;
                    
                    let userAvatar = '';
                    if (!isAdminMessage) {
                        const firstChar = msg.user_name ? msg.user_name.charAt(0) : 'U';
                        userAvatar = `
                            <div class="w-8 h-8 rounded-xl bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 self-end mb-1">
                                ${escapeHtml(firstChar)}
                            </div>
                        `;
                    }

                    const formattedMsg = escapeHtml(msg.message).replace(/\n/g, '<br>');

                    flexDiv.innerHTML = `
                        ${userAvatar}
                        <div class="max-w-[75%]">
                            <div class="px-4 py-3 rounded-2xl shadow-sm text-sm leading-relaxed break-words ${isAdminMessage ? 'bg-primary text-white' : 'bg-white text-text-main border border-border-muted'}" style="word-break: break-word; overflow-wrap: break-word;">
                                ${formattedMsg}
                            </div>
                            <span class="text-[10px] text-gray-400 block mt-1 ${isAdminMessage ? 'text-right' : 'text-left'} px-1 font-medium">
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

    // Image viewer modal functions
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('modal-img');
    const modalDownload = document.getElementById('modal-download');

    function showFullImage(src) {
        modalImg.src = src;
        modalDownload.href = src;
        modal.style.display = 'flex';
    }

    function closeFullImage() {
        modal.style.display = 'none';
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal || e.target.closest('#image-modal') && !e.target.closest('#modal-img') && !e.target.closest('#modal-download') && !e.target.closest('button')) {
            closeFullImage();
        }
    });
</script>
@endsection
