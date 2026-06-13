# Sistem Informasi Apotek Naufal 💊

Sistem Informasi Apotek Naufal adalah aplikasi berbasis web yang dirancang untuk mengelola operasional apotek secara digital. Aplikasi ini mencakup manajemen inventaris obat, transaksi pemesanan (delivery & pickup), verifikasi resep dokter, hingga sistem obrolan konsultasi real-time antara apoteker dan pelanggan.

---

## 👥 Tim Pengembang (PBL)
* **Farid Yahya**
* **Hidayathul Fikri**
* **Nabilla Fitricia Hernanda**
* **Rury Fezriani Matari**

---

## 🛠️ Teknologi Utama
* **Backend**: Laravel (PHP)
* **Frontend**: Tailwind CSS v4 & Laravel Blade templates
* **Build Tool**: Vite (Laravel Vite Plugin)
* **Database**: MySQL / MariaDB
* **Library**: Spatie Permission, Laravel DomPDF, Laravel Excel, SweetAlert2

---

## 🚀 Panduan Instalasi Cepat
1. **Clone repository**:
   ```bash
   git clone https://github.com/barzzly/PBL-APOTEK-NAUFAL.git
   cd PBL-APOTEK-NAUFAL
   ```
2. **Instal dependensi**:
   ```bash
   composer install
   npm install
   ```
3. **Salin file lingkungan**:
   ```bash
   cp .env.example .env
   ```
