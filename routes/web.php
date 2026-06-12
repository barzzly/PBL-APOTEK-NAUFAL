<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
    Route::post('/medicines/generate-description', [AdminController::class, 'generateDescription'])->name('admin.medicines.generate_description');

    // Laporan Penjualan
    Route::get('/laporan-penjualan', [AdminController::class, 'laporanPenjualan'])->name('admin.laporan');
    Route::get('/laporan-penjualan/chart-data', [AdminController::class, 'laporanChartData'])->name('admin.laporan.chart');

    // Admin Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/orders/{id}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::post('/orders/{id}/update-status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update_status');

    // Admin Prescription Routes
    Route::get('/prescriptions', [\App\Http\Controllers\AdminPrescriptionController::class, 'index'])->name('admin.prescriptions.index');
    Route::get('/prescriptions/{id}', [\App\Http\Controllers\AdminPrescriptionController::class, 'show'])->name('admin.prescriptions.show');
    Route::post('/prescriptions/{id}/message', [\App\Http\Controllers\AdminPrescriptionController::class, 'sendMessage'])->name('admin.prescriptions.message');
    Route::post('/prescriptions/{id}/status', [\App\Http\Controllers\AdminPrescriptionController::class, 'changeStatus'])->name('admin.prescriptions.status');
    Route::post('/prescriptions/{id}/add-medicine', [\App\Http\Controllers\AdminPrescriptionController::class, 'addMedicine'])->name('admin.prescriptions.add_medicine');
    Route::delete('/prescriptions/{id}/remove-medicine/{itemId}', [\App\Http\Controllers\AdminPrescriptionController::class, 'removeMedicine'])->name('admin.prescriptions.remove_medicine');
    Route::get('/prescriptions/{id}/messages', [\App\Http\Controllers\AdminPrescriptionController::class, 'getMessages'])->name('admin.prescriptions.messages');
});

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Checkout and customer orders (protected by auth)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/calculate-distance', [CheckoutController::class, 'calculateDistance'])->name('checkout.calculate_distance');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders/success', [CheckoutController::class, 'success'])->name('orders.success');
    Route::get('/orders', [CheckoutController::class, 'history'])->name('orders.history');
    Route::get('/orders/{id}', [CheckoutController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/upload-payment', [CheckoutController::class, 'uploadPaymentProof'])->name('orders.upload_payment');
    Route::post('/obat/{slug}/review', [HomeController::class, 'storeReview'])->name('medicine.review.store');
    // Customer Prescription Routes
    Route::get('/prescriptions/upload', [\App\Http\Controllers\PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/prescriptions/upload', [\App\Http\Controllers\PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/history', [\App\Http\Controllers\PrescriptionController::class, 'history'])->name('prescriptions.history');
    Route::get('/prescriptions/ticket/{id}', [\App\Http\Controllers\PrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::post('/prescriptions/ticket/{id}/message', [\App\Http\Controllers\PrescriptionController::class, 'sendMessage'])->name('prescriptions.message');
    Route::get('/prescriptions/ticket/{id}/messages', [\App\Http\Controllers\PrescriptionController::class, 'getMessages'])->name('prescriptions.messages');
    Route::get('/prescriptions/{filename}', [CheckoutController::class, 'viewPrescription'])->name('prescriptions.view');
});

