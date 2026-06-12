# Sistem Informasi Apotek Naufal 💊

Selamat datang di repositori **Sistem Informasi Apotek Naufal**. Aplikasi ini dikembangkan untuk mengelola operasional apotek secara digital, mulai dari manajemen pengguna, inventaris obat, transaksi pemesanan (baik ambil langsung maupun pengantaran), hingga verifikasi resep dokter.

Aplikasi ini dibangun menggunakan framework **Laravel** dengan integrasi frontend menggunakan **Vite**.

---

## 👥 Tim Pengembang (PBL)
Aplikasi ini dikembangkan oleh kelompok mahasiswa berikut:
* **Farid Yahya**
* **Hidayathul Fikri**
* **Nabilla Fitricia Hernanda**
* **Rury Fezriani Matari**

---

## 🎯 Tujuan Proyek
Proyek ini dibuat dengan tujuan:
* **Digitalisasi Operasional Apotek**: Mengotomatiskan pencatatan stok obat, manajemen kategori, dan transaksi secara real-time.
* **Kemudahan Akses Pelanggan**: Memfasilitasi pelanggan untuk memesan obat secara online baik melalui metode *delivery* (diantar) maupun *pickup* (ambil langsung).
* **Verifikasi Resep Digital**: Menyediakan fitur bagi apoteker untuk memverifikasi unggahan foto resep dokter dari pelanggan sebelum memproses pesanan obat keras.
* **Pelaporan yang Cepat & Akurat**: Membantu pihak administrasi apotek dalam mengunduh laporan penjualan dan stok dalam format PDF maupun Excel secara efisien.

---

## 🌟 Fitur Utama
* **Autentikasi & Multi-Role**: Sistem login dan hak akses terintegrasi untuk 3 role pengguna: **Admin**, **Pharmacist (Apoteker)**, dan **Customer**.
* **Manajemen Inventaris Obat**: Manajemen data obat mencakup kategori, stok, harga diskon, batas minimum stok, status wajib resep dokter, hingga tanggal kadaluwarsa.
* **Sistem Pemesanan (Checkout)**: Fitur pemesanan obat online dengan opsi pengiriman (*delivery*) atau pengambilan di apotek (*pickup*).
* **Upload & Verifikasi Resep**: Pelanggan dapat mengunggah foto resep dokter, dan apoteker dapat memverifikasi atau menolak resep tersebut melalui panel khusus.
* **Metode Pembayaran Variatif**: Mendukung pembayaran via *Cash*, *Transfer* (dengan unggah bukti bayar), *QRIS*, dan *BPJS*.
* **Ekspor & Cetak Laporan**: Generate laporan penjualan format PDF, serta impor/ekspor data obat secara massal menggunakan format Excel.
* **Pagination & Sorting Dinamis (Panel Admin)**: Batasan halaman dinamis (10, 25, 50, 100) serta fitur pengurutan data (*sorting*) interaktif pada header kolom tabel di halaman Riwayat Transaksi, Pesanan Masuk, dan Data Obat.

---

## 🛠️ Teknologi yang Digunakan

Aplikasi ini menggunakan kombinasi teknologi berikut untuk memastikan keandalan dan performa terbaik:

