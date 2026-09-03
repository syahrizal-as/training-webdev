# Laporan: Investigasi Response Time Webhook 5000ms — Lavande Residences

**Tanggal:** 3 September 2026
**Endpoint:** `https://thelavanderesidences.id/api/receive_webhooks`
**Provider:** Qiscus WA Channel (omnichannel) / Meta WhatsApp
**Server:** VPS `103.134.154.19`, nginx + php-fpm, Laravel

---

## 1. Ringkasan Eksekutif

Dashboard Qiscus mencatat satu entri webhook dengan **Status Code = 0** dan **Response Time 5.066 detik** pada jam **08:33 WIB**, padahal **dari sisi server Lavande semua request webhook di-deteksi balas HTTP 200 dalam <300ms**.

Kesimpulan utama:

- **Server & kode webhook SEHAT/CEPAT** — tidak ada request yang datang lalu melambat.
- **Status Code `0` bukan berarti server lambat**, melainkan **request tidak pernah mendapat response HTTP valid** dari perspektif Qiscus/Meta — hampir pasti **gagal di perjalanan/relay (jaringan)**, bukan di aplikasi.
- **Payload yang gagal adalah webhook `statuses`** (laporan status pesan outgoing seperti `read`/`delivered`), **bukan chat inbound** — dan payload jenis ini **tidak seharusnya diproses** sebagai chat masuk oleh aplikasi.

---

## 2. Fakta / Data Bukti

### 2.1 Sisi Server — Semua Request Dibalas Cepat (log `/var/log/nginx/webhook-timing.log`)

Di jam yang sama dengan entri Qiscus yang gagal (08:33 WIB = 01:33 UTC):

```
108.137.63.17 [2026-09-03T01:33:38+00:00] method=POST status=200 bytes=50 rt=0.127 uht=0.128
108.137.63.17 [2026-09-03T01:33:45+00:00] method=POST status=200 bytes=50 rt=0.095 uht=0.092
```

`108.137.63.17` = IP Qiscus. `rt` (request time nginx) dan `uht` (upstream time) **dua-duanya <130ms**, status **200**.

Sampel request lain yang masuk ke server (semua 200, semua cepat):

```
rt=0.057, 0.077, 0.104, 0.108, 0.119, 0.121, 0.127, 0.186, 0.265 ...
```

**Kesimpulan: tidak ada satu pun request yang benar-benar sampai ke nginx lalu melambat.** Mayoritas <150ms.

### 2.2 Sisi Qiscus — Entri yang Dikategorikan "Gagal" (dari HTML dashboard yang dicopy)

```
Time        : 3 September 2026, 8:33
Intent      : WA_CHANNEL_WEBHOOK_INBOUND
URL         : https://thelavanderesidences.id/api/receive_webhooks
Status Code : 0
Response    : 5.066 s
Response    : "returning response"
Request body: (payload statuses, di bawah)
```

### 2.3 Payload Request yang Gagal Tersebut (dari dashboard Qiscus)

```json
{
  "app_code": "zbrda-m5c6q0dufr0bqez",
  "channel_id": 8067,
  "contacts": [{ "user_id": "ID.1691510551882921", "wa_id": "628111716606" }],
  "messaging_product": "whatsapp",
  "metadata": {
    "display_phone_number": "6287797604473",
    "phone_number_id": "960954613757989"
  },
  "statuses": [{
    "conversation": { "id": "04feeeedcaa4890272d5eae3d4db8e1c", "origin": { "type": "utility" } },
    "id": "wamid.HBgMNjI4MTExNzE2NjA2FQIAER...",
    "recipient_id": "628111716606",
    "status": "read",
    "timestamp": "1788399213"
  }]
}
```

**Karakteristik payload:** field `statuses[]` dengan `status:"read"`. Ini **bukan pesan chat masuk** — ini **status update pesan OUTGOING** yang dikirim (pesan "udah dibaca/delivered").

---

## 3. Analisis Akar Masalah

### 3.1 Status Code `0` = request gagal dapat response (bukan server lambat)

- Dalam integrasi webhook, **Status Code `200`** berarti endpoint menerima & membalas sukses.
- **Status Code `0`** (dari perspektif pengirim/Qiscus) berarti **tidak pernah ada response HTTP valid** yang diterima — koneksi timeout/putus/takeover di perjalanan. Nilai inilah yang membuat durasi keliatan ~5 detik (batas tunggu koneksi pengirim), bukan karena aplikasi membaca/komputasi lama.

