# Dokumentasi Dependency — Website Apotek Naufal

Dokumen ini menjelaskan seluruh package eksternal, library frontend, dan integrasi API pihak ketiga yang digunakan dalam proyek Website Apotek Naufal.

---

## 1. Lingkungan PHP & Framework

Aplikasi dikembangkan menggunakan stack Laravel modern dengan spesifikasi:

| Komponen | Versi | Deskripsi |
|---|---|---|
| PHP | `^8.3` | Versi interpreter PHP utama |
| `laravel/framework` | `^13.0` | Core framework aplikasi (Laravel 13) |

---

## 2. Dependensi Produksi (Composer)

Berikut adalah daftar library PHP backend yang terpasang di file `composer.json` (bagian `require`):

| Package | Versi | Fungsi & Status Penggunaan |
|---|---|---|
| `laravel/tinker` | `^3.0` | Interaksi REPL terminal Laravel. |
| `spatie/laravel-permission` | `^8.0` | Terpasang, tetapi sistem otorisasi saat ini menggunakan kolom `role` pada tabel `users` secara langsung (`admin` / `customer`). |
| `barryvdh/laravel-dompdf` | `^3.1` | Terpasang untuk keperluan rendering dokumen PDF di masa mendatang. |

---

## 3. Dependensi Development & Testing (Composer)

Library PHP yang digunakan khusus di lingkungan pengembangan (lokal) dan pengujian (bagian `require-dev`):

| Package | Versi | Fungsi |
|---|---|---|
| `pestphp/pest` | `^4.6` | Framework unit testing utama yang modern. |
| `pestphp/pest-plugin-laravel` | `^4.1` | Plugin integrasi Pest dengan framework Laravel. |
| `barryvdh/laravel-debugbar` | `^4.2` | Menampilkan debug bar info kueri database, log, dan memori di browser. |
| `fakerphp/faker` | `^1.23` | Penyuplai data palsu untuk Database Seeder / Factory. |
| `nunomaduro/collision` | `^8.6` | Penampil error/exception yang interaktif di CLI. |
| `mockery/mockery` | `^1.6` | Object mocking untuk pengujian unit. |
| `laravel/pail` | `^1.2.5` | Log streaming tool bawaan Laravel. |
| `laravel/pao` | `^1.0.6` | Helper utilitas internal development. |
| `laravel/pint` | `^1.27` | Kode PHP style fixer untuk menjaga konsistensi penulisan kode. |

---

## 4. Dependensi Frontend & Build Tool (NPM)

Library JavaScript dan stylesheet yang dikonfigurasi melalui `package.json` dan dibuild via Vite:

| Library / Tool | Versi | Fungsi |
|---|---|---|
| `vite` | `^8.0.0` | Build tool dan bundler asset frontend. |
| `laravel-vite-plugin` | `^3.0.0` | Jembatan integrasi Vite dengan Laravel Asset Loading. |
| `tailwindcss` | `^4.0.0` | Framework Utility-First CSS versi terbaru (Tailwind CSS v4). |
| `@tailwindcss/vite` | `^4.0.0` | Plugin official integrasi kompilasi Tailwind v4 ke dalam Vite. |
| `concurrently` | `^9.0.1` | Menjalankan server lokal PHP (`artisan serve`) dan Vite compiler (`npm run dev`) secara bersamaan. |

---

## 5. Integrasi Layanan Eksternal (API Pihak Ketiga)

Aplikasi Apotek Naufal terintegrasi secara langsung dengan layanan API eksternal berikut untuk menunjang fitur premium:

### A. Google Gemini API
* **Fungsi:** Men-generate deskripsi obat secara otomatis berdasarkan nama dan kategori obat melalui asisten AI.
* **Integrasi:** Dilakukan oleh `App\Services\GeminiService`.
* **API Endpoint:** `https://generativelanguage.googleapis.com/v1beta/models/`
* **Model yang Digunakan:** Mencoba berurutan dari model tercepat: `gemini-2.5-flash-lite`, jika gagal menggunakan `gemini-flash-lite-latest`, dan terakhir fallback ke `gemini-2.5-flash`.
* **Kebutuhan:** Memerlukan konfigurasi `GEMINI_API_KEY` di file `.env`.

### B. OSRM API (Open Source Routing Machine)
* **Fungsi:** Menghitung rute perjalanan dan jarak mengemudi (dalam kilometer) dari koordinat Apotek Naufal (Andalas, Padang) ke koordinat alamat kirim yang dipilih customer di peta.
* **Integrasi:** Dilakukan oleh `CheckoutController` (method `getDistanceAndShipping`).
* **API Endpoint:** `https://router.project-osrm.org/route/v1/driving/`
* **Fallback:** Jika server OSRM sedang lambat atau offline, sistem otomatis beralih menggunakan rumus matematika **Haversine** untuk mendapatkan jarak garis lurus di bumi secara instan.

### C. WhatsApp Bot API (Custom / Buatan Sendiri)
* **Fungsi:** Mengirimkan kode OTP 6-digit untuk otentikasi login/register tanpa kata sandi dan pemberitahuan (notifikasi) pembaruan status pesanan secara real-time kepada pelanggan.
* **Integrasi:** Dilakukan oleh `App\Services\WhatsAppNotificationService`.
* **API Endpoint:** Terhubung ke API perpesanan berbasis Node.js/Next.js kustom yang dikonfigurasi melalui config `services.whatsapp.url`.
* **Kebutuhan:** Memerlukan konfigurasi endpoint dan token bot WhatsApp di file `.env`.

---

## 6. Update & Pemeliharaan Dependensi

1. **Memeriksa Update Tersedia:**
   * Backend (PHP): `composer outdated`
   * Frontend (JS): `npm outdated`
2. **Melakukan Update:**
   * Backend: `composer update`
   * Frontend: `npm update`
3. **Pembersihan Cache Laravel:**
   * Setelah melakukan perubahan dependensi, disarankan melakukan clear cache:
     ```bash
     php artisan config:clear
     php artisan cache:clear
     ```
