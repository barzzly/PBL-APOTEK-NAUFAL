# Panduan Instalasi — Website Apotek Naufal

Dokumen ini menjelaskan langkah-langkah instalasi proyek Laravel Apotek Naufal di lingkungan lokal (development).

---

## Persyaratan Sistem

Pastikan perangkat sudah terinstal:

| Software | Versi Minimum | Keterangan |
|---|---|---|
| PHP | 8.2+ | Dengan ekstensi: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json` |
| Composer | 2.x | Dependency manager PHP |
| MySQL | 8.0+ | Atau MariaDB 10.4+ |
| Node.js | 18.x+ | Untuk build asset frontend |
| NPM | 9.x+ | Otomatis terinstal bersama Node.js |
| Git | 2.x+ | Untuk clone repository |

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

Composer akan mengunduh semua package yang tercantum di `composer.json`, termasuk:
- `spatie/laravel-permission`
- `barryvdh/laravel-dompdf`

### 3. Setup Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Lalu edit file `.env` dan sesuaikan konfigurasi database:

```env
APP_NAME="Apotek Naufal"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apotek_naufal
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Setup Database

Buat database baru di MySQL:

```sql
CREATE DATABASE apotek_naufal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

Seeder akan membuat:
- Akun admin default: `admin@apotek-naufal.com` / `password`
- Role & permission awal via Spatie
- Data kategori obat sample

### 6. Install Asset Frontend

```bash
npm install
npm run dev
```

Untuk production:

```bash
npm run build
```

### 7. Set Permission Storage

```bash
chmod -R 775 storage bootstrap/cache
```

> **Windows (PowerShell):** Lewati langkah ini, tidak diperlukan.

### 8. Jalankan Aplikasi

```bash
php artisan serve
```

Buka browser dan akses: **http://localhost:8000**

---

## Akun Default Setelah Seeding

| Role | Email | Password |
|---|---|---|
| Admin | admin@apotek-naufal.com | password |
| Pelanggan | pelanggan@apotek-naufal.com | password |

> ⚠️ Segera ganti password setelah login pertama kali di lingkungan production.

---

## Troubleshooting

### Error: `php_mbstring` extension not found
Aktifkan ekstensi di `php.ini`:
```ini
extension=mbstring
```

### Error: `SQLSTATE[HY000] [1045] Access denied`
Periksa kembali `DB_USERNAME` dan `DB_PASSWORD` di file `.env`.

### Error: `The stream or file "storage/logs/laravel.log" could not be opened`
Jalankan:
```bash
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

### Error: `Class "Spatie\Permission\PermissionServiceProvider" not found`
Jalankan ulang:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Halaman putih / 500 Error setelah clone
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```
