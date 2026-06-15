@extends('admin.layout')
@section('header_title', 'Edit Obat')

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.medicines') }}" class="text-gray-400 hover:text-primary transition">Data Obat</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Edit Obat</span>
</div>
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-border-muted p-6 md:p-8">
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-border-muted">
            <a href="{{ route('admin.medicines') }}" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-text-muted hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-xl font-bold text-text-main">Edit Data Obat</h2>
        </div>

        <form action="{{ route('admin.medicines.update', $medicine->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-semibold text-text-main mb-2">Nama Obat <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ $medicine->name }}" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-text-main mb-2">Kategori <span class="text-red-500">*</span></label>
                <select name="category_id" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none bg-white transition" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $medicine->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2">Harga (Rp) <span class="text-red-500">*</span></label>
                    <div class="flex items-center border border-border-muted rounded-lg overflow-hidden focus-within:border-primary focus-within:ring-4 focus-within:ring-primary-light transition bg-white">
                        <span class="px-4 py-3 bg-gray-50 text-text-muted border-r border-border-muted font-medium">Rp</span>
                        <input type="number" name="price" value="{{ (int)$medicine->price }}" class="w-full px-4 py-3 outline-none" required min="0">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-text-main mb-2">Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" value="{{ $medicine->stock }}" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition" required min="0">
                </div>
            </div>
            
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-text-main">Deskripsi Obat</label>
                    <button type="button" id="btn-generate-ai" onclick="generateAIDescription()" class="px-3 py-1.5 hover:brightness-105 active:scale-[0.98] text-xs font-bold rounded-lg transition-all duration-200 flex items-center gap-1.5 shadow-sm" style="background-color: #346739; color: #ffffff; border: none; cursor: pointer;">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> ✨ Generate dengan AI
                    </button>
                </div>
                <textarea name="description" id="description-field" rows="4" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition" placeholder="Tuliskan deskripsi obat atau biarkan kosong untuk di-generate otomatis dengan AI...">{{ old('description', $medicine->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-text-main mb-2">Foto Obat</label>
                
                @if($medicine->image)
                <div class="mb-4">
                    <p class="text-xs text-text-muted mb-2">Foto saat ini:</p>
                    <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="w-24 h-24 object-cover rounded-lg border border-border-muted">
                </div>
                @endif
                
                <div class="border-2 border-dashed border-border-muted rounded-xl p-6 text-center hover:bg-gray-50 transition cursor-pointer relative" id="upload-zone">
                    <input type="file" name="image" id="image-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div id="upload-placeholder">
                        <div class="text-4xl text-gray-300 mb-3"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                        <p class="text-sm font-semibold text-text-main mb-1">Upload foto baru untuk mengganti</p>
                        <p class="text-xs text-text-muted">Biarkan kosong jika tidak ingin mengubah foto</p>
                    </div>
                    <div id="image-preview-container" class="hidden flex flex-col items-center justify-center gap-2 relative">
                        <img id="image-preview" src="#" alt="Pratinjau Foto" class="w-32 h-32 object-cover rounded-lg border border-border-muted shadow-sm">
                        <p id="file-name" class="text-xs font-semibold text-text-main max-w-xs truncate"></p>
                        <span class="px-3 py-1.5 bg-primary/10 text-primary hover:bg-primary/20 text-xs rounded-lg font-bold mt-1 transition">Ganti Foto Pilihan</span>
                    </div>
                </div>
            </div>
            
            <div class="pt-4 border-t border-border-muted mt-6">
                <button type="submit" class="w-full py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Perbarui Data Obat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function generateAIDescription() {
        const nameInput = document.querySelector('input[name="name"]');
        const categorySelect = document.querySelector('select[name="category_id"]');
        const descTextarea = document.getElementById('description-field');
        const aiBtn = document.getElementById('btn-generate-ai');

        if (!nameInput.value.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Nama Obat Kosong',
                text: 'Silakan isi Nama Obat terlebih dahulu.',
                confirmButtonColor: '#346739',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
            nameInput.focus();
            return;
        }

        // Set loading state
        const originalBtnText = aiBtn.innerHTML;
        aiBtn.disabled = true;
        aiBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Menulis Deskripsi...';
        aiBtn.classList.add('opacity-75');

        const csrfToken = document.querySelector('input[name="_token"]').value;

        fetch('{{ route('admin.medicines.generate_description') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: nameInput.value,
                category_id: categorySelect.value || null
            })
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan sistem.') });
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                descTextarea.value = data.description;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Deskripsi obat berhasil ditulis oleh AI.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menulis Deskripsi',
                    text: data.message || 'Terjadi kesalahan saat generate deskripsi.',
                    confirmButtonColor: '#79AE6F',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menghubungi Gemini AI',
                text: err.message || 'Terjadi kesalahan sistem.',
                confirmButtonColor: '#79AE6F',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        })
        .finally(() => {
            // Restore button state
            aiBtn.disabled = false;
            aiBtn.innerHTML = originalBtnText;
            aiBtn.classList.remove('opacity-75');
        });
    }

    document.getElementById('image-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const placeholder = document.getElementById('upload-placeholder');
        const previewContainer = document.getElementById('image-preview-container');
        const previewImg = document.getElementById('image-preview');
        const fileName = document.getElementById('file-name');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImg.src = event.target.result;
                fileName.textContent = file.name;
                placeholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '#';
            fileName.textContent = '';
            placeholder.classList.remove('hidden');
            previewContainer.classList.add('hidden');
        }
    });
</script>
@endsection
