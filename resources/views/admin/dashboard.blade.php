@extends('admin.layout')
@section('header_title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-border-muted flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-primary-light text-primary flex items-center justify-center text-2xl">
            <i class="fa-solid fa-list"></i>
        </div>
        <div>
            <div class="text-sm text-text-muted mb-1">Total Kategori</div>
            <div class="text-2xl font-bold">{{ $categoriesCount }}</div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-border-muted flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl">
            <i class="fa-solid fa-pills"></i>
        </div>
        <div>
            <div class="text-sm text-text-muted mb-1">Total Obat</div>
            <div class="text-2xl font-bold">{{ $medicinesCount }}</div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-border-muted p-6">
    <h2 class="text-lg font-bold mb-4">Selamat datang di Panel Admin</h2>
    <p class="text-text-muted">Gunakan menu di sebelah kiri untuk mengelola kategori dan data obat yang akan ditampilkan di halaman utama Apotek Naufal.</p>
</div>
@endsection
