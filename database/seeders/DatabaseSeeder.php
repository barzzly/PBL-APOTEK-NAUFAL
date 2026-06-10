<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin
        User::updateOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'Administrator Apotek',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'phone' => '081234567890',
            ]
        );

        // Buat beberapa User Customer
        $customers = [
            [
                'email' => 'user@gmail.com',
                'name' => 'Customer Biasa',
                'phone' => '089876543210'
            ],
            [
                'email' => 'fauzi@gmail.com',
                'name' => 'Ahmad Fauzi',
                'phone' => '081211112222'
            ],
            [
                'email' => 'siti@gmail.com',
                'name' => 'Siti Aminah',
                'phone' => '081233334444'
            ],
            [
                'email' => 'naufal@gmail.com',
                'name' => 'Naufal Hadi',
                'phone' => '081255556666'
            ],
            [
                'email' => 'budi@gmail.com',
                'name' => 'Budi Santoso',
                'phone' => '081277778888'
            ]
        ];

        $customerModels = [];
        foreach ($customers as $cust) {
            $customerModels[] = User::updateOrCreate(
                ['email' => $cust['email']],
                [
                    'name' => $cust['name'],
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'phone' => $cust['phone'],
                ]
            );
        }

        // 2. Buat Kategori
        $categories = [
            ['name' => 'Vitamin', 'icon' => 'fa-pills'],
            ['name' => 'Obat Resep', 'icon' => 'fa-prescription-bottle-medical'],
            ['name' => 'Ibu & Bayi', 'icon' => 'fa-baby'],
            ['name' => 'P3K', 'icon' => 'fa-band-aid'],
            ['name' => 'Herbal', 'icon' => 'fa-leaf'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['slug' => Str::slug($cat['name']), 'is_active' => true]
            );
        }

        // 3. Buat Obat (10+ Obat)
        $catVitamin = Category::where('name', 'Vitamin')->first();
        $catResep = Category::where('name', 'Obat Resep')->first();
        $catHerbal = Category::where('name', 'Herbal')->first();
        $catP3k = Category::where('name', 'P3K')->first();
        $catBayi = Category::where('name', 'Ibu & Bayi')->first();

        $medicinesData = [
            // Vitamin
            [
                'name' => 'Enervon-C Multivitamin 30 Tablet',
                'category_id' => $catVitamin->id,
                'price' => 38250,
                'price_before_discount' => 45000,
                'stock' => 50,
                'unit' => 'botol'
            ],
            [
                'name' => 'Imboost Force 10 Kaplet',
                'category_id' => $catVitamin->id,
                'price' => 75000,
                'stock' => 100,
                'unit' => 'strip'
            ],
            [
                'name' => 'Sangobion Kapsul 10s',
                'category_id' => $catVitamin->id,
                'price' => 22000,
                'stock' => 75,
                'unit' => 'strip'
            ],
            // Obat Resep
            [
                'name' => 'Panadol Paracetamol 500mg',
                'category_id' => $catResep->id,
                'price' => 12500,
                'stock' => 200,
                'unit' => 'strip'
            ],
            [
                'name' => 'Amoxicillin 500mg',
                'category_id' => $catResep->id,
                'price' => 8000,
                'stock' => 120,
                'unit' => 'strip',
                'requires_prescription' => true
            ],
            [
                'name' => 'Cataflam 50mg Tablet',
                'category_id' => $catResep->id,
                'price' => 85000,
                'stock' => 15,
                'unit' => 'strip',
                'requires_prescription' => true
            ],
            // Herbal
            [
                'name' => 'Tolak Angin Cair 12 Pcs',
                'category_id' => $catHerbal->id,
                'price' => 42000,
                'stock' => 80,
                'unit' => 'box'
            ],
            [
                'name' => 'Minyak Kayu Putih Cap Lang 120ml',
                'category_id' => $catHerbal->id,
                'price' => 46000,
                'stock' => 60,
                'unit' => 'botol'
            ],
            // P3K
            [
                'name' => 'Betadine Solution 15ml',
                'category_id' => $catP3k->id,
                'price' => 18500,
                'stock' => 90,
                'unit' => 'botol'
            ],
            [
                'name' => 'Hansaplast Plester Kain Roll',
                'category_id' => $catP3k->id,
                'price' => 9500,
                'stock' => 150,
                'unit' => 'pcs'
            ],
            // Ibu & Bayi
            [
                'name' => 'Diapers Pampers M34',
                'category_id' => $catBayi->id,
                'price' => 89000,
                'stock' => 25,
                'unit' => 'pack'
            ],
            [
                'name' => 'Minyak Telon My Baby 150ml',
                'category_id' => $catBayi->id,
                'price' => 35000,
                'stock' => 70,
                'unit' => 'botol'
            ]
        ];

        $medicineModels = [];
        foreach ($medicinesData as $med) {
            $medicineModels[] = Medicine::updateOrCreate(
                ['name' => $med['name']],
                [
                    'category_id' => $med['category_id'],
                    'slug' => Str::slug($med['name']),
                    'price' => $med['price'],
                    'price_before_discount' => $med['price_before_discount'] ?? null,
                    'stock' => $med['stock'],
                    'unit' => $med['unit'],
                    'requires_prescription' => $med['requires_prescription'] ?? false,
                    'is_active' => true,
                    'image' => null
                ]
            );
        }

        // 4. Bersihkan orders lama & buat orders baru (12 data transaksi)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OrderItem::truncate();
        Order::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $orderTemplates = [
            // Status, Payment Status, Payment Method, Order Type, Days Ago, Customer Index, Items [Medicine Index, Quantity]
            ['delivered', 'paid', 'qris', 'delivery', 0, 1, [[0, 1], [3, 2]]], // hari ini, c1
            ['processing', 'paid', 'transfer', 'delivery', 0, 2, [[1, 1], [7, 1]]], // hari ini, c2
            ['ready_for_pickup', 'paid', 'cash', 'pickup', 1, 3, [[2, 2]]], // kemarin, c3
            ['delivered', 'paid', 'transfer', 'delivery', 1, 4, [[10, 1], [11, 1]]], // kemarin, c4
            ['cancelled', 'unpaid', 'transfer', 'delivery', 2, 0, [[4, 3]]], // 2 hari lalu, c0
            ['delivered', 'paid', 'bpjs', 'pickup', 3, 1, [[3, 1], [4, 1]]], // 3 hari lalu, c1
            ['delivered', 'paid', 'cash', 'pickup', 4, 2, [[6, 2]]], // 4 hari lalu, c2
            ['delivered', 'paid', 'qris', 'delivery', 5, 3, [[7, 1], [8, 2]]], // 5 hari lalu, c3
            ['pending', 'unpaid', 'transfer', 'delivery', 1, 4, [[9, 3]]], // kemarin, c4
            ['delivered', 'paid', 'qris', 'delivery', 6, 0, [[0, 2], [1, 1]]], // 6 hari lalu, c0
            ['delivered', 'paid', 'transfer', 'pickup', 7, 1, [[5, 1]]], // 7 hari lalu, c1
            ['delivered', 'paid', 'cash', 'pickup', 8, 2, [[2, 1], [9, 2]]], // 8 hari lalu, c2
        ];

        foreach ($orderTemplates as $index => $tpl) {
            $status = $tpl[0];
            $payStatus = $tpl[1];
            $payMethod = $tpl[2];
            $orderType = $tpl[3];
            $daysAgo = $tpl[4];
            $custIdx = $tpl[5];
            $itemsData = $tpl[6];

            $user = $customerModels[$custIdx];
            $createdAt = Carbon::now()->subDays($daysAgo)->subHours($index)->subMinutes($index * 2);

            // Generate order number
            $dateString = $createdAt->format('Ymd');
            $seqNum = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $orderNumber = "ORD-{$dateString}-{$seqNum}";

            // Calculate amounts
            $subtotal = 0;
            $orderItems = [];

            foreach ($itemsData as $itemTpl) {
                $medIdx = $itemTpl[0];
                $qty = $itemTpl[1];
                $med = $medicineModels[$medIdx];
                $itemSub = $med->price * $qty;
                $subtotal += $itemSub;

                $orderItems[] = [
                    'medicine_id' => $med->id,
                    'medicine_name' => $med->name,
                    'medicine_unit' => $med->unit,
                    'quantity' => $qty,
                    'price' => $med->price,
                    'subtotal' => $itemSub,
                ];
            }

            $shippingCost = $orderType === 'delivery' ? 10000 : 0;
            $discount = 0;
            $totalAmount = $subtotal + $shippingCost - $discount;

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'status' => $status,
                'order_type' => $orderType,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $payMethod,
                'payment_status' => $payStatus,
                'paid_at' => $payStatus === 'paid' ? $createdAt : null,
                'shipping_address' => $orderType === 'delivery' ? 'Jl. Kenanga No. ' . (10 + $index) . ', Pekanbaru' : null,
                'notes' => 'Catatan order ke-' . ($index + 1),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }
        }
    }
}
