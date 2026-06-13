<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Medicine;
use App\Models\Rating;
use Illuminate\Database\Seeder;

class RatingsAndOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all medicines and customer users
        $medicines = Medicine::all();
        $customers = User::where('role', 'customer')->get();

        if ($customers->isEmpty()) {
            return;
        }

        $reviewTemplates = [
            5 => [
                'Sangat bermanfaat dan asli obatnya!',
                'Kemasan rapi, pengiriman cepat, recommended seller.',
                'Sangat ampuh, langsung terasa khasiatnya.',
                'Harga termurah dibanding apotek lain, produk original.',
                'Pelayanan ramah dan fast response.'
            ],
            4 => [
                'Obat bekerja dengan baik, sesuai deskripsi.',
                'Pengiriman agak lama tapi kualitas obat bagus dan original.',
                'Cukup meredakan gejala sakit. Terima kasih.',
                'Kemasan aman dan tersegel dengan baik.',
                'Expired date masih sangat lama, bagus.'
            ],
            3 => [
                'Khasiat standar saja, tapi pelayanan apotek oke.',
                'Obat lumayan ampuh untuk sakit ringan.',
                'Harga standar apotek pada umumnya.'
            ]
        ];

        foreach ($medicines as $medicine) {
            // Delete existing ratings to prevent duplication if run multiple times
            Rating::where('medicine_id', $medicine->id)->delete();

            // Generate 1 to 4 reviews per medicine
            $numReviews = rand(1, 4);
            $shuffledCustomers = $customers->shuffle();

            for ($i = 0; $i < $numReviews; $i++) {
                if ($i >= $shuffledCustomers->count()) {
                    break;
                }

                $user = $shuffledCustomers[$i];
                
                // Determine a realistic rating (skewed towards 4 and 5 stars)
                $rand = rand(1, 10);
                if ($rand <= 6) {
                    $score = 5;
                } elseif ($rand <= 9) {
                    $score = 4;
                } else {
                    $score = 3;
                }

                $reviewsForScore = $reviewTemplates[$score];
                $comment = $reviewsForScore[array_rand($reviewsForScore)];

                Rating::create([
                    'medicine_id' => $medicine->id,
                    'user_id' => $user->id,
                    'rating' => $score,
                    'review' => $comment,
                    'created_at' => now()->subDays(rand(1, 10))->subHours(rand(1, 23)),
                ]);
            }
        }
    }
}
