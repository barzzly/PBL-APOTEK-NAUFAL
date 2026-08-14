@extends('admin.layout')
@section('header_title', 'Input Transaksi Stok Supplier')

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <a href="{{ route('admin.supplier_transactions.index') }}" class="text-gray-400 hover:text-primary transition">Riwayat Transaksi Supplier</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Input Pasokan (Masuk / Keluar)</span>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
        <div class="border-b border-gray-100 pb-5 mb-6">
            <h2 class="text-xl font-bold text-gray-800">Input Data Barang Masuk / Keluar Supplier</h2>
            <p class="text-xs text-gray-400 mt-1">Mencatat barang dari supplier. Stok obat di sistem akan diperbarui secara otomatis.</p>
        </div>

        @if($errors->any())
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl text-rose-800 text-sm font-medium mb-6">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.supplier_transactions.store') }}">
            @csrf

            <div class="space-y-6">
                {{-- Jenis Transaksi Radio / Pills --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Jenis Transaksi <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center justify-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-emerald-500 hover:bg-emerald-50/40 transition group has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-600 has-[:checked]:ring-2 has-[:checked]:ring-emerald-500/20">
                            <input type="radio" name="type" value="masuk" checked class="accent-emerald-600 w-4 h-4">
                            <div class="text-left">
                                <div class="text-sm font-bold text-gray-800 flex items-center gap-1.5"><i class="fa-solid fa-circle-arrow-down text-emerald-600"></i> Barang Masuk</div>
                                <div class="text-xs text-gray-400">Pasokan obat baru (Menambah Stok)</div>
                            </div>
                        </label>

                        <label class="relative flex items-center justify-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-rose-500 hover:bg-rose-50/40 transition group has-[:checked]:bg-rose-50 has-[:checked]:border-rose-600 has-[:checked]:ring-2 has-[:checked]:ring-rose-500/20">
                            <input type="radio" name="type" value="keluar" class="accent-rose-600 w-4 h-4">
                            <div class="text-left">
                                <div class="text-sm font-bold text-gray-800 flex items-center gap-1.5"><i class="fa-solid fa-circle-arrow-up text-rose-600"></i> Barang Keluar / Retur</div>
                                <div class="text-xs text-gray-400">Retur obat / kedaluwarsa (Mengurangi Stok)</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Pilih Supplier & Obat --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Supplier <span class="text-red-500">*</span></label>
                        <select name="supplier_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                        @if($suppliers->isEmpty())
                            <p class="text-xs text-amber-600 mt-1"><i class="fa-solid fa-triangle-exclamation"></i> Belum ada supplier. <a href="{{ route('admin.suppliers.index') }}" class="underline font-bold">Tambah supplier dulu</a>.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Obat <span class="text-red-500">*</span></label>
                        <select name="medicine_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white">
                            <option value="">-- Pilih Obat --</option>
                            @foreach($medicines as $med)
                                <option value="{{ $med->id }}">
                                    {{ $med->name }} (Stok saat ini: {{ $med->stock }} {{ $med->unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Jumlah & Harga Satuan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Jumlah (Qty) <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" min="1" required placeholder="10"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Harga Beli Satuan (Rp)</label>
                        <input type="number" name="unit_price" min="0" step="100" placeholder="15000"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                    </div>
                </div>

                {{-- Tanggal Transaksi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Tanggal Transaksi <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="transaction_date" required value="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>

                {{-- Catatan / No. Faktur --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Catatan / No. Faktur / Keterangan</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: No. Faktur INV/2026/08/001 - Kiriman Batch A-12"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-gray-100">
                <a href="{{ route('admin.supplier_transactions.index') }}" class="px-5 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl text-sm font-semibold hover:bg-primary/90 shadow-md shadow-primary/20 transition-all">
                    Simpan & Update Stok
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
