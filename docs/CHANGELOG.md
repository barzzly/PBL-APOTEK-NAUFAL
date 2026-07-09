# Changelog

Semua perubahan penting pada proyek **Website Apotek Naufal** didokumentasikan di file ini.

Format mengacu pada [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [v0.5.0] - 2026-06-16

### Added
- Integrasi WhatsApp Bot API kustom (Node.js/Next.js) untuk pengiriman kode OTP 6-digit dan pembaruan real-time status pesanan pelanggan.
- Fitur WhatsApp OTP Login & Registration: Autentikasi aman tanpa sandi menggunakan One-Time Password (OTP) dikirim langsung ke nomor WhatsApp terdaftar.
- Fitur "Lupa Password" terhubung langsung dengan alur masuk OTP WhatsApp untuk pemulihan akun yang cepat.
- Fitur Password Visibility Toggle (tombol ikon mata) di form Login dan Register untuk menampilkan/menyembunyikan sandi secara real-time.
- Halaman Syarat & Ketentuan (`/syarat-ketentuan`) dan Kebijakan Privasi (`/kebijakan-privasi`) dengan layout premium dua kolom (sticky navigation, scroll highlighting, dan Call-to-Action apoteker).
- Otomatisasi konversi media: Mengonversi aset utama (`hero.png` -> `hero.webp`) serta fitur backend untuk mengonversi gambar obat/kategori yang diunggah secara otomatis ke format WebP untuk efisiensi performa.

### Changed
- Footer website: Memperbarui kontak bisnis Apotek Naufal dan menghapus tautan media sosial Facebook/Instagram yang tidak digunakan.

---

## [v0.4.1] - 2026-06-15

### Added
- Fitur konversi satuan otomatis (1 Kardus = 24 Box) pada input data obat admin untuk menstandardisasi stok ke satuan terkecil.
- Fitur "Aksi Stok" (Tambah/Kurang/Atur Nilai Mutlak) pada halaman edit obat admin untuk memudahkan penyesuaian stok secara fleksibel tanpa salah kalkulasi manual.
- Input kuantitas keranjang belanja yang dapat diketik langsung secara dinamis serta pembaruan jumlah tanpa popup loading modal (silent update).

---

## [v0.4.0] - 2026-06-14

### Added
- Dashboard Laporan Penjualan Admin dengan grafik Chart.js interaktif (pendapatan harian dan jumlah transaksi).
- Integrasi Google Gemini AI (`GeminiService`) untuk otomatisasi deskripsi obat bagi admin.
- Sistem Notifikasi Polling Real-time Admin (`NotificationService` via AJAX) untuk memantau stok habis/kritis, pesanan baru, dan tiket masuk.
- Fitur Ulasan & Rating Obat (bintang 1-5) oleh customer yang telah login.
- Fitur Manajemen Profil Pengguna (pembaruan data profil, unggah foto avatar, dan ganti kata sandi).

### Changed
- Refactoring controller dengan memindahkan logic eksternal API dan kalkulasi status ke Service Class (`GeminiService` & `NotificationService`).

### Dependency
- `add` Google Gemini API (model `gemini-2.5-flash-lite` & fallback) untuk asisten deskripsi AI.
- `add` Chart.js (via asset/CDN) untuk visualisasi diagram penjualan di dashboard laporan.

---

## [v0.3.0] - 2025-06-01

### Added
- Fitur Checkout & Pemesanan Obat:
  - Tipe pengambilan: Pickup (Ambil di Apotek) dan Delivery (Pengiriman Kurir).
  - Metode pembayaran: Cash, Transfer Bank, dan QRIS.
- Perhitungan Jarak & Ongkir Dinamis:
  - Integrasi API OSRM (Open Source Routing Machine) untuk mengukur jarak berkendara dari apotek ke titik lokasi customer.
  - Mekanisme fallback formula Haversine untuk menghitung jarak garis lurus apabila OSRM offline.
  - Tarif ongkos kirim otomatis sebesar Rp2.500 per km (minimum Rp10.000).
- Sistem Chat & Tiket Konsultasi / Resep Dokter:
  - Unggah resep dokter digital (tiket `RX`) atau konsultasi gejala (tiket `TK`).
  - Chat room interaktif (`/tickets/room/{id}`) antara customer dan apoteker.
  - Apoteker dapat menambahkan/menghapus rekomendasi obat langsung ke dalam keranjang belanja customer dari dalam chat room.
- Fitur tracking status pesanan lengkap dengan pengembalian stok otomatis (auto-restock) jika transaksi dibatalkan (`cancelled`).

### Changed
- Keamanan: Upload berkas resep disimpan di direktori lokal terlindung (`storage/app/private/prescriptions`) dan diakses melalui route terkunci (`/tickets/file/{filename}`) dengan validasi hak kepemilikan.
- Aturan Pembayaran: Pembayaran tunai (Cash) dibatasi hanya untuk pesanan bertipe Pickup.

### Fixed
- Pencegahan Overselling: Validasi stok obat ganda (di halaman checkout dan di kueri database transaksi) sebelum order dicatat.

---

## [v0.2.0] - 2025-05-10

### Added
- Fitur keranjang belanja (tambah, ubah kuantitas, hapus item) menggunakan AJAX untuk customer terdaftar dan guest (Session).
- Otorisasi pengguna berbasis kolom `role` (`admin` dan `customer`) pada tabel `users`.
- Dashboard ringkasan statistik admin (total kategori, obat, order, pendapatan).
- Fitur CRUD kategori obat oleh admin.

### Changed
- Pemisahan struktur file `routes/web.php` menjadi grup route tamu, pelanggan terautentikasi, dan admin.

### Refactor
- Abstraksi layout panel admin ke dalam satu berkas master layout `resources/views/admin/layout.blade.php`.

---

## [v0.1.0] - 2025-04-20

### Added
- Inisiasi proyek Laravel 13.
- Autentikasi dasar (login, register, logout) dengan password hashing (`Hash::make`).
- Migrasi database awal: tabel `users`, `medicines`, dan `categories`.
- Fitur CRUD obat oleh admin (nama, kategori, harga, stok, satuan, deskripsi, gambar).
- Halaman katalog obat customer dengan filter kategori di beranda.
- Profil apotek (lokasi, jam operasional, dan informasi kontak).
- Inisiasi berkas `.env.example` dan konfigurasi database.
