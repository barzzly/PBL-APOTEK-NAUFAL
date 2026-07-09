# Dokumentasi Fitur — Website Apotek Naufal

Dokumen ini menjelaskan seluruh fitur utama yang tersedia pada aplikasi Apotek Naufal beserta route dan controller yang mengelolanya.

---

## 1. Login, Register, & Profil Pengguna

**Tujuan:**
Mengamankan akses aplikasi agar pengguna dapat memesan obat, melihat riwayat transaksi, membuat tiket konsultasi, serta mengelola data profil pribadi dan kata sandi mereka.

**Aktor:** Customer, Admin

**Pemberlakuan Role:**
Aplikasi menggunakan kolom `role` (bernilai `admin` atau `customer`) pada tabel `users`.
* Setelah login, **Admin** diarahkan ke `/admin/dashboard`.
* **Customer** diarahkan ke halaman utama `/`.
* Saat login/register berhasil, sistem secara otomatis mensinkronisasikan isi keranjang belanja dari Session (guest) ke database.

**Alur Fitur Profil:**
1. Customer yang telah login mengakses halaman profil di `/profile`.
2. Customer dapat memperbarui nama, email, nomor telepon, alamat, dan mengunggah foto profil (avatar) yang disimpan di storage publik (`storage/app/public/avatars`).
3. Customer dapat memperbarui kata sandi dengan memasukkan kata sandi lama untuk diverifikasi via `Hash::check()`.

**Route & Controller:**

| Method | Route | Controller | Action / Cara Kerja |
|---|---|---|---|
| GET | `/login` | - | *Closure* (menampilkan view `auth.login`) |
| POST | `/login` | `AuthController` | `login` (validasi dan regenerasi session) |
| GET | `/register` | - | *Closure* (menampilkan view `auth.register`) |
| POST | `/register` | `AuthController` | `register` (membuat user baru dengan role `customer`) |
| POST | `/logout` | `AuthController` | `logout` (menghapus session) |
| GET | `/profile` | `ProfileController` | `edit` (menampilkan form edit profil & avatar) |
| POST | `/profile` | `ProfileController` | `update` (menyimpan perubahan profil & kata sandi baru) |

---

## 2. Katalog Obat

**Tujuan:**
Menampilkan daftar obat yang aktif kepada customer dengan fitur pencarian, filter kategori, detail obat, serta rating dan ulasan.

**Aktor:** Customer (guest & login), Admin

