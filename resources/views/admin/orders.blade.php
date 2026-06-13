@extends('admin.layout')
@section('header_title', 'Manajemen Pesanan')

@section('content')
<div class="flex items-center gap-3 mb-6 text-xs">
    <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-primary transition"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-gray-600 font-bold">Pesanan</span>
</div>
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-lg"></i>
        <div class="text-sm font-semibold">{{ session('success') }}</div>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-border-muted overflow-hidden">
    <!-- Header: Title and Actions side-by-side -->
    <div class="p-5 border-b border-border-muted bg-gray-50" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: 16px; width: 100%; box-sizing: border-box; text-align: left;">
        <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
            <h2 class="text-lg font-bold text-text-main" style="margin: 0; text-align: left;">Daftar Pesanan Masuk</h2>
            <span class="text-xs bg-gray-100 border border-gray-200 text-text-muted px-2.5 py-0.5 rounded-full font-semibold">
                <span id="order-count-display">{{ $orders->count() }}</span> Pesanan
            </span>
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
                <input type="text" id="search-input" oninput="filterOrders()" placeholder="Cari pesanan..." 
                       style="width: 100%; padding: 8px 36px 8px 14px; border: 1px solid #e0e0e0; border-radius: 9999px; font-size: 13px; outline: none; transition: all 0.2s; box-sizing: border-box; background-color: #fff;" 
                       onfocus="this.style.borderColor='#346739'; this.style.boxShadow='0 0 0 3px rgba(52, 103, 57, 0.15)';" 
                       onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #346739; font-size: 13px;"></i>
                <button type="button" id="clear-btn" onclick="clearSearch()" style="display: none; position: absolute; right: 28px; top: 50%; transform: translateY(-50%); color: #9ca3af; border: none; background: none; cursor: pointer; padding: 2px; font-size: 13px; align-items: center; justify-content: center; outline: none;" title="Hapus">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-white border-b border-border-muted">
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'order_number', 'sort_order' => ($sortBy === 'order_number' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            No. Pesanan
                            @if($sortBy === 'order_number')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'customer_name', 'sort_order' => ($sortBy === 'customer_name' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Pelanggan
                            @if($sortBy === 'customer_name')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => ($sortBy === 'created_at' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center gap-1 hover:text-gray-700 transition">
                            Tanggal
                            @if($sortBy === 'created_at')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-center">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'order_type', 'sort_order' => ($sortBy === 'order_type' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1 hover:text-gray-700 transition">
                            Pengiriman
                            @if($sortBy === 'order_type')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-center">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'payment_status', 'sort_order' => ($sortBy === 'payment_status' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1 hover:text-gray-700 transition">
                            Pembayaran
                            @if($sortBy === 'payment_status')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-right">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_amount', 'sort_order' => ($sortBy === 'total_amount' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="flex items-center justify-end gap-1 hover:text-gray-700 transition">
                            Total Belanja
                            @if($sortBy === 'total_amount')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-center">
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => ($sortBy === 'status' && $sortOrder === 'asc') ? 'desc' : 'asc', 'page' => 1]) }}" class="inline-flex items-center gap-1 hover:text-gray-700 transition">
                            Status Order
                            @if($sortBy === 'status')
                                <i class="fa-solid {{ $sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }} text-primary"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300"></i>
                            @endif
                        </a>
                    </th>
                    <th class="py-4 px-5 text-sm font-semibold text-text-muted text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b border-border-muted hover:bg-gray-50 transition text-xs order-row">
                    <!-- Order number -->
                    <td class="py-4 px-5">
                        <strong class="text-sm font-bold text-text-main block order-number">{{ $order->order_number }}</strong>
                        @php
                            $prescription = \App\Models\Prescription::where('order_id', $order->id)->first();
                        @endphp
                        @if($prescription)
                            <span class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[9px] font-bold"><i class="fa-solid fa-prescription"></i> Resep Dokter</span>
                        @endif
                    </td>

                    <!-- Customer Info -->
                    <td class="py-4 px-5">
                        <span class="font-semibold text-text-main block text-sm customer-name">{{ $order->user->name }}</span>
                        <span class="text-[10px] text-text-muted customer-email">{{ $order->user->email }}</span>
                    </td>

                    <!-- Created Date -->
                    <td class="py-4 px-5 text-text-muted font-medium order-date">
                        {{ $order->created_at->isoFormat('D MMM YYYY HH:mm') }}
                    </td>

                    <!-- Shipping type -->
                    <td class="py-4 px-5 text-center">
                        @if($order->order_type === 'delivery')
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded border border-blue-100 font-semibold shipping-type">Kirim</span>
                        @else
                            <span class="px-2 py-0.5 bg-purple-50 text-purple-600 rounded border border-purple-100 font-semibold shipping-type">Ambil</span>
                        @endif
                    </td>

                    <!-- Payment details -->
                    <td class="py-4 px-5 text-center">
                        @if($order->payment_status === 'paid')
                            <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded border border-green-100 font-bold payment-status">Lunas</span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded border border-gray-200 payment-status">Refund</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded border border-red-100 font-bold payment-status">Unpaid</span>
                        @endif
                    </td>

                    <!-- Total Amount -->
                    <td class="py-4 px-5 text-right font-bold text-secondary text-sm order-total">
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
                        <span class="px-2.5 py-1 rounded-full border font-bold text-[10px] order-status {{ $colorClass }}">
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
                <tr id="initial-empty-row">
                    <td colspan="8" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-receipt"></i></div>
                        <p class="text-sm">Belum ada data pesanan.</p>
                    </td>
                </tr>
                @endforelse

                <!-- Dynamic Search Empty State Row -->
                <tr id="empty-state-row" style="display: none;">
                    <td colspan="8" class="py-12 text-center text-text-muted">
                        <div class="text-4xl mb-3 opacity-30"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <p class="text-sm">Tidak ditemukan pesanan dengan kata kunci "<span id="search-query-span" class="font-bold text-text-main"></span>".</p>
                    </td>
                </tr>
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

<script>
    function filterOrders() {
        const searchVal = document.getElementById('search-input').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.order-row');
        const clearBtn = document.getElementById('clear-btn');
        let foundCount = 0;

        if (clearBtn) {
            clearBtn.style.display = searchVal.length > 0 ? 'flex' : 'none';
        }

        rows.forEach(row => {
            const number = row.querySelector('.order-number').textContent.toLowerCase();
            const name = row.querySelector('.customer-name').textContent.toLowerCase();
            const email = row.querySelector('.customer-email').textContent.toLowerCase();
            const date = row.querySelector('.order-date').textContent.toLowerCase();
            const shipping = row.querySelector('.shipping-type').textContent.toLowerCase();
            const payment = row.querySelector('.payment-status') ? row.querySelector('.payment-status').textContent.toLowerCase() : '';
            const total = row.querySelector('.order-total').textContent.toLowerCase();
            const status = row.querySelector('.order-status').textContent.toLowerCase();

            if (
                number.includes(searchVal) || 
                name.includes(searchVal) || 
                email.includes(searchVal) || 
                date.includes(searchVal) || 
                shipping.includes(searchVal) || 
                payment.includes(searchVal) || 
                total.includes(searchVal) || 
                status.includes(searchVal)
            ) {
                row.style.display = '';
                foundCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countDisplay = document.getElementById('order-count-display');
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

    function clearSearch() {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = '';
            filterOrders();
            searchInput.focus();
        }
    }
</script>
@endsection
