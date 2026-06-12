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
use App\Models\Prescription;
use App\Models\PrescriptionMessage;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\AdminPrescriptionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

echo "--- STARTING STANDALONE PRESCRIPTION TICKET SYSTEM TEST ---\n\n";

// 1. Setup Users
$customer = User::firstOrCreate(
    ['email' => 'customer_test@example.com'],
    [
        'name' => 'Test Customer',
        'password' => bcrypt('password123'),
        'role' => 'customer',
        'phone' => '081234567890'
    ]
);

$admin = User::firstOrCreate(
    ['email' => 'admin'],
    [
        'name' => 'Administrator',
        'password' => bcrypt('admin123'),
        'role' => 'admin',
        'phone' => '0000'
    ]
);

// Setup Medicine
$category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Category']);
$medicine = Medicine::firstOrCreate(
    ['slug' => 'paracetamol-test'],
    [
        'category_id' => $category->id,
        'name' => 'Paracetamol Test 500mg',
        'price' => 2000,
        'stock' => 100,
        'is_active' => true,
        'unit' => 'Strip'
    ]
);
$medicine->update(['stock' => 100, 'is_active' => true]);

// Clean up existing cart items for customer
CartItem::where('user_id', $customer->id)->delete();

echo "Step 1: Users and test medicine initialized.\n";
echo "        Customer: {$customer->name} ({$customer->email})\n";
echo "        Admin: {$admin->name} ({$admin->email})\n";
echo "        Medicine: {$medicine->name} (Stock: {$medicine->stock})\n\n";

// 2. Customer Uploads a Prescription
Auth::login($customer);

$fakeImage = UploadedFile::fake()->image('resep_dokter_anak.jpg');

$requestStore = Request::create('/prescriptions/upload', 'POST', [
    'doctor_name' => 'dr. John Doe, Sp.A',
    'hospital_clinic' => 'RSUD M. Djamil Padang',
    'prescription_date' => date('Y-m-d'),
    'patient_name' => 'Adit Pratama',
    'patient_age' => 8,
    'customer_notes' => 'Tolong ditebus obat flu dan batuk generik.',
]);
// Inject file upload manually
$requestStore->files->set('image', $fakeImage);

$prescriptionController = new PrescriptionController();
$responseStore = $prescriptionController->store($requestStore);

$prescription = Prescription::where('user_id', $customer->id)->latest()->first();

if (!$prescription) {
    echo "[FAILED] Prescription ticket was not created in database.\n";
    exit(1);
}

echo "Step 2: Prescription uploaded successfully!\n";
echo "        Ticket Number: {$prescription->prescription_number}\n";
echo "        Patient: {$prescription->patient_name}\n";
echo "        Status: {$prescription->status} ({$prescription->status_label})\n";
echo "        Image saved in private storage: {$prescription->image}\n\n";

// Verify image file exists in storage
if (Storage::disk('local')->exists($prescription->image)) {
    echo "        [SUCCESS] File verified to exist in private storage disk.\n\n";
} else {
    echo "        [WARNING] Image file not found in storage disk. Check filesystem configurations.\n\n";
}

// 3. Customer Sends Consultation Message
$requestMsg1 = Request::create("/prescriptions/ticket/{$prescription->id}/message", 'POST', [
    'message' => 'Halo apoteker, apakah resep anak saya sudah bisa disiapkan?'
]);
$prescriptionController->sendMessage($requestMsg1, $prescription->id);

$msg1 = PrescriptionMessage::where('prescription_id', $prescription->id)->latest()->first();
if ($msg1 && $msg1->message === 'Halo apoteker, apakah resep anak saya sudah bisa disiapkan?') {
    echo "Step 3: Customer sent message successfully.\n";
    echo "        Customer Message: \"{$msg1->message}\"\n\n";
} else {
    echo "[FAILED] Customer message was not stored.\n";
    exit(1);
}

// 4. Admin (Pharmacist) Answers and Changes Status
Auth::login($admin);

$adminController = new AdminPrescriptionController();

