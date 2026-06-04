@extends('admin.layout')
@section('header_title', 'Tambah Kategori')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-border-muted p-6 md:p-8">
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-border-muted">
            <a href="{{ route('admin.categories') }}" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-text-muted hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-xl font-bold text-text-main">Form Tambah Kategori</h2>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-text-main mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="w-full px-4 py-3 border border-border-muted rounded-lg text-sm focus:border-primary focus:ring-4 focus:ring-primary-light outline-none transition" placeholder="Contoh: Vitamin & Suplemen" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-text-main mb-2">Gambar / Icon Kategori</label>
                <div class="border-2 border-dashed border-border-muted rounded-xl p-6 text-center hover:bg-gray-50 transition cursor-pointer relative">
                    <input type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="text-4xl text-gray-300 mb-3"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <p class="text-sm font-medium text-text-main mb-1">Klik untuk upload foto</p>
                    <p class="text-xs text-text-muted">Format: JPG, PNG, GIF (Maks. 2MB)</p>
                </div>
            </div>
            
            <div class="pt-4 border-t border-border-muted mt-6">
                <button type="submit" class="w-full py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
