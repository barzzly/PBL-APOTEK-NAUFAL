<?php

use App\Models\User;
use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access medicine import', function () {
    $response = $this->post(route('admin.medicines.import'), [
        'default_price' => 5000,
        'default_stock' => 10,
    ]);

    $response->assertRedirect(route('login'));
});

test('admin can import medicines from tab-separated file with custom price and stock formats', function () {
    $user = User::factory()->create([
        'role' => 'admin'
    ]);

    // Create a temporary TSV file content with custom price and stock formatting
    $tsvContent = "Test Medicine 1\tObat Bebas\t\tStrip\tRp. 20.000\t150\n" .
                 "Test Medicine 2\tObat Keras\t\tBox\tRp. 26.1267\t75\n";

    $file = UploadedFile::fake()->createWithContent('import.txt', $tsvContent);

    $response = $this->actingAs($user)->post(route('admin.medicines.import'), [
        'file' => $file,
        'default_price' => 7500,
        'default_stock' => 25,
    ]);

    $response->assertRedirect(route('admin.medicines'));
    $response->assertSessionHas('success');

    // Assert categories were created
    $this->assertDatabaseHas('categories', [
        'name' => 'Obat Bebas',
        'slug' => 'obat-bebas'
    ]);
    $this->assertDatabaseHas('categories', [
        'name' => 'Obat Keras',
        'slug' => 'obat-keras'
    ]);

    // Assert medicines were created
    $this->assertDatabaseHas('medicines', [
        'name' => 'Test Medicine 1',
        'slug' => 'test-medicine-1',
        'unit' => 'strip',
        'price' => 20000.00,
        'stock' => 150,
        'requires_prescription' => false
    ]);

    $this->assertDatabaseHas('medicines', [
        'name' => 'Test Medicine 2',
        'slug' => 'test-medicine-2',
        'unit' => 'box',
        'price' => 26126.70,
        'stock' => 75,
        'requires_prescription' => true
    ]);
});

test('importing existing medicine updates its category and unit but keeps price and stock unless defined in row', function () {
    $user = User::factory()->create([
        'role' => 'admin'
    ]);

    // Pre-create category and medicine
    $category = Category::create([
        'name' => 'Kategori Lama',
        'slug' => 'kategori-lama'
    ]);

    $medicine = Medicine::create([
        'category_id' => $category->id,
        'name' => 'Test Medicine 1',
        'slug' => 'test-medicine-1',
        'unit' => 'pcs',
        'price' => 12000,
        'stock' => 50,
        'requires_prescription' => false
    ]);

    // TSV file that has same slug but different category and unit
    $tsvContent = "Test Medicine 1\tObat Bebas\t\tStrip\n";

    $file = UploadedFile::fake()->createWithContent('import.txt', $tsvContent);

    $response = $this->actingAs($user)->post(route('admin.medicines.import'), [
        'file' => $file,
        'default_price' => 7500,
        'default_stock' => 25,
    ]);

    $response->assertRedirect(route('admin.medicines'));

    // Assert updated category and unit
    $medicine->refresh();
    expect($medicine->category->name)->toBe('Obat Bebas');
    expect($medicine->unit)->toBe('strip');
    // Price and stock should remain 12000 and 50 (not updated to default values)
    expect((float)$medicine->price)->toEqual(12000.0);
    expect($medicine->stock)->toBe(50);
});
