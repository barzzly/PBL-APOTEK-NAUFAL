<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\CartItem;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

echo "--- STARTING DISTANCE LIMIT OVER 50KM TEST ---\n\n";

// Find test user
$user = User::where('email', 'customer_test@example.com')->firstOrFail();
Auth::login($user);

// Ensure cart has at least one item
CartItem::truncate();
$medicine = Medicine::first();
CartItem::create([
    'user_id' => $user->id,
    'medicine_id' => $medicine->id,
    'quantity' => 1
]);

// Coordinates for target delivery point: Jakarta (very far from Padang)
$targetLat = -6.2088;
$targetLng = 106.8456;

echo "1. Set Target Coords to Jakarta: {$targetLat}, {$targetLng}\n";

// Execute store request
$checkoutController = new CheckoutController();
$request = Request::create('/checkout', 'POST', [
    'order_type' => 'delivery',
    'payment_method' => 'cash',
    'shipping_address' => 'Jakarta Kota',
    'location_details' => 'Rumah tingkat pagar hitam',
    'delivery_latitude' => $targetLat,
    'delivery_longitude' => $targetLng,
    'delivery_distance' => 999.9, // mock distance
]);

// Call store and verify redirection back with error
$response = $checkoutController->store($request);

if ($response->isRedirection()) {
    echo "2. Redirection detected.\n";
    $session = $app['session.store'];
    if ($session->has('error')) {
        echo "   - Error Message: " . $session->get('error') . "\n";
        if (str_contains($session->get('error'), 'Jarak pengiriman terlalu jauh')) {
            echo "   [SUCCESS] Distance limit over 50km correctly blocked by backend validation!\n";
            exit(0);
        }
    }
}

echo "   [FAILED] Distance validation failed to block the request.\n";
exit(1);
