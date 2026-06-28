# Dokumentasi UAS Konstruksi Evolusi Perangkat Lunak

## Identitas Proyek

| Item | Keterangan |
| --- | --- |
| Nama proyek | Apotek Naufal System |
| Jenis aplikasi | Sistem informasi dan e-commerce apotek berbasis web |
| Framework | Laravel 13 |
| Database | MySQL |
| Frontend | Blade, Tailwind CSS v4, Vite, SweetAlert2, Chart.js, OpenStreetMap/OSRM |
| Integrasi | Google Gemini API, WhatsApp Bot API, OSRM API (Open Source Routing Machine) |
| Tujuan | Digitalisasi transaksi obat bebas/keras, konsultasi resep dokter via chat, perhitungan ongkir pengiriman dinamis, dan dashboard laporan penjualan. |

## Pemenuhan Luaran UAS

| Ketentuan UAS | Luaran di repository | Status |
| --- | --- | --- |
| Dokumentasi Utama | `README.md`, seluruh file pada `docs/` | Selesai |
| Pembagian peran di GitHub | `docs/github-roles.md` | Selesai |
| Sprint Planning | `docs/sprint-planning.md` | Selesai |
| GitHub Action Workflow | `.github/workflows/ci.yml` | Selesai |
| Refactoring | `docs/refactoring.md` | Selesai |
| Dependency | `docs/dependency.md` | Selesai |
| Installation doc | `docs/installation.md` | Selesai |
| Feature doc | `docs/features.md` | Selesai |
| Changelog | `docs/CHANGELOG.md` | Selesai |
| GitHub Action doc | `docs/github-actions.md` | Selesai |

## Ringkasan Sistem

Website Apotek Naufal dibangun untuk memodernisasi layanan apotek fisik menjadi platform digital interaktif. Pelanggan dapat berbelanja obat bebas secara instan dengan metode pembayaran bervariasi (Cash, Transfer, QRIS) serta opsi pengambilan langsung (*pickup*) atau pengiriman (*delivery*). Untuk tipe *delivery*, ongkos kirim dihitung otomatis secara dinamis memanfaatkan OSRM API berdasarkan koordinat rute peta ke alamat tujuan. 

Apotek Naufal juga menyediakan fitur penebusan obat keras melalui unggah resep dokter dan konsultasi keluhan kesehatan secara *online*. Melalui ruang live chat AJAX, apoteker (admin) dapat berdiskusi dengan pelanggan dan merekomendasikan obat yang langsung terintegrasi dengan keranjang belanja pelanggan untuk mempermudah checkout. Seluruh transaksi dan operasional dipantau oleh admin melalui grafik laporan penjualan Chart.js, restock stok otomatis jika pesanan dibatalkan, fitur konversi satuan obat, serta sistem notifikasi polling stok dan pesanan masuk secara real-time.

## Scope Fitur Berdasarkan Aktor

| Aktor | Fitur utama |
| --- | --- |
| Pelanggan | Register & Login (dengan OTP WhatsApp), cari obat (autocomplete Suggestions), filter kategori, ulasan/rating produk, kelola keranjang belanja (silent update), checkout (pickup/delivery dengan peta), upload bukti transfer, tracking status pesanan, unggah resep dokter, buat konsultasi apoteker, live chat konsultasi, update profil & avatar |
| Admin (Apoteker) | Dashboard statistik, CRUD obat & kategori, AI Generator deskripsi obat (Gemini API), konversi satuan obat (Box ke Kardus, dsb.) & aksi stok, verifikasi pembayaran, kelola status pesanan, kelola tiket resep & konsultasi (live chat, tambahkan/hapus obat rekomendasi di keranjang pelanggan), monitoring notifikasi polling (stok habis/menipis, order masuk, tiket baru), cetak/lihat grafik laporan penjualan |
| Sistem | Enkripsi kata sandi & OTP WhatsApp, validasi stok obat real-time, generate nomor resep (`RX-`) dan konsultasi (`TK-`), restock otomatis jika order dibatalkan, kalkulasi jarak via OSRM API (dengan fallback Haversine), notifikasi status pesanan via bot WhatsApp, build & test otomatis (CI) |

## Artefak Proyek

