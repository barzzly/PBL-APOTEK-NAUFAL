@extends('admin.layout')
@section('header_title', 'Dashboard')

@section('content')

{{-- ── GREETING BANNER ── --}}
<div class="bg-gradient-to-r from-primary to-emerald-400 rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
    <div class="absolute -right-2 bottom-0 w-24 h-24 bg-white/5 rounded-full"></div>
    <div class="relative">
        <p class="text-sm font-medium text-white/75 mb-1">{{ now()->isoFormat('dddd, DD MMMM YYYY') }}</p>
        <h2 class="text-2xl font-bold mb-1">Selamat datang, {{ auth()->user()->name ?? 'Admin' }}!</h2>
        <p class="text-sm text-white/80">Berikut ringkasan aktivitas Apotek Naufal hari ini.</p>
    </div>
</div>

{{-- ── STAT CARDS ROW 1 ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    {{-- Pendapatan Hari Ini --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Hari Ini</span>
            </div>
            <div class="text-xl font-bold text-gray-800 mb-1 leading-snug">
                Rp {{ number_format($revenueToday, 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-400">Pendapatan Hari Ini</div>
        </div>
    </div>

    {{-- Pendapatan Bulan Ini --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">{{ now()->isoFormat('MMM YYYY') }}</span>
            </div>
            <div class="text-xl font-bold text-gray-800 mb-1 leading-snug">
                Rp {{ number_format($revenueMonth, 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-400">Pendapatan Bulan Ini</div>
        </div>
    </div>

    {{-- Order Hari Ini --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <span class="text-xs font-semibold text-violet-600 bg-violet-50 px-2.5 py-1 rounded-full">Hari Ini</span>
            </div>
            <div class="text-2xl font-bold text-gray-800 mb-1 leading-snug">{{ $ordersToday }}</div>
            <div class="text-sm text-gray-400">Order Masuk</div>
        </div>
    </div>

    {{-- Order Menunggu --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-clock"></i>
                </div>
                @if($ordersPending > 0)
                    <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Perlu Aksi</span>
                @else
                    <span class="text-xs font-semibold text-gray-400 bg-gray-50 px-2.5 py-1 rounded-full">Aman</span>
                @endif
            </div>
            <div class="text-2xl font-bold {{ $ordersPending > 0 ? 'text-amber-600' : 'text-gray-800' }} mb-1 leading-snug">{{ $ordersPending }}</div>
            <div class="text-sm text-gray-400">Order Menunggu</div>
        </div>
    </div>
</div>

{{-- ── STAT CARDS ROW 2 ── --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    {{-- Total Order --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($totalOrders) }}</div>
                <div class="text-sm text-gray-400 mt-0.5">Total Order</div>
            </div>
        </div>
    </div>

    {{-- Total Obat --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-pills"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($medicinesCount) }}</div>
                <div class="text-sm text-gray-400 mt-0.5">Total Obat</div>
            </div>
        </div>
    </div>

    {{-- Total Kategori --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-list"></i>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($categoriesCount) }}</div>
                <div class="text-sm text-gray-400 mt-0.5">Total Kategori</div>
            </div>
        </div>
    </div>

    {{-- Stok Hampir Habis --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 group hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 text-red-500 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-red-500' : 'text-gray-800' }}">
                    {{ $lowStockCount }}
                </div>
                <div class="text-sm text-gray-400 mt-0.5">Stok Hampir Habis</div>
            </div>
        </div>
    </div>
</div>

{{-- ── BOTTOM SECTION: Recent Orders + Low Stock ── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

    {{-- Recent Orders (span 2) --}}
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Order Terbaru</h2>
                    <p class="text-xs text-gray-400 mt-0.5">6 transaksi terakhir</p>
                </div>
            </div>
            <a href="{{ route('admin.laporan') }}" class="text-xs font-semibold text-primary hover:underline">Lihat Semua →</a>
        </div>

        @if($recentOrders->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-gray-300">
            <i class="fa-solid fa-inbox text-4xl mb-3"></i>
            <p class="text-sm text-gray-400">Belum ada order masuk</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($recentOrders as $order)
            @php
                $colorMap = [
                    'yellow' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700'],
                    'blue'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-700'],
                    'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700'],
                    'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700'],
                    'cyan'   => ['bg' => 'bg-cyan-50',   'text' => 'text-cyan-700'],
                    'green'  => ['bg' => 'bg-green-50',  'text' => 'text-green-700'],
                    'red'    => ['bg' => 'bg-red-50',    'text' => 'text-red-600'],
                    'gray'   => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600'],
                ];
                $c = $colorMap[$order->status_color] ?? $colorMap['gray'];
            @endphp
            <div class="px-6 py-3.5 flex items-center gap-4 hover:bg-gray-50/60 transition-colors">
                {{-- avatar initial --}}
                <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                    {{ substr($order->user->name ?? 'U', 0, 1) }}
                </div>
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800 truncate">{{ $order->user->name ?? '-' }}</span>
                        <span class="font-mono text-[11px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">{{ $order->order_number }}</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->isoFormat('DD MMM YYYY, HH:mm') }}</div>
                </div>
                <div class="shrink-0 text-right">
                    <div class="text-sm font-bold text-gray-800 mb-1">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $c['bg'] }} {{ $c['text'] }}">
                        {{ $order->status_label }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Low Stock + Quick Links --}}
    <div class="flex flex-col gap-5">

        {{-- Low Stock Alert --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex-grow">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-500 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-800">Stok Hampir Habis</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Stok &lt; 30 unit</p>
                </div>
            </div>

            @if($lowStockMedicines->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-gray-300">
                <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-300"></i>
                <p class="text-sm text-gray-400">Semua stok aman</p>
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($lowStockMedicines as $med)
                <div class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-gray-50/60 transition-colors">
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">{{ $med->name }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $med->category->name ?? '-' }}</div>
                    </div>
                    <span class="shrink-0 text-xs font-bold px-2.5 py-1 rounded-full
                        {{ $med->stock == 0 ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700' }}">
                        {{ $med->stock }} unit
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Quick Links --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h2 class="text-sm font-bold text-gray-800 mb-3">Akses Cepat</h2>
            <div class="grid grid-cols-2 gap-2.5">
                <a href="{{ route('admin.orders') }}"
                   class="flex flex-col items-center gap-2 p-3 bg-primary/5 hover:bg-primary/10 rounded-xl transition-colors text-center group">
                    <div class="w-9 h-9 bg-primary/10 text-primary rounded-lg flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all">
                        <i class="fa-solid fa-receipt text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-600">Pesanan</span>
                </a>
                <a href="{{ route('admin.categories') }}"
                   class="flex flex-col items-center gap-2 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors text-center group">
                    <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="fa-solid fa-list text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-600">Kategori</span>
                </a>
                <a href="{{ route('admin.medicines') }}"
                   class="flex flex-col items-center gap-2 p-3 bg-cyan-50 hover:bg-cyan-100 rounded-xl transition-colors text-center group">
                    <div class="w-9 h-9 bg-cyan-100 text-cyan-600 rounded-lg flex items-center justify-center group-hover:bg-cyan-600 group-hover:text-white transition-all">
                        <i class="fa-solid fa-pills text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-600">Data Obat</span>
                </a>
                <a href="{{ route('admin.laporan') }}"
                   class="flex flex-col items-center gap-2 p-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-colors text-center group">
                    <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <i class="fa-solid fa-chart-line text-sm"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-600">Laporan</span>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
