<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        // Buat User Customer Biasa
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Customer',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '089876543210',
            ]
        );

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

        // 3. Buat Obat
        $catVitamin = Category::where('name', 'Vitamin')->first();
        $catResep = Category::where('name', 'Obat Resep')->first();
        $catIbuBayi = Category::where('name', 'Ibu & Bayi')->first();
        $catP3K = Category::where('name', 'P3K')->first();
        $catHerbal = Category::where('name', 'Herbal')->first();
        
        if ($catVitamin) {
            Medicine::firstOrCreate(
                ['name' => 'Enervon-C Multivitamin 30 Tablet'],
                [
                    'category_id' => $catVitamin->id,
                    'slug' => Str::slug('Enervon-C Multivitamin 30 Tablet'),
                    'price' => 38250,
                    'price_before_discount' => 45000,
                    'stock' => 50,
                    'is_active' => true,
                    'image' => '/images/product_1.png'
                ]
            );
            
            Medicine::firstOrCreate(
                ['name' => 'Imboost Force 10 Kaplet'],
                [
                    'category_id' => $catVitamin->id,
                    'slug' => Str::slug('Imboost Force 10 Kaplet'),
                    'price' => 75000,
                    'stock' => 100,
                    'is_active' => true,
                    'image' => '/images/product_1.png'
                ]
            );
        }

        if ($catResep) {
            Medicine::firstOrCreate(
                ['name' => 'Panadol Paracetamol 500mg'],
                [
                    'category_id' => $catResep->id,
                    'slug' => Str::slug('Panadol Paracetamol 500mg'),
                    'price' => 12500,
                    'stock' => 200,
                    'is_active' => true,
                    'image' => '/images/product_2.png'
                ]
            );
        }

        if ($catIbuBayi) {
            Medicine::firstOrCreate(
                ['name' => 'Zwitsal Baby Bath Hair & Body 200ml'],
                [
                    'category_id' => $catIbuBayi->id,
                    'slug' => Str::slug('Zwitsal Baby Bath Hair & Body 200ml'),
                    'price' => 22000,
                    'stock' => 30,
                    'is_active' => true,
                    'image' => '/images/product_1.png'
                ]
            );
            
            Medicine::firstOrCreate(
                ['name' => 'SGM Eksplor 1+ Madu 900g'],
                [
                    'category_id' => $catIbuBayi->id,
                    'slug' => Str::slug('SGM Eksplor 1+ Madu 900g'),
                    'price' => 85000,
                    'stock' => 15,
                    'is_active' => true,
                    'image' => '/images/product_2.png'
                ]
            );
        }

        if ($catP3K) {
            Medicine::firstOrCreate(
                ['name' => 'Betadine Antiseptic Solution 15ml'],
                [
                    'category_id' => $catP3K->id,
                    'slug' => Str::slug('Betadine Antiseptic Solution 15ml'),
                    'price' => 18500,
                    'stock' => 40,
                    'is_active' => true,
                    'image' => '/images/product_1.png'
                ]
            );
            
            Medicine::firstOrCreate(
                ['name' => 'Hansaplast Plester Kain Elastis 10 Lembar'],
                [
                    'category_id' => $catP3K->id,
                    'slug' => Str::slug('Hansaplast Plester Kain Elastis 10 Lembar'),
                    'price' => 7500,
                    'stock' => 150,
                    'is_active' => true,
                    'image' => '/images/product_2.png'
                ]
            );
        }

        if ($catHerbal) {
            Medicine::firstOrCreate(
                ['name' => 'Tolak Angin Cair 12 Sachet'],
                [
                    'category_id' => $catHerbal->id,
                    'slug' => Str::slug('Tolak Angin Cair 12 Sachet'),
                    'price' => 42000,
                    'stock' => 80,
                    'is_active' => true,
                    'image' => '/images/product_1.png'
                ]
            );
            
            Medicine::firstOrCreate(
                ['name' => 'Minyak Kayu Putih Cap Lang 120ml'],
                [
                    'category_id' => $catHerbal->id,
                    'slug' => Str::slug('Minyak Kayu Putih Cap Lang 120ml'),
                    'price' => 46000,
                    'stock' => 60,
                    'is_active' => true,
                    'image' => '/images/product_2.png'
                ]
            );
        }
    }
}
