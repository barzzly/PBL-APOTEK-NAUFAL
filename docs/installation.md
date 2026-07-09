# Panduan Instalasi — Website Apotek Naufal

Dokumen ini menjelaskan langkah-langkah instalasi proyek Laravel Apotek Naufal di lingkungan lokal (development).

---

## Persyaratan Sistem

Pastikan perangkat Anda sudah terinstal:

| Perangkat Lunak | Versi Minimum | Keterangan |
|---|---|---|
| PHP | `8.3+` | Dengan ekstensi: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `pdo_mysql` |
| Composer | `2.x` | Dependency manager PHP |
| MySQL | `8.0+` | Atau MariaDB 10.4+ |
| Node.js | `18.x+` | Untuk build asset frontend |
| NPM | `9.x+` | Otomatis terinstal bersama Node.js |
| Git | `2.x+` | Untuk clone repository |

---

## Langkah Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/[username]/apotek-naufal.git
cd apotek-naufal
```

### 2. Install Dependency PHP

```bash
composer install
```

Composer akan mengunduh semua package backend yang tercantum di `composer.json`.

### 3. Setup Environment File

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Lalu edit file `.env` dan sesuaikan konfigurasi database Anda. Contoh menggunakan MySQL lokal:

```env
APP_NAME="Apotek Naufal"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apotek_naufal
DB_USERNAME=root
DB_PASSWORD=

# Gemini API Key (Diperlukan jika ingin menggunakan fitur AI Deskripsi Obat)
GEMINI_API_KEY=isi_dengan_api_key_gemini_anda
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Setup Database

Buat database baru di MySQL dengan nama `apotek_naufal` melalui phpMyAdmin atau terminal MySQL:

```sql
CREATE DATABASE apotek_naufal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Jalankan Migrasi & Database Seeding

Jalankan perintah berikut untuk membuat semua tabel dan mengisinya dengan data sampel (kategori, obat, transaksi, rating, dan pengguna bawaan):

```bash
php artisan migrate --seed
```

### 7. Install Asset Frontend

```bash
npm install
npm run dev
```

Untuk membangun asset dalam mode production:

```bash
npm run build
```

### 8. Atur Permission Storage (Khusus Linux / macOS)

```bash
chmod -R 775 storage bootstrap/cache
```

> *Catatan untuk pengguna Windows (PowerShell/CMD): Langkah ini dilewati saja.*

### 9. Jalankan Server Lokal Laravel

```bash
php artisan serve
```

Aplikasi kini dapat diakses melalui browser di alamat: **[http://localhost:8000](http://localhost:8000)**

---

## Akun Bawaan Hasil Seeding (Default Accounts)

Setelah proses seeding selesai (`php artisan db:seed`), sistem menyediakan beberapa akun siap pakai untuk pengujian:

### A. Akun Admin (Apoteker/Pengelola)

| Nama User | Username / Email | Password | Role |
|---|---|---|---|
| Administrator Apotek | `admin` | `admin` | `admin` |

### B. Akun Customer (Pelanggan)

Semua akun customer di bawah ini memiliki password default: **`password`**

| Nama Customer | Email Login | Role |
|---|---|---|
| Ahmad Fauzi | `fauzi@gmail.com` | `customer` |
| Siti Aminah | `siti@gmail.com` | `customer` |
| Naufal Hadi | `naufal@gmail.com` | `customer` |
| Budi Santoso | `budi@gmail.com` | `customer` |
| Customer Biasa | `user@gmail.com` | `customer` |

---

## Troubleshooting

### 1. Error: `Class "Spatie\Permission\PermissionServiceProvider" not found`
Spatie Permission terdaftar di autoloader composer. Jika terjadi kendala pembacaan service provider, jalankan:
```bash
composer dump-autoload
php artisan config:clear
```

### 2. Error: `SQLSTATE[HY000] [1045] Access denied`
Periksa kembali kesesuaian nilai `DB_USERNAME` dan `DB_PASSWORD` di file `.env` Anda dengan kredensial server MySQL lokal Anda.

### 3. Kendala Gambar Obat atau Resep Tidak Tampil
Aplikasi menyimpan file unggahan secara terorganisir. Jalankan symlink folder storage agar folder publik terhubung ke storage privat:
```bash
php artisan storage:link
```
