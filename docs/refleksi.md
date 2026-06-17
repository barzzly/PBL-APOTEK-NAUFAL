# Refleksi Kelompok: Change Impact Analysis pada Fitur Konversi Satuan Obat

## Anggota Kelompok
- Hidayathul Fikri
- Farid Yahya
- Nabilla Fitricia Hernanda
- Rury Fezriani Matari

---

## 1. Analisis Dampak Perubahan (Change Impact Analysis)
Dalam mengimplementasikan fitur konversi satuan obat (1 Kardus = 24 Box), kami melakukan pemetaan dampak terlebih dahulu. Hasil analisis dampak perubahan ini terbukti sangat membantu proses pengerjaan:
- **Peta Komponen Terpengaruh:** Kami membatasi perubahan hanya pada bagian Controller (`AdminController.php` untuk logika pengali stok) dan View Admin (`medicines_create.blade.php` & `medicines_edit.blade.php` untuk dropdown satuan).
- **Keamanan Alur Utama:** Komponen database, routing, dan middleware diidentifikasi tidak memerlukan perubahan, sehingga kami dapat menjamin alur transaksi apotek yang sudah berjalan tetap aman dan stabil.

## 2. Mitigasi Risiko
Kami memetakan dan menyelesaikan beberapa risiko penting selama implementasi:
- **Kompatibilitas Satuan Lama:** Kami merancang dropdown pada form edit agar tetap menampilkan dan mempertahankan satuan lama (seperti botol, strip, pcs) agar data obat lama tidak rusak saat admin melakukan update.
- **Konsistensi di Backend:** Logika konversi (mengalikan jumlah dengan 24 jika memilih satuan Kardus) diproses seluruhnya di backend sebelum data masuk ke database, guna mencegah manipulasi data dari frontend.

## 3. Kesimpulan Refleksi
Sebagai penutup dari tim kami, praktik Change Impact Analysis ini sangat membantu kolaborasi antara saya, Hidayathul, Nabilla, dan Rury. Dengan memetakan dampaknya sejak awal, kami bisa langsung fokus mengeksekusi kode di bagian yang tepat, sehingga penambahan fitur satuan ini tidak merusak alur transaksi apotek yang sudah berjalan.
