<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CartItem;
use App\Models\User;

// Find the last logged in user or any user who has items in cart_items
$items = CartItem::with('medicine')->get();
echo "TOTAL CART ITEMS IN DB: " . $items->count() . "\n\n";

foreach ($items as $item) {
    echo "User ID: " . $item->user_id . " (Name: " . ($item->user ? $item->user->name : 'N/A') . ")\n";
    echo "Medicine: " . ($item->medicine ? $item->medicine->name : 'N/A') . " (ID: " . $item->medicine_id . ")\n";
    echo "Cart Quantity: " . $item->quantity . "\n";
    echo "Medicine Stock in DB: " . ($item->medicine ? $item->medicine->stock : 'N/A') . "\n";
    echo "Requires Prescription: " . ($item->medicine && $item->medicine->requires_prescription ? 'YES' : 'NO') . "\n";
    echo "----------------------------------------\n";
}