// Change status to processing
$requestStatus1 = Request::create("/admin/prescriptions/{$prescription->id}/status", 'POST', [
    'status' => 'processing'
]);
$adminController->changeStatus($requestStatus1, $prescription->id);

$prescription->refresh();
if ($prescription->status === 'processing') {
    echo "Step 4.1: Ticket status successfully changed to: {$prescription->status} ({$prescription->status_label})\n";
    
    // Check if system message was posted
    $sysMsg = PrescriptionMessage::where('prescription_id', $prescription->id)->orderBy('id', 'desc')->first();
    echo "            System logs message: \"{$sysMsg->message}\"\n";
} else {
    echo "[FAILED] Ticket status was not updated to processing.\n";
    exit(1);
}

// Send Chat from Admin
$requestAdminMsg = Request::create("/admin/prescriptions/{$prescription->id}/message", 'POST', [
    'message' => 'Halo Ibu/Bapak, resep sedang kami periksa. Kami akan menambahkan Paracetamol ke keranjang belanja Anda.'
]);
$adminController->sendMessage($requestAdminMsg, $prescription->id);

$adminMsg = PrescriptionMessage::where('prescription_id', $prescription->id)
    ->where('user_id', $admin->id)
    ->orderBy('id', 'desc')
    ->first();
if ($adminMsg) {
    echo "Step 4.2: Admin chat message successfully stored.\n";
    echo "            Admin Message: \"{$adminMsg->message}\"\n\n";
} else {
    echo "[FAILED] Admin message was not stored.\n";
    exit(1);
}

// 5. Admin Adds Medicine to Customer's Cart
$requestAddMed = Request::create("/admin/prescriptions/{$prescription->id}/add-medicine", 'POST', [
    'medicine_id' => $medicine->id,
    'quantity' => 2
]);
$adminController->addMedicine($requestAddMed, $prescription->id);

// Verify item in user's cart
$cartItem = CartItem::where('user_id', $customer->id)->where('medicine_id', $medicine->id)->first();
if ($cartItem && $cartItem->quantity === 2) {
    echo "Step 5: Admin added medicine directly to Customer's live cart database!\n";
    echo "        Customer Cart Item: {$cartItem->quantity}x {$medicine->name}\n";
    
    // Verify system log message is appended
    $addSysMsg = PrescriptionMessage::where('prescription_id', $prescription->id)->orderBy('id', 'desc')->first();
    echo "        System Message: \"{$addSysMsg->message}\"\n\n";
} else {
    echo "[FAILED] Cart item was not added to the database.\n";
    exit(1);
}

// 6. Admin Marks Ticket as Completed (Closes Ticket)
$requestStatus2 = Request::create("/admin/prescriptions/{$prescription->id}/status", 'POST', [
    'status' => 'completed'
]);
$adminController->changeStatus($requestStatus2, $prescription->id);

$prescription->refresh();
if ($prescription->status === 'completed') {
    echo "Step 6: Ticket successfully marked as completed & closed!\n";
    echo "        Final Status: {$prescription->status} ({$prescription->status_label})\n";
    
    $compSysMsg = PrescriptionMessage::where('prescription_id', $prescription->id)->orderBy('id', 'desc')->first();
    echo "        Final System Message: \"{$compSysMsg->message}\"\n\n";
} else {
    echo "[FAILED] Status was not updated to completed.\n";
    exit(1);
}

// 7. Cleanup Test Records
PrescriptionMessage::where('prescription_id', $prescription->id)->delete();
CartItem::where('user_id', $customer->id)->delete();
$imagePathToClean = $prescription->image;
$prescription->delete();

if ($imagePathToClean && Storage::disk('local')->exists($imagePathToClean)) {
    Storage::disk('local')->delete($imagePathToClean);
    echo "Step 7: Cleaned up uploaded test image file: {$imagePathToClean}\n";
}
echo "        Cleaned up test database records.\n\n";

echo "--- ALL INTEGRATION TESTS COMPLETED SUCCESSFULLY! ---\n";