**Alur Fitur:**
1. Customer mengakses halaman utama `/` atau kategori khusus `/kategori/{slug}`.
2. Fitur pencarian menyaring obat berdasarkan nama, merek, indikasi, atau deskripsi obat.
3. Fitur Auto-Suggestions (AJAX autocomplete) di bilah pencarian header memberikan saran obat secara dinamis.
4. Klik obat mengarah ke `/obat/{slug}` untuk melihat informasi detail, sisa stok, ulasan pengguna, dan memberikan ulasan baru (untuk user login).
5. Admin mengelola data obat dan kategori melalui menu admin khusus.
6. Admin dapat memanfaatkan **Gemini AI** untuk men-generate deskripsi obat secara otomatis berdasarkan nama dan kategori obat.

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/` | `HomeController` | `index` (katalog utama / landing page) |
| GET | `/kategori/{slug}` | `HomeController` | `category` (filter berdasarkan kategori) |
| GET | `/obat/{slug}` | `HomeController` | `show` (detail obat & daftar ulasan) |
| GET | `/search-suggestions` | `HomeController` | `suggestions` (saran pencarian dinamis / autocomplete) |
| GET | `/admin/categories` | `AdminController` | `categories` (daftar kategori admin) |
| GET | `/admin/categories/create`| `AdminController` | `createCategory` (form tambah kategori) |
| POST | `/admin/categories` | `AdminController` | `storeCategory` (simpan kategori baru) |
| GET | `/admin/categories/{id}/edit`| `AdminController` | `editCategory` (form edit kategori) |
| PUT | `/admin/categories/{id}` | `AdminController` | `updateCategory` (perbarui kategori) |
| DELETE| `/admin/categories/{id}` | `AdminController` | `deleteCategory` (hapus kategori) |
| GET | `/admin/medicines` | `AdminController` | `medicines` (daftar obat admin) |
| GET | `/admin/medicines/create`| `AdminController` | `createMedicine` (form tambah obat) |
| POST | `/admin/medicines` | `AdminController` | `storeMedicine` (simpan obat baru) |
| GET | `/admin/medicines/{id}/edit`| `AdminController` | `editMedicine` (form edit obat) |
| PUT | `/admin/medicines/{id}` | `AdminController` | `updateMedicine` (perbarui obat) |
| DELETE| `/admin/medicines/{id}` | `AdminController` | `deleteMedicine` (hapus obat) |
| POST | `/admin/medicines/generate-description` | `AdminController` | `generateDescription` (generate deskripsi obat via Gemini AI) |

---

## 3. Keranjang Belanja (Cart)

**Tujuan:**
Memungkinkan customer menampung obat-obatan sebelum melanjutkan ke proses checkout.

**Aktor:** Customer (guest dan login)

**Alur Fitur:**
1. Jika belum login, keranjang belanja disimpan di dalam **Session** (`session('cart')`).
2. Jika sudah login, keranjang disimpan di database pada tabel `cart_items`.
3. Validasi stok secara real-time dilakukan ketika menambahkan, mengubah jumlah (quantity), atau memvalidasi barang saat checkout.
4. Perubahan jumlah obat dan penghapusan item keranjang dilakukan menggunakan AJAX sehingga halaman tidak perlu dimuat ulang.

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/cart` | `CartController` | `index` (tampilan keranjang belanja) |
| POST | `/cart/add` | `CartController` | `add` (tambah item ke keranjang / sesuaikan kuantitas dengan stok) |
| POST | `/cart/update` | `CartController` | `update` (ubah kuantitas item) |
| POST | `/cart/remove` | `CartController` | `remove` (hapus item dari keranjang) |

---

## 4. Checkout & Pemesanan Obat

**Tujuan:**
Memproses obat yang ada di keranjang menjadi pesanan (*Order*) resmi di database, menghitung ongkos kirim, dan mengelola bukti pembayaran.

**Aktor:** Customer (harus login)

