# Dokumentasi Sprint Planning — Website Apotek Naufal

Dokumen ini menjelaskan perencanaan pengerjaan (sprint planning) proyek pengembangan Website Apotek Naufal, yang dibagi ke dalam beberapa sprint berdasarkan metodologi Scrum/Agile.

---

## Ringkasan Tim & Peran

| Nama | Peran | Deskripsi |
|---|---|---|
| **Farid Yahya** | Database Engineer / Backend Developer | Mengembangkan skema migrasi database, optimasi query relasional, dan seeder data. |
| **Hidayathul Fikri** | Backend Developer / Scrum Master | Mengelola core logic, arsitektur database, otorisasi, integrasi library, dan workflow. |
| **Nabilla Fitricia Hernanda** | Frontend Developer / UI Designer | Mengembangkan desain tampilan (Blade), styling responsif, interaksi keranjang, upload berkas, dan visualisasi data. |
| **Rury Fezriani Matari** | UI/UX Designer / Frontend Developer | Mengembangkan desain antarmuka pengguna (layouts), elemen blade partials, dan gaya visual responsif. |

---

## Struktur Sprint

### Sprint 1: Setup Proyek & Autentikasi Dasar
**Target Rilis:** `v0.1.0`  
**Tujuan Sprint:** Menyiapkan struktur dasar aplikasi Laravel 13, sistem autentikasi pengguna, migrasi tabel-tabel utama, serta halaman katalog obat dasar.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP1-01 | Inisiasi proyek Laravel 13 & setup repository GitHub | Hidayathul Fikri | ✅ Selesai |
| SP1-02 | Setup autentikasi (Login, Register, Logout) dengan AuthController kustom | Hidayathul Fikri | ✅ Selesai |
| SP1-03 | Migrasi database awal (`users`, `medicines`, `categories`) | Farid Yahya | ✅ Selesai |
| SP1-04 | Fitur CRUD data obat oleh admin | Hidayathul Fikri | ✅ Selesai |
| SP1-05 | Pembuatan halaman katalog obat untuk pelanggan | Nabilla Fitricia Hernanda | ✅ Selesai |
| SP1-06 | Implementasi halaman profil apotek | Rury Fezriani Matari | ✅ Selesai |

---

### Sprint 2: Keranjang Belanja & Otorisasi Pengguna
**Target Rilis:** `v0.2.0`  
**Tujuan Sprint:** Membatasi akses menu menggunakan kolom role di user database, membuat halaman keranjang belanja (cart) interaktif (AJAX), dan merapikan arsitektur kode.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP2-01 | Setup kolom role (`admin` / `customer`) di database dan integrasi ke Auth | Hidayathul Fikri | ✅ Selesai |
| SP2-02 | Proteksi route admin menggunakan middleware autentikasi | Hidayathul Fikri | ✅ Selesai |
| SP2-03 | Pembuatan dashboard admin dengan data statistik ringkas | Rury Fezriani Matari | ✅ Selesai |
| SP2-04 | Fitur CRUD kategori obat oleh admin | Farid Yahya | ✅ Selesai |
| SP2-05 | Implementasi fitur keranjang belanja guest (Session) dan user (Database) via AJAX | Nabilla Fitricia Hernanda | ✅ Selesai |
| SP2-06 | Refactoring: Pemindahan logic bisnis eksternal ke Service Class (`GeminiService` & `NotificationService`) | Hidayathul Fikri | ✅ Selesai |
| SP2-07 | Refactoring: Pemisahan layouts admin ke master layout (`admin/layout.blade.php`) | Rury Fezriani Matari | ✅ Selesai |

---

