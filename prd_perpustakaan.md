# Product Requirement Document (PRD)
## Sistem Manajemen Perpustakaan Sederhana (LibSys v1.0)

---

| Parameter | Detail |
| :--- | :--- |
| **Versi Dokumen** | v1.0 (Final) |
| **Tanggal** | 21 Juli 2026 |
| **Penulis / Owner** | Product Manager |
| **Status** | Ready for Development |

---

## 1. Ringkasan Eksekutif & Latar Belakang

Perpustakaan membutuhkan sistem berbasis web modern yang sederhana namun efisien untuk menggantikan pencatatan peminjaman buku manual (berbasis kertas/Excel). Proses manual saat ini menyebabkan berbagai kendala, seperti kesulitan melacak keberadaan buku, keterlambatan pengembalian tanpa sanksi yang terstruktur, serta lamanya waktu rekapitulasi laporan bulanan.

**LibSys v1.0** dirancang sebagai platform digital terpusat yang mempermudah Pustakawan (Admin) dalam mengelola data buku, anggota, dan transaksi peminjaman/pengembalian, serta memberikan kemudahan bagi Anggota Perpustakaan untuk mencari katalog buku secara mandiri.

---

## 2. Tujuan Produk & Indikator Keberhasilan (KPI)

### Tujuan Utama Produk
- Digitalisasi 100% inventaris katalog buku dan data anggota.
- Mempercepat proses peminjaman & pengembalian buku (< 1 menit per transaksi).
- Meningkatkan transparansi dan otomatisasi perhitungan denda keterlambatan.
- Menyediakan dashboard laporan real-time untuk pengelola perpustakaan.

### Target Key Performance Indicators (KPIs)
- **Waktu Peminjaman:** Efisiensi waktu layanan transaksi meningkat sebesar 60%.
- **Akurasi Stok:** 99% akurasi antara status di sistem dan fisik buku di rak.
- **Tingkat Pengembalian Tepat Waktu:** Meningkat 35% dengan notifikasi/pengawasan denda terstruktur.

---

## 3. Target Pengguna (User Persona)

| Peran (Role) | Karakteristik & Deskripsi | Kebutuhan Utama |
| :--- | :--- | :--- |
| **Pustakawan (Admin)** | Staf operasional perpustakaan yang mengelola transaksi harian dan koleksi buku. | Input buku baru dengan cepat, pencarian data anggota instan, pencatatan pinjam/kembali, serta cetak laporan. |
| **Anggota Perpustakaan** | Siswa, mahasiswa, atau pengunjung umum yang meminjam buku. | Cari buku katalog online, cek ketersediaan stok buku, serta melihat riwayat peminjaman dan denda. |
| **Kepala Perpustakaan** | Pimpinan/Supervisor yang memerlukan ringkasan statistik. | Melihat laporan statistik bulanan, buku paling favorit, dan total pendapatan denda. |

---

## 4. Spesifikasi Kebutuhan Fungsional (Functional Requirements)

Fitur-fitur dikategorikan berdasarkan tingkat prioritas:
- **P0 (Must-Have)**: Harus ada pada rilis awal.
- **P1 (Should-Have)**: Penting untuk operasional lengkap.
- **P2 (Nice-to-Have)**: Fitur tambahan opsional.

| ID | Modul Fitur | Deskripsi Kebutuhan | Aktor | Prioritas |
| :--- | :--- | :--- | :--- | :--- |
| **FR-01** | Autentikasi & Hak Akses | Login terpisah untuk Admin dan Anggota. Admin memiliki akses penuh, Anggota memiliki akses *read-only* ke katalog & riwayat sendiri. | Semua | **P0 - High** |
| **FR-02** | Manajemen Buku (CRUD) | Admin dapat menambah, mengedit, menghapus, dan melihat detail buku (Judul, Pengarang, Penerbit, Tahun, ISBN, Kategori, Stok Total, Stok Tersedia, Lokasi Rak). | Admin | **P0 - High** |
| **FR-03** | Pencarian Katalog Buku | Pengguna dapat mencari buku berdasarkan Judul, Pengarang, atau Kategori dengan fitur filter & kata kunci. | Admin, Anggota | **P0 - High** |
| **FR-04** | Manajemen Anggota | Admin dapat mendaftarkan anggota baru (ID Anggota, Nama, Email, No. Telp, Status Aktif) dan mengelola status keanggotaan. | Admin | **P0 - High** |
| **FR-05** | Transaksi Peminjaman | Admin mencatat peminjaman buku oleh Anggota. Sistem otomatis mengurangi stok tersedia dan menentukan Tanggal Jatuh Tempo (default: 7 hari). Maksimal 3 buku per anggota. | Admin | **P0 - High** |
| **FR-06** | Transaksi Pengembalian & Denda | Admin memproses pengembalian buku. Sistem otomatis menghitung keterlambatan dan kalkulasi denda (misal: Rp 1.000/hari) serta mengembalikan stok buku. | Admin | **P0 - High** |
| **FR-07** | Dashboard & Laporan | Admin dapat melihat ringkasan statistik (Total Buku, Total Peminjaman Aktif, Anggota Aktif) dan mengunduh laporan peminjaman bulanan (Format PDF/Excel). | Admin, Kepala | **P1 - Medium** |
| **FR-08** | Booking / Reservasi Buku | Anggota dapat melakukan reservasi buku secara online jika stok tersedia sebelum mengambil langsung di perpustakaan. | Anggota | **P2 - Low** |