| Kategori | Artefak |
| --- | --- |
| Source code backend | `app/`, `routes/`, `database/`, `config/` |
| Source code frontend | `resources/views/`, `resources/css/`, `resources/js/` |
| Test | `tests/Feature/`, `tests/Unit/` |
| Dokumentasi | `README.md`, `docs/` |
| CI/CD | `.github/workflows/ci.yml` |
| Dependency lock | `composer.lock`, `package-lock.json` |

## Alur Demo UAS

1. Buka `README.md` untuk memperlihatkan ringkasan proyek dan indeks dokumen pendukung.
2. Buka `docs/installation.md` untuk cara instalasi proyek.
3. Jalankan migration dan seeder untuk mempersiapkan database:

```bash
composer install
npm install
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
```

4. Jalankan aplikasi di lokal:

```bash
php artisan serve
npm run dev
```

5. Login menggunakan akun bawaan hasil seeder:

| Role | Email / Username | Password | Keterangan |
| --- | --- | --- | --- |
| Admin | `admin` | `admin` | Akun pengelola / Apoteker |
| Pelanggan | `fauzi@gmail.com` | `password` | Akun customer simulasi |

6. Tunjukkan alur demo utama:

| Demo | Langkah |
| --- | --- |
| **Pembelian Obat Bebas** | Login pelanggan -> cari obat di katalog -> tambahkan ke keranjang -> checkout -> pilih tipe Delivery -> pilih lokasi peta -> bayar via QRIS -> unggah bukti transfer. |
| **Verifikasi Pembayaran & Notifikasi WA** | Login admin -> buka detail pesanan -> verifikasi bukti transfer -> ubah status ke *processing* -> Notifikasi perubahan status dikirim ke WhatsApp pelanggan. |
| **Konsultasi & Tebus Resep** | Pelanggan upload resep dokter -> Tiket terbentuk (`RX-YYYYMMDD-XXXXX`) -> Apoteker masuk chat room -> diskusi dengan pelanggan -> Apoteker memasukkan obat resep langsung ke keranjang pelanggan -> selesaikan tiket -> Pelanggan checkout keranjang belanja. |
| **AI Deskripsi Obat & Kelola Stok** | Admin tambah obat baru -> klik tombol generate deskripsi -> Gemini AI menuliskan deskripsi obat secara otomatis -> kelola stok obat & konversi satuan (Kardus ke Box). |
| **Notifikasi Polling Admin** | Kurangi stok obat hingga habis -> Admin menerima notifikasi pop-up polling "Stok Habis" secara real-time. |
| **Grafik Pendapatan** | Admin membuka menu Laporan Penjualan -> melihat grafik pendapatan harian via Chart.js dan daftar 10 obat terlaris. |

7. Tunjukkan GitHub Action CI:

- File workflow: `.github/workflows/ci.yml`
- Dokumentasi CI: `docs/github-actions.md`

8. Tunjukkan Refactoring:

- File dokumentasi: `docs/refactoring.md`
- Contoh kode hasil refactoring: `GeminiService` (untuk integrasi AI), `NotificationService` (untuk notifikasi polling), master layout `admin/layout.blade.php`, dan standarisasi penamaan route admin.

## Quality Gate

Quality gate yang disiapkan di workflow CI:

| Gate | Perintah / Action |
| --- | --- |
| Install backend | `composer install` |
| Setup runtime | Setup PHP 8.4 & MySQL Service Container |
| Setup env | `cp .env.example .env.testing` & `key:generate` |
| Migration DB | `php artisan migrate --env=testing --force` |
| Automated test | `php artisan test` |

Quality gate ini dijalankan otomatis oleh GitHub Actions setiap kali ada aktivitas `push` ke branch `main`/`develop` or pengajuan `pull_request`.

## Catatan Evolusi Perangkat Lunak

Bukti evolusi proyek terdokumentasi dengan baik pada:

- `docs/CHANGELOG.md` untuk merekam riwayat perubahan per sprint secara detail.
- `docs/refactoring.md` untuk detail perubahan arsitektur kode agar lebih bersih (*clean code*) dan terstruktur (*SRP & DRY*).
- `docs/sprint-planning.md` untuk perencanaan backlog, pembagian tim per sprint, serta pelacakan status pekerjaan.
- `tests/` untuk rangkaian skenario unit test/integration test guna menjaga fungsionalitas kode dari error regresi.
