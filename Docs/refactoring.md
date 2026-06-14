# Dokumentasi Refactoring — Website Apotek Naufal

Dokumen ini mencatat proses refactoring yang telah diterapkan pada kode proyek Apotek Naufal guna meningkatkan kualitas, keterbacaan, keterujian (testability), dan pemeliharaan (maintainability) kode.

---

## Refactoring 1 — Ekstraksi Logic Bisnis ke Service Class (`app/Services/`)

### Sebelum Refactoring

**Masalah:**
Logic eksternal yang kompleks (seperti integrasi API Google Gemini AI) dan logic bisnis yang berulang (seperti pemeriksaan notifikasi stok menipis, stok kosong, order baru, dan tiket baru) awalnya ditulis langsung di dalam `AdminController`. Hal ini melanggar prinsip **Single Responsibility Principle (SRP)** dan membuat class controller menjadi sangat gemuk, sulit diuji secara terpisah, serta rentan terjadi penumpukan kode.

### Setelah Refactoring

Logic integrasi AI diekstraksi ke class khusus `GeminiService`, dan logic pemetaan notifikasi diekstraksi ke class `NotificationService`. Keduanya diletakkan di bawah namespace `App\Services`.

#### A. Kelas Layanan AI (`app/Services/GeminiService.php`)
Class ini bertugas menyusun prompt, mengonfigurasi header HTTP, serta menangani kueri berantai (chaining) model Gemini (`gemini-2.5-flash-lite` -> `gemini-flash-lite-latest` -> `gemini-2.5-flash`) dengan mekanisme penanganan error (error handling) yang kokoh.

```php
// app/Services/GeminiService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $models = ['gemini-2.5-flash-lite', 'gemini-flash-lite-latest', 'gemini-2.5-flash'];

    public function __construct() {
        $this->apiKey = config('services.gemini.key');
    }

    public function generateDescription(string $medicineName, ?string $categoryName = null): string {
        // HTTP Request ke Google Gemini API dengan model-model fallback
    }
}
```

Controller (`AdminController`) kini menjadi jauh lebih ramping dan hanya bertugas memanggil service tersebut:
```php
// app/Http/Controllers/AdminController.php
public function generateDescription(Request $request)
{
    $request->validate(['name' => 'required|string|max:255']);
    try {
        $geminiService = new GeminiService();
        $description = $geminiService->generateDescription($request->name, $categoryName);
        return response()->json(['success' => true, 'description' => $description]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
```

#### B. Kelas Layanan Notifikasi (`app/Services/NotificationService.php`)
Class ini merangkum kueri filter database untuk mendeteksi obat dengan stok menipis (< 30), stok kosong (0), pesanan bertipe pending, dan tiket chat baru yang berstatus pending. Semua kueri ini dipetakan ke dalam struktur array notifikasi yang seragam.

```php
// app/Services/NotificationService.php
namespace App\Services;

use App\Models\Medicine;
use App\Models\Order;

class NotificationService
{
    public function getNotifications() {
        // Query database & pembentukan koleksi notifikasi terpadu
    }
}
```

### Manfaat Refactoring
1. **Pemisahan Kepentingan (Separation of Concerns):** Controller hanya bertugas menangani alur HTTP (request & response), sedangkan logic API dan kueri berat didelegasikan ke service.
2. **Keterujian (Testability):** `GeminiService` dan `NotificationService` dapat diuji dengan mudah menggunakan unit testing (Mocking) tanpa perlu mensimulasikan full HTTP routing.
3. **Kode Dapat Digunakan Ulang (Reusability):** Notifikasi atau integrasi AI dapat dipanggil sewaktu-waktu dari Artisan Command, Queue Jobs, atau controller lain tanpa menulis ulang kueri yang sama.

---

## Refactoring 2 — Abstraksi Layout Khusus Admin (`admin/layout.blade.php`)

### Sebelum Refactoring

**Masalah:**
Setiap halaman antarmuka Admin (Dashboard, Manajemen Obat, Kategori, Order, dan Laporan Penjualan) menyalin struktur HTML boilerplate, tag header, file css/js, serta sidebar admin yang sama berulang kali. Ketika admin menambahkan sidebar baru atau mengubah warna navigasi, developer harus mengubahnya di belasan file view admin.

### Setelah Refactoring

Struktur umum visual panel admin dipusatkan ke dalam file layout tunggal yaitu `resources/views/admin/layout.blade.php`. Layout ini menampung HTML scaffold, bilah samping (sidebar), bilah atas (navbar admin), tautan ke Vite asset compilation `@vite(['resources/css/app.css', 'resources/js/app.js'])`, dan notifikasi pop-up.

File view admin spesifik (misalnya `dashboard.blade.php`) cukup memperluas layout tersebut menggunakan direktif Blade `@extends` dan menaruh kontennya di dalam `@section('content')`:

```blade
{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layout')

@section('title', 'Dashboard Admin')

@section('content')
    <!-- Konten statistik, tabel, dan grafik penjualan -->
@endsection
```

### Manfaat Refactoring
* **Prinsip DRY (Don't Repeat Yourself):** Boilerplate HTML dan menu sidebar hanya didefinisikan satu kali.
* **Konsistensi Tampilan:** Seluruh halaman panel admin dijamin memiliki tampilan sidebar, header, dan notifikasi SweetAlert2 yang seragam.
* **Kecepatan Pengembangan:** Saat membuat modul admin baru (misal Laporan Penjualan Grafik), developer cukup fokus membuat konten utama tanpa mengkhawatirkan layouting dasar.

---

## Refactoring 3 — Standardisasi Penamaan Route Admin

### Sebelum Refactoring

**Masalah:**
Route-route manajemen admin pada awalnya menyebar dengan penamaan url yang acak-acakan (menggunakan camelCase atau nama kustom seperti `/admin-manage-obat`). Hal ini menyulitkan pemetaan menu navigasi sidebar dan rentan terjadi konflik url.

### Setelah Refactoring

Semua route admin dikelompokkan dengan rapi di dalam file `routes/web.php` menggunakan prefix `/admin/` dan dilindungi oleh middleware keamanan yang selaras. Selain itu, penamaan route diseragamkan dengan notasi titik (`admin.medicines.index`, `admin.categories.edit`, dll.):

```php
// routes/web.php
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Kategori
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    
    // Obat
    Route::get('/medicines', [AdminController::class, 'medicines'])->name('admin.medicines');
    Route::delete('/medicines/{id}', [AdminController::class, 'deleteMedicine'])->name('admin.medicines.destroy');
    
    // ...
});
```

### Manfaat Refactoring
* Mempermudah penulisan url dinamis di view Blade menggunakan helper `route('admin.medicines')` daripada hardcode `/admin/medicines`.
* URL aplikasi menjadi seragam menggunakan format **kebab-case** yang ramah SEO dan standar Laravel.
