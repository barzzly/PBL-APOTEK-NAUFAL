# Analisis Dependency Laravel pada Proyek Sistem Informasi Apotek Naufal

## Pendahuluan

Dalam pengembangan Sistem Informasi Apotek Naufal, penggunaan dependency atau package pihak ketiga diperlukan untuk mempercepat proses pengembangan aplikasi. Dependency memungkinkan pengembang memanfaatkan fitur yang telah tersedia tanpa harus membangun seluruh fungsionalitas dari awal. Pemilihan dependency dilakukan berdasarkan kebutuhan sistem, kemudahan implementasi, keamanan, serta kompatibilitas dengan framework Laravel yang digunakan.

---

# 1. Spatie Laravel Permission

## What (Apa)

Spatie Laravel Permission merupakan package Laravel yang menyediakan fitur manajemen Role-Based Access Control (RBAC). Package ini memungkinkan sistem untuk mengelola role dan permission pengguna secara fleksibel.

## Why (Mengapa)

Pada Sistem Informasi Apotek terdapat beberapa jenis pengguna dengan hak akses yang berbeda, seperti:

* Administrator
* Apoteker
* Kasir
* Pemilik Apotek

Setiap pengguna hanya boleh mengakses fitur sesuai tugas dan tanggung jawabnya. Oleh karena itu diperlukan mekanisme pengaturan hak akses yang terstruktur dan aman.

Tanpa package ini, pengembang harus membuat sistem role dan permission secara manual yang membutuhkan waktu lebih lama serta berpotensi menimbulkan kesalahan implementasi.

## Who (Siapa)

Dependency ini digunakan oleh:

* Administrator
* Apoteker
* Kasir
* Pemilik Apotek

Selain digunakan oleh pengguna sistem, package ini juga membantu developer dalam mengelola hak akses secara lebih efisien.

## When (Kapan)

Dependency digunakan ketika:

* Pengguna melakukan login
* Sistem memverifikasi hak akses pengguna
* Pengguna membuka menu tertentu
* Pengguna melakukan operasi tambah, ubah, hapus, atau melihat data

## Where (Di Mana)

Dependency ini digunakan pada:

* Modul Login
* Modul Manajemen User
* Modul Hak Akses
* Modul Data Obat
* Modul Transaksi Penjualan
* Modul Laporan

## How (Bagaimana)

Implementasi dilakukan melalui Composer:

```bash
composer require spatie/laravel-permission
```

Package kemudian dihubungkan dengan model User dan digunakan untuk memberikan role serta permission kepada setiap pengguna.

Contoh:

* Admin → akses penuh
* Apoteker → mengelola data obat
* Kasir → melakukan transaksi
* Pemilik → melihat laporan

## Risiko Dependency

* Ketergantungan terhadap package pihak ketiga.
* Potensi perubahan API pada versi terbaru.
* Kesalahan konfigurasi dapat menyebabkan pengguna memperoleh akses yang tidak sesuai.

## Manfaat

* Mempercepat implementasi sistem hak akses.
* Meningkatkan keamanan aplikasi.
* Memudahkan pengelolaan pengguna.

## Referensi

https://spatie.be/docs/laravel-permission

---

# 2. Laravel DomPDF

## What (Apa)

Laravel DomPDF merupakan package yang digunakan untuk menghasilkan dokumen PDF dari tampilan HTML atau Blade Laravel.

## Why (Mengapa)

Sistem Informasi Apotek memerlukan fitur pencetakan laporan untuk kebutuhan administrasi dan dokumentasi.

Beberapa laporan yang perlu dicetak:

* Laporan Penjualan Obat
* Laporan Stok Obat
* Laporan Transaksi
* Laporan Obat Kadaluarsa

Format PDF dipilih karena mudah dibagikan, dicetak, dan memiliki tampilan yang konsisten.

## Who (Siapa)

Dependency digunakan oleh:

* Administrator
* Pemilik Apotek
* Apoteker

## When (Kapan)

Dependency digunakan ketika:

* Pengguna menekan tombol Cetak Laporan
* Pengguna mengunduh laporan
* Sistem menghasilkan dokumen arsip

## Where (Di Mana)

Digunakan pada:

* Modul Laporan Penjualan
* Modul Laporan Stok
* Modul Laporan Transaksi
* Modul Laporan Obat

## How (Bagaimana)

Implementasi dilakukan menggunakan Composer:

```bash
composer require barryvdh/laravel-dompdf
```

Data yang ditampilkan pada halaman Blade kemudian dikonversi menjadi file PDF menggunakan library DomPDF.

## Risiko Dependency

* Membutuhkan memori lebih besar untuk laporan dengan data banyak.
* Tidak semua CSS didukung secara sempurna.
* Proses generate PDF dapat memperlambat aplikasi jika data terlalu besar.

## Manfaat

* Mempermudah pembuatan laporan.
* Menghasilkan dokumen profesional.
* Mengurangi pekerjaan manual pengguna.

## Referensi

https://github.com/barryvdh/laravel-dompdf

---

# 3. Laravel Debugbar

## What (Apa)

Laravel Debugbar adalah package yang digunakan untuk membantu proses debugging dan monitoring aplikasi selama tahap pengembangan.

## Why (Mengapa)

Saat membangun Sistem Informasi Apotek, developer perlu mengetahui:

* Query database yang dijalankan
* Error aplikasi
* Waktu eksekusi program
* Penggunaan memori

Informasi tersebut sangat membantu dalam proses pengembangan dan optimasi sistem.

## Who (Siapa)

Dependency digunakan oleh:

* Programmer
* Tim Pengembang

Dependency ini tidak digunakan langsung oleh pengguna akhir.

## When (Kapan)

Digunakan selama:

* Pengembangan aplikasi
* Pengujian fitur
* Perbaikan bug
* Optimasi performa

## Where (Di Mana)

Digunakan pada seluruh modul aplikasi selama tahap development.

## How (Bagaimana)

Instalasi dilakukan menggunakan Composer:

```bash
composer require barryvdh/laravel-debugbar --dev
```

Setelah terpasang, Debugbar akan menampilkan informasi debugging pada bagian bawah halaman aplikasi.

## Risiko Dependency

* Tidak boleh digunakan pada server production.
* Dapat menampilkan informasi sensitif apabila tidak dikonfigurasi dengan baik.

## Manfaat

* Mempercepat pencarian bug.
* Membantu optimasi query database.
* Mempermudah monitoring performa aplikasi.

## Referensi

https://github.com/barryvdh/laravel-debugbar

---

# Kesimpulan

Dependency merupakan komponen penting dalam pengembangan perangkat lunak modern karena dapat mempercepat proses pembangunan aplikasi dan meningkatkan kualitas sistem. Pada proyek Sistem Informasi Apotek Naufal, dependency yang dipilih adalah Spatie Laravel Permission untuk manajemen hak akses, Laravel DomPDF untuk pembuatan laporan PDF, dan Laravel Debugbar untuk membantu proses debugging. Ketiga dependency tersebut dipilih karena sesuai dengan kebutuhan sistem, mudah diintegrasikan dengan Laravel, serta mendukung proses pengembangan aplikasi secara lebih efektif dan efisien.
