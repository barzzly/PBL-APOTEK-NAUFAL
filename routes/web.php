<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\CartController;

// Auto-create admin user for testing
if (php_sapi_name() !== 'cli') {
    try {
        User::firstOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '0000'
            ]
        );
    } catch (\Exception $e) {}
}

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kategori/{slug}', [HomeController::class, 'category'])->name('category.show');
Route::get('/obat/{slug}', [HomeController::class, 'show'])->name('product.detail');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Cart Route (Action only)
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');

// Admin routes
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('admin.categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categories.destroy');
    
    // Medicines
    Route::get('/medicines', [AdminController::class, 'medicines'])->name('admin.medicines');
    Route::get('/medicines/create', [AdminController::class, 'createMedicine'])->name('admin.medicines.create');
    Route::post('/medicines', [AdminController::class, 'storeMedicine'])->name('admin.medicines.store');
    Route::get('/medicines/{id}/edit', [AdminController::class, 'editMedicine'])->name('admin.medicines.edit');
    Route::put('/medicines/{id}', [AdminController::class, 'updateMedicine'])->name('admin.medicines.update');
    Route::delete('/medicines/{id}', [AdminController::class, 'deleteMedicine'])->name('admin.medicines.destroy');

    // Laporan Penjualan
    Route::get('/laporan-penjualan', [AdminController::class, 'laporanPenjualan'])->name('admin.laporan');
    Route::get('/laporan-penjualan/chart-data', [AdminController::class, 'laporanChartData'])->name('admin.laporan.chart');

    // Admin Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::post('/orders/{id}/update-status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update_status');
});

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Checkout and customer orders (protected by auth)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders/success', [CheckoutController::class, 'success'])->name('orders.success');
    Route::get('/orders', [CheckoutController::class, 'history'])->name('orders.history');
    Route::get('/orders/{id}', [CheckoutController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/upload-payment', [CheckoutController::class, 'uploadPaymentProof'])->name('orders.upload_payment');
});

