<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\Order;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Get all active notifications (low stock, empty stock, new orders).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNotifications()
    {
        $notifications = [];

        // 1. Fetch low/empty stock medicines (stock < 30)
        $lowStockMedicines = Medicine::where('stock', '<', 30)
            ->orderBy('stock')
            ->get();

        foreach ($lowStockMedicines as $med) {
            if ($med->stock == 0) {
                $notifications[] = [
                    'type' => 'stock_empty',
                    'title' => 'Stok Habis!',
                    'message' => "Stok obat <strong>{$med->name}</strong> telah habis. Segera restok!",
                    'time' => 'Habis',
                    'route' => route('admin.medicines.edit', $med->id),
                    'icon' => 'fa-circle-xmark',
                    'icon_bg' => 'bg-red-50 text-red-500',
                    'text_color' => 'text-red-700',
                    'badge' => 'Penting',
                    'badge_class' => 'bg-red-50 text-red-600',
                ];
            } else {
                $notifications[] = [
                    'type' => 'stock_low',
                    'title' => 'Stok Hampir Habis',
                    'message' => "Stok obat <strong>{$med->name}</strong> tersisa <strong>{$med->stock}</strong> {$med->unit}.",
                    'time' => 'Sisa sedikit',
                    'route' => route('admin.medicines.edit', $med->id),
                    'icon' => 'fa-triangle-exclamation',
                    'icon_bg' => 'bg-amber-50 text-amber-500',
                    'text_color' => 'text-amber-700',
                    'badge' => 'Restok',
                    'badge_class' => 'bg-amber-50 text-amber-600',
                ];
            }
        }

        // 2. Fetch pending orders
        $pendingOrders = Order::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        foreach ($pendingOrders as $order) {
            $customerName = $order->user->name ?? 'Pelanggan';
            $notifications[] = [
                'type' => 'new_order',
                'title' => 'Orderan Masuk',
                'message' => "Pesanan baru <strong>{$order->order_number}</strong> dari <strong>{$customerName}</strong> menunggu konfirmasi.",
                'time' => $order->created_at->diffForHumans(),
                'route' => route('admin.laporan'),
                'icon' => 'fa-bag-shopping',
                'icon_bg' => 'bg-blue-50 text-blue-500',
                'text_color' => 'text-blue-700',
                'badge' => 'Pending',
                'badge_class' => 'bg-blue-50 text-blue-600',
            ];
        }

        return collect($notifications);
    }
}