* **Backend / Engine Utama**: [Laravel](https://laravel.com) (Framework PHP MVC modern)
* **Frontend UI**: [Laravel Blade](https://laravel.com/docs/blade) (Templating Engine) & [Tailwind CSS v4](https://tailwindcss.com) (Styling UI)
* **Asset Bundler / Build Tool**: [Vite](https://vite.dev) (Terintegrasi via `laravel-vite-plugin`)
* **Basis Data (Database)**: MySQL / MariaDB
* **Library Pendukung Penting**:
  * **Spatie Laravel Permission**: Mengelola otorisasi akses (Role & Permission) pengguna.
  * **Laravel DomPDF**: Menghasilkan dokumen laporan penjualan dan bukti transaksi berformat PDF.
  * **Laravel Excel**: Memproses ekspor dan impor data inventaris obat dengan format Excel (.xlsx).

---

## 🚀 Panduan Instalasi & Clone Repository

Ikuti langkah-langkah di bawah ini untuk memasang proyek ini di lingkungan lokal Anda:

### 1. Clone Repository
Unduh kode sumber proyek ini dengan menjalankan perintah berikut di terminal Anda:
```bash
git clone https://github.com/barzzly/PBL-APOTEK-NAUFAL.git
cd pbl-fix-dev
```

### 2. Instalasi Dependensi
Pasang semua dependensi PHP (Composer) dan Javascript (NPM) yang diperlukan:
```bash
# Menginstal library backend PHP
composer install

# Menginstal package frontend Javascript
npm install
```

### 3. Konfigurasi Environment File
Salin file konfigurasi lingkungan dari `.env.example` ke `.env`:
```bash
# Pengguna Unix/Linux/macOS atau Git Bash
cp .env.example .env

# Pengguna Windows Command Prompt (CMD)
copy .env.example .env
```

### 4. Generate Application Key
Jalankan perintah berikut untuk membuat kunci enkripsi aplikasi Laravel:
```bash
php artisan key:generate
```

### 5. Konfigurasi Database
Buat database baru di DBMS Anda (misal: MySQL/MariaDB). Setelah itu, buka file `.env` dengan text editor dan sesuaikan kredensial database Anda pada baris berikut:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=username_database_anda
DB_PASSWORD=password_database_anda
```

### 6. Migrasi & Seeding Database
Jalankan migrasi untuk membuat tabel-tabel di database beserta data awal (seed) seperti akun administrator, kategori, dan beberapa data obat contoh:
```bash
php artisan migrate --seed
```

### 7. Jalankan Server Lokal
Untuk menjalankan aplikasi secara lokal, Anda perlu menyalakan server Laravel dan server kompilasi frontend (Vite) secara bersamaan:

* **Terminal 1: Menjalankan backend Laravel**
  ```bash
  php artisan serve
  ```
  Aplikasi dapat diakses melalui browser di: `http://127.0.0.1:8000`

* **Terminal 2: Menjalankan aset frontend (Vite)**
  ```bash
  npm run dev
  ```

---

## 🗃️ Struktur Database

Berikut adalah representasi detail dari struktur tabel database yang digunakan pada sistem ini:

### 1. Tabel: `users`
Menyimpan data pengguna sistem yang terbagi atas beberapa peran (role) hak akses.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `name` | `string` | Nama lengkap pengguna |
| `email` | `string` | Alamat email unik (digunakan untuk login) |
| `email_verified_at` | `timestamp` | Waktu verifikasi email (nullable) |
| `password` | `string` | Kata sandi terenkripsi |
| `phone` | `string(20)` | Nomor telepon aktif (nullable) |
| `address` | `text` | Alamat lengkap (nullable) |
| `role` | `enum('admin', 'customer', 'pharmacist')` | Hak akses pengguna (Default: `customer`) |
| `avatar` | `string` | Path file foto profil (nullable) |
| `remember_token` | `string` | Token untuk fitur *Remember Me* (nullable) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 2. Tabel: `categories`
Menyimpan kategori pengelompokan obat-obatan.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `name` | `string` | Nama kategori obat |
| `slug` | `string` | Slug unik untuk penulisan URL ramah SEO |
| `description` | `text` | Deskripsi singkat mengenai kategori (nullable) |
| `image` | `string` | Gambar representasi kategori (nullable) |
| `is_active` | `boolean` | Status keaktifan kategori (Default: `true`) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 3. Tabel: `medicines`
Menyimpan inventaris dan data obat-obatan yang dijual.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `category_id` | `bigint` | Foreign Key ke `categories.id` 🗝️ (Cascade) |
| `name` | `string` | Nama obat |
| `slug` | `string` | Slug unik untuk penulisan URL ramah SEO |
| `brand` | `string` | Merek atau pabrikan obat (nullable) |
| `sku` | `string` | Kode unik inventaris / SKU (nullable) |
| `description` | `text` | Deskripsi umum obat (nullable) |
| `composition` | `text` | Kandungan / komposisi bahan aktif obat (nullable) |
| `indications` | `text` | Khasiat atau indikasi penggunaan (nullable) |
| `dosage` | `text` | Aturan pakai dan dosis (nullable) |
| `side_effects` | `text` | Efek samping yang mungkin terjadi (nullable) |
| `contraindications`| `text` | Kondisi yang tidak disarankan memakai obat (nullable) |
| `unit` | `string(50)` | Satuan obat, misal: *pcs*, *strip*, *botol* (Default: `pcs`) |
| `price` | `decimal(12,2)` | Harga jual obat |
| `price_before_discount` | `decimal(12,2)` | Harga asli sebelum diskon (nullable) |
| `stock` | `integer` | Jumlah stok obat tersedia |
| `min_stock` | `integer` | Batas minimum stok sebelum memicu peringatan |
| `image` | `string` | Path file gambar obat (nullable) |
| `requires_prescription` | `boolean` | Status apakah memerlukan resep dokter (Default: `false`) |
| `is_active` | `boolean` | Status ketersediaan aktif obat (Default: `true`) |
| `expired_date` | `date` | Tanggal kedaluwarsa obat (nullable) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 4. Tabel: `orders`
Menyimpan informasi transaksi pembelian obat.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `order_number` | `string` | Nomor pesanan unik (contoh: ORD-YYYYMMDD-XXXXX) |
| `user_id` | `bigint` | Foreign Key ke `users.id` 🗝️ (Cascade) |
| `status` | `enum` | Status pesanan (`pending`, `confirmed`, `processing`, `ready_for_pickup`, `shipped`, `delivered`, `cancelled`) |
| `order_type` | `enum('pickup', 'delivery')` | Metode pengambilan barang |
| `subtotal` | `decimal(12,2)` | Total harga obat sebelum ongkir/diskon |
| `shipping_cost` | `decimal(12,2)` | Biaya pengiriman jika tipe pesanan adalah delivery |
| `discount` | `decimal(12,2)` | Potongan harga yang didapatkan |
| `total_amount` | `decimal(12,2)` | Total akhir yang wajib dibayar |
| `payment_method` | `enum('cash', 'transfer', 'bpjs', 'qris')` | Metode pembayaran |
| `payment_status` | `enum('unpaid', 'paid', 'refunded')` | Status pelunasan pembayaran |
| `paid_at` | `timestamp` | Waktu pembayaran terkonfirmasi (nullable) |
| `payment_proof` | `string` | Path file bukti transfer pembayaran (nullable) |
| `shipping_address` | `text` | Alamat tujuan pengantaran (nullable) |
| `notes` | `string` | Catatan tambahan dari customer (nullable) |
| `pharmacist_note` | `string` | Catatan tambahan dari apoteker (nullable) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 5. Tabel: `order_items`
Menyimpan rincian item obat pada setiap pesanan.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `order_id` | `bigint` | Foreign Key ke `orders.id` 🗝️ (Cascade) |
| `medicine_id` | `bigint` | Foreign Key ke `medicines.id` 🗝️ (Cascade) |
| `medicine_name` | `string` | Nama obat saat transaksi dibuat *(Snapshot)* |
| `medicine_unit` | `string(50)` | Satuan obat saat transaksi dibuat *(Snapshot)* |
| `quantity` | `integer` | Jumlah kuantitas obat yang dibeli |
| `price` | `decimal(12,2)` | Harga satuan obat saat transaksi dibuat |
| `subtotal` | `decimal(12,2)` | Subtotal harga untuk item (`quantity * price`) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 6. Tabel: `prescriptions`
Menyimpan data unggahan resep dokter untuk pembelian obat-obat tertentu.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `user_id` | `bigint` | Foreign Key ke `users.id` 🗝️ (Cascade) |
| `order_id` | `bigint` | Foreign Key ke `orders.id` 🗝️ (Set Null, nullable) |
| `prescription_number` | `string` | Nomor resep dokter unik |
| `doctor_name` | `string` | Nama dokter yang memberikan resep |
| `hospital_clinic` | `string` | Nama rumah sakit atau klinik penerbit (nullable) |
| `prescription_date` | `date` | Tanggal penulisan resep |
| `patient_name` | `string` | Nama pasien penerima resep |
| `patient_age` | `integer` | Usia pasien (nullable) |
| `status` | `enum` | Status resep (`pending`, `verified`, `processing`, `completed`, `rejected`) |
| `image` | `string` | Path file scan/foto resep dokter |
| `notes` | `text` | Catatan verifikasi resep oleh apoteker (nullable) |
| `verified_by` | `bigint` | Foreign Key ke `users.id` (Apoteker penanggung jawab) 🗝️ (Set Null, nullable) |
| `verified_at` | `timestamp` | Waktu resep dikonfirmasi/diverifikasi (nullable) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

## ⚙️ Ketergantungan Paket Utama (Dependencies)

Aplikasi ini menggunakan beberapa paket open-source penting berikut untuk mendukung fiturnya:
* **Spatie Laravel Permission**: Digunakan untuk mengelola peran (Role) dan hak akses (Permission) pengguna (misalnya: membedakan menu Admin, Apoteker, dan Kasir/Customer).
* **Laravel DomPDF**: Digunakan untuk mencetak dokumen dan laporan penjualan, stok, serta transaksi ke format PDF.
* **Laravel Excel**: Digunakan untuk mengekspor atau mengimpor data obat-obatan dalam jumlah besar menggunakan file Excel (.xlsx).

---

## 👥 Anggota Tim Pengembang (PBL)

Aplikasi ini dikembangkan oleh kelompok mahasiswa berikut:
* **Hidayathul Fikri**
* **Farid Yahya**
* **Nabilla Fitricia Hernanda**
* **Rury Fezriani Matari**
