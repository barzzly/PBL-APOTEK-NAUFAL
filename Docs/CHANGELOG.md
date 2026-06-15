# Changelog

Semua perubahan penting pada proyek **Website Apotek Naufal** akan didokumentasikan di file ini.

Format mengacu pada [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [Unreleased]

### Planned
- Fitur rekomendasi obat berbasis AI (gejala → rekomendasi)
- Notifikasi status pesanan via email
- Dashboard laporan penjualan dengan grafik

---

## [v0.3.0] - 2025-06-01

### Added
- Fitur checkout dan pemesanan obat
  - Pilih metode pembayaran (transfer bank / COD)
  - Upload resep dokter untuk obat keras
  - Konfirmasi pesanan sebelum submit
- Fitur tracking status pesanan (diproses / dikirim / selesai)
- Cetak struk pesanan dalam format PDF menggunakan `barryvdh/laravel-dompdf`
- Route dan controller `OrderController` untuk mengelola alur pesanan

### Changed
- Tampilan halaman keranjang diperbarui dengan ringkasan harga total
- Validasi stok obat diperketat saat proses checkout

### Fixed
- Bug redirect setelah berhasil checkout tidak mengarah ke halaman pesanan
- Kalkulasi total harga tidak terupdate saat qty diubah di keranjang

### Dependency
- `add` barryvdh/laravel-dompdf ^3.0 — untuk fitur cetak PDF struk pesanan

---

## [v0.2.0] - 2025-05-10

### Added
- Fitur keranjang belanja (tambah, ubah qty, hapus item)
- Manajemen role & permission menggunakan `spatie/laravel-permission`
  - Role: `admin`, `pelanggan`
  - Permission: `manage-obat`, `manage-pesanan`, `manage-user`
- Middleware proteksi route berdasarkan role
- Dashboard admin dengan ringkasan data (total obat, total pesanan, total user)
- Fitur CRUD kategori obat oleh admin

### Changed
- Struktur route dipisahkan menjadi grup `web` (guest), `auth` (pelanggan), dan `admin`
- Tampilan katalog obat menggunakan card grid yang lebih responsif

### Refactor
- Logic validasi stok dipindahkan dari `CartController` ke `StokService`
- Blade template dipecah menjadi partial: `_navbar.blade.php`, `_sidebar.blade.php`, `_footer.blade.php`

### Dependency
- `add` spatie/laravel-permission ^6.0 — manajemen role dan permission berbasis database

---

## [v0.1.0] - 2025-04-20

### Added
- Inisiasi proyek Laravel 11
- Setup autentikasi dasar (login, register, logout) menggunakan Laravel Breeze
- Migrasi database awal: tabel `users`, `obat`, `kategori_obat`
- Fitur CRUD obat oleh admin (tambah, lihat, edit, hapus)
- Katalog obat untuk pelanggan (daftar obat, detail obat, filter kategori)
- Halaman profil apotek (info, jam operasional, lokasi)
- Setup environment `.env.example` dan konfigurasi database
- Inisiasi repository GitHub dan struktur folder proyek

### Dependency
- `add` Laravel Breeze ^2.x — scaffolding autentikasi awal
