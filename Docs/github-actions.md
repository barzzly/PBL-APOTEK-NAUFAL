# Dokumentasi GitHub Actions — Website Apotek Naufal

Dokumen ini menjelaskan workflow CI (Continuous Integration) yang digunakan pada repository proyek.

---

## Workflow yang Digunakan

**Nama Workflow:** Laravel CI — Install & Test

Workflow ini berfungsi untuk memastikan setiap perubahan kode (push atau pull request) tidak merusak project dengan cara:
1. Menginstall seluruh dependency
2. Menyiapkan environment testing
3. Menjalankan migration di database testing
4. Menjalankan seluruh test suite

---

## Lokasi File

```
.github/
└── workflows/
    └── ci.yml
```

---

## Trigger

Workflow dijalankan otomatis saat:

| Event | Keterangan |
|---|---|
| `push` ke branch `main` | Setiap commit yang di-push ke branch utama |
| `push` ke branch `develop` | Setiap commit di branch development |
| `pull_request` ke `main` | Saat ada PR yang ingin di-merge ke main |

---

## Isi File Workflow (`.github/workflows/ci.yml`)

```yaml
name: Laravel CI

on:
  push:
    branches: [ "main", "develop" ]
  pull_request:
    branches: [ "main" ]

jobs:
  laravel-tests:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: apotek_naufal_test
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
      # 1. Checkout kode dari repository
      - name: Checkout code
        uses: actions/checkout@v4

      # 2. Setup PHP 8.2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, bcmath, pdo, pdo_mysql
          coverage: none

      # 3. Cache Composer dependencies
      - name: Cache Composer packages
        uses: actions/cache@v3
        with:
          path: vendor
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-composer-

      # 4. Install dependency via Composer
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-suggest

      # 5. Setup environment file
      - name: Copy .env
        run: cp .env.example .env.testing

      # 6. Generate application key
      - name: Generate app key
        run: php artisan key:generate --env=testing

      # 7. Set permission storage
      - name: Set storage permissions
        run: chmod -R 777 storage bootstrap/cache

      # 8. Jalankan migrasi di database testing
      - name: Run migrations
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: apotek_naufal_test
          DB_USERNAME: root
          DB_PASSWORD: password
        run: php artisan migrate --env=testing --force

      # 9. Jalankan seluruh test suite
      - name: Run tests
        env:
          DB_CONNECTION: mysql
          DB_HOST: 127.0.0.1
          DB_PORT: 3306
          DB_DATABASE: apotek_naufal_test
          DB_USERNAME: root
          DB_PASSWORD: password
        run: php artisan test
```

---

## Tahapan Workflow

| # | Tahap | Keterangan |
|---|---|---|
| 1 | **Checkout code** | Mengambil kode terbaru dari repository |
| 2 | **Setup PHP 8.2** | Menyiapkan runtime PHP dengan ekstensi yang dibutuhkan |
| 3 | **Cache Composer** | Cache vendor folder agar build lebih cepat di run berikutnya |
| 4 | **Composer install** | Mengunduh semua dependency (Laravel, Spatie, DomPDF, dll.) |
| 5 | **Copy .env** | Menyalin konfigurasi environment untuk testing |
| 6 | **Generate app key** | Membuat APP_KEY untuk enkripsi |
| 7 | **Set permissions** | Memberikan akses tulis ke folder storage |
| 8 | **Run migrations** | Membuat struktur tabel di database MySQL testing |
| 9 | **Run tests** | Menjalankan semua test di folder `tests/` |

---

## Status Badge

Tambahkan badge berikut di `README.md` untuk menampilkan status CI secara real-time:

```markdown
![CI](https://github.com/[username]/apotek-naufal/actions/workflows/ci.yml/badge.svg)
```

Contoh tampilan:
`![CI](https://github.com/[username]/apotek-naufal/actions/workflows/ci.yml/badge.svg)`

> Ganti `[username]` dengan username GitHub akun tim.

---

## Hasil Workflow

Setelah workflow berhasil berjalan, GitHub akan menampilkan:

- ✅ **Centang hijau** di setiap commit — menandakan semua test lulus
- ❌ **Silang merah** jika ada test gagal atau error pada salah satu step — tim akan mendapat notifikasi email

> Screenshot hasil workflow dapat dilihat di tab **Actions** pada repository GitHub.

---

## Catatan

Jika workflow belum aktif di repository, ikuti langkah berikut:

1. Buat folder `.github/workflows/` di root project
2. Buat file `ci.yml` dengan isi seperti di atas
3. Commit dan push ke GitHub
4. Buka tab **Actions** di repository untuk melihat workflow berjalan

> Pastikan repository sudah di-push ke GitHub sebelum workflow aktif.
