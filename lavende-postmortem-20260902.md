# Post-Mortem — Insiden Data Loss Database `lavende`
## TheLavendeResidence (103.134.154.19) — 2–3 September 2026

**Severity:** SEV-1 (Data loss produksi, partial permanent)
**Durasi dampak:** ±2,5 jam (00:05–02:30 WIB / 17:05–19:30 UTC, app maintenance mode)
**Penyebab akar:** Proses test otomatis dijalankan di server produksi dengan konfigurasi database test yang tidak aman
**Pelaku utama:** Hermes Agent (eksekusi test di production)

> **Catatan timezone:** Semua timestamp server Lavende & folder backup berada dalam **UTC**. Waktu dalam laporan ini ditampilkan **WIB (UTC+7)** dengan UTC dalam kurung. Insiden terjadi **dini hari 3 September 2026 WIB** (masih tanggal 2 September UTC).

---

## 1. Ringkasan Eksekutif

Pada **dini hari 3 September 2026 sekitar pukul 00:05 WIB** (17:05 UTC, 2 Sep), proses verifikasi patch webhook hardening (Laravel) di server produksi TheLavende menjalankan perintah:

```bash
php artisan test tests/Feature/QiscusWebhookTest.php
```

Test memakai trait `RefreshDatabase` yang menjalankan `migrate:fresh` — yaitu **DROP semua tabel + migrate ulang**. Karena `phpunit.xml` tidak dikonfigurasi ke database test terpisah, Laravel mengeksekusi `DROP TABLE` terhadap **database produksi `lavende`** yang berisi 19 tabel dan ±330 ribu baris data operasional (paket barang masuk sejak 2022).

**Hasil akhir:**
- ✅ `products` (tabel utama, 329.182 baris) — **PULIH 100%** dari backup parsial yang sempat dibuat 9 menit sebelum insiden
- ⚠️ Tabel pendukung (`units`, `product_outs`, `users`, `insidens`, dll) — data periode 2023–Agustus 2026 **hilang permanen**; hanya baseline 2022 + sebagian window 4 Agu–2 Sep 2026 yang tersedia
- ✅ Aplikasi kembali normal ±02:30 WIB (19:30 UTC) setelah recovery + hardening oleh OpenClaw

---

## 2. Kronologi Lengkap

| Waktu WIB (UTC) | Kejadian |
|---|---|
| 2 Sep 20:17 (13:17 UTC) | Hermes pasang observability Nginx + PHP-FPM (webhook timing) di Lavende |
| 2 Sep 23:43 (16:43 UTC) | OpenClaw update patch v2 (10 file) di `/tmp/webhook-hardening.patch` |
| 2 Sep 23:56 (16:56 UTC) | Hermes buat backup **parsial**: `mysqldump lavende products` (94,8 MB) + `.env` → `/root/hermes-backups/lavende-deploy-qiscus-20260902-165639/` |
| 3 Sep 00:04 (17:04 UTC) | OpenClaw apply patch v2 ke working tree `/var/www/lavende` + isi `.env` QISCUS_* |
| 3 Sep 00:05–00:10 (17:05–17:10 UTC) | Hermes commit `1e0bd7f` + `config:clear` |
| 3 Sep 00:11 (17:11 UTC) | Hermes jalankan `php artisan test tests/Feature/QiscusWebhookTest.php` **di production** |
| 3 Sep 00:11–00:15 (17:11–17:15 UTC) | `RefreshDatabase` → `migrate:fresh` → **DROP 19 tabel produksi** → migrate ulang terputus (timeout 180 detik) |
| 3 Sep 00:15 (17:15 UTC) | Hermes sadari tabel hilang → cek state → konfirmasi insiden |
| 3 Sep 00:26 (17:26 UTC) | Hermes stop supervisor worker + maintenance mode |
| 3 Sep 00:27 (17:27 UTC) | Hermes backup binlog 34 file (301 MB, 4 Agu–2 Sep) → `/root/hermes-backups/lavende-binlog-20260902-172749/` |
| 3 Sep 00:34–00:38 (17:34–17:38 UTC) | MySQL restart (proses tidak terkontrol), sempat connection refused |
| 3 Sep 00:40 (17:40 UTC) | Recovery: restore baseline dump 2022 (`project_apart.sql`) → 16 tabel struktur + data lama |
| 3 Sep 00:45 (17:45 UTC) | Migrate upgrade: 8 migration 2023–2025 DONE; 2 migration gagal (butuh kolom `webhook`) |
| 3 Sep 00:45 (17:45 UTC) | Import `products` live terpotong (LOCK TABLES privilege) |
| 3 Sep 01:30–02:30 (18:30–19:30 UTC) | OpenClaw (Insight) takeover recovery: sanitasi dump, restore products 329.182 baris, perbaiki struktur, pasang guard |
| 3 Sep 02:15 (19:15 UTC) | Backup harian otomatis pertama berjalan (32 MB .sql.gz) |
| 3 Sep ±02:30 (19:30 UTC) | App UP, worker RUNNING, recovery selesai |

