@extends('admin.layout')
@section('header_title', 'Manajemen Pesanan')

@section('content')
<!-- Status Filters -->
<div class="mb-6 flex flex-wrap gap-2">
    @php
        $statuses = [
            'all' => 'Semua Pesanan',
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready_for_pickup' => 'Siap Diambil',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
    @endphp

    @foreach($statuses as $key => $label)
    <a href="{{ route('admin.orders', ['status' => $key]) }}" 
       class="px-4 py-2 rounded-lg text-xs font-semibold border transition 
       {{ $status === $key 
          ? 'bg-primary text-white border-primary shadow-sm' 
          : 'bg-white text-text-main border-border-muted hover:bg-gray-50' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <div class="text-sm font-semibold">{{ session('success') }}</div>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-border-muted overflow-hidden">
    <div class="p-5 border-b border-border-muted bg-gray-50">
        <h2 class="text-base font-bold text-text-main">Daftar Pesanan Masuk</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-white border-b border-border-muted">
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">No. Pesanan</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Pelanggan</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">Tanggal</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-center">Pengiriman</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-center">Pembayaran</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-right">Total Belanja</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-center">Status Order</th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b border-border-muted hover:bg-gray-50 transition text-xs">
                    <!-- Order number -->
                    <td class="py-4 px-5">
                        <strong class="text-sm font-bold text-text-main block">{{ $order->order_number }}</strong>
                        @php
                            $prescription = \App\Models\Prescription::where('order_id', $order->id)->first();
                        @endphp
                        @if($prescription)
                            <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[9px] font-bold"><i class="fa-solid fa-prescription"></i> Resep Dokter</span>
                        @endif
                    </td>

                    <!-- Customer Info -->
                    <td class="py-4 px-5">
                        <span class="font-semibold text-text-main block text-sm">{{ $order->user->name }}</span>
                        <span class="text-[10px] text-text-muted">{{ $order->user->email }}</span>
                    </td>

                    <!-- Created Date -->
                    <td class="py-4 px-5 text-text-muted font-medium">
                        {{ $order->created_at->isoFormat('D MMM YYYY HH:mm') }}
                    </td>

                    <!-- Shipping type -->
                    <td class="py-4 px-5 text-center">
                        @if($order->order_type === 'delivery')
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded border border-blue-100 font-semibold">Kirim</span>
                        @else
                            <span class="px-2 py-0.5 bg-purple-50 text-purple-600 rounded border border-purple-100 font-semibold">Ambil</span>
                        @endif
                    </td>

                    <!-- Payment details -->
                    <td class="py-4 px-5 text-center">
                        <span class="block font-semibold text-[10px] text-text-main mb-1">{{ $order->payment_method_label }}</span>
                        @if($order->payment_status === 'paid')
                            <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded border border-green-100 font-bold">Lunas</span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded border border-gray-200">Refund</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded border border-red-100 font-bold">Unpaid</span>
                        @endif
                    </td>

                    <!-- Total Amount -->
                    <td class="py-4 px-5 text-right font-bold text-secondary text-sm">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>

                    <!-- Status Order -->
                    <td class="py-4 px-5 text-center">
                        @php
                            $badgeColors = [
                                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'ready_for_pickup' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'shipped' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                'delivered' => 'bg-green-50 text-green-700 border-green-200',
                                'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                            ];
                            $colorClass = $badgeColors[$order->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                        @endphp
                        <span class="px-2.5 py-1 rounded-full border font-bold text-[10px] {{ $colorClass }}">
                            {{ $order->status_label }}
                        </span>
                    </td>

                    <!-- Aksi -->
                    <td class="py-4 px-5 text-right">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 rounded bg-primary text-white text-[11px] font-bold hover:bg-primary-dark transition inline-flex items-center gap-1 shadow-sm">
                            <i class="fa-solid fa-folder-open"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-receipt"></i></div>
                        <p class="text-sm">Tidak ditemukan pesanan dengan status "{{ $statuses[$status] ?? $status }}".</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="p-5 border-t border-border-muted bg-gray-50">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