### Sprint 3: Alur Checkout, Ongkir Dinamis, Rating & Chat Konsultasi
**Target Rilis:** `v0.3.0`  
**Tujuan Sprint:** Menyelesaikan alur checkout transaksi, kalkulasi ongkos kirim berbasis lokasi, pelacakan status pesanan secara real-time, sistem rating produk, dan konsultasi chat.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP3-01 | Alur checkout pesanan dari keranjang belanja | Hidayathul Fikri | ✅ Selesai |
| SP3-02 | Form upload resep dokter untuk pembelian obat resep keras | Nabilla Fitricia Hernanda | ✅ Selesai |
| SP3-03 | Pilihan metode pembayaran (Transfer Bank, COD, QRIS, BPJS) | Farid Yahya | ✅ Selesai |
| SP3-04 | Halaman dan fitur tracking status pesanan beserta auto-restock jika batal | Rury Fezriani Matari | ✅ Selesai |
| SP3-05 | Integrasi OSRM API & Haversine formula untuk perhitungan ongkir delivery dinamis | Hidayathul Fikri | ✅ Selesai |
| SP3-06 | Pengembangan ruang live chat konsultasi dokter & resep (`/tickets/room/{id}`) | Farid Yahya | ✅ Selesai |
| SP3-07 | Fitur ulasan (review) dan penilaian (rating) obat oleh pelanggan | Nabilla Fitricia Hernanda | ✅ Selesai |

---

### Sprint 4: Dashboard Grafik, Polling Notifikasi & Fitur AI (Released)
**Target Rilis:** `v0.4.0`  
**Tujuan Sprint:** Meningkatkan pengalaman admin dengan visualisasi grafik penjualan interaktif, sistem notifikasi polling apoteker, dan asisten generator deskripsi obat bertenaga AI.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP4-01 | Fitur generator deskripsi obat otomatis menggunakan Google Gemini AI | Hidayathul Fikri | ✅ Selesai |
| SP4-02 | Fitur AJAX Polling Notifikasi admin (stok menipis, order masuk, tiket baru) | Farid Yahya | ✅ Selesai |
| SP4-03 | Dashboard laporan penjualan dengan grafik Chart.js interaktif | Nabilla Fitricia Hernanda | ✅ Selesai |
| SP4-04 | Fitur pengelolaan profil pengguna (edit data & update avatar/password) | Hidayathul Fikri | ✅ Selesai |

---

### Sprint 5: Optimasi UX, Konversi Satuan, Bot WhatsApp & Halaman Hukum
**Target Rilis:** `v0.5.0`  
**Tujuan Sprint:** Meningkatkan keandalan transaksi dengan fitur konversi satuan obat otomatis di backend, silent update keranjang belanja, memperluas fitur keamanan otentikasi via WhatsApp OTP, pengiriman notifikasi pesanan via WhatsApp, serta menyediakan halaman hukum Syarat & Ketentuan serta Kebijakan Privasi yang interaktif.

| Task ID | Item Pekerjaan (Backlog) | Assignee | Status |
|---|---|---|---|
| SP5-01 | Fitur konversi satuan otomatis (Kardus ke Box) & Aksi Stok (Tambah/Kurang/Atur) di panel admin | Farid Yahya & Hidayathul Fikri | ✅ Selesai |
| SP5-02 | Input kuantitas keranjang belanja dinamis (silent update) | Nabilla Fitricia Hernanda | ✅ Selesai |
| SP5-03 | Integrasi WhatsApp Bot API untuk notifikasi status pesanan pelanggan | Farid Yahya & Hidayathul Fikri | ✅ Selesai |
| SP5-04 | Sistem autentikasi OTP WhatsApp (Login, Register & Lupa Password via WA) | Hidayathul Fikri | ✅ Selesai |
| SP5-05 | Halaman Syarat & Ketentuan & Kebijakan Privasi dengan layout interaktif (Sticky Nav & Scroll Highlighting) | Nabilla Fitricia Hernanda & Rury Fezriani Matari | ✅ Selesai |
| SP5-06 | Optimasi media aset (konversi ke WebP) & pembaruan kontak bisnis di footer | Rury Fezriani Matari | ✅ Selesai |

---

## Cara Membaca Status Sprint

- ✅ **Selesai**: Fitur telah diimplementasikan, diuji melalui manual testing, dan aktif di codebase utama.
- 🔄 **Sedang Berjalan**: Fitur sedang dikembangkan di branch terkait.
- ⏳ **Direncanakan**: Fitur masuk backlog masa depan dan belum dikerjakan.
