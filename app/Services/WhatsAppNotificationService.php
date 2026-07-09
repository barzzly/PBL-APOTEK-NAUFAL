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

        // Map order process type to illustration image filename
        $imageMap = [
            'pending' => 'order_pending.png',
            'confirmed' => 'order_confirmed.png',
            'ready_for_pickup' => 'order_ready.png',
            'shipped' => 'order_shipped.png',
            'delivered' => 'order_delivered.png',
            'cancelled' => 'order_cancelled.png',
        ];
        $imageFile = $imageMap[$type] ?? null;

        $imageUrl = null;
        if ($imageFile) {
            $imagePath = public_path("images/notifications/" . $imageFile);
            if (file_exists($imagePath)) {
                $imageData = base64_encode(file_get_contents($imagePath));
                $imageUrl = 'data:image/png;base64,' . $imageData;
            } else {
                $imageUrl = asset("images/notifications/" . $imageFile);
            }
        }

        $message = "";

        switch ($type) {
            case 'pending':
                $message = "🏥 *[APOTEK NAUFAL - PESANAN DIBUAT]*\n" .
                    "══════════════════════════\n" .
                    "Halo *{$customerName}*, terima kasih telah berbelanja di Apotek Naufal! 🙏\n\n" .
                    "Pesanan Anda dengan nomor *#{$orderNumber}* telah berhasil dibuat dan saat ini sedang menunggu verifikasi oleh apoteker kami.\n\n" .
                    "📋 *Rincian Pesanan:*\n" .
                    $itemsText . "\n" .
                    "💳 *Total Pembayaran:* Rp {$totalAmount}\n" .
                    "🏦 *Metode Pembayaran:* {$paymentMethod}\n" .
                    "🚚 *Tipe Penerimaan:* " . ($order->order_type === 'delivery' ? 'Kirim ke Alamat (Delivery)' : 'Ambil Sendiri (Pickup)') . "\n\n" .
                    "──────────────────────────\n" .
                    "🔄 *Status Saat Ini:* _Menunggu Verifikasi_\n" .
                    "Kami akan segera mengirimkan notifikasi terbaru setelah pesanan Anda dikonfirmasi. Semoga sehat selalu! 💚";
                break;

            case 'confirmed':
                $message = "🏥 *[APOTEK NAUFAL - PESANAN DIKONFIRMASI]*\n" .
                    "══════════════════════════\n" .
                    "Halo *{$customerName}*, kabar baik! 🎉\n\n" .
                    "Pesanan Anda dengan nomor *#{$orderNumber}* telah *DIKONFIRMASI* oleh apoteker kami.\n\n" .
                    "💊 *Status Saat Ini:* _Sedang Disiapkan_\n" .
                    "Staf apotek kami sedang menyiapkan dan mengemas obat-obatan Anda dengan protokol kesehatan yang ketat. Silakan tunggu notifikasi selanjutnya saat pesanan siap dikirim atau diambil.\n\n" .
                    "Terima kasih atas kesabaran Anda! 💚";
                break;

            case 'ready_for_pickup':
                $message = "🏥 *[APOTEK NAUFAL - PESANAN SIAP DIAMBIL]*\n" .
                    "══════════════════════════\n" .
                    "Halo *{$customerName}*, pesanan Anda telah selesai disiapkan! 🛍️\n\n" .
                    "Pesanan dengan nomor *#{$orderNumber}* kini *SIAP DIAMBIL* di Apotek Naufal.\n\n" .
                    "📍 *Lokasi Pengambilan:*\n" .
                    "Apotek Naufal Jakarta\n" .
                    "Jl. Kesehatan No. 123, Jakarta Pusat\n" .
                    "📞 Telp: (021) 1500-123\n\n" .
                    "Tunjukkan nomor pesanan di atas kepada staf kami di loket pengambilan. Kami tunggu kedatangan Anda! 💚";
                break;

            case 'shipped':
                $message = "🏥 *[APOTEK NAUFAL - PESANAN DIKIRIM]*\n" .
                    "══════════════════════════\n" .
                    "Halo *{$customerName}*, pesanan Anda sedang dalam perjalanan! 🚚\n\n" .
                    "Pesanan dengan nomor *#{$orderNumber}* telah diserahkan kepada kurir dan sedang *DIKIRIM* ke alamat Anda.\n\n" .
                    "📍 *Alamat Pengiriman:*\n" .
                    "_{$order->shipping_address}_\n\n" .
                    "Kurir kami akan segera menghubungi Anda saat tiba di lokasi. Harap pastikan nomor telepon Anda aktif. Terima kasih! 💚";
                break;

            case 'delivered':
                $message = "🏥 *[APOTEK NAUFAL - PESANAN SELESAI]*\n" .
                    "══════════════════════════\n" .
                    "Halo *{$customerName}*, pesanan Anda telah selesai! ✅\n\n" .
                    "Pesanan dengan nomor *#{$orderNumber}* telah berhasil *DITERIMA / DISERAHKAN*.\n\n" .
                    "Terima kasih telah mempercayai Apotek Naufal untuk kebutuhan kesehatan Anda. Semoga lekas sembuh dan sehat selalu! 💚\n\n" .
                    "_Punya masukan? Berikan ulasan obat Anda di website kami ya!_ ⭐⭐⭐⭐⭐";
                break;

            case 'cancelled':
                $message = "🏥 *[APOTEK NAUFAL - PESANAN DIBATALKAN]*\n" .
                    "══════════════════════════\n" .
                    "Halo *{$customerName}*, kami ingin menginformasikan bahwa pesanan Anda telah dibatalkan. ❌\n\n" .
                    "Nomor Pesanan: *#{$orderNumber}*\n\n" .
                    ($order->pharmacist_note ? "⚠️ *Catatan Pembatalan:*\n_{$order->pharmacist_note}_\n\n" : "") .
                    "Jika pembatalan ini merupakan kesalahan atau Anda membutuhkan bantuan lebih lanjut, silakan hubungi Customer Service kami. Terima kasih.";
                break;
        }

        if (empty($message)) {
            return false;
        }

        try {
            $response = Http::timeout(5)->post($apiUrl, [
                'to' => $phone,
                'message' => $message,
                'imageUrl' => $imageUrl
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
