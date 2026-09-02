# Workshop: Hermes For Developer

> **Dari bantu ngoding sampai security — bagaimana AI agent bekerja bareng developer Indonesia**
>
> Disusun dari pengalaman nyata: Hermes Agent + OpenClaw membantu membangun NadiBursa, mengelola 8 server, audit keamanan, sampai deploy aplikasi production.

---

## Daftar Isi

1. [Apa itu Hermes Agent?](#1-apa-itu-hermes-agent)
2. [Kenapa developer butuh AI agent (bukan cuma chatbot)?](#2-kenapa-developer-butuh-ai-agent)
3. [Konsep inti: Tools, Memory, Skills, Cron](#3-konsep-inti)
4. [Siapa Hermes dan siapa OpenClaw? (Multi-agent)](#4-multi-agent)
5. [Persiapan: yang kamu butuhkan](#5-persiapan)
6. [Instalasi step-by-step](#6-instalasi)
7. [Setup pertama: model, provider, gateway](#7-setup-pertama)
8. [Praktik 1: Bantu ngoding](#8-praktik-1-bantu-ngoding)
9. [Praktik 2: Code review & audit](#9-praktik-2-code-review)
10. [Praktik 3: Security & server](#10-praktik-3-security)
11. [Praktik 4: Otomasi dengan Cron](#11-praktik-4-otomasi)
12. [Best practices & pitfalls](#12-best-practices)
13. [FAQ](#13-faq)

---

## 1. Apa itu Hermes Agent?

Hermes Agent adalah **framework AI agent open-source** dari Nous Research yang berjalan di terminal, desktop, IDE, dan platform chat (Telegram, Discord, WhatsApp, dll).

Bedanya dengan chatbot biasa:

| | Chatbot biasa | Hermes Agent |
|---|---|---|
| Kemampuan | Menjawab pertanyaan | **Melakukan aksi** (buka file, edit kode, jalankan command, deploy) |
| Akses | Tidak ada | Terminal, filesystem, internet, database, API |
| Memori | Tidak ingat | **Ingat preferensi kamu antar sesi** |
| Belajar | Statis | **Belajar dari pengalaman** (skills) |
| Otomasi | Tidak bisa | **Bisa jalan terjadwal** (cron) |

Contoh nyata dari kita:

```text
Kamu: "deploy update aplikasi keuangan (Next.js) di server keuangan"
Hermes: → cek status PM2 & git → build via GitHub Actions runner
        → copy .next/standalone + static/public → pm2 restart
        → verifikasi HTTP 200 → lapor bukti end-state
```

Bukan sekadar "menjelaskan cara deploy" — **dia yang mengerjakan**.

### Kenapa namanya Hermes?

Hermes adalah dewa pembawa pesan dalam mitologi Yunani — cepat, gesit, dan penghubung antar dunia. Sesuai perannya: penghubung antara kamu dan mesin-mesinmu.

### Ekosistem Hermes

```text
Hermes Agent (Nous Research)
├── CLI          → hermes (terminal chat)
├── Desktop app  → hermes desktop (Electron)
├── Dashboard    → hermes dashboard (web admin)
├── Gateway      → konek ke Telegram/Discord/WhatsApp/Email/dll
├── IDE          → ACP server (VS Code, Zed, JetBrains)
└── OpenClaw     → agent saudara untuk task paralel / coding agent
```

---

## 2. Kenapa developer butuh AI agent?

### Masalah developer sehari-hari

```text
1. Banyak task repetitif   → bikin migration, scaffolding, boilerplate
2. Banyak file & konteks   → "di mana bug-nya?" butuh baca banyak file
3. Error berulang          → nginx 502, OOM, DB connection refused
4. Security kompleks       → audit server, firewall, secret management
5. Waktu terbatas          → deadline fitur + maintain infra

AI agent = asisten yang bisa BACA kode, JALANKAN command, dan LAPOR balik.
```

### Studi kasus nyata: 3 hari kerja yang bisa dikerjakan agent

```text
Hari 1 — Membangun NadiBursa (SaaS analitik saham):
  - PRD ditulis → diimplementasikan Next.js (screener, market overview)
  - Deploy ke server produksi (PM2, Nginx, domain, SSL)
  - 3 formula trading diimplementasikan (Entry Gate, Pullback, Trend Ride)

Hari 2 — Audit keamanan 8 server:
  - SSH key verification, UFW rules, listener, disk, package updates
  - Temuan: PasswordAuthentication yes, disk 77%, MySQL exposure

Hari 3 — Debug production (aplikasi keuangan, Next.js):
  - Bot WhatsApp tidak membalas → cek PM2 (online) & WAHA session
  - Root cause: session WAHA FAILED (403 logged out) + pesan kedua
    dibuang oleh cooldown 120 detik tanpa retry
  - Fix + verifikasi end-to-end
```

### Prinsip kerja agent (penting!)

```text
AI agent ≠ manusia yang sempurna.
AI agent = rekan kerja yang cepat tapi kadang salah.

Karena itu:
- Minta BUKTI (hasil command, HTTP status, output test)
- Jangan percaya buta — verifikasi klaim (seperti "kata OpenClaw sudah fix")
- Selalu ada backup & rollback plan
```

---

## 3. Konsep Inti

### 3.1 Tools — "tangan" agent

Hermes punya tools bawaan yang bisa dipanggil:

```text
terminal        → jalankan command (ssh, git, docker, npm, npx, pm2)
read_file       → baca file dengan nomor baris
write_file      → tulis/overwrite file
patch           → edit file secara presisi (find & replace)
search_files    → cari teks/file (seperti grep/find)
web_search      → cari informasi internet
web_extract     → baca isi halaman web
browser_*       → kontrol browser (navigate, click, type, screenshot)
delegate_task   → kirim subtask ke agent lain (paralel)
cronjob         → jadwalkan task otomatis
memory          → simpan fakta permanen
skill_manage    → buat/update prosedur reusable
```

Contoh alur kerja:

```text
Kamu: "kenapa bot WhatsApp aplikasi keuangan tidak membalas?"

Agent:
1. terminal  → pm2 list → catatan-keuangan ONLINE, tapi WAHA?
2. terminal  → curl WAHA session status → FAILED (403: logged out)
3. read_file → kode webhook → ketemu sender-cooldown 120 detik
4. terminal  → cek log PM2 → pesan kedua dibuang tanpa retry
5. terminal  → recover session + verifikasi WORKING → tes kirim
6. Lapor    → "session logout + cooldown membuang pesan; sudah pulih"
```

### 3.2 Memory — "ingatan jangka panjang"

```text
User profile : siapa kamu, preferensi, gaya kerja
Memory       : fakta environment (IP server, path, konvensi)

Contoh yang tersimpan:
- Fleet 8 server (IP, user, key SSH)
- "User suka beli saham dari bawah (pullback), bukan ngejar harga"
- "Deploy Keuangan via runner, tanpa git pull"
- "CPU host tanpa X86_V2: pin numpy==1.26.4"

Manfaat: kamu tidak perlu mengulang penjelasan tiap sesi.
```

### 3.3 Skills — "prosedur reusable"

Skill = prosedur tersimpan yang bisa dipanggil saat task cocok:

```text
skills/
├── ssh-fleet-access/        → cara akses 8 server
├── idx-bullish-screener/    → formula screening saham
├── production-infrastructure-auditing/ → cara audit server
├── github-pr-workflow/      → cara bikin PR
└── systematic-debugging/    → cara debug 4 fase
```

Contoh: skill `idx-bullish-screener` menyimpan formula v1.1, aturan RVOL20, batas RSI — jadi tiap screening konsisten, tidak asal.

### 3.4 Cron — "otomasi terjadwal"

```text
Setiap 20 menit jam trading → scan saham IDX → simpan cache
Setiap pagi 07:00          → laporan server + Cloudflare
Setiap hari                → alert kalau disk > 85%
```

### 3.5 Approval & keamanan

```text
Mode approval:
- Perintah berbahaya (hapus file, restart service, ubah firewall)
  → butuh persetujuan kamu
- Perintah read-only (baca file, status) → jalan otomatis

Prinsip:
- "Read-only sampai ada approval" untuk production
- Jangan ubah firewall/DB/service tanpa scope yang jelas
- Semua perubahan idempotent (bisa diulang tanpa efek ganda)
```

---

## 4. Multi-Agent

### Hermes vs OpenClaw — pembagian kerja

Dari pengalaman kita:

```text
┌─────────────┐         ┌─────────────┐
│   Hermes    │         │  OpenClaw   │
│ (Lead Eng)  │         │ (Coding)    │
├─────────────┤         ├─────────────┤
│ Strategi    │         │ Implementasi│
│ Arsitektur  │         │ TDD         │
│ Audit server│         │ Git commit  │
│ Security    │         │ Build/deploy│
│ Verifikasi  │         │ Feature     │
│ Deployment  │         │             │
└─────────────┘         └─────────────┘
```

Alur kerja nyata di project NadiBursa:

```text
1. Kamu minta fitur "Pullback Watchlist"
2. Hermes: buat spesifikasi formula (Stage A/B/C, risk plan) dalam MD
3. OpenClaw: implementasi di kode + commit + build
4. Hermes: verifikasi hasil (baca kode, cek commit, test HTTP)
5. Hermes: laporkan temuan (misal: "PM2 belum restart, build tidak aktif")
```

**Penting:** klaim OpenClaw "sudah beres" ≠ fakta. Hermes selalu verifikasi ulang:

```text
OpenClaw bilang : "RVOL sudah di-clamp"
Hermes cek      : grep clamp di kode → ada rvolClamped = min(rvol, 20) ✅

OpenClaw bilang : "teaser jadi 5 baris"
Hermes cek      : TEASER_COUNT = 5 di kode ✅ TAPI PM2 belum restart ❌
                  → restart → baru 5 baris muncul
```

---

## 5. Persiapan

### Requirement minimal

```text
OS        : Linux (Ubuntu/Debian direkomendasikan), macOS, Windows (WSL)
RAM       : 2 GB minimal (4 GB nyaman)
Disk      : 2 GB free
Python    : 3.10+ (installer otomatis mengurus via uv)
Internet  : dibutuhkan untuk install & API
API key   : minimal satu provider LLM (lihat bawah)
```

### Pilihan provider model

```text
Murah / hemat   : DeepSeek, OpenRouter (banyak model murah)
Balance         : OpenRouter (GPT, Claude, Gemini, Llama)
Premium         : Anthropic Claude, OpenAI GPT
Local / gratis  : Ollama, LM Studio (jalankan model sendiri)

Tips hemat:
- Gunakan model murah untuk task ringan (chat, edit sederhana)
- Model bagus untuk task kompleks (debug, arsitektur)
- Hermes bisa swap model mid-workflow
```

### Persiapan akun

```text
1. Buat akun di provider pilihan (contoh: OpenRouter / DeepSeek)
2. Generate API key
3. Simpan key — nanti dimasukkan saat `hermes setup`
```

---

## 6. Instalasi

### Step 1: Install Hermes

```bash
# Linux / macOS / WSL
curl -fsSL https://hermes-agent.nousresearch.com/install.sh | bash
```

Installer otomatis mengurus:

```text
uv (Python package manager) → Python environment → Hermes launcher
```

### Step 2: Verifikasi instalasi

```bash
hermes --version
hermes doctor        # health check: cek semua komponen
```

`hermes doctor` akan menunjukkan status tiap bagian — pastikan tidak ada yang merah.

### Step 3: Setup pertama

```bash
hermes setup
```

Wizard akan memandu:

```text
1. Pilih provider model
2. Masukkan API key
3. Pilih model default
4. Konfigurasi dasar (nama agent, dsb)
```

### Step 4: Coba jalankan

```bash
# Chat interaktif
hermes

# Pertanyaan langsung (one-shot)
hermes chat -q "Jelaskan apa itu REST API dalam 2 kalimat"

# TUI (terminal UI lebih keren)
hermes --tui
```

### Step 5: (Opsional) Hubungkan ke Telegram

```bash
hermes gateway connect telegram
```

Sekarang kamu bisa chat dengan agent dari HP — **full tool access**, bukan cuma chatbot:

```text
Dari Telegram: "cek status server keuangan"
Agent: SSH → cek UFW/listener/disk → kirim laporan ke Telegram kamu
```

---

## 7. Setup Pertama

### Konfigurasi penting

```bash
hermes config set model.provider deepseek
hermes config set model.model deepseek-chat
hermes config set display.interface tui     # terminal UI
hermes model                                 # ganti model cepat
```

### Struktur folder

```text
~/.hermes/
├── config.yaml          → pengaturan (bukan secret!)
├── .env                 → API keys (secret saja!)
├── skills/              → prosedur reusable
├── sessions/            → riwayat percakapan
├── state.db             → database sesi
└── logs/                → log gateway
```

**Aturan emas:**

```text
- Secret (API key, password) → .env — jangan pernah di config.yaml
- Pengaturan → config.yaml via `hermes config set` (jangan edit manual)
- Jangan commit .env ke git!
```

### Mode kerja

```bash
hermes                    # interactive chat
hermes chat -q "..."      # one-shot
hermes --continue         # lanjut sesi terakhir
hermes --resume <session> # lanjut sesi tertentu
hermes desktop            # aplikasi desktop
hermes dashboard          # web admin
```

---

## 8. Praktik 1: Bantu Ngoding

### 8.1 Scaffolding project

```text
Kamu: "buatkan API Next.js (App Router) untuk manajemen invoice,
       dengan prisma schema, route handler CRUD, dan validasi Zod"

Hermes:
1. npx prisma init → definisikan model Invoice (number, customer, amount, status)
2. prisma migrate dev → buat tabel
3. Buat route handler app/api/invoices/route.ts (GET/POST)
4. Buat app/api/invoices/[id]/route.ts (GET/PATCH/DELETE)
5. Validasi input pakai Zod (schema + pesan error)
6. Jalankan build + test sederhana → verifikasi
```

### 8.2 Debug error

```text
Kamu: "kenapa webhook WhatsApp di aplikasi keuangan tidak memproses
       event session.status?"

Hermes:
1. read_file app/api/webhooks/whatsapp/route.ts
   → ketemu: handler lama mewajibkan fromNumber, padahal event
     session.status tidak punya fromNumber
2. Cek log PM2 → event session.status ditolak handler
3. Fix: branch khusus untuk session.status sebelum validasi pesan
4. Test ulang → event terproses
```

### 8.3 TDD (Test-Driven Development)

```text
Alur TDD yang dipakai OpenClaw:
1. Tulis test dulu (RED — test gagal)
2. Implementasi minimal (GREEN — test lolos)
3. Refactor

Hermes punya skill test-driven-development untuk memandu ini.
```

### 8.4 Review code

```text
Kamu: "review PR ini sebelum merge"

Hermes:
1. git diff / buka PR
2. Cek: security (SQL injection, XSS), logic bug, duplicate code
3. Cek: apakah ada secret di code?
4. Lapor temuan per file + rekomendasi
```

**Contoh temuan review nyata di NadiBursa:**

```text
🔴 Kritis:
  - Data UMA/suspensi "Clean (No Notasi)" hardcoded — selalu PASS padahal
    tidak pernah dicek ke sumber → data palsu
  - 3 file punya 3 formula berbeda → hasil inkonsisten antar halaman

🟠 Penting:
  - Teks rule bilang "RVOL20" tapi datanya RVOL10 → menyesatkan
  - RR "1 : 2.50" hardcoded, tidak dihitung dari level nyata

🟡 Minor:
  - Judul dobel "Rule Formula Rule"
```

---

## 9. Praktik 2: Code Review

### Checklist review yang dipakai

```text
Security:
  [ ] SQL injection (query mentah, tanpa binding)
  [ ] XSS (output tanpa escape)
  [ ] Secret/credential di code atau log
  [ ] Authorization (user A bisa akses data user B?)
  [ ] Rate limiting di endpoint publik

Logic:
  [ ] Null handling (data kosong → error?)
  [ ] Boundary (angka 0, negatif, besar)
  [ ] Race condition (double submit, duplicate insert)
  [ ] Error handling (catch + log + user-friendly)

Kualitas:
  [ ] Duplicate code
  [ ] Magic number / hardcoded
  [ ] Naming jelas
  [ ] Test tersedia
```

### Alur verifikasi "klaim vs fakta"

```text
Seseorang bilang: "sudah fix"
→ Jangan langsung percaya
→ Verifikasi: baca kode, jalankan test, cek output asli

Contoh nyata:
  Klaim   : "RVOL di-clamp maks 20x"
  Cek     : grep rvolClamped → `rvol > 20 ? 20 : rvol` ✅

  Klaim   : "teaser naik jadi 5 baris"
  Cek     : TEASER_COUNT = 5 ✅ tapi PM2 belum restart → masih 2 baris ❌
  Aksi    : restart PM2 → 5 baris ✅
```

---

## 10. Praktik 3: Security & Server

### 10.1 Audit server (read-only dulu!)

```text
Prinsip: JANGAN ubah apa pun sebelum tahu kondisinya.

Audit standar:
1. SSH access        → key verification, strict known_hosts
2. Firewall (UFW)    → status, default policy, rule yang ada
3. Listener          → port apa yang terbuka ke publik?
4. Disk & memory     → kapasitas, sisa
5. Package updates   → berapa pending, ada security update?
6. Service health    → pm2, next-server, node, mysql, nginx
7. Log check         → error berulang?
```

Hasil audit nyata (server thelavande):

```text
Hostname : TheLavendeResidence
OS       : Ubuntu 20.04.5 LTS
UFW      : active, default deny incoming
Temuan:
  🟠 Disk 77% terpakai
  🟠 PasswordAuthentication yes (harusnya key only)
  🟡 SSH terbuka ke semua IP
  🟡 78 package pending update
```

### 10.2 Menambahkan akses SSH dengan aman

```text
1. Generate key pair: ssh-keygen -t ed25519
2. Public key → server: ~/.ssh/authorized_keys
3. Private key → SIMPAN RAPAT (mode 600, jangan pernah share)
4. Test koneksi dengan StrictHostKeyChecking
5. (Opsional) matikan PasswordAuthentication
```

### 10.3 Firewall (UFW) — contoh nyata

```bash
# Allowlist IP tertentu untuk SSH
sudo ufw allow from 125.162.167.235 to any port 22 proto tcp comment "Approved client SSH"

# Backup rules dulu sebelum ubah!
sudo cp /etc/ufw/user.rules /etc/ufw/backups-hermes/user.rules.$(date +%Y%m%d)

# Cek status
sudo ufw status verbose
```

```text
Pola yang benar:
- Default: deny incoming
- Allowlist IP spesifik, bukan "Anywhere"
- Backup sebelum perubahan
- Verifikasi setelah perubahan
```

### 10.4 Secret management

```text
JANGAN PERNAH:
- Menaruh password/API key di code atau log
- Commit .env ke git
- Menampilkan secret di chat/laporan

HARUS:
- Secret di .env / secret manager (mode 600)
- Log memakai redaction ([REDACTED])
- Rotasi credential → update SEMUA yang pakai (pengalaman: WAHA API key
  dirotasi tapi .env aplikasi keuangan tidak update → session gagal!)
```

### 10.5 Debug production (studi kasus nyata — aplikasi keuangan, Next.js)

```text
Gejala   : Bot WhatsApp aplikasi keuangan tidak membalas pesan
Cek 1    : pm2 list → catatan-keuangan ONLINE (aplikasi hidup)
Cek 2    : curl WAHA session status → FAILED (403: primary device logged out)
Cek 3    : tail PM2 log → webhook session.status ditolak handler lama
          (wajib fromNumber, padahal event status tidak punya)
Cek 4    : read_file route.ts → ada cooldown 120 detik → pesan kedua
          dibuang tanpa retry
Kesimpulan: 2 masalah — session WAHA logout + pesan kedua di-drop
Fix      : recover session WAHA (login ulang) + buka outbound gate
          (dengan approval) → verifikasi WORKING + tes kirim
```

**Pelajaran:** aplikasi hidup (PM2 ONLINE) ≠ bot sehat. Cek layer transportnya (WAHA session) dan log webhook — kadang ada 2 masalah sekaligus (session logout + cooldown drop), bukan satu.

---

## 11. Praktik 4: Otomasi dengan Cron

### Contoh cron nyata di NadiBursa

```text
1. Worker scan saham IDX tiap 20 menit (jam trading 09:00-16:00)
   → fetch TradingView → filter formula → simpan cache

2. Alert server tiap pagi
   → cek disk, memory, service → kirim ke Telegram kalau ada anomali

3. (Rencana) Laporan Cloudflare mingguan
   → pull analytics → generate PDF + PNG → kirim
```

### Cara bikin cron di Hermes

```text
Di dalam sesi Hermes, cukup minta:

"Jadwalkan scan saham IDX setiap 20 menit jam 09:00-16:00
 hari Senin-Jumat, hasilnya kirim ke Telegram"

Hermes akan membuat cron job dengan:
- schedule (cron expression)
- prompt self-contained (bisa jalan tanpa konteks)
- delivery target (Telegram / local / email)
- skills yang dibutuhkan
```

### Pola watchdog yang baik

```text
- Silent kalau normal, baru ngomong kalau ada masalah
- Idempotent (tidak dobel saat dijalankan ulang)
- Ada kill switch (bisa pause tanpa hapus)
- Log & audit
```

---

## 12. Best Practices

### 12.1 Cara meminta bantuan yang efektif

```text
❌ "cek server"
✅ "cek server keuangan: SSH, UFW, listener, disk — read-only, jangan ubah apa pun"

❌ "perbaiki error ini"
✅ "bot WhatsApp aplikasi keuangan tidak membalas. Cek PM2, WAHA session,
    dan log webhook — lapor root cause dulu sebelum fix"

Rumus: KONTEKS + SCOPE + BATASAN
```

### 12.2 Prinsip untuk production

```text
1. Read-only dulu, baru ubah (setelah approval)
2. Backup sebelum perubahan
3. Perubahan idempotent
4. Verifikasi end-state (HTTP 200, test lolos, output asli)
5. Rollback plan selalu siap
6. Jangan pernah ubah firewall/DB/service tanpa scope jelas
7. Secret jangan pernah muncul di log/chat/laporan
```

### 12.3 Bekerja dengan multi-agent

```text
- Satu agent buat spesifikasi/arsitektur, satu buat implementasi
- Selalu verifikasi klaim agent lain (baca kode, cek output)
- Bagi task berdasarkan kekuatan: strategi vs eksekusi
- Transisi: spesifikasi tertulis (file MD) → implementasi → verifikasi
```

### 12.4 Menghemat token

```text
- Gunakan model murah untuk task ringan
- Model premium untuk debug kompleks
- Simpan prosedur sebagai skill (tidak perlu dijelaskan ulang)
- Memory menyimpan fakta → tidak perlu konteks berulang
```

### 12.5 Pitfalls yang sering terjadi

```text
⚠️ Agent bilang "selesai" tapi belum di-verify → minta bukti
⚠️ Build tanpa restart → perubahan tidak aktif (PM2!)
⚠️ .env beda antar environment → password basi, config beda
⚠️ Fallback data statis ditampilkan sebagai live → user salah keputusan
⚠️ Hapus file log dengan rm → proses nulis gagal → pakai truncate
⚠️ NumPy wheel crash di CPU lama → pin versi (numpy==1.26.4)
⚠️ Cron jalan di luar jam → hemat resource, guard jam trading
```

---

## 13. FAQ

### Q: Hermes vs Claude Code / Codex / Cursor — bedanya apa?

```text
Semua sama-sama AI coding agent. Hermes bedanya:
- Multi-platform (Telegram, Discord, WhatsApp, email, dsb)
- Memory & skills persisten (belajar dari pengalaman)
- Gateway & cron (otomasi terjadwal)
- Provider-agnostic (ganti model kapan saja)
- Open-source + extensible (plugin, MCP, custom tools)
```

### Q: Apakah butuh GPU / server sendiri?

```text
Tidak. Hermes jalan di laptop/VPS biasa; model dipanggil via API cloud
(DeepSeek, OpenRouter, dll). Kalau mau 100% lokal, bisa pakai Ollama.
```

### Q: Berapa biaya?

```text
Hermes sendiri open-source (gratis).
Biaya = token API model. Model hemat (DeepSeek) bisa beberapa dolar/bulan
untuk penggunaan normal.
```

### Q: Apakah aman memberi akses terminal ke AI?

```text
- Hermes punya approval mode: perintah berbahaya butuh persetujuan
- Prinsip: read-only default untuk production
- Jangan pernah kasih secret; gunakan .env + redaction
- Audit log tersedia
Seperti memberi akses ke rekan kerja: percaya tapi verifikasi.
```

### Q: Bisakah Hermes bantu di luar coding?

```text
Bisa! Dari pengalaman kita:
- Screening saham (formula trading otomatis)
- Audit server & Cloudflare
- Bikin logo & desain
- Riset & laporan
- ERP / akuntansi (jurnal, pajak)
- WhatsApp bot (dengan guardrail anti-ban)
```

### Q: Mulai dari mana?

```text
1. Install: curl -fsSL https://hermes-agent.nousresearch.com/install.sh | bash
2. Setup: hermes setup
3. Coba: hermes chat -q "apa isi folder ini?"
4. Lanjutkan dengan task kecil: bantu baca kode, review, cek server
```

---

## Lampiran A: Cheat Sheet Perintah

```bash
# Instalasi & setup
curl -fsSL https://hermes-agent.nousresearch.com/install.sh | bash
hermes doctor                      # health check
hermes setup                       # wizard setup
hermes model                       # ganti model

# Menjalankan
hermes                             # chat interaktif
hermes chat -q "query"             # one-shot
hermes --tui                       # terminal UI
hermes --continue                  # lanjut sesi
hermes desktop                     # desktop app
hermes dashboard                   # web admin

# Konfigurasi
hermes config set KEY VALUE        # ubah pengaturan
hermes gateway connect telegram    # hubungkan platform

# Di dalam sesi
/help                              # daftar perintah
/new                               # sesi baru
/exit                              # keluar
```

## Lampiran B: Glossary

```text
Agent        : program AI yang bisa menggunakan tools untuk melakukan aksi
Tool         : kemampuan agent (terminal, file, web, browser, dll)
Tool calling : mekanisme model memanggil tools
Memory       : penyimpanan fakta jangka panjang
Skill        : prosedur reusable yang dimuat saat task cocok
Cron         : penjadwalan task otomatis
Gateway      : penghubung ke platform (Telegram, Discord, dll)
Provider     : penyedia model LLM (OpenAI, DeepSeek, OpenRouter, dll)
Model        : LLM spesifik (gpt-4o, deepseek-chat, claude-sonnet, dll)
MCP          : protokol standar menghubungkan agent ke tools eksternal
TUI          : Terminal User Interface
Idempotent   : operasi yang hasilnya sama walau dijalankan berulang
```

---

## Penutup

```text
Hermes bukan pengganti developer — Hermes adalah force multiplier.
Yang kamu bawa: konteks bisnis, keputusan, dan tanggung jawab.
Yang Hermes bawa: kecepatan eksekusi, ingatan, dan konsistensi.

Mulai dari task kecil. Verifikasi setiap hasil.
Lalu naikkan level: dari bantu ngoding → audit security → otomasi penuh.

Selamat membangun! 🚀
```

---

*Materi disusun dari pengalaman praktis project NadiBursa (SaaS analitik saham), pengelolaan 8 server produksi, dan kolaborasi Hermes + OpenClaw. Update terakhir: September 2026.*