---

## 3. Root Cause Analysis

### 3.1 Penyebab langsung

```text
php artisan test → QiscusWebhookTest.php menggunakan RefreshDatabase
→ Laravel memanggil migrate:fresh → DROP TABLE seluruh isi DB aktif
```

### 3.2 Mengapa DB produksi yang kena?

`phpunit.xml` memiliki konfigurasi database test yang **di-comment**:

```xml
<!-- <env name="DB_CONNECTION" value="sqlite"/> -->
<!-- <env name="DB_DATABASE" value=":memory:"/> -->
```

Tanpa override, Laravel memakai koneksi default dari `.env` → `DB_DATABASE=lavende` (produksi). `RefreshDatabase` tidak tahu ini produksi; ia hanya menjalankan `migrate:fresh` di koneksi aktif.

### 3.3 Faktor kontributor

```text
1. Hermes tidak memverifikasi phpunit.xml SEBELUM menjalankan test
2. Test dijalankan langsung di server produksi (tidak ada staging)
3. Backup yang ada hanya parsial (products), bukan full DB
4. Tidak ada cron backup DB terjadwal sebelumnya
5. Tidak ada guard otomatis (mis. cek APP_ENV=productions → tolak test)
6. Proses test dihentikan paksa (timeout 180s) → DB dalam state
   setengah migrate (tabel hilang, sebagian dibuat ulang kosong)
```

---

## 4. Dampak

### 4.1 Data

| Tabel | Kondisi Asli (estimasi) | Setelah Recovery | Status |
|---|---|---|---|
| `products` | 329.182 baris (2022–2026) | 329.182 baris | ✅ **PULIH 100%** |
| `units` | data 2023–2026 (asli >763) | 763 (baseline 2022) | ⚠️ Hilang 2023–2026 |
| `product_outs` | data 2023–2026 | 844 (baseline 2022) | ⚠️ Hilang 2023–2026 |
| `users` | data 2023–2026 | 5 (baseline 2022) | ⚠️ Hilang 2023–2026 |
| `insidens` | tabel dibuat 2025, ada data | 0 | ❌ Hilang semua |
| `log_activities` | 2023–2026 | 143 (baseline 2022) | ⚠️ Hilang 2023–2026 |
| `jenis_paket`, `phone_units`, `roles`, `permissions` | sebagian | baseline 2022 | ⚠️ Sebagian |

### 4.2 Operasional

- Aplikasi tidak bisa diakses ±2,5 jam (maintenance mode)
- Notifikasi WhatsApp paket terhenti sementara (worker di-stop)
- Kepercayaan terhadap proses deploy berkurang

---

## 5. Recovery yang Dilakukan

### 5.1 Yang menyelamatkan data

```text
1. Backup parsial products 23:56 WIB 2 Sep (94,8 MB) — dibuat 9 menit
   sebelum insiden karena task deploy migration index products
   → SATU-SATUNYA alasan 329.182 baris data utama selamat

2. Backup binlog 34 file (301 MB, 4 Agu–2 Sep) — dibuat 00:27 WIB
   → menyediakan window 30 hari terakhir untuk recovery tabel lain
   (belum dieksploitasi penuh; hanya sebagian)
```

### 5.2 Urutan recovery