### 3.2 Tidak ada request lambat di server → masalah di relay/jaringan, bukan kode

Di jam yang sama (08:33 WIB), server mencatat 2 request dari IP Qiscus yang **sukses 200 dalam 127ms & 95ms**. Artinya:

- **Koneksi server Lavande sehat** dan cepat membalas.
- Request yang Qiscus tandai gagal (status 0) **kemungkinan adalah percobaan yang tidak pernah sampai** ke nginx, atau **salah satu percobaan dari beberapa retry** yang kena kelambatan jaringan di titik relay Meta ↔ Qiscus ↔ VPS.

### 3.3 Jenis payload = webhook `statuses`, bukan chat client

Payload yang gagal membawa `statuses[].status:"read"` — laporan bahwa pesan yang **kita kirim** sudah dibaca customer. Endpoint `/receive_webhooks` kita dirancang untuk **proses pesan chat masuk** (inbound yang butuh balasan otomatis), dan webhook `statuses` **tidak membutuhkan proses/bisa langsung di-discard** dengan ACK 200.

Kode `ReceiveWebhooks()` saat ini **tidak memiliki guard khusus untuk payload `statuses`** — ia akan mencoba mengekstrak `event_id`/`from` dari struktur pesan chat yang tidak ada di payload status, lalu lanjut (meski tanpa `from` ia tetap melempar ACK `queued`). Ini tidak menyebabkan error fatal, tetapi **tidak optimal** dan bisa membuat status update ikut diproses/idempotency terpolusi.

---

## 4. Kesimpulan

1. **Response cepat benar** dari sisi server — bukti log nginx konsisten 200 <300ms di jam yang sama.
2. **Angka 5000ms / Status 0 adalah catatan GAGAL-KONEKSI**, bukan catatan server lambat. Ini hampir pasti **masalah timer/jaringan pada relay Qiscus–Meta** untuk payload webhook `statuses` (laporan status outgoing), bukan bug di aplikasi Lavande.
3. **Server tidak perlu di-tuning untuk mempercepat** — sudah cepat.

---

## 5. Rekomendasi / Langkah Lanjut (opsional, belum dieksekusi)

| # | Aksi | Tujuan | Prioritas |
|---|------|--------|-----------|
| 1 | **Tambah guard di `ReceiveWebhooks()`**: jika payload mengandung field `statuses` (webhook status, bukan message), langsung `return 200` & skip tanpa proses apapun | Menghindari payload status ikut diproses; pola standar integrasi WA | Tinggi (perbaikan kode kecil) |
| 2 | **Diskusikan dengan Qiscus** soal entri `Status 0` pada webhook `statuses` jarak jauh; tanyakan IP/range & relai mana yang kirim status ke VPS | Pastikan request non-infrastruktur (relay) tidak timeout | Sedang |
| 3 | **Naikkan timeout cURL keluar** (default 5000ms) untuk API Qiscus hanya jika keluhan latency muncul lagi | Hindari false-negative saat API provider sedang lambat | Rendah |
| 4 | **Pantau selama 1–2 hari**: apakah entri `Status 0` ini berulang atau hanya sekali | Menentukan apakah ini insiden intermittent wajar atau masalah berulang | Sedang |

> Catatan: perbaikan #1 dilakukan di repo **Mac (source of truth)** lalu di-pull ke server, bukan diedit langsung di server, supaya riwayat tetap sinkron.

---

## Lampiran: Konfigurasi & Status Terkait

- Queue worker: **RUNNING** (supervisor `lavende-worker`, `queue:work --tries=3 --timeout=900`)
- Tabel `failed_jobs`: **0 (tidak ada job gagal)**
- Job webhook masuk `ProcessQiscusWebhookJob`: konsisten cepat (161ms, 586ms)
- Terdapat log `cURL error 28: timed out after 5000 milliseconds ke omnichannel.qiscus.com` pada **1–2 Sep** (insiden lama/berbeda, bukan penyebab hari ini). Tes langsung dari server ke API Qiscus saat ini: HTTP 200 dalam 1.25s (sehat).
