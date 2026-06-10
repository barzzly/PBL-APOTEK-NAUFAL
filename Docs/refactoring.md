# Dokumentasi Refactoring — Website Apotek Naufal

Dokumen ini mencatat perubahan refactoring yang dilakukan pada kode proyek untuk meningkatkan kualitas, keterbacaan, dan maintainability.

---

## Refactoring 1 — Ekstraksi Logic Stok ke StokService

### Sebelum

**Masalah:**
Logic validasi dan pengurangan stok obat ditulis langsung di dalam `CartController` dan `OrderController`. Kedua controller menjadi terlalu besar dan logic yang sama diulang di dua tempat (duplikasi kode).

```php
// CartController.php — SEBELUM
public function store(Request $request)
{
    $obat = Obat::find($request->obat_id);

    // Logic stok ditulis langsung di controller
    if (!$obat) {
        return back()->withErrors(['msg' => 'Obat tidak ditemukan']);
    }
    if ($obat->stok < $request->qty) {
        return back()->withErrors(['msg' => 'Stok tidak mencukupi']);
    }

    // ... lanjut proses cart
}
```

---

### Perubahan

Logic validasi dan pengurangan stok dipindahkan ke class `StokService` di folder `app/Services/`.

```php
// app/Services/StokService.php — SESUDAH
class StokService
{
    public function cekKetersediaan(Obat $obat, int $qty): bool
    {
        return $obat->stok >= $qty;
    }

    public function kurangiStok(Obat $obat, int $qty): void
    {
        $obat->decrement('stok', $qty);
    }
}
```

```php
// CartController.php — SESUDAH (lebih ringkas)
public function store(Request $request)
{
    $obat = Obat::findOrFail($request->obat_id);

    if (!$this->stokService->cekKetersediaan($obat, $request->qty)) {
        return back()->withErrors(['msg' => 'Stok tidak mencukupi']);
    }

    // ... lanjut proses cart
}
```

---

### Alasan

- Menghilangkan duplikasi kode yang sama di `CartController` dan `OrderController`
- Mempermudah testing — `StokService` bisa diuji secara unit tanpa melibatkan HTTP request
- Controller lebih fokus pada alur HTTP (terima request → kembalikan response)

### Dampak

- Kode lebih modular dan mengikuti prinsip **Single Responsibility**
- Jika logic stok berubah, cukup ubah di satu tempat (`StokService`)
- Ukuran controller berkurang ~30 baris per controller

---

## Refactoring 2 — Pemisahan Blade Template ke Partial

### Sebelum

**Masalah:**
Setiap halaman Blade (`katalog.blade.php`, `keranjang.blade.php`, `pesanan.blade.php`) memiliki kode HTML navbar dan footer yang identik. Jika ada perubahan desain navbar, harus diubah di semua file satu per satu.

---

### Perubahan

Navbar, sidebar admin, dan footer dipecah menjadi file partial terpisah:

```
resources/views/
├── layouts/
│   └── app.blade.php          ← layout utama
├── partials/
│   ├── _navbar.blade.php      ← navbar pelanggan
│   ├── _sidebar-admin.blade.php ← sidebar admin
│   └── _footer.blade.php      ← footer
```

Penggunaan di halaman lain:

```blade
{{-- katalog.blade.php --}}
@extends('layouts.app')

@section('content')
    @include('partials._navbar')
    
    {{-- konten katalog --}}
    
    @include('partials._footer')
@endsection
```

---

### Alasan

- Perubahan navbar/footer cukup dilakukan di satu file partial
- Mengurangi ukuran setiap file Blade secara signifikan
- Konsistensi tampilan terjamin di seluruh halaman

### Dampak

- Setiap file view menjadi lebih pendek dan mudah dibaca
- Proses onboarding developer baru lebih cepat karena struktur lebih jelas

---

## Refactoring 3 — Cleanup dan Penamaan Route

### Sebelum

**Masalah:**
Nama route tidak konsisten — sebagian menggunakan snake_case, sebagian lagi camelCase. Route admin dan user tidak dikelompokkan dengan jelas.

```php
// routes/web.php — SEBELUM
Route::get('/daftarObat', [ObatController::class, 'index']);
Route::get('/detail_obat/{id}', [ObatController::class, 'show']);
Route::get('/admin-manage-obat', [Admin\ObatController::class, 'index']);
```

---

### Perubahan

Route dikonversi ke konvensi **kebab-case** dan dikelompokkan dalam prefix yang jelas:

```php
// routes/web.php — SESUDAH
// Route pelanggan
Route::get('/katalog', [ObatController::class, 'index'])->name('katalog.index');
Route::get('/obat/{id}', [ObatController::class, 'show'])->name('obat.show');

// Route admin — dikelompokkan dalam prefix 'admin'
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('obat', Admin\ObatController::class);
    Route::resource('pesanan', Admin\OrderController::class);
});
```

---

### Alasan

- Konsisten dengan konvensi Laravel (kebab-case URL, dot-notation route name)
- Named route memudahkan penggunaan `route('katalog.index')` di Blade dan controller
- Grouping route memperjelas mana yang butuh middleware auth/role

### Dampak

- File `routes/web.php` lebih mudah dibaca dan dikelola
- Tidak ada lagi URL yang inkonsisten antar halaman

---

## Ringkasan Refactoring

| # | Jenis | File Terdampak | Manfaat |
|---|---|---|---|
| 1 | Service Extraction | `CartController`, `OrderController`, `StokService` | Hilangkan duplikasi, mudah ditest |
| 2 | Blade Partial | Semua file `*.blade.php` | Konsistensi UI, mudah diubah |
| 3 | Route Cleanup | `routes/web.php` | Konvensi konsisten, mudah navigasi |
