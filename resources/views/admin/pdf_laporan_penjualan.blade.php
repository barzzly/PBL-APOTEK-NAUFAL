<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan - Apotek Naufal</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #16704A;
            padding-bottom: 10px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand {
            font-size: 20px;
            font-weight: bold;
            color: #16704A;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 13px;
            color: #4b5563;
            font-weight: bold;
            margin-top: 2px;
        }
        .meta-info {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
        }
        
        /* Summary Section */
        .summary-container {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-card {
            width: 24%;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 12px;
            vertical-align: top;
        }
        .summary-title {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .data-table th {
            background-color: #16704A;
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 8px;
            border: 1px solid #12583a;
            text-align: left;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        /* Utility */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-info { background-color: #dbeafe; color: #1e40af; }

        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #9ca3af;
            width: 100%;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand">APOTEK NAUFAL</div>
                    <div class="subtitle">Laporan Penjualan Pembelian & Transaksi</div>
                </td>
                <td class="meta-info">
                    <div><strong>Periode:</strong> {{ $from->isoFormat('DD MMMM YYYY') }} - {{ $to->isoFormat('DD MMMM YYYY') }}</div>
                    <div><strong>Status Filter:</strong> {{ $statusFilter === 'all' ? 'Semua (Selesai & Dibatalkan)' : ucfirst($statusFilter) }}</div>
                    <div><strong>Metode Bayar:</strong> {{ $paymentFilter === 'all' ? 'Semua Metode' : strtoupper($paymentFilter) }}</div>
                    <div><strong>Dicetak pada:</strong> {{ \Carbon\Carbon::now()->isoFormat('DD MMM YYYY HH:mm') }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Summary Cards --}}
    <table class="summary-container">
        <tr>
            <td class="summary-card">
                <div class="summary-title">Total Pendapatan Lunas</div>
                <div class="summary-value" style="color: #16704A;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </td>
            <td style="width: 1.33%;"></td>
            <td class="summary-card">
                <div class="summary-title">Total Transaksi</div>
                <div class="summary-value">{{ number_format($totalOrder) }} Order</div>
            </td>
            <td style="width: 1.33%;"></td>
            <td class="summary-card">
                <div class="summary-title">Order Selesai</div>
                <div class="summary-value" style="color: #059669;">{{ number_format($orderSelesai) }} Order</div>
            </td>
            <td style="width: 1.33%;"></td>
            <td class="summary-card">
                <div class="summary-title">Order Dibatalkan</div>
                <div class="summary-value" style="color: #dc2626;">{{ number_format($orderDibatalkan) }} Order</div>
            </td>
        </tr>
    </table>

    {{-- Data Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">No</th>
                <th style="width: 11%;">Tanggal</th>
                <th style="width: 14%;">No. Order</th>
                <th style="width: 16%;">Pelanggan</th>
                <th class="text-center" style="width: 9%;">Tipe Order</th>
                <th class="text-center" style="width: 10%;">Status Order</th>
                <th class="text-center" style="width: 11%;">Metode Bayar</th>
                <th class="text-center" style="width: 11%;">Status Bayar</th>
                <th class="text-right" style="width: 14%;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                <td class="font-bold">{{ $order->order_number }}</td>
                <td>
                    <div class="font-bold">{{ optional($order->user)->name ?? 'Guest' }}</div>
                    <div style="font-size: 8.5px; color: #6b7280;">{{ optional($order->user)->phone ?? '-' }}</div>
                </td>
                <td class="text-center">
                    {{ $order->order_type === 'delivery' ? 'Delivery' : 'Pickup' }}
                </td>
                <td class="text-center">
                    @if($order->status === 'delivered')
                        <span class="badge badge-success">Selesai</span>
                    @elseif($order->status === 'cancelled')
                        <span class="badge badge-danger">Dibatalkan</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($order->status) }}</span>
                    @endif
                </td>
                <td class="text-center">
                    {{ strtoupper($order->payment_method ?? 'CASH') }}
                </td>
                <td class="text-center">
                    @if($order->payment_status === 'paid')
                        <span class="badge badge-success">Lunas</span>
                    @else
                        <span class="badge badge-warning">Unpaid</span>
                    @endif
                </td>
                <td class="text-right font-bold">
                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px; color: #9ca3af;">
                    Tidak ada data transaksi penjualan pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($orders->count() > 0)
        <tfoot>
            <tr style="background-color: #f3f4f6; font-weight: bold;">
                <td colspan="8" class="text-right" style="padding: 8px;">Total Pendapatan (Lunas):</td>
                <td class="text-right" style="padding: 8px; color: #16704A; font-size: 11px;">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Footer --}}
    <table class="footer">
        <tr>
            <td>Apotek Naufal - Dokumen Resmi Laporan Penjualan</td>
            <td class="text-right">Halaman 1 dari 1</td>
        </tr>
    </table>

</body>
</html>
