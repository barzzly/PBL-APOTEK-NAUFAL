# Dokumentasi Fitur — Website Apotek Naufal

Dokumen ini menjelaskan setiap fitur utama yang tersedia pada aplikasi.

---

## Login & Register

**Tujuan:**
Mengamankan akses aplikasi agar hanya pengguna terdaftar yang dapat memesan obat, dan memisahkan hak akses antara admin dan pelanggan.

**Aktor:** Pelanggan, Admin

**Alur Fitur:**
```
Pengguna buka /login
→ Input email & password
→ Sistem validasi kredensial via Auth::attempt()
→ Cek role pengguna (admin / pelanggan)
→ Admin → redirect /admin/dashboard
→ Pelanggan → redirect /user/dashboard
→ Jika gagal → kembali ke form login dengan pesan error
```

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/login` | `AuthController` | `showLogin` |
| POST | `/login` | `AuthController` | `login` |
| GET | `/register` | `AuthController` | `showRegister` |
| POST | `/register` | `AuthController` | `register` |
| POST | `/logout` | `AuthController` | `logout` |

> Screenshot: `screenshot/login.png`

---

## Katalog Obat

**Tujuan:**
Menampilkan daftar obat yang tersedia di apotek lengkap dengan kategori, harga, dan status stok, sehingga pelanggan dapat mencari dan memilih obat sebelum memesan.

**Aktor:** Pelanggan (guest dan login), Admin

**Alur Fitur:**
```
Pelanggan buka /katalog
→ Sistem mengambil daftar obat dari database (filter kategori opsional)
→ Tampil daftar obat dalam card grid
→ Pelanggan klik detail obat → halaman /obat/{id}
→ Pelanggan klik "Tambah ke Keranjang" → sistem cek auth
→ Jika belum login → redirect /login
→ Jika sudah login → obat masuk keranjang
```

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/katalog` | `ObatController` | `index` |
| GET | `/obat/{id}` | `ObatController` | `show` |
| GET | `/admin/obat` | `Admin\ObatController` | `index` |
| POST | `/admin/obat` | `Admin\ObatController` | `store` |
| PUT | `/admin/obat/{id}` | `Admin\ObatController` | `update` |
| DELETE | `/admin/obat/{id}` | `Admin\ObatController` | `destroy` |

> Screenshot: `screenshot/katalog.png`, `screenshot/detail-obat.png`

---

## Keranjang Belanja (Cart)

**Tujuan:**
Memungkinkan pelanggan mengumpulkan obat yang ingin dipesan sebelum melanjutkan ke proses checkout.

**Aktor:** Pelanggan (harus login)

**Alur Fitur:**
```
Pelanggan klik "Tambah ke Keranjang" di halaman katalog
→ Sistem cek stok obat
→ Jika stok cukup → obat masuk / qty bertambah di tabel cart
→ Pelanggan buka /keranjang → lihat daftar obat di cart
→ Pelanggan bisa ubah qty atau hapus item
→ Pelanggan klik "Checkout" → lanjut ke proses pemesanan
```

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| POST | `/keranjang/tambah` | `CartController` | `store` |
| GET | `/keranjang` | `CartController` | `index` |
| PATCH | `/keranjang/{id}` | `CartController` | `update` |
| DELETE | `/keranjang/{id}` | `CartController` | `destroy` |

> Screenshot: `screenshot/keranjang.png`

---

## Checkout & Pemesanan Obat

**Tujuan:**
Memproses pesanan pelanggan dari keranjang menjadi order yang tercatat di database, termasuk validasi resep dokter dan pemilihan metode pembayaran.

**Aktor:** Pelanggan (harus login)

**Alur Fitur:**
```
Pelanggan klik "Checkout" dari halaman keranjang
→ Sistem validasi: keranjang tidak boleh kosong
→ Jika ada obat keras → wajib upload resep dokter
→ Pelanggan pilih metode pembayaran (Transfer Bank / COD)
→ Pelanggan konfirmasi pesanan
→ Sistem buat record Order dan OrderItem
→ Sistem kurangi stok obat
→ Redirect ke halaman detail pesanan dengan pesan sukses
```

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/checkout` | `OrderController` | `checkout` |
| POST | `/checkout` | `OrderController` | `store` |

> Screenshot: `screenshot/checkout.png`

---

## Tracking Status Pesanan

**Tujuan:**
Memberikan visibilitas kepada pelanggan mengenai perkembangan status pesanannya secara real-time.

**Aktor:** Pelanggan, Admin

**Status yang tersedia:**

| Status | Keterangan |
|---|---|
| `menunggu_konfirmasi` | Pesanan transfer menunggu verifikasi pembayaran oleh admin |
| `diproses` | Apotek sedang menyiapkan obat |
| `dikirim` | Obat sedang dalam perjalanan ke alamat pelanggan |
| `selesai` | Pesanan telah diterima pelanggan |

**Alur Fitur:**
```
Pelanggan buka /pesanan
→ Tampil daftar seluruh pesanan milik pelanggan
→ Pelanggan klik detail pesanan → /pesanan/{id}
→ Tampil status terkini dan riwayat item pesanan
→ Admin dapat update status pesanan dari /admin/pesanan/{id}
```

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/pesanan` | `OrderController` | `index` |
| GET | `/pesanan/{id}` | `OrderController` | `show` |
| PATCH | `/admin/pesanan/{id}/status` | `Admin\OrderController` | `updateStatus` |

> Screenshot: `screenshot/tracking.png`

---

## Cetak Struk PDF

**Tujuan:**
Menghasilkan dokumen PDF berisi detail pesanan yang dapat diunduh atau dicetak oleh pelanggan sebagai bukti pembelian.

**Aktor:** Pelanggan

**Alur Fitur:**
```
Pelanggan buka halaman detail pesanan /pesanan/{id}
→ Klik tombol "Cetak Struk"
→ Sistem generate PDF via DomPDF menggunakan view Blade
→ Browser otomatis mengunduh file PDF: struk-{order_id}.pdf
```

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/pesanan/{id}/cetak` | `OrderController` | `cetakPdf` |

**Package yang digunakan:** `barryvdh/laravel-dompdf`

> Screenshot: `screenshot/cetak-pdf.png`

---

## Manajemen Role & Permission (Admin)

**Tujuan:**
Membatasi akses fitur berdasarkan peran pengguna agar admin dapat mengelola sistem sementara pelanggan hanya dapat mengakses fitur pemesanan.

**Aktor:** Admin

**Role yang tersedia:**

| Role | Akses |
|---|---|
| `admin` | Semua fitur termasuk kelola obat, pesanan, user |
| `pelanggan` | Katalog, keranjang, checkout, tracking pesanan |

**Permission yang digunakan:**

| Permission | Deskripsi |
|---|---|
| `manage-obat` | CRUD data obat dan kategori |
| `manage-pesanan` | Update status pesanan |
| `manage-user` | Kelola data pengguna |

**Package yang digunakan:** `spatie/laravel-permission`

> Screenshot: `screenshot/admin-dashboard.png`
