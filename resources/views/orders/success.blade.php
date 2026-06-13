<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Apotek Naufal</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg-body text-text-main font-sans antialiased flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white py-4 shadow-sm border-b border-border-muted">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <a href="/" class="text-primary text-2xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-notes-medical text-3xl"></i> Apotek Naufal
            </a>
            <a href="/" class="text-sm font-semibold text-text-muted hover:text-primary transition"><i class="fa-solid fa-arrow-left"></i> Kembali ke Toko</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-3xl mx-auto px-4 py-12 w-full flex flex-col items-center">
        
        <!-- Success Icon -->
        <div class="w-20 h-20 rounded-full bg-primary-light text-primary flex items-center justify-center text-4xl mb-6 shadow-sm border border-green-200">
            <i class="fa-solid fa-check"></i>
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-text-main text-center mb-2">Pesanan Berhasil Dibuat!</h1>
        <p class="text-sm text-text-muted text-center mb-8">Terima kasih telah berbelanja di Apotek Naufal. Nomor pesanan Anda adalah <strong class="text-text-main font-semibold">{{ $order->order_number }}</strong>.</p>

        @if(session('success'))
            <div class="w-full mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <div class="text-sm">{{ session('success') }}</div>
            </div>
        @endif

        <div class="w-full bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6 space-y-6">
            
            <!-- Ringkasan Info Pesanan -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs pb-6 border-b border-gray-100">
                <div>
                    <span class="text-text-muted block mb-1">Tipe Pesanan</span>
                    <strong class="text-sm text-text-main font-semibold">
                        {{ $order->order_type === 'delivery' ? 'Kirim ke Alamat' : 'Ambil di Apotek' }}
                    </strong>
                </div>
                <div>
                    <span class="text-text-muted block mb-1">Metode Pembayaran</span>
                    <strong class="text-sm text-text-main font-semibold">
                        {{ $order->payment_method_label }}
                    </strong>
                </div>
                <div>
                    <span class="text-text-muted block mb-1">Status Pembayaran</span>
                    <strong class="text-sm text-text-main font-semibold">
                        {{ $order->payment_status_label }}
                    </strong>
                </div>
                <div class="text-right">
                    <span class="text-text-muted block mb-1">Total Pembayaran</span>
                    <strong class="text-sm text-secondary font-bold">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </strong>
                </div>
            </div>

            <!-- Petunjuk Pembayaran Dinamis -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-text-main">Instruksi Langkah Selanjutnya:</h3>

                @if($order->payment_method === 'transfer' || $order->payment_method === 'qris')
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs text-text-main space-y-3 leading-relaxed">
                        @if($order->payment_method === 'transfer')
                            <p>Silakan selesaikan transfer bank Anda sejumlah <strong class="text-secondary text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> ke rekening berikut:</p>
                            <div class="p-3 bg-white border border-gray-100 rounded-lg font-mono text-sm max-w-sm">
                                <span class="text-text-muted text-[10px] font-sans block uppercase">Bank Mandiri</span>
                                <strong>123-000-456789-0</strong>
                                <span class="text-xs font-sans text-text-muted block">a/n Apotek Naufal Jaya</span>
                            </div>
                        @else
                            <p>Silakan scan QRIS di bawah senilai <strong class="text-secondary text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> menggunakan e-wallet Anda:</p>
                            <div class="bg-white p-3 rounded-lg border border-gray-100 flex flex-col items-center w-max">
                                <div class="w-32 h-32 bg-gray-100 flex items-center justify-center border border-gray-200">
                                    <i class="fa-solid fa-qrcode text-7xl text-gray-800"></i>
                                </div>
                                <span class="text-[9px] font-mono mt-1 text-text-muted">APOTEK NAUFAL</span>
                            </div>
                        @endif

                        @if(!$order->payment_proof)
                            <div class="pt-4 border-t border-gray-200 space-y-3">
                                <h4 class="font-bold text-text-main">Unggah Bukti Pembayaran</h4>
                                <p class="text-text-muted">Unggah bukti transfer atau tangkapan layar transaksi pembayaran QRIS Anda untuk mempermudah verifikasi oleh admin kami:</p>
                                
                                <form action="{{ route('orders.upload_payment', $order->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-3 items-start md:items-center">
                                    @csrf
                                    <input type="file" name="payment_proof" accept="image/*" required class="text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-green-100 cursor-pointer">
                                    <button type="submit" class="px-4 py-1.5 bg-primary hover:bg-primary-dark text-white rounded text-xs font-semibold shadow-sm transition">
                                        Unggah Sekarang
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Bukti pembayaran telah berhasil diunggah. Admin sedang melakukan verifikasi.</span>
                            </div>
                        @endif
                    </div>
                @elseif($order->payment_method === 'cash')
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs text-text-main leading-relaxed">
                        @if($order->order_type === 'pickup')
                            <p><i class="fa-solid fa-store text-primary mr-1"></i> Silakan kunjungi apotek kami di <strong>Jl. Kesehatan No. 123</strong> untuk mengambil pesanan Anda dan lakukan pembayaran secara tunai di kasir.</p>
                        @else
                            <p><i class="fa-solid fa-motorcycle text-primary mr-1"></i> Pesanan Anda akan dikirimkan oleh kurir kami. Mohon siapkan uang tunai sebesar <strong class="text-secondary font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> pas saat pesanan Anda tiba.</p>
                        @endif
                    </div>
                @elseif($order->payment_method === 'bpjs')
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 leading-relaxed">
                        <p><i class="fa-solid fa-id-card text-amber-600 mr-1"></i> Harap membawa kartu BPJS asli dan fotokopi resep dokter BPJS saat melakukan pengambilan obat atau saat menerima obat dari kurir untuk keperluan verifikasi kepesertaan BPJS Anda.</p>
                    </div>
                @endif
            </div>

            <!-- Detail Pesanan Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-gray-100 justify-center">
                <a href="{{ route('orders.show', $order->id) }}" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl text-center transition shadow-sm hover:shadow">
                    Lacak Detail Pesanan
                </a>
                <a href="{{ route('orders.history') }}" class="px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-text-main text-sm font-semibold rounded-xl text-center transition">
                    Riwayat Belanja Saya
                </a>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted py-8 mt-12 text-center text-xs text-text-muted">
        <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
    </footer>

</body>
</html>
