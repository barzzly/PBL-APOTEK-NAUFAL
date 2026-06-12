@extends('admin.layout')
@section('header_title', 'Laporan Penjualan')

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Laporan Penjualan</span>
</div>
{{-- ======================== FILTER BAR ======================== --}}
<form method="GET" action="{{ route('admin.laporan') }}" id="filterForm">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">

    {{-- Filter row --}}
    <div class="flex flex-wrap gap-4 items-end">

        {{-- Periode --}}
        <div class="flex flex-col gap-1.5">
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Periode</label>
            <select name="period" id="periodSelect" onchange="toggleCustomDate(this.value)"
                class="min-w-[155px] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition bg-white">
                <option value="7"   {{ $period == '7'      ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30"  {{ $period == '30'     ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="90"  {{ $period == '90'     ? 'selected' : '' }}>90 Hari Terakhir</option>
                <option value="365" {{ $period == '365'    ? 'selected' : '' }}>1 Tahun Terakhir</option>
                <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Kustom...</option>
            </select>
        </div>

        {{-- Custom date range --}}
        <div id="customDateRange" class="flex items-end gap-3 {{ $period !== 'custom' ? 'hidden' : '' }}">
            <div class="flex flex-col gap-1.5">
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Dari</label>
                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}"
                    class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Sampai</label>
                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}"
                    class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
            </div>
        </div>

        {{-- Status Order --}}
        <div class="flex flex-col gap-1.5">
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Status Order</label>
            <select name="status" class="min-w-[165px] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition bg-white">
                <option value="all"       {{ $statusFilter == 'all'       ? 'selected' : '' }}>Semua Status (Selesai/Batal)</option>
                <option value="delivered" {{ $statusFilter == 'delivered' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ $statusFilter == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
        </div>

        {{-- Metode Bayar --}}
        <div class="flex flex-col gap-1.5">
            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Metode Bayar</label>
            <select name="payment_method" class="min-w-[155px] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition bg-white">
                <option value="all"      {{ $paymentFilter == 'all'      ? 'selected' : '' }}>Semua Metode</option>
                <option value="cash"     {{ $paymentFilter == 'cash'     ? 'selected' : '' }}>Tunai</option>
                <option value="transfer" {{ $paymentFilter == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                <option value="bpjs"     {{ $paymentFilter == 'bpjs'     ? 'selected' : '' }}>BPJS</option>
                <option value="qris"     {{ $paymentFilter == 'qris'     ? 'selected' : '' }}>QRIS</option>
            </select>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex items-end gap-2 ml-auto">
            <button type="submit"
                class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-primary/20 hover:bg-primary/90 transition-all whitespace-nowrap">
                <i class="fa-solid fa-magnifying-glass text-xs"></i> Terapkan Filter
            </button>
            <a href="{{ route('admin.laporan') }}"
                class="flex items-center gap-2 border border-gray-200 text-gray-500 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all whitespace-nowrap">
                <i class="fa-solid fa-rotate-right text-xs"></i> Reset
            </a>
        </div>
    </div>

    {{-- Info tanggal aktif --}}
    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-400">
        <i class="fa-regular fa-calendar text-gray-300"></i>
        <span>Menampilkan data dari
            <strong class="text-gray-600">{{ $from->isoFormat('DD MMM YYYY') }}</strong>
            sampai
            <strong class="text-gray-600">{{ $to->isoFormat('DD MMM YYYY') }}</strong>
        </span>
    </div>
</div>
</form>

{{-- ======================== SUMMARY CARDS ======================== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

    {{-- Total Pendapatan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-5">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Lunas</span>
            </div>
            <div class="text-2xl font-bold text-gray-800 mb-1 leading-snug">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </div>
            <div class="text-sm text-gray-400">Total Pendapatan</div>
        </div>
    </div>

    {{-- Total Order --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-5">
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Semua Status</span>
            </div>
            <div class="text-2xl font-bold text-gray-800 mb-1 leading-snug">{{ number_format($totalOrder) }}</div>
            <div class="text-sm text-gray-400">Total Order</div>
        </div>
    </div>

    {{-- Order Selesai --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-5">
                <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <span class="text-xs font-semibold text-violet-600 bg-violet-50 px-2.5 py-1 rounded-full">Delivered</span>
            </div>
            <div class="text-2xl font-bold text-gray-800 mb-1 leading-snug">{{ number_format($orderSelesai) }}</div>
            <div class="text-sm text-gray-400">Order Selesai</div>
        </div>
    </div>

    {{-- Order Dibatalkan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
        <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl"></div>
        <div class="relative">
            <div class="flex items-start justify-between mb-5">
                <div class="w-11 h-11 rounded-xl bg-red-100 text-red-500 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <span class="text-xs font-semibold text-red-500 bg-red-50 px-2.5 py-1 rounded-full">Cancelled</span>
            </div>
            <div class="text-2xl font-bold text-gray-800 mb-1 leading-snug">{{ number_format($orderDibatalkan) }}</div>
            <div class="text-sm text-gray-400">Order Dibatalkan</div>
        </div>
    </div>

</div>

{{-- ======================== CHARTS ======================== --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-5 mb-6">
    {{-- Revenue chart (wider) --}}
    <div class="xl:col-span-3 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-gray-800">Grafik Pendapatan Harian</h2>
                <p class="text-xs text-gray-400 mt-1">Pendapatan dari order yang sudah lunas</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chart-line text-sm"></i>
            </div>
        </div>
        <div class="relative h-56">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Order count chart --}}
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-gray-800">Jumlah Order Harian</h2>
                <p class="text-xs text-gray-400 mt-1">Semua status order</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chart-bar text-sm"></i>
            </div>
        </div>
        <div class="relative h-56">
            <canvas id="orderChart"></canvas>
        </div>
    </div>
</div>

{{-- ======================== TOP MEDICINES + TRANSACTIONS ======================== --}}
<div class="grid grid-cols-1 xl:grid-cols-5 gap-5 mb-6">
    {{-- Top Medicines --}}
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800">Obat Terlaris</h2>
                <p class="text-xs text-gray-400 mt-0.5">Berdasarkan kuantitas terjual (order lunas)</p>
            </div>
        </div>

        @if($topMedicines->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
            <i class="fa-solid fa-box-open text-3xl mb-3 text-gray-300"></i>
            <p class="text-sm">Belum ada data penjualan</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($topMedicines as $index => $item)
            <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50/60 transition-colors">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0
                    {{ $index === 0 ? 'bg-amber-100 text-amber-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-400')) }}">
                    {{ $index + 1 }}
                </div>
                <div class="flex-grow min-w-0">
                    <div class="text-sm font-semibold text-gray-800 truncate">{{ $item->medicine_name }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">
                        Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <div class="text-sm font-bold text-primary">{{ number_format($item->total_qty) }}</div>
                    <div class="text-xs text-gray-400">unit</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Quick stats --}}
    <div class="xl:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-5 content-start">
        @php
            $conversionRate = $totalOrder > 0 ? round(($orderSelesai / $totalOrder) * 100, 1) : 0;
            $cancelRate     = $totalOrder > 0 ? round(($orderDibatalkan / $totalOrder) * 100, 1) : 0;
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-percent"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">Tingkat Keberhasilan</span>
            </div>
            <div class="text-3xl font-bold text-emerald-600 mb-1">{{ $conversionRate }}%</div>
            <div class="text-xs text-gray-400 mb-3">Order selesai dari total order</div>
            <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full bg-emerald-500 transition-all duration-700"
                     style="width: {{ $conversionRate }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-red-100 text-red-500 flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">Tingkat Pembatalan</span>
            </div>
            <div class="text-3xl font-bold text-red-500 mb-1">{{ $cancelRate }}%</div>
            <div class="text-xs text-gray-400 mb-3">Order dibatalkan dari total order</div>
            <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full rounded-full bg-red-400 transition-all duration-700"
                     style="width: {{ $cancelRate }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:col-span-2">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-primary-light text-primary flex items-center justify-center text-sm shrink-0">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">Rata-rata Nilai Order</span>
            </div>
            @php $avgOrder = $totalOrder > 0 ? $totalPendapatan / $orderSelesai : 0; @endphp
            <div class="text-2xl font-bold text-gray-800">
                Rp {{ $orderSelesai > 0 ? number_format($avgOrder, 0, ',', '.') : '0' }}
            </div>
            <div class="text-xs text-gray-400 mt-1">Rata-rata per transaksi yang selesai</div>
        </div>
    </div>
</div>

{{-- ======================== TRANSACTION TABLE ======================== --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <!-- Header: Title and Search side-by-side -->
    <div class="px-6 py-4 border-b border-gray-100 bg-white" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 16px; width: 100%; box-sizing: border-box; text-align: left;">
        <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-sm shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-800" style="margin: 0; text-align: left;">Riwayat Transaksi</h2>
                <p class="text-xs text-gray-400 mt-0.5" style="margin: 0; text-align: left;">Total <span id="transaction-count-display">{{ $orders->total() }}</span> transaksi ditemukan</p>
            </div>
        </div>
        
        <!-- Actions: Search input -->
        <div style="display: flex; align-items: center; gap: 12px; justify-content: flex-end; margin: 0; flex-wrap: wrap;">
            <!-- Limit Dropdown -->
            <div style="display: flex; align-items: center; gap: 8px; shrink-0;">
                <span class="text-xs text-text-muted whitespace-nowrap" style="font-size: 12px; color: #9ca3af;">Tampilkan:</span>
                <select onchange="changePerPage(this)" 
                        style="padding: 6px 10px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 12px; outline: none; background-color: #fff; cursor: pointer; color: #4b5563;">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ ($perPage ?? 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Client-Side Instant Search Input -->
            <div style="position: relative; display: inline-block; width: 100%; min-width: 200px; max-width: 320px; box-sizing: border-box;">
                <input type="text" id="search-input" oninput="filterTransactions()" placeholder="Cari transaksi..." 
                       style="width: 100%; padding: 8px 36px 8px 14px; border: 1px solid #e0e0e0; border-radius: 9999px; font-size: 13px; outline: none; transition: all 0.2s; box-sizing: border-box; background-color: #fff;" 
                       onfocus="this.style.borderColor='#00A651'; this.style.boxShadow='0 0 0 3px rgba(0, 166, 81, 0.15)';" 
                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #00A651; font-size: 13px;"></i>
                <button type="button" id="clear-btn" onclick="clearSearch()" style="display: none; position: absolute; right: 28px; top: 50%; transform: translateY(-50%); color: #9ca3af; border: none; background: none; cursor: pointer; padding: 2px; font-size: 13px; align-items: center; justify-content: center; outline: none;" title="Hapus">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm" style="table-layout: fixed; min-width: 1000px;">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                    <th class="px-6 py-3.5 border-r border-gray-200/80" style="width: 14%;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'order_number', 'sort_order' => ($sortBy === 'order_number' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            No. Order
                            @if($sortBy === 'order_number')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3.5 border-r border-gray-200/80" style="width: 18%;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'customer_name', 'sort_order' => ($sortBy === 'customer_name' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Pelanggan
                            @if($sortBy === 'customer_name')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3.5 border-r border-gray-200/80" style="width: 13%;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($sortBy === 'created_at' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Tanggal
                            @if($sortBy === 'created_at')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3.5 border-r border-gray-200/80" style="width: 10%;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'order_type', 'sort_order' => ($sortBy === 'order_type' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Jenis
                            @if($sortBy === 'order_type')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3.5 border-r border-gray-200/80" style="width: 13%;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_method', 'sort_order' => ($sortBy === 'payment_method' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Metode Bayar
                            @if($sortBy === 'payment_method')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3.5 border-r border-gray-200/80" style="width: 15%;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => ($sortBy === 'status' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Status Order
                            @if($sortBy === 'status')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3.5 border-r border-gray-200/80" style="width: 15%;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_status', 'sort_order' => ($sortBy === 'payment_status' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Status Bayar
                            @if($sortBy === 'payment_status')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3.5 text-right" style="width: 12%;">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_amount', 'sort_order' => ($sortBy === 'total_amount' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center justify-end gap-1 hover:text-gray-700 transition">
                            Total
                            @if($sortBy === 'total_amount')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50/60 transition-colors transaction-row">
                    <td class="px-6 py-4 border-r border-gray-100">
                        <span class="font-mono text-xs font-semibold text-gray-700 bg-gray-100 px-2 py-1 rounded-lg whitespace-nowrap transaction-number">
                            {{ $order->order_number }}
                        </span>
                    </td>
                    <td class="px-6 py-4 border-r border-gray-100">
                        <div class="font-medium text-gray-800 customer-name">{{ $order->user->name ?? '-' }}</div>
                        <div class="text-xs text-gray-400 mt-0.5 customer-email">{{ $order->user->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap border-r border-gray-100 transaction-date">
                        <div>{{ $order->created_at->isoFormat('DD MMM YYYY') }}</div>
                        <div class="text-gray-400 mt-0.5">{{ $order->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 border-r border-gray-100">
                        @if($order->order_type === 'delivery')
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full whitespace-nowrap shipping-type">
                                <i class="fa-solid fa-truck text-[10px]"></i> Dikirim
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-full whitespace-nowrap shipping-type">
                                <i class="fa-solid fa-store text-[10px]"></i> Ambil
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-xs font-medium whitespace-nowrap border-r border-gray-100 payment-method">
                        {{ $order->payment_method_label }}
                    </td>
                    <td class="px-6 py-4 border-r border-gray-100">
                        @php
                            $colorMap = [
                                'yellow' => 'text-yellow-700 bg-yellow-50',
                                'blue'   => 'text-blue-700 bg-blue-50',
                                'indigo' => 'text-indigo-700 bg-indigo-50',
                                'purple' => 'text-purple-700 bg-purple-50',
                                'cyan'   => 'text-cyan-700 bg-cyan-50',
                                'green'  => 'text-green-700 bg-green-50',
                                'red'    => 'text-red-600 bg-red-50',
                                'gray'   => 'text-gray-600 bg-gray-50',
                            ];
                            $colorClass = $colorMap[$order->status_color] ?? $colorMap['gray'];
                        @endphp
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap order-status {{ $colorClass }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 border-r border-gray-100">
                        @if($order->payment_status === 'paid')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full whitespace-nowrap payment-status">
                                <i class="fa-solid fa-check text-[10px]"></i> Lunas
                            </span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full whitespace-nowrap payment-status">
                                <i class="fa-solid fa-rotate-left text-[10px]"></i> Dikembangkan
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full whitespace-nowrap payment-status">
                                <i class="fa-regular fa-clock text-[10px]"></i> Belum Bayar
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-800 whitespace-nowrap transaction-total">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr id="initial-empty-row">
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-4xl text-gray-200"></i>
                            <div>
                                <p class="font-semibold text-gray-500">Tidak ada transaksi</p>
                                <p class="text-xs mt-1">Coba ubah filter atau rentang tanggal</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse

                <!-- Dynamic Search Empty State Row -->
                <tr id="empty-state-row" style="display: none;">
                    <td colspan="8" class="px-6 py-16 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <p class="text-sm font-semibold text-gray-500">Tidak ditemukan transaksi dengan kata kunci "<span id="search-query-span" class="font-bold text-text-main"></span>".</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>

{{-- ======================== SCRIPTS ======================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Toggle custom date range
    window.toggleCustomDate = function(val) {
        document.getElementById('customDateRange').classList.toggle('hidden', val !== 'custom');
    }

    // Fetch chart data from AJAX endpoint
    const params = new URLSearchParams({
        period:         '{{ $period }}',
        date_from:      '{{ $dateFrom ?? '' }}',
        date_to:        '{{ $dateTo ?? '' }}',
    });

    fetch('{{ route('admin.laporan.chart') }}?' + params.toString())
        .then(r => r.json())
        .then(data => {
            buildRevenueChart(data.labels, data.revenues);
            buildOrderChart(data.labels, data.counts);
        });

    function buildRevenueChart(labels, revenues) {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // Gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(0, 166, 81, 0.18)');
        gradient.addColorStop(1, 'rgba(0, 166, 81, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: revenues,
                    borderColor: '#00A651',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#00A651',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        },
                        backgroundColor: '#1f2937',
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af',
                            maxTicksLimit: 10 }
                    },
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 11 }, color: '#9ca3af',
                            callback: v => 'Rp ' + (v >= 1000000
                                ? (v/1000000).toFixed(1) + 'jt'
                                : v >= 1000 ? (v/1000).toFixed(0) + 'rb' : v)
                        }
                    }
                }
            }
        });
    }

    function buildOrderChart(labels, counts) {
        const ctx = document.getElementById('orderChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Order',
                    data: counts,
                    backgroundColor: 'rgba(59, 130, 246, 0.15)',
                    borderColor: '#3b82f6',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' order'
                        },
                        backgroundColor: '#1f2937',
                        padding: 10,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#9ca3af',
                            maxTicksLimit: 10 }
                    },
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 11 }, color: '#9ca3af',
                            stepSize: 1,
                            precision: 0,
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    }
});

window.filterTransactions = function() {
    const searchVal = document.getElementById('search-input').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.transaction-row');
    const clearBtn = document.getElementById('clear-btn');
    let foundCount = 0;

    if (clearBtn) {
        clearBtn.style.display = searchVal.length > 0 ? 'flex' : 'none';
    }

    rows.forEach(row => {
        const number = row.querySelector('.transaction-number').textContent.toLowerCase();
        const name = row.querySelector('.customer-name') ? row.querySelector('.customer-name').textContent.toLowerCase() : '';
        const email = row.querySelector('.customer-email') ? row.querySelector('.customer-email').textContent.toLowerCase() : '';
        const date = row.querySelector('.transaction-date').textContent.toLowerCase();
        const shipping = row.querySelector('.shipping-type').textContent.toLowerCase();
        const paymentMethod = row.querySelector('.payment-method').textContent.toLowerCase();
        const orderStatus = row.querySelector('.order-status').textContent.toLowerCase();
        const paymentStatus = row.querySelector('.payment-status').textContent.toLowerCase();
        const total = row.querySelector('.transaction-total').textContent.toLowerCase();

        if (
            number.includes(searchVal) || 
            name.includes(searchVal) || 
            email.includes(searchVal) || 
            date.includes(searchVal) || 
            shipping.includes(searchVal) || 
            paymentMethod.includes(searchVal) || 
            orderStatus.includes(searchVal) || 
            paymentStatus.includes(searchVal) || 
            total.includes(searchVal)
        ) {
            row.style.display = '';
            foundCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const countDisplay = document.getElementById('transaction-count-display');
    if (countDisplay) {
        countDisplay.innerText = foundCount;
    }

    const emptyRow = document.getElementById('empty-state-row');
    if (emptyRow) {
        if (foundCount === 0 && rows.length > 0) {
            emptyRow.style.display = '';
            const querySpan = document.getElementById('search-query-span');
            if (querySpan) {
                querySpan.innerText = searchVal;
            }
        } else {
            emptyRow.style.display = 'none';
        }
    }
}

window.clearSearch = function() {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.value = '';
        filterTransactions();
        searchInput.focus();
    }
}
</script>
@endsection
