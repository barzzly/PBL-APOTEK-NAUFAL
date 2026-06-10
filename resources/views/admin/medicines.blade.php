@extends('admin.layout')
@section('header_title', 'Data Obat')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-border-muted overflow-hidden">
    <div class="p-5 border-b border-border-muted flex justify-between items-center bg-gray-50">
        <h2 class="text-lg font-bold text-text-main">Daftar Obat</h2>
        <a href="{{ route('admin.medicines.create') }}" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-dark transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Obat Baru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-white border-b border-border-muted">
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Gambar</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Nama Obat</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Kategori</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Harga</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Stok</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicines as $medicine)
                <tr class="border-b border-border-muted hover:bg-gray-50 transition">
                    <td class="py-3 px-5">
                        @if($medicine->image)
                            <img src="{{ str_starts_with($medicine->image, '/') ? $medicine->image : '/' . $medicine->image }}" alt="{{ $medicine->name }}" class="w-14 h-14 object-cover rounded-lg border border-gray-100 bg-white">
                        @else
                            <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200"><i class="fa-solid fa-pills"></i></div>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-sm font-medium text-text-main">{{ $medicine->name }}</td>
                    <td class="py-3 px-5 text-sm">
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium border border-gray-200">{{ $medicine->category->name ?? '-' }}</span>
                    </td>
                    <td class="py-3 px-5 text-sm font-semibold text-secondary">Rp {{ number_format($medicine->price, 0, ',', '.') }}</td>
                    <td class="py-3 px-5 text-sm">
                        <span class="px-3 py-1 {{ $medicine->stock > 5 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full font-semibold text-xs">{{ $medicine->stock }}</span>
                    </td>
                    <td class="py-3 px-5 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.medicines.edit', $medicine->id) }}" class="w-8 h-8 rounded bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.medicines.destroy', $medicine->id) }}" method="POST" class="confirm-delete" data-message="Apakah Anda yakin ingin menghapus obat ini?">
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
                    <td colspan="6" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-box-open"></i></div>
                        <p class="text-sm">Belum ada data obat. Silakan tambahkan obat baru.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
