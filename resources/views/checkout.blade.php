<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Apotek Naufal</title>
    
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
                        {{ array_sum(array_column(session('cart', []), 'quantity')) }}
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
            <a href="{{ route('cart.index') }}" class="text-text-muted hover:text-primary transition text-sm">Keranjang Belanja</a>
            <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
            <span class="text-text-main font-semibold text-sm">Checkout</span>
        </div>

        <h1 class="text-2xl font-bold text-text-main mb-6">Checkout</h1>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                <h4 class="font-bold mb-2 flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Terjadi Kesalahan Input:</h4>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" id="checkout-form">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Inputs Section -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- 1. Tipe Pengiriman -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs">1</span>
                            Tipe Pengiriman
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Option Pickup -->
                            <label class="border-2 border-primary rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="label-pickup">
                                <input type="radio" name="order_type" value="pickup" checked onchange="toggleOrderType('pickup')" class="mt-1 accent-primary">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-sm text-text-main">Ambil di Apotek (Pickup)</span>
                                    <span class="text-xs text-text-muted mt-1">Gratis Biaya Pengiriman</span>
                                    <span class="text-[10px] text-primary font-semibold mt-2"><i class="fa-solid fa-location-dot"></i> Jl. Kesehatan No. 123</span>
                                </div>
                            </label>
                            
                            <!-- Option Delivery -->
                            <label class="border border-gray-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="label-delivery">
                                <input type="radio" name="order_type" value="delivery" onchange="toggleOrderType('delivery')" class="mt-1 accent-primary">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-sm text-text-main">Kirim ke Alamat (Delivery)</span>
                                    <span class="text-xs text-text-muted mt-1">Biaya Flat: Rp 15.000</span>
                                    <span class="text-[10px] text-secondary font-semibold mt-2"><i class="fa-solid fa-motorcycle"></i> Pengiriman Instant / Reguler</span>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Shipping Address Container -->
                        <div id="address-container" class="hidden space-y-2 mt-4 pt-2 border-t border-gray-100">
                            <label class="text-xs font-semibold text-text-main block">Alamat Pengiriman Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="shipping_address" id="shipping_address" placeholder="Tuliskan alamat lengkap pengiriman, beserta kelurahan, kecamatan, dan kode pos..." class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition h-24">{{ old('shipping_address') }}</textarea>
                        </div>
                    </div>

                    <!-- 2. Informasi Resep Dokter (Conditional) -->
                    @if($requiresPrescription)
                    <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-6 space-y-4 bg-amber-50/20">
                        <h3 class="text-base font-bold text-amber-800 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-xs"><i class="fa-solid fa-prescription"></i></span>
                            Informasi Resep Dokter
                        </h3>
                        <p class="text-xs text-amber-700 leading-relaxed">Keranjang Anda mengandung obat keras yang memerlukan verifikasi resep dokter. Silakan isi form di bawah ini dan unggah foto resep dokter yang valid.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-text-main block">Nama Dokter <span class="text-red-500">*</span></label>
                                <input type="text" name="doctor_name" value="{{ old('doctor_name') }}" placeholder="Dr. John Doe" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-text-main block">Tanggal Resep <span class="text-red-500">*</span></label>
                                <input type="date" name="prescription_date" value="{{ old('prescription_date') ?? date('Y-m-d') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-text-main block">Nama Pasien <span class="text-red-500">*</span></label>
                                <input type="text" name="patient_name" value="{{ old('patient_name') ?? $user->name }}" placeholder="Nama Pasien" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-text-main block">Umur Pasien (Tahun)</label>
                                <input type="number" name="patient_age" value="{{ old('patient_age') }}" placeholder="Contoh: 25" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-text-main block">Rumah Sakit / Klinik</label>
                            <input type="text" name="hospital_clinic" value="{{ old('hospital_clinic') }}" placeholder="Contoh: RS Medika" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition">
                        </div>

                        <div class="space-y-2 pt-2">
                            <label class="text-xs font-semibold text-text-main block">Unggah Foto / Scan Resep Dokter <span class="text-red-500">*</span></label>
                            <input type="file" name="prescription_image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                            <p class="text-[10px] text-text-muted">Format file: JPG, PNG, JPEG. Ukuran maksimum: 2MB.</p>
                        </div>
                    </div>
                    @endif

                    <!-- 3. Metode Pembayaran -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs">2</span>
                            Metode Pembayaran
                        </h3>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Tunai -->
                            <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="pay-cash">
                                <input type="radio" name="payment_method" value="cash" checked onchange="togglePaymentMethod('cash')" class="absolute top-3 right-3 accent-primary">
                                <div class="text-xl text-primary mt-2"><i class="fa-solid fa-money-bill-wave"></i></div>
                                <span class="text-xs font-semibold text-text-main mt-1">Tunai</span>
                            </label>
                            
                            <!-- Transfer Bank -->
                            <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="pay-transfer">
                                <input type="radio" name="payment_method" value="transfer" onchange="togglePaymentMethod('transfer')" class="absolute top-3 right-3 accent-primary">
                                <div class="text-xl text-primary mt-2"><i class="fa-solid fa-building-columns"></i></div>
                                <span class="text-xs font-semibold text-text-main mt-1">Transfer Bank</span>
                            </label>
                            
                            <!-- QRIS -->
                            <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="pay-qris">
                                <input type="radio" name="payment_method" value="qris" onchange="togglePaymentMethod('qris')" class="absolute top-3 right-3 accent-primary">
                                <div class="text-xl text-primary mt-2"><i class="fa-solid fa-qrcode"></i></div>
                                <span class="text-xs font-semibold text-text-main mt-1">QRIS / E-Wallet</span>
                            </label>

                            <!-- BPJS -->
                            <label class="border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden" id="pay-bpjs">
                                <input type="radio" name="payment_method" value="bpjs" onchange="togglePaymentMethod('bpjs')" class="absolute top-3 right-3 accent-primary">
                                <div class="text-xl text-primary mt-2"><i class="fa-solid fa-id-card"></i></div>
                                <span class="text-xs font-semibold text-text-main mt-1">BPJS</span>
                            </label>
                        </div>

                        <!-- Transfer Instruction Info -->
                        <div id="transfer-info" class="hidden p-4 rounded-xl bg-gray-50 border border-gray-200 space-y-3 mt-4 text-xs text-text-main">
                            <h4 class="font-bold text-sm text-primary">Informasi Rekening Bank Apotek Naufal</h4>
                            <p>Silakan lakukan pembayaran ke nomor rekening di bawah ini:</p>
                            <div class="p-3 bg-white border border-gray-100 rounded-lg font-mono flex justify-between items-center text-sm">
                                <div>
                                    <span class="text-text-muted text-[10px] font-sans block uppercase">Bank Mandiri</span>
                                    <strong>123-000-456789-0</strong>
                                    <span class="text-xs font-sans text-text-muted block">a/n Apotek Naufal Jaya</span>
                                </div>
                            </div>
                            <div class="space-y-2 pt-2">
                                <label class="text-xs font-semibold block">Unggah Bukti Transfer (Opsional)</label>
                                <input type="file" name="payment_proof" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                                <p class="text-[10px] text-text-muted">Format file: JPG, PNG, JPEG. Ukuran maksimum: 2MB. Anda juga dapat mengunggah bukti setelah pemesanan dilakukan.</p>
                            </div>
                        </div>

                        <!-- QRIS Instruction Info -->
                        <div id="qris-info" class="hidden p-4 rounded-xl bg-gray-50 border border-gray-200 space-y-3 mt-4 text-xs text-text-main flex flex-col items-center">
                            <h4 class="font-bold text-sm text-primary w-full text-left">Pembayaran QRIS Apotek Naufal</h4>
                            <p class="w-full">Pindai kode QR di bawah ini menggunakan aplikasi e-wallet Anda (Gopay, OVO, Dana, LinkAja) atau m-Banking:</p>
                            
                            <!-- Mock QR Code Design -->
                            <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col items-center shadow-inner mt-2">
                                <div class="w-40 h-40 bg-gray-200 flex items-center justify-center border-4 border-primary relative">
                                    <i class="fa-solid fa-qrcode text-8xl text-gray-800"></i>
                                    <div class="absolute inset-0 bg-black/5 flex items-center justify-center">
                                        <span class="bg-primary text-white font-bold text-[10px] px-2 py-1 rounded shadow-sm">APOTEK NAUFAL</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold tracking-widest mt-2 uppercase">NMID: ID10304958102</span>
                            </div>

                            <div class="space-y-2 pt-2 w-full">
                                <label class="text-xs font-semibold block">Unggah Bukti Bayar QRIS (Opsional)</label>
                                <input type="file" name="payment_proof_qris" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                                <p class="text-[10px] text-text-muted">Format file: JPG, PNG, JPEG. Ukuran maksimum: 2MB.</p>
                            </div>
                        </div>

                        <!-- BPJS Info -->
                        <div id="bpjs-info" class="hidden p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 leading-relaxed mt-4">
                            <h4 class="font-bold mb-1"><i class="fa-solid fa-circle-info"></i> Ketentuan Klaim BPJS</h4>
                            <p>Pembayaran menggunakan BPJS memerlukan verifikasi manual oleh staf apotek kami. Pastikan kartu BPJS Anda aktif dan unggah resep dokter BPJS resmi pada form resep dokter di atas.</p>
                        </div>
                    </div>

                    <!-- 4. Catatan Tambahan -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="text-base font-bold text-text-main flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs">3</span>
                            Catatan Tambahan
                        </h3>
                        <textarea name="notes" placeholder="Tuliskan catatan khusus untuk apoteker atau kurir jika diperlukan..." class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition h-20">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Summary Column -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                        <h3 class="text-base font-bold text-text-main">Ringkasan Pesanan</h3>
                        
                        <!-- Mini items list -->
                        <div class="divide-y divide-gray-100 max-h-60 overflow-y-auto pr-1">
                            @foreach($cart as $item)
                            <div class="py-3 flex justify-between gap-3 text-xs">
                                <div class="flex-grow">
                                    <span class="font-medium text-text-main block">{{ $item['name'] }}</span>
                                    <span class="text-text-muted text-[10px]">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }} ({{ $item['unit'] ?? 'pcs' }})</span>
                                </div>
                                <span class="font-semibold text-text-main shrink-0">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="space-y-3 pt-4 border-t border-gray-100">
                            <div class="flex justify-between text-sm text-text-muted">
                                <span>Subtotal Produk</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-text-muted">
                                <span>Biaya Pengiriman</span>
                                <span id="shipping-cost-label">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <span class="text-base font-bold text-text-main">Total Bayar</span>
                                <span class="text-lg font-bold text-secondary" id="total-amount-label">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl text-center text-sm shadow-sm transition-all hover:shadow-md cursor-pointer">
                            Buat Pesanan Sekarang
                        </button>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 space-y-3">
                        <h4 class="text-xs font-bold text-text-main uppercase tracking-wider"><i class="fa-solid fa-shield-halved text-primary mr-1"></i> Keamanan & Privasi</h4>
                        <p class="text-[10px] text-text-muted leading-relaxed">Seluruh transaksi Anda dienkripsi secara aman. Data resep dan rekam medis dilindungi sesuai undang-undang kesehatan Republik Indonesia.</p>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted pt-12 pb-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center text-sm text-text-muted">
            <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
        </div>
    </footer>

    <!-- Interactive script for dynamic forms -->
    <script>
        const subtotal = {{ $subtotal }};
        
        function toggleOrderType(type) {
            const addressContainer = document.getElementById('address-container');
            const shippingCostLabel = document.getElementById('shipping-cost-label');
            const totalAmountLabel = document.getElementById('total-amount-label');
            const inputAddress = document.getElementById('shipping_address');

            const labelPickup = document.getElementById('label-pickup');
            const labelDelivery = document.getElementById('label-delivery');

            if (type === 'delivery') {
                addressContainer.classList.remove('hidden');
                inputAddress.setAttribute('required', 'required');
                
                // Styling labels
                labelDelivery.className = "border-2 border-primary rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
                labelPickup.className = "border border-gray-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
                
                // Pricing
                const total = subtotal + 15000;
                shippingCostLabel.innerText = "Rp 15.000";
                totalAmountLabel.innerText = formatRupiah(total);
            } else {
                addressContainer.classList.add('hidden');
                inputAddress.removeAttribute('required');
                
                // Styling labels
                labelPickup.className = "border-2 border-primary rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
                labelDelivery.className = "border border-gray-200 rounded-xl p-4 flex items-start gap-3 cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
                
                // Pricing
                shippingCostLabel.innerText = "Rp 0";
                totalAmountLabel.innerText = formatRupiah(subtotal);
            }
        }

        function togglePaymentMethod(method) {
            const transferInfo = document.getElementById('transfer-info');
            const qrisInfo = document.getElementById('qris-info');
            const bpjsInfo = document.getElementById('bpjs-info');

            // Reset borders
            document.getElementById('pay-cash').className = "border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
            document.getElementById('pay-transfer').className = "border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
            document.getElementById('pay-qris').className = "border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";
            document.getElementById('pay-bpjs').className = "border border-gray-200 rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";

            // Set primary border for active payment method
            document.getElementById(`pay-${method}`).className = "border-2 border-primary rounded-xl p-3 flex flex-col items-center gap-2 text-center cursor-pointer hover:bg-gray-50 transition relative overflow-hidden";

            // Show instruction
            transferInfo.classList.add('hidden');
            qrisInfo.classList.add('hidden');
            bpjsInfo.classList.add('hidden');

            if (method === 'transfer') {
                transferInfo.classList.remove('hidden');
            } else if (method === 'qris') {
                qrisInfo.classList.remove('hidden');
            } else if (method === 'bpjs') {
                bpjsInfo.classList.remove('hidden');
            }
        }

        // Initialize active class
        togglePaymentMethod('cash');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(number).replace("Rp", "Rp ");
        }

        // Form submission loading
        document.getElementById('checkout-form').addEventListener('submit', function () {
            Swal.fire({
                title: 'Sedang memproses pesanan...',
                text: 'Mohon tunggu beberapa saat.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    </script>
</body>
</html>
