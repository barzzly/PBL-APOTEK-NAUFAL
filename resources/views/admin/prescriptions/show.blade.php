@extends('admin.layout')
@section('header_title', 'Workspace Pelayanan Resep')

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
    <a href="{{ route('admin.prescriptions.index') }}" class="text-gray-400 hover:text-primary transition">Resep Obat</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Workspace Pelayanan ({{ $prescription->prescription_number }})</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    
    <!-- Left Column: Prescription & Patient Details -->
    <div class="space-y-6">
        <!-- Patient metadata -->
        <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Resep</h3>
            
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-gray-400 block">Nama Pasien:</span>
                    <strong class="text-gray-700 text-sm block mt-0.5">{{ $prescription->patient_name }} ({{ $prescription->patient_age ?? '-' }} th)</strong>
                </div>
                <div>
                    <span class="text-gray-400 block">Dokter Penulis:</span>
                    <strong class="text-gray-700 text-sm block mt-0.5">{{ $prescription->doctor_name }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block">Rumah Sakit / Klinik:</span>
                    <strong class="text-gray-700 text-sm block mt-0.5">{{ $prescription->hospital_clinic ?? '-' }}</strong>
                </div>
                <div>
                    <span class="text-gray-400 block">Tanggal Resep:</span>
                    <strong class="text-gray-700 text-sm block mt-0.5">{{ $prescription->prescription_date->isoFormat('D MMMM YYYY') }}</strong>
                </div>
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

        <!-- Prescription Image Viewer -->
        <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Berkas Foto Resep</h3>
            <div class="relative rounded-xl overflow-hidden bg-gray-50 border border-gray-100 max-h-72 flex items-center justify-center p-2 group">
                <img src="{{ route('prescriptions.view', ['filename' => basename($prescription->image)]) }}" 
                     alt="Resep Dokter" 
                     class="max-h-60 object-contain w-full rounded-lg hover:scale-[1.03] transition-transform duration-300 cursor-zoom-in"
                     onclick="showFullImage(this.src)">
                <div class="absolute bottom-2 right-2 bg-black/60 text-white rounded p-1.5 text-[10px] font-semibold flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass-plus"></i> Klik perbesar
                </div>
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('prescriptions.view', ['filename' => basename($prescription->image)]) }}" target="_blank" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                    <i class="fa-solid fa-up-right-from-square"></i> Buka di Tab Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Middle Column: Chat Consultation Thread -->
    <div class="flex flex-col bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden h-[620px]">
        <!-- Chat Header -->
        <div class="px-5 py-4 bg-gray-50 border-b border-gray-150 flex items-center gap-3 shrink-0">
            <div class="w-10 h-10 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold">
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
            
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-[11px] text-blue-900 leading-relaxed shadow-sm">
                <strong class="font-bold flex items-center gap-1.5 mb-1"><i class="fa-solid fa-info-circle"></i> Chat Konsultasi Aktif</strong>
                Gunakan ruang obrolan ini untuk bertanya kepada pelanggan mengenai dosis obat, alergi obat, atau mengkonfirmasi obat alternatif jika stok resep asli habis. Semua penambahan obat ke keranjang akan tercatat di log obrolan secara otomatis.
            </div>

            @foreach($prescription->messages as $msg)
                @php
                    // Message is sent by Admin if it is NOT the customer
                    $isAdminMessage = ($msg->user_id !== $prescription->user_id);
                    $isSystem = str_starts_with($msg->message, '[SISTEM APOTEK]');
                @endphp
                
                @if($isSystem)
                    <!-- System log bubble -->
                    <div class="flex justify-center my-2">
                        <div class="px-3.5 py-1.5 bg-gray-200 border border-gray-300 rounded-xl text-[10px] font-semibold text-gray-600 max-w-[90%] text-center">
                            <i class="fa-solid fa-gears mr-1"></i> {{ str_replace('[SISTEM APOTEK]:', '', $msg->message) }}
                        </div>
                    </div>
                @else
                    <div class="flex {{ $isAdminMessage ? 'justify-end' : 'justify-start' }} gap-2">
                        @if(!$isAdminMessage)
                            <div class="w-7 h-7 rounded-full bg-primary-light text-primary flex items-center justify-center text-[10px] font-bold shrink-0 self-end mb-1">
                                {{ substr($prescription->user->name, 0, 1) }}
                            </div>
                        @endif

                        <div class="max-w-[80%]">
                            <div class="px-3.5 py-2.5 rounded-2xl shadow-sm text-xs leading-relaxed {{ $isAdminMessage ? 'bg-indigo-650 text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-150 rounded-bl-none' }}">
                                {!! nl2br(e($msg->message)) !!}
                            </div>
                            <span class="text-[9px] text-gray-400 block mt-1 {{ $isAdminMessage ? 'text-right' : 'text-left' }} px-1">
                                {{ $msg->created_at->isoFormat('D MMM HH:mm') }}
                            </span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Chat Input Form -->
        @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
        <form action="{{ route('admin.prescriptions.message', $prescription->id) }}" method="POST" class="p-3 bg-white border-t border-gray-150 flex gap-2 shrink-0 items-center">
            @csrf
            <input type="text" name="message" required autocomplete="off"
                class="flex-grow px-3 py-2 bg-gray-50 border border-gray-250 rounded-xl text-xs outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition"
                placeholder="Balas konsultasi pelanggan...">
            <button type="submit" class="w-10 h-10 rounded-xl bg-primary hover:bg-primary-dark text-white flex items-center justify-center text-sm transition shadow-md shrink-0 cursor-pointer">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
        @else
        <div class="p-3 bg-gray-150 text-center text-xs text-gray-500 font-semibold shrink-0">
            <i class="fa-solid fa-lock mr-1"></i> Tiket konsultasi resep ini telah ditutup.
        </div>
        @endif
    </div>

    <!-- Right Column: Customer Live Cart & Action Panel -->
    <div class="space-y-6">
        
        <!-- Live Cart Manager -->
        <div class="bg-white rounded-2xl border border-gray-150 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Keranjang Belanja Pelanggan</h3>
                <span class="text-[10px] bg-primary-light text-primary px-2 py-0.5 rounded-full font-bold">Live Sync</span>
            </div>

            <!-- Cart Items List -->
            <div class="space-y-3 max-h-60 overflow-y-auto">
                @forelse($cartItems as $item)
                <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-100 rounded-xl">
                    <div class="w-10 h-10 rounded bg-white border border-gray-150 flex items-center justify-center overflow-hidden shrink-0">
                        @if($item->medicine && $item->medicine->image)
                        <img src="{{ $item->medicine->image }}" alt="" class="w-full h-full object-cover">
                        @else
                        <i class="fa-solid fa-pills text-gray-300 text-lg"></i>
                        @endif
                    </div>
                    <div class="flex-grow min-w-0">
                        <h4 class="text-xs font-bold text-gray-800 truncate">{{ $item->medicine->name ?? 'Obat' }}</h4>
                        <div class="text-[10px] text-gray-450 mt-0.5 flex justify-between items-center">
                            <span>{{ $item->quantity }}x @ Rp {{ number_format($item->medicine->price, 0, ',', '.') }}</span>
                            <span class="font-bold text-secondary text-xs">Rp {{ number_format($item->medicine->price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
                    <form action="{{ route('admin.prescriptions.remove_medicine', ['id' => $prescription->id, 'itemId' => $item->id]) }}" method="POST" class="shrink-0 ml-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-6 h-6 rounded bg-red-50 hover:bg-red-150 text-red-500 flex items-center justify-center transition" title="Hapus Obat">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </form>
                    @endif
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
            <div class="pt-4 border-t border-gray-100 space-y-3">
                <h4 class="text-xs font-bold text-gray-700">Tambah Obat Rekep ke Keranjang</h4>
                
                <form action="{{ route('admin.prescriptions.add_medicine', $prescription->id) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block text-gray-400 mb-1">Pilih Obat:</label>
                        <select name="medicine_id" required class="w-full px-3 py-2 border border-gray-250 rounded-xl bg-white outline-none focus:border-primary">
                            <option value="">-- Cari/Pilih Obat --</option>
                            @foreach($medicines as $med)
                            <option value="{{ $med->id }}">
                                {{ $med->name }} (Stok: {{ $med->stock }} - Rp {{ number_format($med->price, 0, ',', '.') }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-1/3">
                            <label class="block text-gray-400 mb-1">Kuantitas:</label>
                            <input type="number" name="quantity" value="1" min="1" required class="w-full px-3 py-2 border border-gray-250 rounded-xl outline-none focus:border-primary">
                        </div>
                        <div class="w-2/3 self-end">
                            <button type="submit" class="w-full py-2 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl transition shadow-sm shadow-primary/10 flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-cart-plus"></i> Masukkan Obat
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Action Center: Status update actions -->
        <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi Pelayanan</h3>

            @if($prescription->status !== 'completed' && $prescription->status !== 'rejected')
            <div class="flex flex-col gap-2.5">
                
                <!-- If status is pending, show Start Processing button -->
                @if($prescription->status === 'pending')
                <form action="{{ route('admin.prescriptions.status', $prescription->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer shadow-md shadow-indigo-600/10">
                        <i class="fa-solid fa-play"></i> Mulai Memproses Resep
                    </button>
                </form>
                @endif

                <!-- Complete / Close ticket button -->
                @if($prescription->status === 'processing' || $prescription->status === 'pending' || $prescription->status === 'verified')
                <form action="{{ route('admin.prescriptions.status', $prescription->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="w-full py-3 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer shadow-md shadow-primary/15">
                        <i class="fa-solid fa-circle-check"></i> Tandai Selesai & Kirim Obat
                    </button>
                </form>
                @endif

                <!-- Reject prescription button -->
                <form action="{{ route('admin.prescriptions.status', $prescription->id) }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menolak resep dokter ini?')" class="w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-650 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer border border-red-150">
                        <i class="fa-solid fa-circle-xmark"></i> Tolak Resep Dokter
                    </button>
                </form>

            </div>
            @else
            <div class="p-4 bg-gray-50 border border-gray-150 rounded-xl text-center text-xs text-gray-500 font-semibold">
                <i class="fa-solid fa-circle-info text-primary mr-1 text-sm block mb-1"></i>
                Pelayanan resep ini telah selesai diproses. Status saat ini: 
                <span class="block mt-1 font-bold text-gray-700 uppercase tracking-wide">{{ $prescription->status_label }}</span>
            </div>
            @endif
        </div>
    </div>

</div>

<!-- Modal Full Image View -->
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

<script>
    // Scroll to the bottom of the chat box on page load
    document.addEventListener('DOMContentLoaded', () => {
        const chatMessages = document.getElementById('chat-messages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    });

    // Image viewer modal functions
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('modal-img');
    const modalDownload = document.getElementById('modal-download');

    function showFullImage(src) {
        modalImg.src = src;
        modalDownload.href = src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeFullImage() {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal || e.target.closest('#image-modal') && !e.target.closest('#modal-img') && !e.target.closest('#modal-download') && !e.target.closest('button')) {
            closeFullImage();
        }
    });
</script>
@endsection
