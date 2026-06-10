@extends('admin.layout')
@section('header_title', 'Kategori Obat')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-border-muted overflow-hidden">
    <div class="p-5 border-b border-border-muted flex justify-between items-center bg-gray-50">
        <h2 class="text-lg font-bold text-text-main">Daftar Kategori</h2>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-dark transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Kategori
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-white border-b border-border-muted">
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Gambar/Icon</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Nama Kategori</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-center">Jumlah Obat</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr class="border-b border-border-muted hover:bg-gray-50 transition">
                    <td class="py-3 px-5">
                        @if($category->image)
                            <img src="{{ str_starts_with($category->image, '/') ? $category->image : '/' . $category->image }}" alt="{{ $category->name }}" class="w-12 h-12 object-cover rounded-lg border border-gray-100 bg-white">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200"><i class="fa-solid fa-tags"></i></div>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-sm font-medium text-text-main">{{ $category->name }}</td>
                    <td class="py-3 px-5 text-sm text-center">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full font-semibold text-xs border border-blue-100">{{ $category->medicines ? $category->medicines->count() : 0 }} Produk</span>
                    </td>
                    <td class="py-3 px-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="w-8 h-8 rounded bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="confirm-delete" data-message="Menghapus kategori ini akan menghapus semua obat di dalamnya. Yakin?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-tags"></i></div>
                        <p class="text-sm">Belum ada kategori. Silakan tambahkan kategori baru.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