**Alur Fitur:**
1. Halaman checkout mengonfirmasi daftar obat.
2. Memilih tipe pesanan: **Pengambilan di Apotek (Pickup)** atau **Pengiriman (Delivery)**.
3. Memilih metode pembayaran: **Cash** (hanya untuk Pickup), **Transfer Bank**, atau **QRIS**.
4. Jika memilih **Delivery**, peta/koordinat pengiriman diset, dan jarak dihitung menggunakan API OSRM (Open Source Routing Machine) dengan fallback formula Haversine. Tarif ongkir disesuaikan dinamis: **Rp2.500 per km** (minimum Rp10.000, maksimum 50 km).
5. Penyimpanan order dilakukan dalam satu transaksi database (`DB::transaction`) untuk memastikan data konsisten dan langsung memotong stok obat.
6. Customer mengunggah bukti transfer jika menggunakan metode non-tunai.

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/checkout` | `CheckoutController` | `index` (tampilan checkout obat) |
| GET | `/checkout/calculate-distance`| `CheckoutController` | `calculateDistance` (hitung jarak pengiriman via OSRM/Haversine) |
| POST | `/checkout` | `CheckoutController` | `store` (pembuatan pesanan baru & pengurangan stok) |
| GET | `/orders/success` | `CheckoutController` | `success` (halaman pesanan berhasil dibuat) |
| POST | `/orders/{id}/upload-payment`| `CheckoutController` | `uploadPaymentProof` (unggah bukti pembayaran) |

---

## 5. Tracking Status Pesanan

**Tujuan:**
Memberikan visibilitas status pesanan secara real-time kepada customer dan mempermudah admin mengelola status transaksi.

**Aktor:** Customer, Admin

**Daftar Status Pesanan:**
* `pending`: Pesanan baru dibuat, menunggu verifikasi pembayaran (jika transfer/QRIS) atau konfirmasi admin.
* `confirmed`: Pembayaran dikonfirmasi, pesanan disetujui.
* `processing`: Apoteker sedang menyiapkan obat.
* `ready_for_pickup`: Obat siap diambil di apotek (tipe *pickup*).
* `shipped`: Obat sedang dikirim oleh kurir (tipe *delivery*).
* `delivered`: Obat telah diterima oleh pelanggan (selesai).
* `cancelled`: Pesanan dibatalkan (stok obat akan otomatis dikembalikan ke sistem).

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/orders` | `CheckoutController` | `history` (daftar riwayat transaksi customer) |
| GET | `/orders/{id}` | `CheckoutController` | `show` (detail dan tracking pesanan customer) |
| GET | `/admin/orders` | `AdminController` | `orders` (daftar semua pesanan masuk bagi admin) |
| GET | `/admin/orders/{id}` | `AdminController` | `showOrder` (detail pesanan masuk admin) |
| POST | `/admin/orders/{id}/update-status` | `AdminController` | `updateOrderStatus` (update status & pengembalian stok jika dibatalkan) |

---

## 6. Ulasan & Rating Obat

**Tujuan:**
Memungkinkan customer yang telah login memberikan penilaian bintang (1-5) dan ulasan tertulis pada produk obat yang dibeli.

**Aktor:** Customer (harus login)

**Alur Fitur:**
1. Di halaman detail obat `/obat/{slug}`, customer dapat melihat daftar ulasan, rata-rata rating, dan jumlah rating obat tersebut.
2. User terautentikasi dapat mengisi rating berupa bintang 1 sampai 5 beserta komentar ulasan (dibatasi 1 ulasan per user per obat).

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| POST | `/obat/{slug}/review` | `HomeController` | `storeReview` (menyimpan rating & ulasan baru) |

---

## 7. Chat & Tiket Konsultasi / Resep Dokter

**Tujuan:**
Memberikan layanan konsultasi medis online dengan apoteker dan mempermudah penebusan resep dokter secara digital.

**Aktor:** Customer, Admin (Apoteker)

