@extends('admin.layout')
@section('header_title', 'Transaksi Pasokan Supplier')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3 text-xs">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
        <span class="text-gray-600 font-bold">Riwayat Stok Masuk / Keluar</span>
    </div>
    <a href="{{ route('admin.supplier_transactions.create') }}" class="flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-primary/20 hover:bg-primary/90 transition-all">
        <i class="fa-solid fa-plus text-xs"></i> Catat Stok Masuk / Keluar
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

{{-- FILTER BAR --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.supplier_transactions.index') }}" class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 flex-1 min-w-[280px]">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari supplier, nama obat, atau catatan..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
            
            <select name="type" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary bg-white">
                <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Semua Jenis (Masuk/Keluar)</option>
                <option value="masuk" {{ $type == 'masuk' ? 'selected' : '' }}>Barang Masuk (Stok +)</option>
                <option value="keluar" {{ $type == 'keluar' ? 'selected' : '' }}>Barang Keluar / Retur (Stok -)</option>
            </select>

            <button type="submit" class="bg-gray-800 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-900 transition-all">
                Terapkan Filter
            </button>
            @if($search || $type !== 'all')
                <a href="{{ route('admin.supplier_transactions.index') }}" class="text-xs text-gray-400 hover:text-red-500 underline whitespace-nowrap">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- TRANSACTIONS TABLE --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 border-b border-gray-100 text-xs font-bold uppercase tracking-wider text-gray-400">
                <tr>
                    <th class="py-4 px-6">Tanggal</th>
                    <th class="py-4 px-6">Supplier</th>
                    <th class="py-4 px-6">Obat</th>
                    <th class="py-4 px-6 text-center">Jenis</th>
                    <th class="py-4 px-6 text-right">Jumlah</th>
                    <th class="py-4 px-6 text-right">Harga Satuan</th>
                    <th class="py-4 px-6 text-right">Total Biaya</th>
                    <th class="py-4 px-6">Catatan / Faktur</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="py-4 px-6 font-medium text-gray-700 whitespace-nowrap">
                        {{ optional($trx->transaction_date)->format('d/m/Y H:i') }}
                    </td>
                    <td class="py-4 px-6">
                        <div class="font-bold text-gray-800">{{ optional($trx->supplier)->name ?? 'Supplier Terhapus' }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <div class="font-semibold text-gray-900">{{ optional($trx->medicine)->name ?? 'Obat Terhapus' }}</div>
                        <span class="text-xs text-gray-400">Kemasan: {{ optional($trx->medicine)->unit ?? '-' }}</span>
                    </td>
                    <td class="py-4 px-6 text-center whitespace-nowrap">
                        @if($trx->type === 'masuk')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                <i class="fa-solid fa-arrow-down-left text-[10px]"></i> Barang Masuk
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                <i class="fa-solid fa-arrow-up-right text-[10px]"></i> Barang Keluar
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right font-bold {{ $trx->type === 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $trx->type === 'masuk' ? '+' : '-' }}{{ number_format($trx->quantity, 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-6 text-right text-gray-700">
                        Rp {{ number_format($trx->unit_price, 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-6 text-right font-bold text-gray-900">
                        Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-6 text-xs text-gray-500 max-w-xs leading-relaxed">
                        {{ $trx->notes ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-truck-ramp-box text-4xl mb-3 text-gray-200"></i>
                        <p class="text-sm font-medium">Belum ada riwayat transaksi stok supplier.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="p-4 border-t border-gray-100">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
