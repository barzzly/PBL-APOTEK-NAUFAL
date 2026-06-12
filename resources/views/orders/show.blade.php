<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan {{ $order->order_number }} - Apotek Naufal</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                    <div class="px-3 py-2 text-sm font-semibold text-text-main flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center"><i class="fa-solid fa-user"></i></div>
                        {{ auth()->user()->name }}
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
        <div class="flex items-center gap-3 mb-6">
            <a href="/" class="text-text-muted hover:text-primary transition text-sm"><i class="fa-solid fa-house"></i> Beranda</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <a href="{{ route('orders.history') }}" class="text-text-muted hover:text-primary transition text-sm">Pesanan Saya</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <span class="text-text-main font-semibold text-sm">Detail Pesanan</span>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-text-main">Pesanan: {{ $order->order_number }}</h1>
                <p class="text-xs text-text-muted mt-1">Dibuat pada: {{ $order->created_at->isoFormat('D MMMM YYYY HH:mm') }}</p>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Payment Status Badge -->
                @if($order->payment_status === 'paid')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200"><i class="fa-solid fa-circle-check"></i> Lunas</span>
                @elseif($order->payment_status === 'refunded')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">Dikembalikan</span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">Belum Lunas</span>
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
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $colorClass }}">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <div class="text-sm">{{ session('success') }}</div>
            </div>
        @endif

        <!-- Order Stepper Progress Tracking -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 overflow-x-auto">
            <h3 class="text-sm font-bold text-text-main mb-6">Status Pelacakan Pesanan:</h3>
            
            <div class="min-w-[600px] flex items-center justify-between relative px-8">
                <!-- Background Line -->
                <div class="absolute top-[18px] left-[45px] right-[45px] h-1 bg-gray-100 z-0"></div>
                
                @php
                    $steps = [
                        'pending' => ['label' => 'Dibuat', 'icon' => 'fa-file-invoice'],
                        'confirmed' => ['label' => 'Dikonfirmasi', 'icon' => 'fa-check-double'],
                        'processing' => ['label' => 'Diproses', 'icon' => 'fa-gears'],
                        'ready_or_shipped' => [
                            'label' => $order->order_type === 'delivery' ? 'Dikirim' : 'Siap Diambil',
                            'icon' => $order->order_type === 'delivery' ? 'fa-truck' : 'fa-box-archive'
                        ],
                        'delivered' => ['label' => 'Selesai', 'icon' => 'fa-circle-check']
                    ];

                    $statusSequence = ['pending', 'confirmed', 'processing', 'ready_or_shipped', 'delivered'];
                    
                    // Map current database status to sequence
                    $currentStatusIndex = 0;
                    if ($order->status === 'confirmed') $currentStatusIndex = 1;
                    if ($order->status === 'processing') $currentStatusIndex = 2;
                    if ($order->status === 'ready_for_pickup' || $order->status === 'shipped') $currentStatusIndex = 3;
                    if ($order->status === 'delivered') $currentStatusIndex = 4;
                    if ($order->status === 'cancelled') $currentStatusIndex = -1; // special case
                @endphp

                <!-- Active Line -->
                @if($currentStatusIndex >= 0)
                <div class="absolute top-[18px] left-[45px] h-1 bg-primary z-0 transition-all duration-500" 
                     style="width: calc({{ ($currentStatusIndex / 4) * 100 }}% - 15px);"></div>
                @endif

                @foreach($statusSequence as $index => $stepKey)
                    @php
                        $step = $steps[$stepKey];
                        $isCompleted = $currentStatusIndex >= $index && $currentStatusIndex !== -1;
                        $isActive = $currentStatusIndex === $index;
                    @endphp
                    <div class="flex flex-col items-center z-10 relative">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm transition-all duration-300 border-2
                            @if($isActive) 
                                bg-primary border-primary text-white shadow-md shadow-primary/20 scale-110
                            @elseif($isCompleted)
                                bg-primary border-primary text-white
                            @else
                                bg-white border-gray-200 text-text-muted
                            @endif">
                            <i class="fa-solid {{ $step['icon'] }}"></i>
                        </div>
                        <span class="text-xs font-semibold mt-2.5 @if($isActive || $isCompleted) text-primary @else text-text-muted @endif">
                            {{ $step['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            @if($order->status === 'cancelled')
            <div class="mt-6 p-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl flex items-start gap-2.5">
                <i class="fa-solid fa-ban text-base mt-0.5"></i>
                <div>
                    <h4 class="font-bold">Pesanan Dibatalkan</h4>
                    <p class="mt-1">Mohon maaf, pesanan ini telah dibatalkan. Hubungi Customer Service kami jika ada kendala atau jika Anda telah melakukan transfer dana.</p>
                </div>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Info column (Items details) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Rincian Obat -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                    <h3 class="text-base font-bold text-text-main">Rincian Obat Yang Dibeli</h3>
                    
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                        <div class="py-4 flex gap-4 items-center justify-between">
                            <div class="flex gap-4">
                                <div class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                    @if($item->medicine && $item->medicine->image)
                                        <img src="{{ $item->medicine->image }}" alt="{{ $item->medicine_name }}" class="max-w-full max-h-full object-contain p-1">
                                    @else
                                        <div class="text-gray-300 text-xl"><i class="fa-solid fa-pills"></i></div>
                                    @endif
                                </div>
                                <div class="flex flex-col justify-center">
                                    <h4 class="text-sm font-semibold text-text-main leading-tight">{{ $item->medicine_name }}</h4>
                                    <span class="text-[10px] text-text-muted mt-1">Kuantitas: {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }} / {{ $item->medicine_unit }}</span>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-text-main shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    <!-- Totals box -->
                    <div class="pt-4 border-t border-gray-100 space-y-2 text-xs">
                        <div class="flex justify-between text-text-muted">
                            <span>Subtotal Produk</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-text-muted">
                            <span>Biaya Pengiriman</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-bold text-text-main pt-2 border-t border-gray-50">
                            <span>Total Pembayaran</span>
                            <span class="text-secondary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Catatan Apoteker -->
                @if($order->pharmacist_note)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-2">
                    <h3 class="text-sm font-bold text-text-main flex items-center gap-2"><i class="fa-solid fa-comment-medical text-primary"></i> Catatan dari Apoteker:</h3>
                    <p class="text-xs text-text-muted leading-relaxed bg-primary-light/40 border border-green-100 p-4 rounded-xl italic">
                        "{{ $order->pharmacist_note }}"
                    </p>
                </div>
                @endif
            </div>

            <!-- Right Info column (Delivery & Payment details) -->
            <div class="space-y-6">
                <!-- Tipe Pengiriman -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4 text-xs">
                    <h3 class="text-sm font-bold text-text-main"><i class="fa-solid fa-truck-ramp-box text-primary mr-1"></i> Informasi Pengiriman</h3>
                    <div>
                        <span class="text-text-muted block mb-1">Tipe Penerimaan</span>
                        <strong class="text-sm font-semibold text-text-main">
                            {{ $order->order_type === 'delivery' ? 'Kirim ke Alamat (Delivery)' : 'Ambil di Apotek (Pickup)' }}
                        </strong>
                    </div>
                    @if($order->order_type === 'delivery')
                    <div>
                        <span class="text-text-muted block mb-1">Alamat Penerima</span>
                        <p class="text-text-main leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">
                            {{ $order->shipping_address }}
                        </p>
                    </div>
                    @else
                    <div>
                        <span class="text-text-muted block mb-1">Lokasi Pengambilan</span>
                        <p class="text-text-main leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <strong>Apotek Naufal Jakarta</strong><br>
                            Jl. Kesehatan No. 123, Jakarta Pusat. Telp: (021) 1500-123
                        </p>
                    </div>
                    @endif

                    @if($order->notes)
                    <div>
                        <span class="text-text-muted block mb-1">Catatan Anda</span>
                        <p class="italic text-text-muted">"{{ $order->notes }}"</p>
                    </div>
                    @endif
                </div>

                <!-- Metode Pembayaran -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4 text-xs">
                    <h3 class="text-sm font-bold text-text-main"><i class="fa-solid fa-credit-card text-primary mr-1"></i> Informasi Pembayaran</h3>
                    <div>
                        <span class="text-text-muted block mb-1">Metode Pembayaran</span>
                        <strong class="text-sm font-semibold text-text-main">
                            {{ $order->payment_method_label }}
                        </strong>
                    </div>

                    <!-- Payment Proof Form / Display -->
                    @if($order->payment_method === 'transfer' || $order->payment_method === 'qris')
                        @if($order->payment_proof)
                            <div>
                                <span class="text-text-muted block mb-1">Bukti Pembayaran</span>
                                <a href="{{ $order->payment_proof }}" target="_blank" class="text-primary hover:underline font-semibold flex items-center gap-1.5 mt-1">
                                    <i class="fa-solid fa-image"></i> Lihat Bukti Pembayaran
                                </a>
                            </div>
                        @else
                            <div class="pt-2 border-t border-gray-100 space-y-2">
                                <span class="text-text-muted block">Bukti Pembayaran Belum Diunggah</span>
                                <form action="{{ route('orders.upload_payment', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2" id="upload-proof-form">
                                    @csrf
                                    <input type="file" name="payment_proof" accept="image/*" required class="w-full text-[10px] text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-[10px] file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                                    <button type="submit" class="w-full py-2 bg-primary hover:bg-primary-dark text-white rounded font-semibold text-xs shadow-sm transition">
                                        Unggah Bukti Bayar
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Info Resep Dokter -->
                @if($prescription)
                <div class="bg-white rounded-2xl shadow-sm border border-amber-200 bg-amber-50/10 p-6 space-y-4 text-xs">
                    <h3 class="text-sm font-bold text-amber-800"><i class="fa-solid fa-prescription-bottle text-amber-700 mr-1"></i> Berkas Resep Dokter</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-text-muted block">Nama Dokter</span>
                            <strong class="text-text-main">{{ $prescription->doctor_name }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block">Nama Pasien</span>
                            <strong class="text-text-main">{{ $prescription->patient_name }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block">Tanggal Resep</span>
                            <strong class="text-text-main">{{ $prescription->prescription_date->format('d/m/Y') }}</strong>
                        </div>
                        <div>
                            <span class="text-text-muted block">Status Resep</span>
                            <strong class="text-text-main">{{ $prescription->status_label }}</strong>
                        </div>
                    </div>
                    <div>
                        <span class="text-text-muted block mb-1">Foto Resep</span>
                        <a href="{{ route('prescriptions.view', basename($prescription->image)) }}" target="_blank" class="text-amber-800 hover:underline font-semibold flex items-center gap-1.5">
                            <i class="fa-solid fa-file-image"></i> Lihat Resep Dokter
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted py-8 mt-12 text-center text-xs text-text-muted">
        <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
    </footer>

    <script>
        const proofForm = document.getElementById('upload-proof-form');
        if (proofForm) {
            proofForm.addEventListener('submit', function() {
                Swal.fire({
                    title: 'Mengunggah bukti pembayaran...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        }
    </script>
</body>
</html>