**Alur Fitur:**
1. Customer mengunggah foto resep dokter (`/tickets/upload`) atau membuat konsultasi keluhan keluhan kesehatan (`/tickets/consult`).
2. Tiket baru mendapatkan nomor unik: `RX-YYYYMMDD-XXXXX` (resep) atau `TK-YYYYMMDD-XXXXX` (konsultasi).
3. Terjadi obrolan langsung (live chat) via AJAX polling di halaman detail tiket (`/tickets/room/{id}`).
4. Apoteker (admin) memeriksa resep atau keluhan, lalu merekomendasikan obat dengan cara **memasukkan obat secara langsung ke keranjang belanja customer** melalui sistem admin tiket (`/admin/tickets/{id}/add-medicine`).
5. Setelah selesai, apoteker mengubah status tiket menjadi `completed`, yang otomatis memicu pesan sistem di chat room agar customer bisa langsung melakukan checkout obat di keranjang mereka.
6. Gambar resep dokter dilindungi secara ketat via middleware akses khusus (`CheckoutController@viewPrescription`) agar hanya pemilik tiket dan admin yang bisa melihat berkas resep tersebut.

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/tickets/upload` | `PrescriptionController` | `create` (form unggah resep dokter) |
| POST | `/tickets/upload` | `PrescriptionController` | `store` (menyimpan tiket resep baru & file resep) |
| GET | `/tickets/consult` | `PrescriptionController` | `createConsult` (form konsultasi apoteker) |
| POST | `/tickets/consult` | `PrescriptionController` | `storeConsult` (menyimpan tiket konsultasi keluhan) |
| GET | `/tickets/history` | `PrescriptionController` | `history` (riwayat tiket konsultasi/resep customer) |
| GET | `/tickets/room/{id}` | `PrescriptionController` | `show` (ruang chat tiket customer) |
| POST | `/tickets/room/{id}/message`| `PrescriptionController` | `sendMessage` (kirim chat dari customer) |
| GET | `/tickets/room/{id}/messages`| `PrescriptionController` | `getMessages` (ambil/sync pesan via AJAX customer) |
| GET | `/tickets/file/{filename}` | `CheckoutController` | `viewPrescription` (view file resep dengan proteksi keamanan) |
| GET | `/admin/tickets` | `AdminPrescriptionController`| `index` (daftar seluruh tiket masuk bagi admin) |
| GET | `/admin/tickets/{id}` | `AdminPrescriptionController`| `show` (ruang chat & keranjang manajemen admin) |
| POST | `/admin/tickets/{id}/message`| `AdminPrescriptionController`| `sendMessage` (kirim chat dari apoteker) |
| GET | `/admin/tickets/{id}/messages`| `AdminPrescriptionController`| `getMessages` (sync pesan via AJAX admin) |
| POST | `/admin/tickets/{id}/status` | `AdminPrescriptionController`| `changeStatus` (ubah status tiket: processing / completed / rejected) |
| POST | `/admin/tickets/{id}/add-medicine` | `AdminPrescriptionController`| `addMedicine` (apoteker menambahkan obat ke keranjang customer) |
| DELETE| `/admin/tickets/{id}/remove-medicine/{itemId}` | `AdminPrescriptionController`| `removeMedicine` (apoteker menghapus obat dari keranjang customer) |

---

## 8. Dashboard Grafik Laporan Penjualan (Admin)

**Tujuan:**
Menyediakan grafik interaktif dan statistik ringkas performa penjualan apotek dalam rentang waktu tertentu bagi admin.

**Aktor:** Admin

**Alur Fitur:**
1. Grafik menampilkan data Pendapatan Harian (dari order berstatus `paid`) dan Jumlah Transaksi Harian dalam periode terpilih (7 hari, 30 hari, atau kustom tanggal).
2. Menampilkan informasi ringkas: Total Pendapatan, Total Order, Order Selesai, dan Order Dibatalkan.
3. Menampilkan daftar **10 Obat Terlaris** berdasarkan kuantitas penjualan beserta total pendapatan yang dihasilkan.

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/admin/laporan-penjualan` | `AdminController` | `laporanPenjualan` (halaman laporan & statistik obat terlaris) |
| GET | `/admin/laporan-penjualan/chart-data` | `AdminController` | `laporanChartData` (JSON API untuk penyuplai data Chart.js) |

---

## 9. Sistem Notifikasi Polling Admin

**Tujuan:**
Memberikan notifikasi instan secara visual di panel admin ketika terdapat aktivitas penting atau masalah pada persediaan.

**Aktor:** Admin

**Jenis Notifikasi:**
1. **Stok Habis (Penting):** Terpicu saat stok obat bernilai 0.
2. **Stok Hampir Habis (Restok):** Terpicu saat stok obat di bawah 30.
3. **Orderan Masuk (Pending):** Terpicu ketika ada pesanan baru berstatus `pending`.
4. **Tiket Baru:** Terpicu ketika ada tiket konsultasi atau resep baru yang membutuhkan verifikasi apoteker.

**Route & Controller:**

| Method | Route | Controller | Action |
|---|---|---|---|
| GET | `/admin/notifications/fetch` | `AdminController` | `fetchNotifications` (AJAX polling notifikasi menggunakan `NotificationService`) |