---

## 5. Alur Kerja Utama (Workflow)

### 5.1 Alur Peminjaman Buku
1. **Langkah 1**: Anggota memilih buku dan mendatangi meja pustakawan (Admin).
2. **Langkah 2**: Admin melakukan pencarian ID Anggota di sistem dan memastikan status anggota aktif tanpa denda menunggak.
3. **Langkah 3**: Admin memasukkan kode/ISBN buku ke menu transaksi peminjaman.
4. **Langkah 4**: Sistem memvalidasi ketersediaan stok & limit peminjaman anggota.
5. **Langkah 5**: Sistem mengonfirmasi transaksi, memperbarui stok, dan menampilkan tanggal wajib kembali.

### 5.2 Alur Pengembalian Buku
1. **Langkah 1**: Anggota menyerahkan buku yang dipinjam kepada Admin.
2. **Langkah 2**: Admin membuka menu pengembalian dan memasukkan ID Transaksi / Kode Buku.
3. **Langkah 3**: Sistem mengecek tanggal pengembalian. Jika melewati tanggal jatuh tempo, sistem otomatis menghitung denda keterlambatan.
4. **Langkah 4**: Admin menerima pembayaran denda (jika ada) dan menandai transaksi selesai. Stok buku bertambah otomatis.

---

## 6. Kebutuhan Non-Fungsional (Non-Functional Requirements)

- **Performa:** Respon pencarian katalog buku harus di bawah 1.5 detik untuk 10.000 data buku.
- **Usabilitas (Usability):** Antarmuka intuitif dan responsif (dapat diakses via Desktop dan Tablet/Mobile).
- **Keamanan (Security):** Enkripsi kata sandi menggunakan hashing (bcrypt) dan autentikasi berbasis session/JWT token.
- **Ketersediaan (Availability):** Uptime sistem minimal 99% pada jam operasional perpustakaan.
- **Kompatibilitas:** Mampu berjalan dengan lancar pada browser modern (Chrome, Firefox, Edge, Safari).

---

## 7. Batasan & Asumsi Sistem

> **Batasan Sistem (Out of Scope v1.0):**
> 1. Sistem tidak mencakup pembayaran denda secara online (E-Wallet/Payment Gateway) pada versi ini — pembayaran denda dilakukan secara tunai di kasir.
> 2. Belum terintegrasi dengan pemindai barcode fisik otomatis (penginputan menggunakan input teks / scanner keyboard emulation).
> 3. Tidak ada fitur e-book / pembacaan buku secara digital.

---

## 8. Rencana Peluncuran & Roadmap (Timeline)

| Fase | Aktivitas | Target Waktu | Output / Deliverable |
| :--- | :--- | :--- | :--- |
| **Fase 1: Desain & PRD** | Finalisasi PRD, Wireframe UI/UX, & Perancangan Database (ERD). | Minggu 1 | Dokumen Spesifikasi & Design Prototype |
| **Fase 2: Development** | Pengembangan backend (API), frontend, dan modul CRUD & Transaksi. | Minggu 2 - 3 | Sistem LibSys Build Alpha |
| **Fase 3: Pengujian (QA)** | User Acceptance Testing (UAT), bug fixing, & migrasi data awal. | Minggu 4 | Laporan QA & Sistem Siap Deploy |
| **Fase 4: Go-Live & Pelatihan** | Deployment ke server produksi & pelatihan penggunaan bagi Pustakawan. | Minggu 5 | Sistem Berjalan Operasional |

---

*--- Akhir Dokumen Product Requirement Document (PRD) ---*
