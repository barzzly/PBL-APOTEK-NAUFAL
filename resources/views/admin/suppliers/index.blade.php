@extends('admin.layout')
@section('header_title', 'Data Supplier')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3 text-xs">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
        <span class="text-gray-600 font-bold">Data Supplier</span>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-primary/20 hover:bg-primary/90 transition-all">
        <i class="fa-solid fa-plus text-xs"></i> Tambah Supplier Baru
    </a>
</div>

@if(session('success'))
<div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-emerald-800 text-sm font-medium mb-6 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
</div>
@endif

{{-- FILTER & SEARCH BAR --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.suppliers.index') }}" class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3 flex-1 min-w-[280px]">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama supplier, telepon, atau email..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-900 transition-all">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.suppliers.index') }}" class="text-xs text-gray-400 hover:text-red-500 underline whitespace-nowrap">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- SUPPLIER TABLE --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-400">
                <tr>
                    <th class="py-4 px-6">No</th>
                    <th class="py-4 px-6">Nama Supplier</th>
                    <th class="py-4 px-6">Kontak</th>
                    <th class="py-4 px-6">Alamat</th>
                    <th class="py-4 px-6 text-center">Total Transaksi</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($suppliers as $index => $supplier)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="py-4 px-6 font-medium text-gray-400">{{ $suppliers->firstItem() + $index }}</td>
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-800 text-base">{{ $supplier->name }}</div>
                        @if($supplier->notes)
                            <div class="text-xs text-gray-400 truncate max-w-xs mt-0.5"><i class="fa-regular fa-note-sticky mr-1"></i>{{ $supplier->notes }}</div>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        @if($supplier->phone)
                            <div class="text-xs font-medium text-gray-700 flex items-center gap-1.5"><i class="fa-solid fa-phone text-emerald-600 w-4"></i> {{ $supplier->phone }}</div>
                        @endif
                        @if($supplier->email)
                            <div class="text-xs text-gray-400 flex items-center gap-1.5 mt-0.5"><i class="fa-solid fa-envelope text-blue-500 w-4"></i> {{ $supplier->email }}</div>
                        @endif
                        @if(!$supplier->phone && !$supplier->email)
                            <span class="text-xs text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-xs text-gray-600 max-w-xs leading-relaxed">
                        {{ $supplier->address ?? '-' }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600">
                            {{ $supplier->transactions_count }} Pasokan
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}"
                                class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:text-primary hover:border-primary flex items-center justify-center transition" title="Edit Supplier">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                            <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg border border-gray-200 text-gray-600 hover:text-red-600 hover:border-red-500 flex items-center justify-center transition" title="Hapus Supplier">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-boxes-packing text-4xl mb-3 text-gray-200"></i>
                        <p class="text-sm font-medium">Belum ada data supplier.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>
@endsection
