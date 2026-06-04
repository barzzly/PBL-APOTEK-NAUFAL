@extends('admin.layout')
@section('header_title', 'Edit Obat')

@section('content')
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
                <label class="block text-sm font-semibold text-text-main mb-2">Foto Obat</label>
                
                @if($medicine->image)
                <div class="mb-4">
                    <p class="text-xs text-text-muted mb-2">Foto saat ini:</p>
                    <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="w-24 h-24 object-cover rounded-lg border border-border-muted">
                </div>
                @endif
                
                <div class="border-2 border-dashed border-border-muted rounded-xl p-6 text-center hover:bg-gray-50 transition cursor-pointer relative">
                    <input type="file" name="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="text-4xl text-gray-300 mb-3"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <p class="text-sm font-medium text-text-main mb-1">Upload foto baru untuk mengganti</p>
                    <p class="text-xs text-text-muted">Biarkan kosong jika tidak ingin mengubah foto</p>
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
@endsection
