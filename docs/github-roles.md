# Pembagian Peran GitHub — Website Apotek Naufal

Dokumen ini menjelaskan pembagian peran tim pada GitHub untuk proyek Website Apotek Naufal. Pembagian dibuat berdasarkan modul yang sudah ada pada repository dan catatan sprint proyek.

## Struktur Tim

| Anggota | Fokus modul | Tanggung jawab GitHub | Path utama |
| --- | --- | --- | --- |
| **Hidayathul Fikri** | Core Logic, Keamanan, Otentikasi OTP WA, Integrasi AI & Jarak | Membuat issue/branch otentikasi kustom, integrasi Gemini AI, hitung jarak OSRM, WhatsApp Notification Service | `app/Http/Controllers/AuthController.php`, `app/Http/Controllers/WhatsAppAuthController.php`, `app/Http/Controllers/CheckoutController.php`, `app/Services/GeminiService.php`, `app/Services/WhatsAppNotificationService.php`, `routes/web.php` |
| **Farid Yahya** | Database, Seeder, Manajemen Stok/Satuan, Polling Notifikasi | Membuat issue/branch skema database/migration, dummy seeder, konversi satuan otomatis, logic polling notifikasi admin | `database/migrations/`, `database/seeders/`, `app/Services/NotificationService.php`, `app/Http/Controllers/AdminController.php` |
| **Nabilla Fitricia Hernanda** | Keranjang AJAX, Checkout, Konsultasi Resep, Dashboard Laporan | Membuat issue/branch frontend e-commerce, interaksi keranjang belanja, upload resep dokter, integrasi Chart.js | `resources/views/checkout.blade.php`, `resources/views/cart.blade.php`, `resources/views/admin/laporan_penjualan.blade.php`, `app/Http/Controllers/CartController.php`, `app/Http/Controllers/PrescriptionController.php`, `app/Http/Controllers/AdminPrescriptionController.php` |
| **Rury Fezriani Matari** | Desain Antarmuka, Master Layouts, Optimasi Aset | Membuat issue/branch layout, partial views, visual design system, konversi media ke WebP | `resources/views/admin/layout.blade.php`, `resources/views/layouts/`, `resources/css/`, `public/images/`, `docs/refleksi.md` |

## Role Repository yang Disarankan

| GitHub role | Pemegang | Hak |
| --- | --- | --- |
| Owner/Maintainer | Ketua tim / Akun Organisasi | Mengatur repository, branch protection, secrets, merge final |
| Write | Semua developer | Push branch fitur, membuat pull request, review PR |
| Triage | Dosen/asisten bila diperlukan | Melihat issue, memberi label, memberi komentar review |
| Read | Penguji/auditor | Melihat source code dan dokumentasi |

## Branch Strategy

Branch utama:

| Branch | Fungsi | Aturan |
| --- | --- | --- |
| `main` | Versi stabil untuk demo/pengumpulan | Merge hanya lewat pull request yang lulus CI |
| `develop` | Integrasi fitur sebelum stabil | Opsional bila tim memakai staging branch |

Format branch fitur:

```text
feature/<inisial>-<nama-fitur>
fix/<inisial>-<nama-bug>
refactor/<inisial>-<nama-refactor>
docs/<inisial>-<nama-dokumen>
```

Contoh:

```text
feature/hf-wa-otp-login
feature/fy-stock-conversion
fix/nfh-ajax-cart-reload
refactor/rfm-admin-layout
docs/uas-documentation
```

Catatan: gunakan huruf kecil, tanda hubung, dan nama fitur yang singkat.

## Commit Convention

Format commit yang disarankan:

```text
<type>(<scope>): <pesan singkat>
```

Contoh:

```text
feat(auth): add WhatsApp OTP registration flow
fix(cart): prevent double quantity adding in ajax call
refactor(layout): extract sidebar navigation to admin layout
docs(uas): add github roles and strategy documentation
test(order): verify delivery shipping cost calculations
```

Tipe commit:

| Type | Keterangan |
| --- | --- |
| `feat` | Penambahan fitur |
| `fix` | Perbaikan bug |
| `refactor` | Perubahan struktur kode tanpa mengubah perilaku |
| `docs` | Perubahan dokumentasi |
| `test` | Penambahan/perubahan test |
| `chore` | Perubahan konfigurasi/dependency |

## Pull Request Workflow

1. Ambil task dari GitHub Issue atau Project Board.
2. Buat branch sesuai format.
3. Commit perubahan secara bertahap.
4. Update dokumentasi terkait jika fitur berubah.
5. Update `docs/CHANGELOG.md`.
6. Push branch dan buat Pull Request.
7. Pastikan GitHub Action lulus.
8. Minta review minimal satu anggota yang bukan pembuat PR.
9. Perbaiki feedback review.
10. Merge ke `main` setelah disetujui.

## Review Matrix

| Pembuat PR | Reviewer utama | Fokus review |
| --- | --- | --- |
| Hidayathul Fikri | Farid Yahya atau Nabilla | Alur otentikasi, integrasi API, security, routing |
| Farid Yahya | Hidayathul Fikri atau Rury | Skema query database, relasi tabel, REST API polling |
| Nabilla Fitricia | Hidayathul Fikri atau Rury | Kompilasi JS/CSS, validasi keranjang belanja, AJAX rendering |
| Rury Fezriani | Nabilla atau Farid | Keandalan responsif UI, assets file path, styling layout |

## Issue Label

| Label | Fungsi |
| --- | --- |
| `feature` | Fitur baru |
| `bug` | Perbaikan bug |
| `refactor` | Perbaikan struktur kode |
| `documentation` | Dokumentasi |
| `test` | Pengujian |
| `admin` | Modul admin |
| `customer` | Modul pelanggan |
| `integration` | API eksternal seperti Gemini AI / WhatsApp / OSRM |
| `priority-high` | Perlu dikerjakan segera |

## Project Board

Kolom board yang disarankan:

| Kolom | Makna |
| --- | --- |
| Backlog | Ide/task belum dipilih |
| Todo | Task siap dikerjakan |
| In Progress | Sedang dikerjakan pada branch |
| Review | Pull Request menunggu review |
| Testing | Perlu validasi manual/otomatis |
| Done | Sudah merge dan terdokumentasi |

## Definition of Done

Satu task dianggap selesai bila:

- Kode sudah berada di branch fitur.
- Test terkait sudah dibuat atau test regresi yang relevan sudah dijalankan.
- `npm run build` dan `php artisan test` tidak gagal untuk perubahan yang memengaruhi aplikasi.
- Dokumentasi fitur/dependency/instalasi diperbarui bila ada perubahan perilaku atau konfigurasi.
- Changelog diperbarui.
- Pull request sudah direview dan lulus GitHub Action.
