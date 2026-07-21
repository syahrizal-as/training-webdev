# Rancangan Database (Database Schema)
## Sistem Manajemen Perpustakaan Sederhana (LibSys v1.0)

---

| Parameter | Detail |
| :--- | :--- |
| **Versi Dokumen** | v1.0 (Final) |
| **Database Management System (DBMS)** | MySQL / MariaDB |
| **Charset / Collation** | `utf8mb4_unicode_ci` |
| **Tanggal Pembuatan** | 21 Juli 2026 |

---

## 1. ERD (Entity Relationship Diagram) Overview

Sistem perpustakaan sederhana ini dirancang dengan **4 tabel utama** dan **1 tabel relasi (transaksi)** untuk mengelola entitas Buku, Anggota, Kategori, Admin/User, dan Transaksi Peminjaman.

```text
+-------------------+       +-------------------+       +--------------------+
|     categories    | 1---N |       books       | N---1 |       users        |
+-------------------+       +-------------------+       +--------------------+
| id (PK)           |       | id (PK)           |       | id (PK)            |
| name              |       | title             |       | username           |
| slug              |       | author            |       | password           |
| description       |       | publisher         |       | role               |
+-------------------+       | year              |       +--------------------+
                            | isbn              |
                            | category_id (FK)  |       +--------------------+
                            | total_stock       |       |      members       |
                            | available_stock   |       +--------------------+
                            | rack_location     |       | id (PK)            |
                            +-------------------+       | member_code        |
                                      |                 | name               |
                                      | N               | email              |
                                      |                 | phone              |
                            +-------------------+       | status             |
                            | transaction_items | <-----+--------------------+
                            +-------------------+       | (1 member can have |
                            | id (PK)           |       |  many loans)       |
                            | transaction_id(FK)|       +--------------------+
                            | book_id (FK)      |                 |
                            +-------------------+                 | 1
                                      |                           |
                                      | N                         |
                               +-------------------------+        |
                               |    borrowing_transactions | <------+
                               +-------------------------+
                               | id (PK)                 |
                               | transaction_code        |
                               | member_id (FK)          |
                               | user_id (FK) [Admin]    |
                               | borrow_date             |
                               | due_date                |
                               | return_date             |
                               | fine_amount             |
                               | status                  |
                               +-------------------------+
```

---

## 2. Struktur Tabel & Skema SQL

Berikut adalah perintah DDL (Data Definition Language) untuk membuat skema database lengkap dengan tipe data, *primary key*, *foreign key*, dan *index*:

```sql
-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `libsys_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `libsys_db`;

-- --------------------------------------------------------

--
-- 1. Tabel: users (Pengguna Sistem / Admin / Pustakawan)
--
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL COMMENT 'Hashed password (bcrypt)',
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'staff', 'head') NOT NULL DEFAULT 'staff',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 2. Tabel: categories (Kategori Buku)
--
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 3. Tabel: books (Katalog Buku / Inventaris)
--
CREATE TABLE `books` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(150) NOT NULL,
  `publisher` VARCHAR(150) DEFAULT NULL,
  `year` YEAR DEFAULT NULL,
  `isbn` VARCHAR(30) DEFAULT NULL,
  `category_id` INT(11) NOT NULL,
  `total_stock` INT(11) NOT NULL DEFAULT 0,
  `available_stock` INT(11) NOT NULL DEFAULT 0,
  `rack_location` VARCHAR(50) DEFAULT NULL COMMENT 'Contoh: Rak A-01',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_isbn` (`isbn`),
  KEY `fk_books_category` (`category_id`),
  CONSTRAINT `fk_books_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 4. Tabel: members (Anggota Perpustakaan)
--
CREATE TABLE `members` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `member_code` VARCHAR(30) NOT NULL COMMENT 'Nomor Induk Siswa / Kartu Anggota',
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(25) NOT NULL,
  `address` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_member_code` (`member_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 5. Tabel: borrowing_transactions (Header Transaksi Peminjaman)
--
CREATE TABLE `borrowing_transactions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `transaction_code` VARCHAR(50) NOT NULL COMMENT 'Format: TRX-YYYYMMDD-XXXX',
  `member_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL COMMENT 'Admin/Staff yang melayani',
  `borrow_date` DATE NOT NULL,
  `due_date` DATE NOT NULL COMMENT 'Batas tanggal pengembalian',
  `return_date` DATE DEFAULT NULL COMMENT 'Tanggal aktual pengembalian',
  `fine_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Akumulasi denda keterlambatan',
  `status` ENUM('borrowed', 'returned', 'overdue', 'lost') NOT NULL DEFAULT 'borrowed',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_transaction_code` (`transaction_code`),
  KEY `fk_trx_member` (`member_id`),
  KEY `fk_trx_user` (`user_id`),
  CONSTRAINT `fk_trx_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_trx_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 6. Tabel: transaction_items (Detail Buku yang Dipinjam / Pivot Table)
--
CREATE TABLE `transaction_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` INT(11) NOT NULL,
  `book_id` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_item_transaction` (`transaction_id`),
  KEY `fk_item_book` (`book_id`),
  CONSTRAINT `fk_item_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `borrowing_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_item_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. Penjelasan Relasi & Aturan Bisnis Database

1. **Relasi Kategori ke Buku (`categories` ➔ `books`)**:
   - Hubungan **One-to-Many**: Satu kategori dapat memiliki banyak buku, tetapi satu buku hanya memiliki satu kategori. Jika kategori dihapus, buku di dalamnya akan ikut terhapus (`ON DELETE CASCADE`).

2. **Relasi Peminjaman ke Item Buku (`borrowing_transactions` ➔ `transaction_items` ➔ `books`)**:
   - Hubungan **Many-to-Many**: Satu transaksi peminjaman dapat mencakup beberapa buku sekaligus (maksimal 3 buku sesuai aturan bisnis PRD). Tabel perantara (`transaction_items`) digunakan untuk menampung relasi ini.

3. **Integritas Stok (`available_stock`)**:
   - Ketika transaksi peminjaman berhasil dibuat, sistem aplikasi wajib mengurangi kolom `available_stock` pada tabel `books`.
   - Ketika buku dikembalikan (`return_date` terisi dan status berubah menjadi `returned`), kolom `available_stock` pada tabel `books` akan ditambah kembali secara otomatis melalui *trigger* atau *transaction block* di sisi backend.

4. **Perhitungan Denda (`fine_amount`)**:
   - Dihitung saat proses pengembalian: Jika `return_date > due_date`, maka selisih hari dikalikan tarif denda harian (misal: Rp 1.000 per hari per buku).

---

*--- Akhir Dokumen Rancangan Database LibSys v1.0 ---*
