<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Clean and format phone number to international code (e.g. 62...)
     */
    protected static function cleanPhone($phone)
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }
        return $cleanPhone;
    }

    /**
     * Send order status notification via WhatsApp
     */
    public static function sendOrderNotification(Order $order, string $type)
    {
        $order->loadMissing(['user', 'items']);
        $user = $order->user;
        if (!$user || !$user->phone) {
            return false;
        }

        $phone = self::cleanPhone($user->phone);
        $apiUrl = config('services.whatsapp.api_url') . '/api/send-message';

        $orderNumber = $order->order_number;
        $customerName = $user->name;
        $totalAmount = number_format($order->total_amount, 0, ',', '.');
        $paymentMethod = $order->payment_method_label;

        // Build item list
        $itemsText = "";
        foreach ($order->items as $index => $item) {
            $num = $index + 1;
            $itemsText .= "{$num}. {$item->medicine_name} ({$item->quantity} {$item->medicine_unit})\n";
        }

        $message = "";

        switch ($type) {
            case 'pending':
                $message = "*[Apotek Naufal - Pesanan Dibuat]*\n\n" .
                    "Halo {$customerName},\n" .
                    "Pesanan Anda dengan nomor *{$orderNumber}* telah berhasil dibuat.\n\n" .
                    "*Detail Pesanan:*\n" .
                    $itemsText . "\n" .
                    "Total Pembayaran: *Rp {$totalAmount}*\n" .
                    "Metode Pembayaran: *{$paymentMethod}*\n" .
                    "Tipe Pesanan: *" . ($order->order_type === 'delivery' ? 'Diantar (Delivery)' : 'Diambil (Pickup)') . "*\n\n" .
                    "Pesanan Anda saat ini sedang menunggu verifikasi oleh admin/apoteker. Kami akan mengabari Anda setelah pesanan dikonfirmasi.";
                break;

            case 'confirmed':
                $message = "*[Apotek Naufal - Pesanan Dikonfirmasi]*\n\n" .
                    "Halo {$customerName},\n" .
                    "Pesanan Anda dengan nomor *{$orderNumber}* telah *DIKONFIRMASI*.\n\n" .
                    "Obat-obatan Anda sedang disiapkan oleh staf apotek kami. Silakan tunggu informasi selanjutnya.";
                break;

            case 'ready_for_pickup':
                $message = "*[Apotek Naufal - Siap Diambil]*\n\n" .
                    "Halo {$customerName},\n" .
                    "Pesanan Anda dengan nomor *{$orderNumber}* telah *SIAP DIAMBIL*.\n\n" .
                    "Silakan datang ke Apotek Naufal untuk mengambil pesanan Anda. Sebutkan nomor pesanan di atas kepada staf kami.";
                break;

            case 'shipped':
                $message = "*[Apotek Naufal - Pesanan Dikirim]*\n\n" .
                    "Halo {$customerName},\n" .
                    "Pesanan Anda dengan nomor *{$orderNumber}* telah *DIKIRIM / DIANTAR*.\n\n" .
                    "Kurir kami sedang dalam perjalanan mengantarkan pesanan ke alamat Anda:\n" .
                    "_{$order->shipping_address}_";
                break;

            case 'delivered':
                $message = "*[Apotek Naufal - Pesanan Selesai]*\n\n" .
                    "Halo {$customerName},\n" .
                    "Pesanan Anda dengan nomor *{$orderNumber}* telah *SELESAI / DITERIMA*.\n\n" .
                    "Terima kasih telah mempercayai Apotek Naufal untuk kebutuhan kesehatan Anda. Semoga lekas sembuh!";
                break;

            case 'cancelled':
                $message = "*[Apotek Naufal - Pesanan Dibatalkan]*\n\n" .
                    "Halo {$customerName},\n" .
                    "Mohon maaf, pesanan Anda dengan nomor *{$orderNumber}* telah *DIBATALKAN*.\n\n" .
                    ($order->pharmacist_note ? "Catatan apoteker: _{$order->pharmacist_note}_\n\n" : "") .
                    "Jika Anda memiliki pertanyaan, silakan hubungi apotek kami.";
                break;
        }

        if (empty($message)) {
            return false;
        }

        try {
            $response = Http::timeout(5)->post($apiUrl, [
                'to' => $phone,
                'message' => $message
            ]);

            if ($response->failed()) {
                Log::error("Failed to send WA notification for order {$orderNumber}: " . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending WA notification for order {$orderNumber}: " . $e->getMessage());
            return false;
        }
    }
}
