# Rancangan Skema Database (KosHub)

Dokumen ini merinci rancangan tabel database untuk sistem manajemen kos-kosan KosHub sesuai dengan kebutuhan PRD.

---

## ERD Relationships (Ringkasan Relasi)
- **`properties`** (1) ─── (N) **`rooms`**
- **`facilities` (Master Fasilitas)** (N) ─── (N) **`properties`** (via `facility_property`)
- **`facilities` (Master Fasilitas)** (N) ─── (N) **`rooms`** (via `facility_room`)
- **`rooms`** (1) ─── (N) **`room_images`** (Multi foto per kamar)
- **`rooms`** (1) ─── (N) **`rentals`** (Penyewaan)
- **`users` (Tenant)** (1) ─── (N) **`rentals`**
- **`rentals`** (1) ─── (N) **`invoices`** (Tagihan & Pembayaran)
- **`rooms`** (1) ─── (N) **`complaints`**
- **`users` (Tenant)** (1) ─── (N) **`complaints`**
- **`users` (Sender)** (1) ─── (N) **`messages`** (Untuk user terdaftar)
- **`users` (Receiver)** (1) ─── (N) **`messages`** (Untuk user terdaftar)

---

## Kamus Data / Rancangan Tabel

### 1. Tabel `properties` (Properti Kos)
Tabel ini menyimpan data properti kos yang dikelola oleh pemilik (owner).

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | bigint | unsigned, primary key, auto-increment | ID Properti |
| `name` | varchar(255) | not null | Nama kos/properti |
| `address` | text | not null | Alamat properti |
| `created_at` | timestamp | nullable | Waktu data dibuat |
| `updated_at` | timestamp | nullable | Waktu data diperbarui |

---

### 2. Tabel `rooms` (Kamar Kos)
Tabel ini menyimpan data kamar yang dimiliki oleh suatu properti kos.

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | bigint | unsigned, primary key, auto-increment | ID Kamar |
| `property_id` | bigint | unsigned, foreign key, index | Kunci asing ke `properties.id` |
| `room_number` | varchar(50) | not null | Nomor/Nama kamar (misal: 101, A-02) |
| `type` | varchar(100) | not null | Tipe/kelas kamar (misal: Standard, Deluxe) |
| `price` | decimal(12,2) | not null | Harga sewa per bulan |
| `status` | enum | not null, default: `'Available'` | Status kamar: `'Available'` (Tersedia), `'Occupied'` (Terisi), `'Repair'` (Perbaikan) |
| `created_at` | timestamp | nullable | Waktu data dibuat |
| `updated_at` | timestamp | nullable | Waktu data diperbarui |

- **Foreign Key constraint:** `property_id` references `properties(id)` on delete cascade.

---

### 3. Tabel `room_images` (Foto Kamar)
Tabel ini menyimpan beberapa foto untuk setiap kamar kos (One-to-Many).

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | bigint | unsigned, primary key, auto-increment | ID Foto |
| `room_id` | bigint | unsigned, foreign key, index | Kunci asing ke `rooms.id` |
| `image_path` | varchar(255) | not null | Path/URL penyimpanan file gambar |
| `is_primary` | boolean | default: `false` | Menandakan apakah gambar utama/cover kamar |
| `created_at` | timestamp | nullable | Waktu data dibuat |
| `updated_at` | timestamp | nullable | Waktu data diperbarui |

- **Foreign Key constraint:** `room_id` references `rooms(id)` on delete cascade.

---

### 4. Tabel `facilities` (Master Fasilitas)
Tabel master untuk menyimpan semua daftar fasilitas yang tersedia baik untuk properti maupun kamar.

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | bigint | unsigned, primary key, auto-increment | ID Fasilitas |
| `name` | varchar(255) | not null | Nama fasilitas (misal: Wi-Fi, AC, Kasur, Kolam Renang) |
| `type` | enum | not null | Jenis fasilitas: `'Property'` (Fasilitas Umum Properti) atau `'Room'` (Fasilitas Khusus Kamar) |
| `created_at` | timestamp | nullable | Waktu data dibuat |
| `updated_at` | timestamp | nullable | Waktu data diperbarui |

---

### 5. Tabel Pivot `facility_property` (Fasilitas Properti)
Menghubungkan fasilitas dengan properti (Many-to-Many).

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `property_id` | bigint | unsigned, primary key, foreign key | ID Properti |
| `facility_id` | bigint | unsigned, primary key, foreign key | ID Fasilitas |

- **Foreign Key constraints:**
  - `property_id` references `properties(id)` on delete cascade.
  - `facility_id` references `facilities(id)` on delete cascade.

---

### 6. Tabel Pivot `facility_room` (Fasilitas Kamar)
Menghubungkan fasilitas dengan kamar (Many-to-Many).

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `room_id` | bigint | unsigned, primary key, foreign key | ID Kamar |
| `facility_id` | bigint | unsigned, primary key, foreign key | ID Fasilitas |

- **Foreign Key constraints:**
  - `room_id` references `rooms(id)` on delete cascade.
  - `facility_id` references `facilities(id)` on delete cascade.

---

