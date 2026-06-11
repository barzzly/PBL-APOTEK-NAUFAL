# Dokumentasi Sprint Planning — Website Apotek Naufal

Dokumen ini menjelaskan perencanaan pengerjaan (sprint planning) proyek pengembangan Website Apotek Naufal, yang dibagi ke dalam beberapa sprint berdasarkan metodologi Scrum/Agile.

---

## Ringkasan Tim & Peran

| Nama | Peran | Deskripsi |
|---|---|---|
| **Farid Yahya** | Database Engineer / Backend Developer | Mengembangkan skema migrasi database, optimasi query relasional, dan validasi data. |
| **Hidayathul Fikri** | Backend Developer / Scrum Master | Mengelola core logic, arsitektur database, otorisasi, integrasi library, dan workflow CI. |
| **Nabilla Fitricia Hernanda** | Frontend Developer / UI Designer | Mengelola desain tampilan (Blade), styling responsif, interaksi keranjang, upload berkas, dan visualisasi data. |
| **Rury Fezriani Matari** | UI/UX Designer / Frontend Developer | Mengembangkan desain antarmuka pengguna (layouts), elemen blade partials, dan gaya visual responsif. |

---

## Struktur Sprint

### Sprint 1: Setup Proyek & Autentikasi Dasar
**Periode:** 1 April - 20 April 2025  
**Target Rilis:** `v0.1.0`  
**Tujuan Sprint:** Menyiapkan struktur dasar aplikasi Laravel 11, sistem autentikasi pengguna, migrasi tabel-tabel utama, serta halaman katalog obat dasar.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP1-01 | Inisiasi proyek Laravel 11 & setup repository GitHub | Hidayathul Fikri | ✅ Selesai |
| SP1-02 | Setup autentikasi (Login, Register, Logout) dengan Laravel Breeze | Hidayathul Fikri | ✅ Selesai |
| SP1-03 | Migrasi database awal (`users`, `obat`, `kategori_obat`) | Farid Yahya | ✅ Selesai |
| SP1-04 | Fitur CRUD data obat oleh admin | Hidayathul Fikri | ✅ Selesai |
| SP1-05 | Pembuatan halaman katalog obat untuk pelanggan | Nabilla Fitricia Hernanda | ✅ Selesai |
| SP1-06 | Implementasi halaman profil apotek | Rury Fezriani Matari | ✅ Selesai |

---

### Sprint 2: Keranjang Belanja & Otorisasi Pengguna
**Periode:** 21 April - 10 Mei 2025  
**Target Rilis:** `v0.2.0`  
**Tujuan Sprint:** Membatasi akses menu menggunakan Spatie Laravel-Permission, membuat halaman keranjang belanja (cart) interaktif, dan merapikan visual layout menggunakan partials.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP2-01 | Integrasi package `spatie/laravel-permission` | Hidayathul Fikri | ✅ Selesai |
| SP2-02 | Setup middleware proteksi route (admin vs pelanggan) | Hidayathul Fikri | ✅ Selesai |
| SP2-03 | Pembuatan dashboard admin dengan data statistik ringkas | Rury Fezriani Matari | ✅ Selesai |
| SP2-04 | Fitur CRUD kategori obat oleh admin | Farid Yahya | ✅ Selesai |
| SP2-05 | Implementasi fitur keranjang belanja (tambah, edit qty, hapus) | Nabilla Fitricia Hernanda | ✅ Selesai |
| SP2-06 | Refactoring: Pemindahan logic validasi stok ke `StokService` | Hidayathul Fikri | ✅ Selesai |
| SP2-07 | Refactoring: Pemisahan layouts Blade ke partials (`_navbar`, `_sidebar`, `_footer`) | Rury Fezriani Matari | ✅ Selesai |

---

### Sprint 3: Alur Checkout, Pemesanan & Cetak PDF
**Periode:** 11 Mei - 1 Juni 2025  
**Target Rilis:** `v0.3.0`  
**Tujuan Sprint:** Menyelesaikan alur checkout transaksi, menangani upload resep dokter untuk obat keras, pelacakan status pesanan secara real-time, dan ekspor struk dalam format PDF.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP3-01 | Alur checkout pesanan dari keranjang belanja | Hidayathul Fikri | ✅ Selesai |
| SP3-02 | Form upload resep dokter untuk pembelian obat keras | Nabilla Fitricia Hernanda | ✅ Selesai |
| SP3-03 | Pilihan metode pembayaran (Transfer Bank / COD) | Farid Yahya | ✅ Selesai |
| SP3-04 | Halaman dan fitur tracking status pesanan | Rury Fezriani Matari | ✅ Selesai |
| SP3-05 | Integrasi package `barryvdh/laravel-dompdf` | Hidayathul Fikri | ✅ Selesai |
| SP3-06 | Fitur cetak struk PDF pesanan pelanggan | Farid Yahya | ✅ Selesai |

---

### Sprint 4: Dashboard Grafik, Notifikasi & Fitur AI (Direncanakan)
**Periode:** Juni 2025 - Selesai  
**Target Rilis:** `v0.4.0` (Unreleased)  
**Tujuan Sprint:** Meningkatkan pengalaman pengguna dengan visualisasi grafik penjualan, notifikasi email, dan asisten rekomendasi obat berbasis AI.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP4-01 | Fitur rekomendasi obat berbasis AI (keluhan/gejala -> obat) | Hidayathul Fikri | ⏳ Direncanakan |
| SP4-02 | Notifikasi perubahan status pesanan otomatis via email | Farid Yahya | ⏳ Direncanakan |
| SP4-03 | Dashboard laporan penjualan dengan grafik interaktif | Nabilla Fitricia Hernanda | ⏳ Direncanakan |

---

## Cara Membaca Status Sprint

- ✅ **Selesai**: Fitur telah diimplementasikan, diuji melalui manual testing, dan lolos uji Continuous Integration (CI).
- 🔄 **Sedang Berjalan**: Fitur sedang dikembangkan di branch terkait.
- ⏳ **Direncanakan**: Fitur masuk backlog masa depan dan belum dikerjakan.
