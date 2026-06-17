# Change Request: Fitur Konversi Satuan Obat (Kardus ke Box) pada Form Input Admin

## Latar Belakang

Satuan obat pada inventaris harus disamakan ke hitungan terkecil (eceran) agar tidak terjadi error pada saat transaksi penjualan di kasir. 

Sebagai contoh, jika di database stok tercatat sebesar 2 Kardus dan ada pasien membeli obat eceran sebanyak 3 Box, sistem akan mengalami kegagalan kalkulasi pengurangan (2 Kardus dikurangi 3 Box). Namun, jika dari awal input barang (2 Kardus) sudah dikonversi otomatis oleh sistem menjadi 48 Box, maka saat terjadi transaksi pembelian 3 Box, sistem dapat melakukan pengurangan matematika biasa dengan benar: 48 - 3 = 45 Box.

## Deskripsi Perubahan

Sistem menambahkan opsi pilihan satuan baru pada form tambah dan edit obat oleh admin:

1. Satuan **Box** (Stok disimpan apa adanya).
2. Satuan **Kardus** (Stok dikonversi otomatis dengan pengali 24).

Jika admin memilih satuan "Kardus", controller di backend akan otomatis mengalikan angka stok yang diinput dengan 24 dan mengubah satuan obat menjadi "box" sebelum data disimpan ke dalam database.

Contoh:
- Input Stok: 2
- Pilihan Satuan: Kardus
- Konversi Sistem: 2 * 24 = 48
- Tersimpan di Database: 48 Box

Perhitungan konversi dilakukan di backend (controller) untuk menjamin validitas data dan menghindari manipulasi input dari frontend.

## Tujuan

- Menyediakan dropdown pilihan satuan ("Box" dan "Kardus") pada antarmuka admin.
- Melakukan konversi otomatis stok obat satuan Kardus ke satuan terkecil Box (dikali 24) di backend.
- Menyimpan stok dalam unit eceran agar transaksi penjualan kasir berjalan lancar.
- Menjaga data obat lama dengan satuan non-standar (seperti botol, strip, dll.) tetap terbaca dengan normal di form edit.

## Dampak Perubahan

| Komponen | Terdampak | Keterangan |
|---|---|---|
| Database | Tidak | Tidak membutuhkan migrasi kolom baru; kolom `stock` (integer) dan `unit` (string) yang ada sudah mencukupi. |
| Model | Tidak | Model `Medicine` menggunakan struktur fillable yang sudah ada untuk menyimpan `stock` dan `unit`. |
| Controller | Ya | `AdminController` pada method `storeMedicine` dan `updateMedicine` mengalikan stok dengan 24 dan memaksa unit menjadi 'box' jika input unit adalah 'kardus'. |
| Request Validation | Ya | Menambahkan validasi `unit` sebagai input wajib (required) pada penyimpanan dan pembaruan obat. |
| View Pelanggan | Tidak | Tidak ada perubahan visual langsung, namun stok obat yang ditampilkan di detail produk dan keranjang akan otomatis menggunakan nilai hasil konversi. |
| View Admin (Tambah) | Ya | Form tambah obat (`medicines_create.blade.php`) diubah dari 2 kolom menjadi 3 kolom untuk menambahkan dropdown pilihan satuan Box/Kardus. |
| View Admin (Edit) | Ya | Form edit obat (`medicines_edit.blade.php`) diubah dari 2 kolom menjadi 3 kolom untuk menambahkan dropdown pilihan satuan serupa dengan *selection preservation* untuk satuan kustom lama. |
| Route | Tidak | Route resource admin obat yang ada tetap digunakan tanpa perubahan. |
| Middleware | Tidak | Hak akses kontrol panel admin tetap aman menggunakan middleware `auth` dan pengecekan role. |
| Dokumentasi | Ya | File `Docs/CHANGELOG.md` diperbarui untuk mencatat rilis `[v0.4.1]` terkait fitur konversi satuan obat. |
| Testing | Ya | Memastikan seluruh fungsi dasar controller dan routing tetap berjalan sukses melalui unit/feature testing. |

## Risiko

- Admin salah memilih opsi satuan Box atau Kardus saat input barang (human error), yang berakibat pada ketidaksesuaian stok nyata.
- Retur barang dengan jumlah ganjil atau transaksi ganjil berpotensi meninggalkan sisa desimal jika tidak ditangani sebagai integer bulat.
- Data lama yang tidak menggunakan Box/Kardus (seperti botol) bisa terpaksa diubah jika admin mengedit obat tersebut tanpa memeriksa ulang dropdown satuannya.

## Rencana Implementasi

1. **Membuat/checkout branch khusus**:  
   `change-request`

2. **Memperbarui halaman tambah obat**:  
   - Mengubah grid layout `medicines_create.blade.php` menjadi 3 kolom.
   - Menambahkan elemen `<select name="unit">` dengan pilihan Box dan Kardus.

3. **Memperbarui halaman edit obat**:  
   - Mengubah grid layout `medicines_edit.blade.php` menjadi 3 kolom.
   - Menambahkan dropdown serupa yang otomatis memilih opsi tersimpan dan tetap mempertahankan opsi satuan lama di luar Box/Kardus (seperti Botol/Strip).

4. **Memperbarui controller admin**:  
   - Menambahkan validasi input `unit` pada `storeMedicine` dan `updateMedicine`.
   - Mengimplementasikan logika matematika: jika `$request->unit` bernilai `'kardus'`, maka `$stock = $request->stock * 24` dan `$unit = 'box'`.

5. **Memperbarui dokumentasi changelog**:  
   - Mencatat penambahan fitur di `Docs/CHANGELOG.md` di bawah versi `[v0.4.1]`.

6. **Menjalankan verifikasi akhir**:  
   - Menjalankan perintah `php artisan test` untuk memastikan fungsionalitas sistem berjalan normal.

## Acceptance Criteria

- Form tambah dan edit obat menyediakan dropdown satuan "Box" dan "Kardus".
- Menginput stok `2` dengan satuan `Kardus` otomatis menyimpan stok senilai `48` dan unit `'box'` ke database.
- Menginput stok `10` dengan satuan `Box` menyimpan stok senilai `10` dan unit `'box'` ke database.
- Satuan kustom pada data obat lama tetap tampil terpilih dengan benar di form edit obat.
- Seluruh pengujian sistem (`php artisan test`) berhasil dilewati tanpa error.
