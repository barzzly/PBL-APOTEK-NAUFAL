# Dependency Laravel pada Proyek PBL Apotek Naufal

## 1. Spatie Laravel Permission

### What

Spatie Laravel Permission adalah package Laravel yang digunakan untuk mengelola Role dan Permission pengguna.

### Why

Package ini digunakan untuk membatasi hak akses pengguna berdasarkan peran masing-masing, seperti Admin, Apoteker, dan Kasir.

### Who

Digunakan oleh seluruh pengguna sistem yang memiliki level akses berbeda.

### When

Digunakan saat proses login dan ketika pengguna mengakses menu atau fitur tertentu.

### Where

Modul Manajemen User, Login, Role Management, dan Hak Akses Sistem.

### How

Package diinstall menggunakan Composer dan diintegrasikan dengan model User untuk mengatur role dan permission.

### Risiko Dependency

* Ketergantungan terhadap package pihak ketiga.
* Perlu pembaruan versi secara berkala untuk menjaga keamanan.

### Referensi

https://spatie.be/docs/laravel-permission

---

## 2. Laravel DomPDF

### What

Laravel DomPDF adalah package yang digunakan untuk membuat dan mengunduh dokumen PDF dari data aplikasi.

### Why

Digunakan untuk menghasilkan laporan penjualan, laporan stok obat, dan laporan transaksi dalam format PDF.

### Who

Digunakan oleh Admin dan Pemilik Apotek.

### When

Digunakan saat pengguna memilih menu cetak atau unduh laporan.

### Where

Modul Laporan Penjualan, Laporan Obat, dan Laporan Transaksi.

### How

Package diinstall melalui Composer kemudian digunakan untuk mengubah tampilan Blade menjadi file PDF.

### Risiko Dependency

* Proses generate PDF dapat memperlambat sistem jika data sangat banyak.
* Beberapa elemen CSS tidak selalu didukung sempurna.

### Referensi

https://github.com/barryvdh/laravel-dompdf

---

## 3. Laravel Excel

### What

Laravel Excel adalah package untuk melakukan import dan export data Excel.

### Why

Digunakan untuk mempermudah pengelolaan data obat dalam jumlah besar melalui file Excel.

### Who

Digunakan oleh Admin dan Apoteker.

### When

Digunakan saat menambah data obat secara massal atau mengunduh laporan dalam format Excel.

### Where

Modul Data Obat, Stok Obat, dan Laporan.

### How

Package diinstall menggunakan Composer dan memanfaatkan class Export serta Import yang disediakan package.

### Risiko Dependency

* Membutuhkan memori lebih besar ketika mengolah file Excel berukuran besar.
* Kesalahan format file dapat menyebabkan proses import gagal.

### Referensi

https://laravel-excel.com

---

## Kesimpulan

Dependency membantu mempercepat pengembangan aplikasi karena menyediakan fitur yang siap digunakan. Pada proyek Sistem Informasi Apotek, package Spatie Permission digunakan untuk manajemen hak akses, DomPDF untuk pembuatan laporan PDF, dan Laravel Excel untuk import-export data. Penggunaan dependency harus didokumentasikan dan dikelola dengan baik karena dapat memengaruhi pemeliharaan dan evolusi perangkat lunak di masa mendatang.