```text
1. Stop worker + maintenance mode (cegah penulisan baru)
2. Backup binlog + artefak ke /root/hermes-backups/
3. Restore baseline struktur/data 2022 (project_apart.sql)
4. Migrate upgrade 2023–2025 (8 migration)
5. Import products live dari backup 23:56 (329.182 baris)
6. Sanitasi dump (buang LOCK TABLES — user mac tanpa privilege)
7. Verifikasi count + konsistensi FK
```

### 5.3 Yang TIDAK bisa dipulihkan

Data tabel pendukung periode **2023 – 3 Agustus 2026** tidak tersedia di:
- Binlog (purge otomatis 30 hari — file tertua 4 Agustus)
- Backup lain (tidak ada dump penuh berkala sebelumnya)
- Storage eksternal (tidak ada off-site backup)

---

## 6. Guard & Perbaikan yang Sudah Dipasang (oleh OpenClaw)

| Guard | Detail | Status |
|---|---|---|
| DB test terpisah | `phpunit.xml`: `DB_CONNECTION=mysql`, `DB_DATABASE=lavende_test` | ✅ Terverifikasi |
| Backup harian full DB | Cron `0 2 * * *` (UTC) → `/usr/local/bin/backup_lavende.sh` (mysqldump full + gzip, retensi 2 file) | ✅ Aktif — backup pertama 32 MB (02:15 WIB / 19:15 UTC) |
| Worker | Supervisor `lavande-worker` RUNNING | ✅ |
| App | Maintenance mode off | ✅ |

---

## 7. Pelajaran & Komitmen (Hermes)

### 7.1 Pelajaran kunci

```text
1. JANGAN PERNAH jalankan php artisan test / migrate:fresh di Laravel
   production tanpa memastikan phpunit.xml memakai DB test terpisah
   (sqlite :memory: atau DB khusus *_test)

2. Backup FULL DB (semua tabel) sebelum operasi berisiko — backup
   parsial per-tabel TIDAK cukup untuk recovery penuh

3. Test suite dijalankan di staging/CI, bukan di server produksi

4. Verifikasi environment (APP_ENV, DB_DATABASE, phpunit.xml) SEBELUM
   eksekusi apa pun — jangan asumsi

5. Cek isi file konfigurasi (jangan percaya komentar/README) —
   baris sqlite di phpunit.xml ternyata di-comment

6. Perhatikan timezone server (UTC vs WIB) saat menulis laporan —
   timestamp server jangan ditulis sebagai WIB tanpa konversi
```

### 7.2 Komitmen ke depan

```text
1. Setiap deploy/test di server klien: cek APP_ENV + DB_DATABASE +
   phpunit.xml terlebih dahulu (read-only)
2. Backup FULL DB otomatis sebelum perubahan skema apa pun
3. Tidak menjalankan test suite di production — minta staging
4. Verifikasi independen tetap jalan, tapi dengan guard environment
5. Konversi timezone UTC→WIB (+7) dengan benar di semua laporan
```

---

## 8. Status Akhir & Rekomendasi

### Status akhir (3 September 2026)

```text
✅ Aplikasi TheLavende normal
✅ products 329.182 baris (100%)
✅ Backup harian aktif (cron 02:00 UTC / 09:00 WIB + backup pertama sudah ada)
✅ phpunit aman (DB test terpisah lavende_test)
✅ Worker supervisor running
```

### Rekomendasi lanjutan (opsional)

```text
1. Buat DB `lavende_test` + pastikan bisa migrate:fresh aman
2. Jalankan 1× test suite penuh di lavende_test sebagai bukti
3. Pertimbangkan retensi backup >2 file (mis. 7 hari) + off-site
   (rsync ke server lain)
4. Tambah guard di kode: tolak `migrate:fresh`/test jika
   APP_ENV=productions (fail-closed)
5. Evaluasi apakah data tabel pendukung perlu diinput ulang manual
   (units/product_outs/insidens) berdasarkan catatan operasional
```

---

*Report disusun oleh Hermes Agent sebagai bentuk pertanggungjawaban penuh atas insiden. Data didasarkan pada log sesi, artefak backup di `/root/hermes-backups/`, dan verifikasi langsung ke server. Seluruh waktu telah dikonversi UTC → WIB (UTC+7).*
