@extends('admin.layout')
@section('header_title', 'Detail Pesanan: ' . $order->order_number)

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.orders') }}" class="text-gray-400 hover:text-primary transition">Pesanan</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Detail Pesanan (#{{ $order->order_number }})</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column (Order Details & Items) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Order info & items -->
        <div class="bg-white rounded-xl shadow-sm border border-border-muted overflow-hidden">
            <div class="p-5 border-b border-border-muted bg-gray-50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-text-main">Rincian Obat</h3>
                <span class="text-xs text-text-muted">Tanggal Order: {{ $order->created_at->isoFormat('D MMM YYYY HH:mm') }}</span>
            </div>
            
            <div class="p-5">
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <div class="py-3 flex justify-between gap-4 items-center">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-gray-50 border border-gray-150 rounded flex items-center justify-center overflow-hidden shrink-0">
                                @if($item->medicine && $item->medicine->image)
                                    <img src="{{ $item->medicine->image }}" alt="{{ $item->medicine_name }}" class="max-w-full max-h-full object-contain p-1">
                                @else
                                    <div class="text-gray-300 text-lg"><i class="fa-solid fa-pills"></i></div>
                                @endif
                            </div>
                            <div class="flex flex-col justify-center">
                                <strong class="text-sm font-semibold text-text-main block leading-tight">{{ $item->medicine_name }}</strong>
                                <span class="text-[10px] text-text-muted mt-1">Kuantitas: {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }} ({{ $item->medicine_unit }})</span>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-text-main shrink-0">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-gray-150 space-y-2 text-xs">
                    <div class="flex justify-between text-text-muted">
                        <span>Subtotal Produk</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-text-muted">
                        <span>Biaya Pengiriman</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm font-bold text-text-main pt-2 border-t border-gray-100">
                        <span>Total Nilai Pesanan</span>
                        <span class="text-secondary">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Note -->
        @if($order->notes)
        <div class="bg-white rounded-xl shadow-sm border border-border-muted p-5 space-y-2">
            <h3 class="text-xs font-bold text-text-main uppercase tracking-wider">Catatan Pelanggan:</h3>
            <p class="text-xs text-text-muted italic bg-gray-50 border border-gray-150 p-3 rounded-lg">
                "{{ $order->notes }}"
            </p>
        </div>
        @endif

        <!-- Prescription Details -->
        @if($prescription)
        <div class="bg-amber-50/20 border border-amber-200 rounded-xl shadow-sm p-5 space-y-4">
            <h3 class="text-sm font-bold text-amber-800 flex items-center gap-1.5">
                <i class="fa-solid fa-prescription-bottle-medical"></i> Berkas Resep Dokter
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="p-3 bg-white border border-amber-100 rounded-lg space-y-2">
                    <div>
                        <span class="text-text-muted text-[10px] uppercase">Nama Dokter</span>
                        <strong class="text-text-main block">{{ $prescription->doctor_name }}</strong>
                    </div>
                    <div>
                        <span class="text-text-muted text-[10px] uppercase">Rumah Sakit / Klinik</span>
                        <strong class="text-text-main block">{{ $prescription->hospital_clinic ?? '-' }}</strong>
                    </div>
                </div>

                <div class="p-3 bg-white border border-amber-100 rounded-lg space-y-2">
                    <div>
                        <span class="text-text-muted text-[10px] uppercase">Nama Pasien</span>
                        <strong class="text-text-main block">{{ $prescription->patient_name }}</strong>
                    </div>
                    <div>
                        <span class="text-text-muted text-[10px] uppercase">Umur Pasien</span>
                        <strong class="text-text-main block">{{ $prescription->patient_age ? $prescription->patient_age . ' Tahun' : '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-text-muted text-[10px] uppercase">Status Verifikasi Resep</span>
                        <strong class="text-text-main block">
                            @if($prescription->status === 'verified')
                                <span class="text-green-600 font-semibold"><i class="fa-solid fa-circle-check"></i> Diverifikasi</span>
                            @elseif($prescription->status === 'rejected')
                                <span class="text-red-650 font-semibold"><i class="fa-solid fa-circle-xmark"></i> Ditolak</span>
                            @else
                                <span class="text-yellow-600 font-semibold"><i class="fa-solid fa-clock"></i> Menunggu Verifikasi</span>
                            @endif
                        </strong>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <span class="text-text-muted text-xs block mb-1">Unggahan Foto Resep Dokter:</span>
                <div class="w-full max-w-sm border border-amber-100 rounded-lg overflow-hidden bg-white">
                    <img src="{{ route('tickets.view', basename($prescription->image)) }}" alt="Resep Dokter" class="w-full max-h-64 object-contain p-2">
                    <div class="p-3 bg-gray-50 border-t border-amber-100 text-center">
                        <a href="{{ route('tickets.view', basename($prescription->image)) }}" target="_blank" class="text-xs text-primary hover:underline font-semibold flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-up-right-from-square"></i> Buka Gambar Resep Ukuran Penuh
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column (Status Updates & Verification) -->
    <div class="space-y-6">
        
        <!-- Status Form -->
        <div class="bg-white rounded-xl shadow-sm border border-border-muted p-5 space-y-4">
            <h3 class="text-sm font-bold text-text-main">Pembaruan Status Pesanan</h3>
            
            <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="space-y-4" id="status-update-form">
                @csrf
                
                <!-- Order Status select -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-text-muted block">Status Pesanan</label>
                    <select name="status" class="w-full border border-border-muted rounded-lg px-3 py-2 text-xs focus:border-primary outline-none bg-white">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Menunggu Konfirmasi (Pending)</option>
                        <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi (Confirmed)</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Sedang Disiapkan (Processing)</option>
                        
                        @if($order->order_type === 'delivery')
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Sedang Dikirim (Shipped)</option>
                        @else
                            <option value="ready_for_pickup" {{ $order->status === 'ready_for_pickup' ? 'selected' : '' }}>Siap Diambil (Ready for Pickup)</option>
                        @endif
                        
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Selesai (Delivered)</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Batalkan Pesanan (Cancelled)</option>
                    </select>
                </div>

                <!-- Payment Status select -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-text-muted block">Status Pembayaran</label>
                    <select name="payment_status" class="w-full border border-border-muted rounded-lg px-3 py-2 text-xs focus:border-primary outline-none bg-white">
                        <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Belum Bayar (Unpaid)</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Sudah Bayar / Lunas (Paid)</option>
                        <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Dikembalikan (Refunded)</option>
                    </select>
                </div>

                <!-- Pharmacist Note -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-text-muted block">Catatan Apoteker</label>
                    <textarea name="pharmacist_note" placeholder="Tuliskan catatan khusus apotek untuk pembeli..." class="w-full border border-border-muted rounded-lg p-2.5 text-xs focus:border-primary outline-none h-20">{{ $order->pharmacist_note }}</textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg text-xs font-semibold shadow-sm transition">
                    Simpan Perubahan
                </button>
            </form>
        </div>

        <!-- Customer & Delivery Info -->
        <div class="bg-white rounded-xl shadow-sm border border-border-muted p-5 space-y-4 text-xs">
            <h3 class="text-sm font-bold text-text-main">Informasi Kontak & Pengiriman</h3>
            
            <div class="space-y-3">
                <div>
                    <span class="text-text-muted block">Nama Pembeli</span>
                    <strong class="text-sm text-text-main font-semibold">{{ $order->user->name }}</strong>
                </div>
                <div>
                    <span class="text-text-muted block">Nomor Telepon</span>
                    <strong class="text-text-main">{{ $order->user->phone ?? '-' }}</strong>
                </div>
                <div>
                    <span class="text-text-muted block">Metode Penerimaan</span>
                    <strong class="text-text-main">{{ $order->order_type === 'delivery' ? 'Kirim ke Alamat' : 'Ambil di Apotek' }}</strong>
                </div>
                @if($order->order_type === 'delivery')
                <div>
                    <span class="text-text-muted block mb-1">Alamat Penerima</span>
                    <div class="p-3 bg-gray-50 border border-gray-150 rounded text-text-main leading-relaxed space-y-2">
                        <p>{{ $order->shipping_address }}</p>
                        @if($order->delivery_latitude && $order->delivery_longitude)
                        <div class="pt-2 border-t border-gray-200 mt-2 flex flex-col gap-1.5 text-[10px]">
                            <span class="flex items-center gap-1.5"><i class="fa-solid fa-route text-primary"></i> Jarak: <strong>{{ number_format($order->delivery_distance, 2) }} km</strong></span>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $order->delivery_latitude }},{{ $order->delivery_longitude }}" target="_blank" class="text-primary hover:underline flex items-center gap-1.5 mt-1 font-semibold">
                                <i class="fa-solid fa-map-location-dot"></i> Buka Titik Antar Kurir di Google Maps <i class="fa-solid fa-up-right-from-square text-[8px]"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Verification Proof -->
        @if($order->payment_method === 'transfer' || $order->payment_method === 'qris')
        <div class="bg-white rounded-xl shadow-sm border border-border-muted p-5 space-y-4 text-xs">
            <h3 class="text-sm font-bold text-text-main"><i class="fa-solid fa-receipt text-primary mr-1"></i> Bukti Pembayaran</h3>
            
            @if($order->payment_proof)
            <div class="border border-gray-150 rounded-lg overflow-hidden bg-gray-50">
                <img src="{{ $order->payment_proof }}" alt="Bukti Transfer" class="w-full max-h-64 object-contain p-2">
                <div class="p-3 bg-white border-t border-gray-150 text-center">
                    <a href="{{ $order->payment_proof }}" target="_blank" class="text-primary hover:underline font-semibold flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-up-right-from-square"></i> Buka Gambar Bukti Pembayaran
                    </a>
                </div>
            </div>
            @else
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-center rounded-xl font-semibold">
                Bukti transfer / bayar belum diunggah oleh customer.
            </div>
            @endif
        </div>
        @endif
        
    </div>
</div>

<script>
    document.getElementById('status-update-form').addEventListener('submit', function() {
        Swal.fire({
            title: 'Menyimpan perubahan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>
@endsection
