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

echo "--- STARTING STANDALONE PRESCRIPTION & CONSULTATION TICKET SYSTEM TEST ---\n\n";

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

// 2. Customer Uploads a Prescription Ticket
Auth::login($customer);

$fakeImage = UploadedFile::fake()->image('resep_dokter_anak.jpg');

$requestStore = Request::create('/tickets/upload', 'POST', [
    'doctor_name' => 'dr. John Doe, Sp.A',
    'hospital_clinic' => 'RSUD M. Djamil Padang',
    'patient_name' => 'Adit Pratama',
    'patient_age' => 8,
    'customer_notes' => 'Tolong ditebus obat flu dan batuk generik.',
]);
// Inject file upload manually
$requestStore->files->set('image', $fakeImage);

$prescriptionController = new PrescriptionController();
$responseStore = $prescriptionController->store($requestStore);

$prescription = Prescription::where('user_id', $customer->id)->where('type', 'prescription')->latest()->first();

if (!$prescription) {
    echo "[FAILED] Prescription ticket was not created in database.\n";
    exit(1);
}

echo "Step 2: Prescription ticket uploaded successfully!\n";
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

// 2.1 Customer submits a General Consultation Ticket (TK-prefix, no doctor details, optional image)
$requestConsult = Request::create('/tickets/consult', 'POST', [
    'patient_name' => 'Fadhil Naufal',
    'patient_age' => 20,
    'customer_notes' => 'Saya merasa demam dan pusing sejak kemarin malam.',
]);

$responseConsult = $prescriptionController->storeConsult($requestConsult);

$consultTicket = Prescription::where('user_id', $customer->id)->where('type', 'consultation')->latest()->first();

if (!$consultTicket) {
    echo "[FAILED] Consultation ticket was not created in database.\n";
    exit(1);
}

echo "Step 2.1: Consultation ticket created successfully!\n";
echo "        Ticket Number: {$consultTicket->prescription_number}\n";
echo "        Patient: {$consultTicket->patient_name}\n";
echo "        Type: {$consultTicket->type}\n";
echo "        Status: {$consultTicket->status} ({$consultTicket->status_label})\n\n";

// 3. Customer Sends Consultation Message (AJAX/JSON)
$requestMsg1 = Request::create("/tickets/room/{$prescription->id}/message", 'POST', [
    'message' => 'Halo apoteker, apakah resep anak saya sudah bisa disiapkan?'
]);
$requestMsg1->headers->set('Accept', 'application/json');
$responseJson1 = $prescriptionController->sendMessage($requestMsg1, $prescription->id);

$json1 = json_decode($responseJson1->getContent(), true);
if ($json1 && isset($json1['success']) && $json1['success'] === true) {
    echo "Step 3.1: Customer sent AJAX message successfully and received correct JSON response.\n";
} else {
    echo "[FAILED] Customer AJAX message response invalid.\n";
    exit(1);
}

// Fetch messages (AJAX/JSON)
$responseFetchCustomer = $prescriptionController->getMessages($prescription->id);
$fetchCustomerData = json_decode($responseFetchCustomer->getContent(), true);
if ($fetchCustomerData && count($fetchCustomerData['messages']) > 0) {
    echo "Step 3.2: Customer fetched messages via API successfully. Messages count: " . count($fetchCustomerData['messages']) . "\n\n";
} else {
    echo "[FAILED] Customer could not fetch messages via API.\n";
    exit(1);
}

// 4. Admin (Pharmacist) Answers and Changes Status
Auth::login($admin);

$adminController = new AdminPrescriptionController();

// Change status to processing
$requestStatus1 = Request::create("/admin/tickets/{$prescription->id}/status", 'POST', [
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

// Send Chat from Admin (AJAX/JSON)
$requestAdminMsg = Request::create("/admin/tickets/{$prescription->id}/message", 'POST', [
    'message' => 'Halo Ibu/Bapak, resep sedang kami periksa. Kami akan menambahkan Paracetamol ke keranjang belanja Anda.'
]);
$requestAdminMsg->headers->set('Accept', 'application/json');
$responseAdminJson = $adminController->sendMessage($requestAdminMsg, $prescription->id);
$adminJson = json_decode($responseAdminJson->getContent(), true);
if ($adminJson && isset($adminJson['success']) && $adminJson['success'] === true) {
    echo "Step 4.2: Admin sent AJAX message successfully and received correct JSON response.\n";
} else {
    echo "[FAILED] Admin AJAX message response invalid.\n";
    exit(1);
}

// Fetch messages from Admin side (AJAX/JSON)
$responseFetchAdmin = $adminController->getMessages($prescription->id);
$fetchAdminData = json_decode($responseFetchAdmin->getContent(), true);
if ($fetchAdminData && count($fetchAdminData['messages']) > 0) {
    echo "Step 4.3: Admin fetched messages via API successfully. Messages count: " . count($fetchAdminData['messages']) . "\n\n";
} else {
    echo "[FAILED] Admin could not fetch messages via API.\n";
    exit(1);
}

// 5. Admin Adds Medicine to Customer's Cart
$requestAddMed = Request::create("/admin/tickets/{$prescription->id}/add-medicine", 'POST', [
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
$requestStatus2 = Request::create("/admin/tickets/{$prescription->id}/status", 'POST', [
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

$consultTicket->delete();
echo "        Cleaned up test database records (including consultation ticket).\n\n";

echo "--- ALL INTEGRATION TESTS COMPLETED SUCCESSFULLY! ---\n";
