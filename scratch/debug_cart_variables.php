<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\CartController;
use App\Models\User;

$user = User::find(1);
auth()->login($user);

$cartController = new CartController();
$cart = $cartController->getCartItems();

echo "USER: " . auth()->user()->name . "\n";
echo "CART ARRAY:\n";
print_r($cart);

$hasStockIssue = false;
foreach($cart as $item) {
    if($item['stock'] == 0 || $item['quantity'] > $item['stock']) {
        $hasStockIssue = true;
        echo "STOCK ISSUE TRIGGERED FOR: " . $item['name'] . " (Qty: " . $item['quantity'] . ", Stock: " . $item['stock'] . ")\n";
    }
}

echo "HAS STOCK ISSUE? " . ($hasStockIssue ? 'YES' : 'NO') . "\n";
