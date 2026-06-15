<?php

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

echo "--- STARTING INTEGRATION TEST FOR AUTOMATIC CLEANUP ---\n\n";

// Ensure clean storage test environment
Storage::disk('public')->delete('medicines/test_old.png');
Storage::disk('public')->delete('medicines/test_new.png');
Storage::disk('public')->delete('categories/cat_old.png');
Storage::disk('public')->delete('categories/cat_new.png');

// Create a test category
$category = Category::create([
    'name' => 'Test Category Cleanup',
    'slug' => 'test-category-cleanup',
    'image' => '/images/default_category.png', // Default placeholder
]);

// Create a test medicine
$medicine = Medicine::create([
    'category_id' => $category->id,
    'name' => 'Test Medicine Cleanup',
    'slug' => 'test-medicine-cleanup',
    'price' => 1000,
    'stock' => 10,
    'image' => '/images/default_medicine.png', // Default placeholder
]);

echo "1. Created test records with default placeholders.\n";

// Scenario A: Updating image from default placeholder to a dynamic upload
// Put a mock dynamic file
Storage::disk('public')->put('medicines/test_old.png', 'dummy_content');
assert(Storage::disk('public')->exists('medicines/test_old.png'), "Mock old file should exist");

$medicine->update([
    'image' => '/storage/medicines/test_old.png'
]);

// Placeholder image in /images/ should NOT be deleted (we check this manually by verifying no errors/deletes on public/images)
echo "   - Updated medicine image to storage path: /storage/medicines/test_old.png\n";

// Scenario B: Updating dynamic upload with a new dynamic upload
Storage::disk('public')->put('medicines/test_new.png', 'new_dummy_content');
assert(Storage::disk('public')->exists('medicines/test_new.png'), "Mock new file should exist");

$medicine->update([
    'image' => '/storage/medicines/test_new.png'
]);

// Verify old file is deleted, new file is kept
if (!Storage::disk('public')->exists('medicines/test_old.png')) {
    echo "   [SUCCESS] Old medicine file /storage/medicines/test_old.png was automatically DELETED!\n";
} else {
    echo "   [FAILED] Old medicine file was NOT deleted.\n";
    exit(1);
}

if (Storage::disk('public')->exists('medicines/test_new.png')) {
    echo "   [SUCCESS] New medicine file /storage/medicines/test_new.png is KEPT!\n";
} else {
    echo "   [FAILED] New medicine file was deleted.\n";
    exit(1);
}

// Scenario C: Deleting medicine
$medicine->delete();

if (!Storage::disk('public')->exists('medicines/test_new.png')) {
    echo "   [SUCCESS] Medicine file /storage/medicines/test_new.png was automatically DELETED upon model deletion!\n";
} else {
    echo "   [FAILED] Medicine file was NOT deleted on deletion.\n";
    exit(1);
}

echo "\n--------------------------------------------------\n";

// Scenario D: Testing Category
Storage::disk('public')->put('categories/cat_old.png', 'cat_dummy_content');
$category->update([
    'image' => '/storage/categories/cat_old.png'
]);

Storage::disk('public')->put('categories/cat_new.png', 'cat_new_dummy_content');
$category->update([
    'image' => '/storage/categories/cat_new.png'
]);

if (!Storage::disk('public')->exists('categories/cat_old.png')) {
    echo "   [SUCCESS] Old category file /storage/categories/cat_old.png was automatically DELETED!\n";
} else {
    echo "   [FAILED] Old category file was NOT deleted.\n";
    exit(1);
}

$category->delete();

if (!Storage::disk('public')->exists('categories/cat_new.png')) {
    echo "   [SUCCESS] Category file /storage/categories/cat_new.png was automatically DELETED upon model deletion!\n";
} else {
    echo "   [FAILED] Category file was NOT deleted on deletion.\n";
    exit(1);
}

echo "\n--- ALL TESTS PASSED SUCCESSFULLY! ---\n";
