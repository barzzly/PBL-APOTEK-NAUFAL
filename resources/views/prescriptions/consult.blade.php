<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Chat dengan Apoteker - Apotek Naufal</title>
    
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
                    <a href="{{ route('profile.edit') }}" class="px-3 py-2 text-sm font-semibold text-text-main flex items-center gap-2 hover:text-primary transition">
                                <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center overflow-hidden border border-gray-100 shadow-sm">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ str_starts_with(auth()->user()->avatar, '/') ? auth()->user()->avatar : '/' . auth()->user()->avatar }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user"></i>
                                    @endif
                                </div>
                                {{ auth()->user()->name }}
                            </a>
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
        <div style="max-width: 900px; margin: 0 auto; width: 100%;">
            <div class="flex items-center gap-3 mb-6">
                <a href="/" class="text-text-muted hover:text-primary transition text-sm"><i class="fa-solid fa-house"></i> Beranda</a>
                <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
                <a href="{{ route('tickets.history') }}" class="text-text-muted hover:text-primary transition text-sm">Ticket Saya</a>
                <i class="fa-solid fa-chevron-right text-xs text-text-muted"></i>
                <span class="text-text-main font-semibold text-sm">Konsultasi Chat</span>
            </div>

            <div class="bg-white rounded-3xl overflow-hidden border border-border-muted shadow-xl">
                <!-- Form Header -->
                <div class="bg-primary p-6 md:p-8 text-white relative overflow-hidden">
                    <h1 class="text-xl md:text-2xl font-bold mb-2 flex items-center gap-2"><i class="fa-solid fa-comments"></i> Konsultasi Chat dengan Apoteker</h1>
                    <p class="text-xs md:text-sm text-white/90">Silakan tanyakan keluhan penyakit, dosis obat, atau kebutuhan obat Anda langsung kepada apoteker kami melalui tiket obrolan konsultasi.</p>
                </div>

                <!-- Form Content -->
                <form action="{{ route('tickets.consult.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
                    @csrf

                    @if($errors->any())
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-800 text-sm space-y-1">
                        <strong class="font-semibold block mb-1">Periksa kembali isian Anda:</strong>
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Patient Name -->
                        <div>
                            <label for="patient_name" class="text-xs font-semibold text-text-main block mb-2">Nama Pasien <span class="text-red-500">*</span></label>
                            <input type="text" name="patient_name" id="patient_name" value="{{ old('patient_name', auth()->user()->name) }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition"
                                placeholder="Masukkan nama pasien">
                        </div>

                        <!-- Patient Age -->
                        <div>
                            <label for="patient_age" class="text-xs font-semibold text-text-main block mb-2">Umur Pasien (Tahun)</label>
                            <input type="number" name="patient_age" id="patient_age" value="{{ old('patient_age') }}" min="0" max="150"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition"
                                placeholder="Contoh: 25">
                        </div>

                        <!-- Complaints / Symptoms -->
                        <div class="md:col-span-2">
                            <label for="customer_notes" class="text-xs font-semibold text-text-main block mb-2">Detail Keluhan / Pertanyaan Konsultasi <span class="text-red-500">*</span></label>
                            <textarea name="customer_notes" id="customer_notes" rows="4" required
                                class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition resize-none"
                                placeholder="Tuliskan gejala yang Anda rasakan, atau pertanyaan mengenai kebutuhan obat Anda secara detail agar apoteker kami dapat memberikan rekomendasi obat yang tepat.">{{ old('customer_notes') }}</textarea>
                        </div>

                        <!-- Optional Photo Upload Zone -->
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-text-main block mb-2">Foto Gejala / Obat yang Ditanyakan (Opsional)</label>
                            <div id="drop-zone" class="w-full border-2 border-dashed border-border-muted rounded-xl p-6 text-center hover:bg-gray-50 transition cursor-pointer relative flex flex-col items-center justify-center">
                                <input type="file" name="image" id="file-input" accept="image/*" class="hidden">
                                
                                <div id="upload-prompt" class="flex flex-col items-center justify-center">
                                    <div class="text-4xl text-gray-300 mb-3">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-text-main mb-1">Klik untuk upload foto</p>
                                    <p class="text-xs text-text-muted">Format: JPG, PNG, GIF (Maks. 2MB)</p>
                                </div>

                                <div id="upload-preview" class="hidden w-full flex flex-col items-center space-y-3">
                                    <div class="w-full max-h-64 rounded-xl overflow-hidden shadow-md bg-white border border-border-muted p-2 flex items-center justify-center">
                                        <img id="preview-img" src="#" alt="Preview" class="w-full h-full object-contain max-h-56">
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span id="preview-filename" class="text-xs font-semibold text-gray-650 truncate max-w-xs">foto.jpg</span>
                                        <button type="button" id="remove-file-btn" class="text-xs font-bold text-red-500 hover:text-red-600 flex items-center gap-1"><i class="fa-solid fa-trash-can"></i> Ganti Foto</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('tickets.history') }}" class="w-full sm:w-1/3 text-center px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">Batal</a>
                        <button type="submit" class="w-full sm:w-2/3 py-3 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl transition shadow-md shadow-primary/20 flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-paper-plane"></i> Mulai Konsultasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-border-muted py-8 mt-12 text-center text-xs text-text-muted">
        <p>&copy; 2026 Apotek Naufal. All rights reserved.</p>
    </footer>

    <script>
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-input');
        const uploadPrompt = document.getElementById('upload-prompt');
        const uploadPreview = document.getElementById('upload-preview');
        const previewImg = document.getElementById('preview-img');
        const previewFilename = document.getElementById('preview-filename');
        const removeFileBtn = document.getElementById('remove-file-btn');

        // Click on dropzone triggers input click
        dropZone.addEventListener('click', (e) => {
            if (e.target.closest('#remove-file-btn')) return;
            fileInput.click();
        });

        // Handle file change
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        // Drag and drop handlers
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('border-primary', 'bg-primary/5');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-primary', 'bg-primary/5');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            handleFiles(files);
        });

        // Show image preview
        function handleFiles(files) {
            if (files && files[0]) {
                const file = files[0];
                if (!file.type.startsWith('image/')) {
                    alert('Mohon pilih berkas gambar/foto.');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewFilename.textContent = file.name;
                    uploadPrompt.classList.add('hidden');
                    uploadPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        }

        // Remove / Replace file
        removeFileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = '';
            previewImg.src = '#';
            previewFilename.textContent = '';
            uploadPrompt.classList.remove('hidden');
            uploadPreview.classList.add('hidden');
        });
    </script>
</body>
</html>
