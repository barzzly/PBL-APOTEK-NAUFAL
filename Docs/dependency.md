# Dokumentasi Dependency — Website Apotek Naufal

Dokumen ini menjelaskan seluruh package eksternal yang digunakan dalam proyek beserta alasan pemilihan, cara instalasi, dan dampaknya terhadap proyek.

---

## Daftar Dependency Utama

| Package | Fungsi | Alasan | Versi | Risiko |
|---|---|---|---|---|
| `laravel/framework` | Core framework aplikasi | Foundation utama proyek | ^11.0 | Low — LTS, update rutin |
| `laravel/breeze` | Scaffolding autentikasi (login, register) | Setup auth cepat dan ringan tanpa overhead | ^2.0 | Low — official package |
| `spatie/laravel-permission` | Manajemen Role & Permission berbasis database | Fleksibel, dokumentasi lengkap, komunitas besar | ^6.0 | Low — actively maintained |
| `barryvdh/laravel-dompdf` | Generate dokumen PDF dari view Blade | Mudah diintegrasikan dengan Blade template | ^3.0 | Medium — bergantung DomPDF core |

---

## Detail Setiap Package

---

### 1. `laravel/breeze`

**Fungsi:**
Scaffolding autentikasi minimal untuk Laravel, menyediakan halaman login, register, lupa password, dan verifikasi email siap pakai.

**Alasan Memilih:**
- Lebih ringan dibanding Jetstream (tidak ada Livewire/Inertia overhead)
- Menggunakan Blade template yang konsisten dengan stack proyek
- Mudah dikustomisasi sesuai desain Apotek Naufal

**Cara Install:**

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
php artisan migrate
```

**Dampak pada Proyek:**
- Menambah halaman autentikasi siap pakai di `resources/views/auth/`
- Menambah route autentikasi di `routes/auth.php`
- Menambah ~5MB pada ukuran project (node_modules)

---

### 2. `spatie/laravel-permission`

**Fungsi:**
Manajemen role dan permission berbasis database. Memungkinkan pembatasan akses fitur berdasarkan peran pengguna (admin / pelanggan).

**Alasan Memilih:**
- Integrasi mulus dengan Laravel Auth
- Permission disimpan di database → mudah dikelola tanpa hardcode
- Middleware bawaan: `role:admin`, `permission:manage-obat`
- Dokumentasi sangat lengkap di [spatie.be/docs/laravel-permission](https://spatie.be/docs/laravel-permission)

**Cara Install:**

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

Tambahkan trait ke model `User`:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

Contoh penggunaan di controller:

```php
// Assign role ke user baru
$user->assignRole('pelanggan');

// Cek role di controller
if ($user->hasRole('admin')) { ... }

// Protect route dengan middleware
Route::middleware(['role:admin'])->group(function () {
    Route::resource('/admin/obat', Admin\ObatController::class);
});
```

**Dampak pada Proyek:**
- Menambah 4 tabel di database: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`
- Menambah ~2MB pada ukuran vendor
- Risiko: jika upgrade versi major (v6 → v7) bisa ada breaking change pada method

---

### 3. `barryvdh/laravel-dompdf`

**Fungsi:**
Mengonversi view Blade HTML menjadi file PDF yang dapat diunduh atau dicetak. Digunakan untuk fitur cetak struk pesanan.

**Alasan Memilih:**
- Paling populer untuk PDF di ekosistem Laravel
- Sintaks sederhana dan langsung terintegrasi dengan Blade
- Mendukung CSS dasar untuk styling PDF

**Cara Install:**

```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

Contoh penggunaan di controller:

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function cetakPdf($id)
{
    $order = Order::with('items.obat')->findOrFail($id);
    $pdf = Pdf::loadView('pesanan.struk', compact('order'));
    return $pdf->download('struk-' . $order->id . '.pdf');
}
```

**Dampak pada Proyek:**
- Menambah fitur unduh PDF tanpa perlu service eksternal
- Menambah ~8MB pada ukuran vendor (DomPDF core)
- Risiko: rendering CSS kompleks kurang sempurna — gunakan CSS inline untuk PDF
- Performa: generate PDF berat jika data pesanan sangat banyak (>100 item)

---

## Ringkasan Ukuran Dependency

| Package | Ukuran Perkiraan | Keterangan |
|---|---|---|
| `laravel/framework` | ~50MB | Core framework |
| `laravel/breeze` | ~5MB | Dev only (tidak dibawa ke production build) |
| `spatie/laravel-permission` | ~2MB | Production |
| `barryvdh/laravel-dompdf` | ~8MB | Production |

---

## Update & Maintenance

Untuk memeriksa package yang sudah outdated:

```bash
composer outdated
```

Untuk update semua package sesuai batasan versi di `composer.json`:

```bash
composer update
```

> ⚠️ Selalu jalankan `php artisan test` setelah update dependency untuk memastikan tidak ada breaking change.
