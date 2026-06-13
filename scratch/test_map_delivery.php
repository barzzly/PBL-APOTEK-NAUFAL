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
use App\Models\Order;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

echo "--- STARTING DYNAMIC MAP DELIVERY & SHIPPING CALCULATIONS TEST ---\n\n";

// Clear previous states
CartItem::truncate();

// Find test user
$user = User::firstOrCreate(
    ['email' => 'customer_test@example.com'],
    [
        'name' => 'Test Customer',
        'password' => bcrypt('password123'),
        'role' => 'customer',
        'phone' => '12345678'
    ]
);
Auth::login($user);

// Create category and medicine
$category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Category']);
$medicine = Medicine::firstOrCreate(
    ['slug' => 'test-med-cart'],
    [
        'category_id' => $category->id,
        'name' => 'Test Medicine Cart',
        'price' => 5000,
        'stock' => 10,
        'is_active' => true
    ]
);
$medicine->update(['stock' => 10]);

// Add item to cart_items table in database
CartItem::create([
    'user_id' => $user->id,
    'medicine_id' => $medicine->id,
    'quantity' => 1
]);

echo "1. Created test cart item: 1 x " . $medicine->name . " (Price: " . $medicine->price . ").\n";

// Coordinates for target delivery point: near UNAND (Universitas Andalas), Padang
// Coordinate target: -0.923423, 100.418723
// Pharmacy coordinates: -0.937722, 100.3878982 (Jl. Andalas Raya No. 125)
$targetLat = -0.923423;
$targetLng = 100.418723;

echo "2. Set Pharmacy Coords: -0.937722, 100.3878982\n";
echo "   Set Target Coords (UNAND, Padang): {$targetLat}, {$targetLng}\n";

// Manually verify routing/distance calculation (OSRM with Haversine fallback)
$pharmacyLat = -0.937722;
$pharmacyLng = 100.3878982;

$expectedDistance = null;
$osrmUrl = "https://router.project-osrm.org/route/v1/driving/{$pharmacyLng},{$pharmacyLat};{$targetLng},{$targetLat}?overview=false";
$options = [
    'http' => [
        'header' => "User-Agent: ApotekNaufalApp/1.0\r\n",
        'timeout' => 3
    ]
];
$context = stream_context_create($options);
$osrmResponse = @file_get_contents($osrmUrl, false, $context);
if ($osrmResponse) {
    $osrmData = json_decode($osrmResponse, true);
    if (isset($osrmData['routes'][0]['distance'])) {
        $expectedDistance = $osrmData['routes'][0]['distance'] / 1000;
    }
}

if ($expectedDistance === null) {
    $earthRadius = 6371; // km
    $dLat = deg2rad($targetLat - $pharmacyLat);
    $dLon = deg2rad($targetLng - $pharmacyLng);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($pharmacyLat)) * cos(deg2rad($targetLat)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $expectedDistance = $earthRadius * $c;
}

$expectedFee = max(ceil($expectedDistance) * 2500, 10000);
echo "   - Expected Distance (via OSRM/Fallback): " . number_format($expectedDistance, 2) . " km\n";
echo "   - Expected Shipping Fee: Rp " . number_format($expectedFee, 0, ',', '.') . "\n";

// Execute store request
$checkoutController = new CheckoutController();
$request = Request::create('/checkout', 'POST', [
    'order_type' => 'delivery',
    'payment_method' => 'cash',
    'shipping_address' => 'UNAND, Padang',
    'location_details' => 'Pagar hitam cat hijau',
    'delivery_latitude' => $targetLat,
    'delivery_longitude' => $targetLng,
    'delivery_distance' => $expectedDistance,
]);

// Call store and catch order number
$response = $checkoutController->store($request);

// Dump errors if it redirected back
if ($response->isRedirection()) {
    echo "   [REDIRECT DETECTED]\n";
    $session = $app['session.store'];
    if ($session->has('error')) {
        echo "   - Error Message: " . $session->get('error') . "\n";
    }
    if ($session->has('errors')) {
        $errors = $session->get('errors')->getBag('default')->all();
        echo "   - Validation Errors:\n";
        foreach ($errors as $err) {
            echo "     * " . $err . "\n";
        }
    }
}

// Check database for newly created order
$order = Order::where('user_id', $user->id)->latest()->first();

if ($order) {
    echo "3. Order created successfully! (Order Number: {$order->order_number})\n";
    echo "   - Saved Shipping Cost in DB: Rp " . number_format($order->shipping_cost, 0, ',', '.') . "\n";
    echo "   - Saved Total Amount in DB: Rp " . number_format($order->total_amount, 0, ',', '.') . "\n";
    echo "   - Saved Latitude in DB: " . $order->delivery_latitude . "\n";
    echo "   - Saved Longitude in DB: " . $order->delivery_longitude . "\n";
    echo "   - Saved Distance in DB: " . number_format($order->delivery_distance, 2) . " km\n";

    if (abs($order->shipping_cost - $expectedFee) < 1) {
        echo "   [SUCCESS] Dynamic shipping cost matched expected fee perfectly!\n";
    } else {
        echo "   [FAILED] Dynamic shipping cost does not match. Expected: {$expectedFee}, Got: {$order->shipping_cost}\n";
        exit(1);
    }
} else {
    echo "   [FAILED] Order was not created.\n";
    exit(1);
}

echo "\n--- ALL TESTS PASSED SUCCESSFULLY! ---\n";
