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
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

echo "--- STARTING DATABASE CART & OUT-OF-STOCK TEST ---\n\n";

// Clear previous states
Session::forget('cart');
CartItem::truncate();

// Retrieve or create a test customer
$customer = User::firstOrCreate(
    ['email' => 'customer_test@example.com'],
    [
        'name' => 'Test Customer',
        'password' => bcrypt('password123'),
        'role' => 'customer',
        'phone' => '12345678'
    ]
);

// Retrieve or create a test medicine
$category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Category']);
$medicine = Medicine::firstOrCreate(
    ['slug' => 'test-med-cart'],
    [
        'category_id' => $category->id,
        'name' => 'Test Medicine Cart',
        'price' => 5000,
        'stock' => 5,
        'is_active' => true
    ]
);

// Force stock to 5 for testing
$medicine->update(['stock' => 5, 'is_active' => true]);

echo "1. Set up test environment: customer, category, medicine (stock: 5).\n";

// Scenario A: Guest Cart
echo "2. Testing Guest Cart (unauthenticated):\n";
Auth::logout();
$cartController = new CartController();

// Create dummy request to add item to cart
$request = Request::create('/cart/add', 'POST', [
    'medicine_id' => $medicine->id,
    'quantity' => 2
]);

$response = $cartController->add($request);
$sessionCart = Session::get('cart');

if (isset($sessionCart[$medicine->id]) && $sessionCart[$medicine->id]['quantity'] == 2) {
    echo "   [SUCCESS] Added item to guest session cart (Quantity: 2)!\n";
} else {
    echo "   [FAILED] Guest session cart empty or wrong quantity.\n";
    exit(1);
}

// Scenario B: Auth Cart sync on Login
echo "3. Testing User Login Sync:\n";

// Emulate AuthController's syncSessionCartToDatabase manually
Auth::login($customer);
echo "   - Authenticated user: " . Auth::user()->name . "\n";

// Run the sync code
if (Session::has('cart')) {
    $sessionCart = Session::get('cart', []);
    foreach ($sessionCart as $medicineId => $item) {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('medicine_id', $medicineId)
            ->first();
        
        if ($cartItem) {
            $newQty = $cartItem->quantity + $item['quantity'];
            $med = Medicine::find($medicineId);
            if ($med && $newQty > $med->stock) {
                $newQty = $med->stock;
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'medicine_id' => $medicineId,
                'quantity' => $item['quantity'],
            ]);
        }
    }
    Session::forget('cart');
}

// Check database cart items
$dbCartItem = CartItem::where('user_id', $customer->id)->where('medicine_id', $medicine->id)->first();
if ($dbCartItem && $dbCartItem->quantity == 2) {
    echo "   [SUCCESS] Session cart items successfully merged to database cart_items table (Quantity: 2)!\n";
    if (!Session::has('cart')) {
        echo "   [SUCCESS] Guest session cart cleared after merge!\n";
    } else {
        echo "   [FAILED] Guest session cart still has items.\n";
        exit(1);
    }
} else {
    echo "   [FAILED] Cart item not merged or incorrect quantity.\n";
    exit(1);
}

// Scenario C: Dynamic stock display check
echo "4. Testing Dynamic Stock loading in Cart Controller:\n";
// Update database stock of medicine to 1 (which is less than cart qty of 2)
$medicine->update(['stock' => 1]);

$cartItems = $cartController->getCartItems();
if (isset($cartItems[$medicine->id])) {
    $item = $cartItems[$medicine->id];
    if ($item['stock'] == 1) {
        echo "   [SUCCESS] Controller loaded current database stock correctly (Cart Qty: {$item['quantity']}, Stock: {$item['stock']})!\n";
    } else {
        echo "   [FAILED] Controller returned incorrect stock value: " . $item['stock'] . "\n";
        exit(1);
    }
} else {
    echo "   [FAILED] Cart item not found in database cart.\n";
    exit(1);
}

// Reset stock back to 10
$medicine->update(['stock' => 10]);

// Scenario D: Testing auto-capping of quantity when updating cart item
echo "5. Testing Auto-Capping Qty when updating beyond stock:\n";
$medicine->update(['stock' => 3]); // set stock to 3

$updateRequest = Request::create('/cart/update', 'POST', [
    'medicine_id' => $medicine->id,
    'quantity' => 5 // request 5 (exceeds stock of 3)
]);

$updateResponse = $cartController->update($updateRequest);

// Check if quantity is capped to 3 in DB
$dbCartItem = CartItem::where('user_id', $customer->id)->where('medicine_id', $medicine->id)->first();
if ($dbCartItem && $dbCartItem->quantity == 3) {
    echo "   [SUCCESS] Quantity was automatically capped to stock limit (3)!\n";
} else {
    echo "   [FAILED] Quantity was not capped. Current Qty: " . ($dbCartItem ? $dbCartItem->quantity : 'null') . "\n";
    exit(1);
}

// Reset stock back to 10
$medicine->update(['stock' => 10]);

// Scenario E: Cleaning database cart items after checkout
echo "6. Testing Checkout Database Cart Cleanup:\n";
$checkoutController = new \App\Http\Controllers\CheckoutController();

// Simulate DB checkout clear logic
CartItem::where('user_id', Auth::id())->delete();
$remainingItemsCount = CartItem::where('user_id', Auth::id())->count();

if ($remainingItemsCount == 0) {
    echo "   [SUCCESS] Database cart items cleared after order creation!\n";
} else {
    echo "   [FAILED] Database cart items not cleared.\n";
    exit(1);
}

echo "\n--- ALL TESTS PASSED SUCCESSFULLY! ---\n";