### 7. Tabel `rentals` (Penyewaan / Kontrak)
Tabel ini mencatat masa sewa kamar oleh penghuni (tenant).

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | bigint | unsigned, primary key, auto-increment | ID Transaksi Penyewaan |
| `room_id` | bigint | unsigned, foreign key, index | Kunci asing ke `rooms.id` |
| `tenant_id` | bigint | unsigned, foreign key, index | Kunci asing ke `users.id` (User dengan role tenant) |
| `start_date` | date | not null | Tanggal masuk/awal sewa |
| `duration` | int | not null | Durasi sewa dalam hitungan bulan |
| `status` | enum | not null, default: `'Active'` | Status kontrak: `'Active'` (Aktif), `'Finished'` (Selesai), `'Cancelled'` (Batal) |
| `agreement_file`| varchar(255) | nullable | Path/URL surat perjanjian sewa digital |
| `created_at` | timestamp | nullable | Waktu data dibuat |
| `updated_at` | timestamp | nullable | Waktu data diperbarui |

- **Foreign Key constraints:**
  - `room_id` references `rooms(id)` on delete cascade.
  - `tenant_id` references `users(id)` on delete cascade.

---

### 8. Tabel `invoices` (Tagihan & Pembayaran dengan Midtrans)
Tabel ini merekam semua tagihan bulanan dan status pembayaran dari penyewaan yang aktif terintegrasi dengan Payment Gateway (seperti Midtrans).

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | bigint | unsigned, primary key, auto-increment | ID Tagihan |
| `rental_id` | bigint | unsigned, foreign key, index | Kunci asing ke `rentals.id` |
| `invoice_code` | varchar(100) | unique, not null | Kode unik tagihan (misal: INV-20260712-0001) |
| `amount` | decimal(12,2) | not null | Nominal tagihan |
| `due_date` | date | not null | Tanggal batas pembayaran (jatuh tempo) |
| `status` | enum | not null, default: `'Pending'` | Status bayar: `'Pending'` (Belum bayar), `'Paid'` (Lunas), `'Cancelled'` (Batal) |
| `payment_method`| varchar(100) | nullable | Metode pembayaran (VA, QRIS, dll.) |
| `transaction_id`| varchar(255) | nullable | ID Transaksi unik dari Midtrans / Payment Gateway |
| `snap_token` | varchar(255) | nullable | Token Midtrans Snap untuk checkout page |
| `payment_url` | varchar(500) | nullable | URL Pembayaran/Redirect Link Midtrans |
| `paid_at` | timestamp | nullable | Waktu pembayaran dilakukan |
| `created_at` | timestamp | nullable | Waktu data dibuat |
| `updated_at` | timestamp | nullable | Waktu data diperbarui |

- **Foreign Key constraint:** `rental_id` references `rentals(id)` on delete cascade.

---

### 9. Tabel `complaints` (Tiket Komplain Kerusakan)
Tabel ini digunakan penghuni untuk melaporkan masalah atau kerusakan fasilitas.

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | bigint | unsigned, primary key, auto-increment | ID Tiket Komplain |
| `room_id` | bigint | unsigned, foreign key, index | Kunci asing ke `rooms.id` |
| `tenant_id` | bigint | unsigned, foreign key, index | Kunci asing ke `users.id` (Pelapor) |
| `category` | enum | not null | Kategori: `'Room'`, `'General'`, `'Security'`, `'Cleanliness'` |
| `description` | text | not null | Rincian keluhan/kerusakan |
| `photo` | varchar(255) | nullable | Lampiran foto bukti kerusakan |
| `status` | enum | not null, default: `'Pending'` | Status tiket: `'Pending'` (Diterima), `'Processing'` (Diproses), `'Solved'` (Selesai) |
| `created_at` | timestamp | nullable | Waktu tiket dibuat |
| `updated_at` | timestamp | nullable | Waktu tiket diperbarui |

- **Foreign Key constraints:**
  - `room_id` references `rooms(id)` on delete cascade.
  - `tenant_id` references `users(id)` on delete cascade.

---

### 10. Tabel `messages` (Chat/Pesan)
Tabel ini menyimpan riwayat pesan chat langsung antara Tenant/Guest dan Owner/Admin.

| Nama Kolom | Tipe Data | Atribut | Keterangan |
| :--- | :--- | :--- | :--- |
| `id` | bigint | unsigned, primary key, auto-increment | ID Pesan |
| `sender_id` | bigint | unsigned, foreign key, index, nullable | ID Pengirim (relasi ke `users.id`). Bernilai `null` jika pengirim adalah Guest (belum login). |
| `receiver_id` | bigint | unsigned, foreign key, index, nullable | ID Penerima (relasi ke `users.id`). Bernilai `null` jika ditujukan ke admin utama / belum terarah. |
| `guest_token` | varchar(255) | nullable, index | Token unik sesi guest (disimpan di cookie/localStorage browser guest) untuk mengelompokkan chat guest. |
| `guest_name` | varchar(255) | nullable | Nama pengirim jika pengirim adalah guest (pengguna belum login). |
| `guest_email` | varchar(255) | nullable | Email opsional dari pengirim guest. |
| `guest_phone` | varchar(50) | nullable | Nomor telepon opsional dari pengirim guest. |
| `message` | text | not null | Isi pesan chat |
| `read_at` | timestamp | nullable | Waktu pesan dibaca (null = belum dibaca) |
| `created_at` | timestamp | nullable | Waktu pesan dikirim |
| `updated_at` | timestamp | nullable | Waktu data diperbarui |

- **Foreign Key constraints:**
  - `sender_id` references `users(id)` on delete cascade.
  - `receiver_id` references `users(id)` on delete cascade.