4. **Konfigurasi Database & Jalankan Migrasi**:
   Sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env` Anda, lalu jalankan:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
5. **Jalankan Aplikasi**:
   - Terminal 1: `php artisan serve`
   - Terminal 2: `npm run dev`

---

## 🔗 Daftar API Internal (AJAX / JSON Endpoints)
Aplikasi ini menyediakan berbagai API internal berbasis JSON untuk mendukung interaksi tanpa muat ulang halaman (AJAX):

### 1. API Obrolan Konsultasi (Real-time Sync)
* **`GET /tickets/room/{id}/messages`**: Mengambil daftar pesan konsultasi terbaru untuk sisi Pelanggan.
* **`POST /tickets/room/{id}/message`**: Mengirim pesan konsultasi baru dari sisi Pelanggan.
* **`GET /admin/tickets/{id}/messages`**: Mengambil daftar pesan konsultasi terbaru untuk sisi Apoteker/Admin.
* **`POST /admin/tickets/{id}/message`**: Mengirim pesan konsultasi baru dari sisi Apoteker/Admin.

### 2. API Pengelolaan Keranjang Belanja (Live Cart)
* **`POST /cart/add`**: Menambahkan obat ke dalam keranjang belanja.
* **`POST /cart/update`**: Memperbarui kuantitas obat di keranjang belanja dengan validasi stok real-time.
* **`POST /cart/remove`**: Menghapus obat dari keranjang belanja secara dinamis.

### 3. API Checkout & Logistik
* **`GET /checkout/calculate-distance`**: Menghitung jarak pengantaran alamat pelanggan secara dinamis guna menghitung estimasi ongkos kirim (*shipping cost*).

### 4. API Dashboard & Administrasi
* **`GET /admin/laporan-penjualan/chart-data`**: Mengambil data akumulasi transaksi bulanan dalam format JSON untuk dirender ke dalam Chart.js Dashboard.
* **`POST /admin/medicines/generate-description`**: Menghasilkan deskripsi obat secara otomatis menggunakan integrasi AI.

---

## 🗃️ Struktur Database (Schema)

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
| `image` | `string` | Gambar kategori (nullable) |
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
| `composition` | `text` | Kandungan / komposisi obat (nullable) |
| `indications` | `text` | Khasiat atau indikasi penggunaan (nullable) |
| `dosage` | `text` | Aturan pakai dan dosis (nullable) |
| `side_effects` | `text` | Efek samping (nullable) |
| `contraindications`| `text` | Kontraindikasi obat (nullable) |
| `unit` | `string(50)` | Satuan obat, misal: *strip*, *botol* (Default: `pcs`) |
| `price` | `decimal(12,2)` | Harga jual obat |
| `price_before_discount` | `decimal(12,2)` | Harga asli sebelum diskon (nullable) |
| `stock` | `integer` | Jumlah stok obat tersedia |
| `min_stock` | `integer` | Batas minimum stok sebelum warning |
| `image` | `string` | Path file gambar obat (nullable) |
| `requires_prescription` | `boolean` | Status wajib resep dokter (Default: `false`) |
| `is_active` | `boolean` | Status keaktifan obat (Default: `true`) |
| `expired_date` | `date` | Tanggal kedaluwarsa obat (nullable) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 4. Tabel: `orders`
Menyimpan informasi transaksi pembelian obat.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `order_number` | `string` | Nomor pesanan unik (ORD-YYYYMMDD-XXXXX) |
| `user_id` | `bigint` | Foreign Key ke `users.id` 🗝️ (Cascade) |
| `status` | `enum('pending', 'confirmed', 'processing', 'ready_for_pickup', 'shipped', 'delivered', 'cancelled')` | Status pesanan |
| `order_type` | `enum('pickup', 'delivery')` | Metode pengambilan barang |
| `subtotal` | `decimal(12,2)` | Total harga obat sebelum ongkir/diskon |
| `shipping_cost` | `decimal(12,2)` | Biaya pengiriman jika delivery |
| `discount` | `decimal(12,2)` | Potongan harga (nullable) |
| `total_amount` | `decimal(12,2)` | Total akhir yang wajib dibayar |
| `payment_method` | `enum('cash', 'transfer', 'bpjs', 'qris')` | Metode pembayaran |
| `payment_status` | `enum('unpaid', 'paid', 'refunded')` | Status pembayaran |
| `paid_at` | `timestamp` | Waktu pembayaran terkonfirmasi (nullable) |
| `payment_proof` | `string` | Bukti transfer pembayaran (nullable) |
| `delivery_latitude` | `double` | Koordinat lintang tujuan pengantaran (nullable) [BARU] |
| `delivery_longitude`| `double` | Koordinat bujur tujuan pengantaran (nullable) [BARU] |
| `delivery_distance` | `double` | Jarak pengantaran dalam kilometer (nullable) [BARU] |
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
| `medicine_name` | `string` | Nama obat saat transaksi dibuat (Snapshot) |
| `medicine_unit` | `string(50)` | Satuan obat saat transaksi dibuat (Snapshot) |
| `quantity` | `integer` | Jumlah kuantitas obat yang dibeli |
| `price` | `decimal(12,2)` | Harga satuan obat saat transaksi dibuat |
| `subtotal` | `decimal(12,2)` | Subtotal harga untuk item (`quantity * price`) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 6. Tabel: `prescriptions`
Menyimpan data tiket konsultasi / tebus resep dokter dari pelanggan.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `user_id` | `bigint` | Foreign Key ke `users.id` 🗝️ (Cascade) |
| `order_id` | `bigint` | Foreign Key ke `orders.id` 🗝️ (Set Null, nullable) |
| `prescription_number` | `string` | Nomor tiket unik (RX- atau TK- YYYYMMDD-XXXXX) |
| `type` | `enum('prescription', 'consultation')` | Tipe tiket: tebus resep dokter atau konsultasi umum [BARU] |
| `doctor_name` | `string` | Nama dokter penulis resep (nullable) [MODIFIKASI: NULLABLE] |
| `hospital_clinic` | `string` | Nama rumah sakit atau klinik (nullable) |
| `patient_name` | `string` | Nama pasien penerima resep/konsultasi |
| `patient_age` | `integer` | Usia pasien (nullable) |
| `status` | `enum('pending', 'verified', 'processing', 'completed', 'rejected')` | Status verifikasi/proses tiket |
| `image` | `string` | Path foto scan resep / keluhan (nullable) [MODIFIKASI: NULLABLE] |
| `customer_notes` | `text` | Catatan/detail keluhan dari pelanggan (nullable) [BARU] |
| `notes` | `text` | Catatan verifikasi resep oleh apoteker (nullable) |
| `verified_by` | `bigint` | Foreign Key ke `users.id` (Apoteker penanggung jawab) 🗝️ (nullable) |
| `verified_at` | `timestamp` | Waktu resep diverifikasi/dikonfirmasi (nullable) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 7. Tabel: `cart_items` [TABEL BARU]
Menyimpan data keranjang belanja pelanggan secara persisten di database (Live Sync).

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `user_id` | `bigint` | Foreign Key ke `users.id` 🗝️ (Cascade) |
| `medicine_id` | `bigint` | Foreign Key ke `medicines.id` 🗝️ (Cascade) |
| `quantity` | `integer` | Jumlah kuantitas obat dalam keranjang |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 8. Tabel: `ratings` [TABEL BARU]
Menyimpan data ulasan dan rating bintang obat-obatan dari pelanggan.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `medicine_id` | `bigint` | Foreign Key ke `medicines.id` 🗝️ (Cascade) |
| `user_id` | `bigint` | Foreign Key ke `users.id` 🗝️ (Cascade) |
| `rating` | `integer` | Skor ulasan (skala 1 hingga 5) |
| `review` | `text` | Ulasan/komentar produk (nullable) |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |

---

### 9. Tabel: `prescription_messages` [TABEL BARU]
Menyimpan riwayat obrolan konsultasi di dalam masing-masing tiket pelayanan.

| Nama Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `bigint` | Primary Key 🔑 |
| `prescription_id` | `bigint` | Foreign Key ke `prescriptions.id` 🗝️ (Cascade) |
| `user_id` | `bigint` | Foreign Key ke `users.id` 🗝️ (Cascade) |
| `message` | `text` | Konten isi pesan obrolan |
| `created_at` | `timestamp` | Tanggal & waktu pembuatan data |
| `updated_at` | `timestamp` | Tanggal & waktu pembaruan data |
