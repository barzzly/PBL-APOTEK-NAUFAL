<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderDummySeeder extends Seeder
{
    /**
     * Run the database seeds to generate 100 realistic orders in the last 365 days.
     */
    public function run(): void
    {
        // 1. Clean up old orders and items first to start fresh with beautiful stats
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OrderItem::truncate();
        Order::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Fetch customers and medicines
        $customers = User::where('role', 'customer')->get();
        if ($customers->isEmpty()) {
            // Create a backup customer if none exist
            $customers = collect([
                User::create([
                    'name' => 'Budi Santoso',
                    'email' => 'budi@gmail.com',
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                    'phone' => '081234567890',
                ])
            ]);
        }

        $medicines = Medicine::all();
        if ($medicines->isEmpty()) {
            $this->command->error('No medicines found. Please run DatabaseSeeder first to seed categories and medicines.');
            return;
        }

        $statuses = ['delivered', 'cancelled', 'pending', 'confirmed', 'processing', 'ready_for_pickup', 'shipped'];
        $paymentMethods = ['cash', 'transfer', 'bpjs', 'qris'];

        // 3. Generate 100 orders distributed over the last 365 days
        $totalOrdersCount = 100;
        
        for ($i = 1; $i <= $totalOrdersCount; $i++) {
            // Random customer
            $customer = $customers->random();
            
            // Random date in the last 30 days (so all 100 orders show up in the default 30-day filter)
            $randomDaysAgo = rand(0, 30);
            $createdAt = Carbon::now()->subDays($randomDaysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            
            // Skew status: 80% delivered, 10% cancelled, 10% other statuses
            $randStatus = rand(1, 100);
            if ($randStatus <= 80) {
                $status = 'delivered';
            } elseif ($randStatus <= 90) {
                $status = 'cancelled';
            } else {
                $status = $statuses[array_rand(array_slice($statuses, 2))]; // pending, confirmed, processing, ready_for_pickup, shipped
            }

            // Payment method & status logic
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            
            if ($status === 'delivered') {
                $paymentStatus = 'paid';
            } elseif ($status === 'cancelled') {
                $paymentStatus = (rand(1, 2) === 1) ? 'unpaid' : 'refunded';
            } else {
                // If it's pending/confirmed/processing
                $paymentStatus = ($paymentMethod === 'cash') ? 'unpaid' : ((rand(1, 4) <= 3) ? 'paid' : 'unpaid');
            }

            $orderType = (rand(1, 2) === 1) ? 'delivery' : 'pickup';
            $shippingCost = ($orderType === 'delivery') ? 10000 : 0;
            $discount = 0;

            // Generate order number: ORD-YYYYMMDD-XXXX
            $dateString = $createdAt->format('Ymd');
            $seqNum = str_pad($i, 4, '0', STR_PAD_LEFT);
            $orderNumber = "ORD-{$dateString}-{$seqNum}";

            // Select 1 to 3 random medicines
            $orderMedicines = $medicines->random(rand(1, 3));
            $subtotal = 0;
            $orderItemsData = [];

            foreach ($orderMedicines as $medicine) {
                $qty = rand(1, 4);
                $itemSubtotal = $medicine->price * $qty;
                $subtotal += $itemSubtotal;

                $orderItemsData[] = [
                    'medicine_id' => $medicine->id,
                    'medicine_name' => $medicine->name,
                    'medicine_unit' => $medicine->unit ?? 'tablet',
                    'quantity' => $qty,
                    'price' => $medicine->price,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $totalAmount = $subtotal + $shippingCost - $discount;

            // Save order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $customer->id,
                'status' => $status,
                'order_type' => $orderType,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'paid_at' => ($paymentStatus === 'paid') ? $createdAt : null,
                'shipping_address' => ($orderType === 'delivery') ? 'Jl. Sudirman No. ' . rand(1, 250) . ', Pekanbaru' : null,
                'notes' => 'Catatan order dummy ke-' . $i,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Save order items
            foreach ($orderItemsData as $item) {
                $order->items()->create($item);
            }
        }

        $this->command->info("Successfully seeded {$totalOrdersCount} dummy orders over the last 1 year.");
    }
}
