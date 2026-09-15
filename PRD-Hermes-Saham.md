# Product Requirements Document (PRD)

# Hermes Saham

> **Working title:** Hermes Saham  
> **Kategori:** AI Stock Decision Assistant untuk saham Indonesia  
> **Platform awal:** Telegram-first dengan web companion  
> **Versi PRD:** 1.0 Draft  
> **Tanggal:** 15 September 2026  
> **Owner:** Product & Engineering  
> **Status:** Draft untuk validasi produk

---

## 1. Executive Summary

Hermes Saham adalah asisten saham berbasis percakapan yang membantu investor dan trader ritel Indonesia menyaring saham, memahami kondisi pasar, menyusun rencana entry, mengendalikan risiko, memantau posisi, dan mengevaluasi keputusan secara disiplin.

Produk ini mengambil pengalaman pendampingan saham yang biasanya hanya tersedia melalui analis pribadi, lalu mengubahnya menjadi sistem yang dapat digunakan banyak orang melalui Telegram dan web.

Hermes Saham bukan mesin “sinyal pasti cuan”. Produk harus berfungsi sebagai **decision-support system** dengan alur yang transparan:

```text
Data pasar
→ validasi freshness dan market phase
→ formula deterministik
→ risk engine
→ keputusan terstruktur
→ penjelasan AI yang grounded
→ alert dan evaluasi hasil
```

AI tidak boleh menciptakan keputusan BUY, entry, stop loss, target, atau klaim fundamental tanpa dukungan data dan formula yang memadai. Sistem harus dapat mengatakan **tidak ada saham yang layak**, `WAIT`, atau `INVALIDATED`.

---

## 2. Visi Produk

### 2.1 Visi

Membuat pendamping analisis saham yang personal, disiplin, transparan, dan dapat dipercaya oleh investor ritel Indonesia—seperti memiliki analis saham pribadi yang memahami profil risiko, modal, gaya trading, posisi aktif, dan kondisi pasar pengguna.

### 2.2 Misi

1. Mengurangi keputusan impulsif, FOMO, dan kebiasaan mengejar saham yang sudah terlalu tinggi.
2. Membantu pengguna mencari entry yang lebih aman dan terukur.
3. Mengubah data teknikal yang kompleks menjadi rencana eksekusi sederhana.
4. Menjelaskan alasan sebuah saham layak dipantau, layak dimasuki, harus ditunggu, atau sudah invalid.
5. Membiasakan pengguna menentukan risiko sebelum memikirkan keuntungan.
6. Menyediakan jejak audit agar keputusan dan performa formula dapat dievaluasi.

### 2.3 Value Proposition

> “Bukan sekadar memberi saham pilihan, tetapi membantu pengguna mengetahui **mengapa**, **kapan masuk**, **berapa risiko**, **kapan batal**, dan **apa yang harus dilakukan setelah membeli**.”

---

## 3. Problem Statement

Investor dan trader ritel menghadapi beberapa masalah utama:

1. Screener biasa menghasilkan terlalu banyak saham tanpa prioritas yang jelas.
2. Banyak rekomendasi hanya mengatakan “buy” tanpa kondisi entry dan invalidation.
3. Data, narasi, dan level harga sering tidak sinkron atau sudah kedaluwarsa.
4. Pengguna mudah membeli saham yang sudah extended karena takut ketinggalan.
5. Stop loss dan position sizing baru dipikirkan setelah transaksi dilakukan.
6. Rekomendasi AI generik dapat mengarang level harga atau menggunakan data yang tidak segar.
7. Sinyal grup sering tidak menjelaskan formula, sumber, timestamp, dan kegagalan filter.
8. Level entry, stop, dan target dapat tidak sesuai fraksi harga IDX sehingga tidak dapat dipasang sebagai order.
9. Pengguna tidak memiliki jurnal objektif untuk mengetahui apakah strategi benar-benar bekerja.
10. Pendampingan analis personal tidak mudah diakses oleh semua orang.

---

## 4. Tujuan Produk

### 4.1 Tujuan Utama

- Menghasilkan shortlist saham yang relevan dengan profil pengguna dan kondisi pasar.
- Menyediakan rencana trading kondisional yang dapat dieksekusi.
- Menjelaskan keputusan formula dalam bahasa sederhana.
- Menjaga pengguna dari entry impulsif dan risiko berlebihan.
- Mendampingi posisi dari fase watchlist sampai exit/evaluasi.
- Mengukur kualitas sinyal secara objektif melalui outcome tracking.

### 4.2 Non-Goals

MVP tidak ditujukan untuk:

- Menjanjikan keuntungan atau akurasi pasti.
- Menjalankan transaksi otomatis ke broker.
- Mengelola dana pengguna.
- Menggantikan keputusan pengguna atau penasihat investasi berizin.
- Menyediakan rekomendasi tanpa data yang cukup dan segar.
- Menyalin sinyal komunitas atau influencer tanpa verifikasi.
- Menampilkan broker-flow/bandarmology tanpa hak data komersial.
- Mendukung derivatif, forex, kripto, atau short selling pada MVP.
- Melakukan prediksi harga menggunakan LLM secara bebas.

---

## 5. Target Pengguna

### 5.1 Persona A — Pemula Terarah

**Karakteristik:**

- Baru mulai berinvestasi atau trading saham.
- Bingung memilih saham dari ratusan emiten.
- Belum konsisten menentukan entry, stop loss, dan ukuran posisi.
- Membutuhkan penjelasan sederhana dan edukatif.

**Kebutuhan:**

- Shortlist terbatas.
- Bahasa nonteknis.
- Penjelasan risiko.
- Larangan entry ketika setup belum lengkap.

### 5.2 Persona B — Swing Trader Sibuk

**Karakteristik:**

- Memiliki pekerjaan atau bisnis utama.
- Tidak bisa memantau pasar sepanjang hari.
- Memegang saham beberapa hari sampai beberapa minggu.

**Kebutuhan:**

- Screening pagi dan intraday.
- Alert perubahan status.
- Pullback entry, breakout confirmation, dan trailing stop.
- Portfolio-aware recommendations.

### 5.3 Persona C — Momentum Trader Disiplin

**Karakteristik:**

- Memburu pergerakan dengan volume dan momentum.
- Membutuhkan keputusan cepat tetapi tetap terukur.

**Kebutuhan:**

- RVOL, likuiditas, VWAP, resistance, extension, dan market regime.
- Harga entry yang executable.
- Invalidation yang tegas.
- Peringatan anti-chase.

### 5.4 Persona D — Investor yang Ingin Belajar

**Karakteristik:**

- Tidak hanya ingin menerima ticker.
- Ingin memahami alasan dan memperbaiki kualitas keputusan.

**Kebutuhan:**

- Penjelasan setiap gate.
- Jurnal keputusan.
- Review mingguan.
- Perbandingan rencana dengan hasil aktual.

---

## 6. Jobs To Be Done

Ketika pengguna membuka Hermes Saham, mereka ingin:

1. “Carikan saham yang paling layak saya pantau hari ini.”
2. “Saya punya modal tertentu; berapa lot yang aman?”
3. “Apakah saham ini masih boleh dibeli atau sudah terlalu tinggi?”
4. “Saya ingin beli dari bawah, bukan mengejar harga.”
5. “Apa yang harus terjadi agar status berubah dari WATCH menjadi ENTER?”
6. “Kalau skenarionya gagal, pada harga berapa saya harus keluar?”
7. “Saya sudah punya saham ini; hold, tambah, kurangi, atau keluar?”
8. “Kenapa saham ini ditolak meskipun sedang naik?”
9. “Apakah data yang dipakai masih live atau hanya harga penutupan?”
10. “Bagaimana hasil strategi saya selama satu bulan?”

---

## 7. Prinsip Produk

### 7.1 Data Before Narrative

AI hanya menjelaskan data terstruktur. Jika data tidak tersedia, AI harus mengatakan tidak tersedia.

### 7.2 Deterministic Decision, Grounded Explanation

Formula menentukan status. LLM tidak boleh mengubah `WAIT` menjadi `ENTER` atau membuat level sendiri.

### 7.3 No Signal Is a Valid Outcome

Sistem boleh menyatakan tidak ada setup layak. Kuantitas sinyal bukan ukuran utama kualitas.

### 7.4 Conditional, Not Certain

Gunakan pola:

```text
Jika A dan B terjadi → rencana C dapat dipertimbangkan.
Jika D terjadi → setup invalid.
```

Hindari “pasti naik”, “auto cuan”, dan kepastian serupa.

### 7.5 Risk First

Setiap rencana entry harus memiliki invalidation, risiko per saham, position sizing, dan R:R aktual.

### 7.6 Anti-Chase by Default

Produk harus memperingatkan saham extended dan mengutamakan entry dekat support/VWAP/SMA ketika profil pengguna memilih pendekatan konservatif.

### 7.7 Explain Every Decision

Pengguna harus dapat melihat gate mana yang lolos, gagal, tidak tersedia, atau belum terkonfirmasi.

### 7.8 Same Snapshot Everywhere

Web, Telegram, API, alert, dan AI Chat harus memakai snapshot keputusan yang sama—bukan menghitung ulang secara berbeda.

---

## 8. Status Keputusan

### 8.1 `WATCH`

Saham layak dipantau, tetapi trigger entry belum lengkap.

Wajib menampilkan:

- Alasan masuk watchlist.
- Kondisi yang belum terpenuhi.
- Trigger yang ditunggu.
- Batas waktu atau kondisi kedaluwarsa setup.

### 8.2 `ENTER`

Semua gate wajib, data freshness, risk plan, dan executable price validation telah lulus.

Wajib menampilkan:

- Entry area.
- Entry reference untuk perhitungan risiko.
- Stop loss/invalidation.
- Target atau trailing policy.
- R:R aktual.
- Position sizing berdasarkan profil pengguna.

### 8.3 `WAIT`

Pengguna tidak sebaiknya entry sekarang karena data tidak cukup, harga extended, konfirmasi belum muncul, market regime tidak mendukung, atau risiko tidak menarik.

`WAIT` bukan sinyal gagal; ia adalah kontrol risiko.

### 8.4 `INVALIDATED`

Setup tidak berlaku lagi karena struktur harga, stop, formula, corporate action, atau batas waktu setup telah rusak.

Status ini harus menjelaskan:

- Penyebab invalidation.
- Timestamp.
- Apakah pengguna masih memiliki posisi.
- Tindakan yang perlu dipertimbangkan berdasarkan plan awal.

### 8.5 Status Post-MVP

- `MANAGE` — posisi aktif sedang dikelola.
- `TRIM` — pengurangan parsial berdasarkan aturan.
- `EXIT` — kondisi exit terpenuhi.
- `CLOSED` — transaksi telah ditutup dan outcome dicatat.

---

## 9. Pengalaman Pengguna Utama

### 9.1 Onboarding

Hermes Saham mengumpulkan:

- Tujuan: belajar, swing trading, momentum, atau investasi.
- Modal yang dialokasikan.
- Risiko maksimal per transaksi.
- Risiko maksimal total posisi terbuka.
- Horizon waktu.
- Preferensi entry: pullback, breakout, atau kombinasi.
- Sektor atau saham yang dihindari.
- Posisi aktif dan average price jika pengguna bersedia mengisi.
- Jam dan channel alert yang diinginkan.
- Tingkat pengalaman pengguna.

Sistem tidak boleh menerbitkan rekomendasi terpersonalisasi sebelum parameter risiko minimum tersedia. Pengguna yang melewati onboarding hanya memperoleh analisis edukatif dan watchlist nonpersonal.

### 9.2 Daily Brief

Contoh permintaan:

```text
“Pagi ini ada saham menarik?”
```

Jawaban harus berurutan:

1. Status dan fase pasar.
2. Timestamp dan umur data.
3. Kondisi IHSG/market breadth jika tersedia.
4. Maksimal beberapa kandidat prioritas.
5. Status setiap kandidat: WATCH, ENTER, WAIT, atau INVALIDATED.
6. Alasan singkat.
7. Trigger dan invalidation.
8. Risk warning.
9. Pernyataan “tidak ada kandidat” jika tidak ada yang lolos.

### 9.3 Analisis Ticker

Contoh:

```text
“Analisa MGRO. Saya belum punya.”
```

Hermes harus:

- Memastikan ticker valid.
- Mengambil snapshot terbaru.
- Menyebut market phase dan freshness.
- Menjalankan formula yang sesuai.
- Menampilkan pass/fail/unknown gates.
- Menentukan status final.
- Menghasilkan risk plan hanya jika syarat terpenuhi.
- Memastikan seluruh harga sesuai fraksi IDX.
- Menyertakan sumber data dan formula version.

### 9.4 Analisis Posisi Aktif

Contoh:

```text
“Saya punya ABCD average 1.000 sebanyak 100 lot.”
```

Hermes harus membedakan:

- Analisis setup baru.
- Manajemen posisi yang sudah dimiliki.
- Unrealized P/L.
- Risiko jika stop tersentuh.
- Concentration risk terhadap modal total.
- Plan awal versus kondisi terbaru.

Hermes tidak boleh menganggap pengguna belum memiliki posisi jika portfolio context menyatakan sebaliknya.

### 9.5 Follow-Up Conversation

Konteks percakapan yang harus dipertahankan:

- Ticker yang sedang dibahas.
- Apakah pengguna sudah memiliki saham.
- Average price dan ukuran posisi.
- Rencana aktif.
- Profil risiko.
- Horizon.
- Snapshot data yang menjadi dasar jawaban.

Jika data pasar berubah material, Hermes harus mengambil snapshot baru dan menjelaskan bahwa konteks harga telah diperbarui.

---

## 10. Fitur Utama

### 10.1 Conversational Stock Screener — P0

Pengguna dapat meminta filter dalam bahasa natural:

```text
“Carikan saham mid-cap yang likuid, belum naik terlalu tinggi, dan dekat SMA20.”
```

Sistem mengubah permintaan menjadi filter terstruktur yang dapat dilihat pengguna.

Output harus memuat:

- Filter yang digunakan.
- Universe count.
- Jumlah yang lolos setiap tahap.
- Kandidat akhir.
- Alasan kandidat yang hampir lolos tetapi ditolak.
- Timestamp dan sumber.

### 10.2 Safe Pullback Mode — P0

Mode default untuk pengguna konservatif:

- Trend utama masih hidup.
- Harga berada dekat support/SMA20/VWAP sesuai formula version.
- Volume saat koreksi mengecil.
- Tidak ada pola falling knife.
- Entry menunggu konfirmasi.
- Harga tidak extended.
- R:R memenuhi minimum strategi.

Status:

```text
PULLBACK_WATCH
PULLBACK_READY
PULLBACK_ENTER
PULLBACK_INVALID
```

### 10.3 Momentum Mode — P0

Menyaring saham berdasarkan kombinasi:

- Likuiditas.
- Relative volume yang didefinisikan jelas, misalnya RVOL10 atau RVOL20.
- Struktur trend.
- Momentum.
- VWAP.
- Resistance/breakout.
- Extension guard.
- Risk/reward.

Semua threshold harus berada dalam formula version dan tidak boleh diubah diam-diam.

### 10.4 Anti-Chase and Falling-Knife Guard — P0

Sistem harus memberikan `WAIT` atau penolakan ketika:

- Harga terlalu jauh dari SMA20.
- Performa satu bulan sudah ekstrem sesuai strategi.
- RSI berada pada zona yang ditolak formula.
- Gap atau pergerakan harian ekstrem.
- Harga jatuh tanpa konfirmasi dan volume tidak mendukung healthy pullback.
- Likuiditas tidak memadai.
- Data tidak cukup.

### 10.5 Executable Risk Plan — P0

Risk plan mencakup:

- Entry minimum dan maksimum.
- Entry reference.
- Breakout resistance dan executable trigger.
- Stop loss.
- Target 1 dan Target 2 atau trailing-stop policy.
- Risk per share.
- R:R aktual ke setiap target.
- Position sizing.
- Maksimum loss dalam rupiah.

Semua harga order saham IDX harus mengikuti tick size berdasarkan Harga Previous:

```text
Previous < Rp200              → fraksi Rp1
Rp200 sampai < Rp500          → fraksi Rp2
Rp500 sampai < Rp2.000        → fraksi Rp5
Rp2.000 sampai < Rp5.000      → fraksi Rp10
Rp5.000 ke atas               → fraksi Rp25
```

Rounding harus direction-aware. R:R dihitung ulang setelah seluruh level menjadi executable.

Jika R:R aktual tidak memenuhi minimum, status tidak boleh tetap `ENTER`.

### 10.6 Position Sizing — P0

Input minimum:

- Modal atau buying power.
- Risk percentage atau maksimum loss rupiah.
- Entry reference.
- Stop loss.
- Lot size IDX.
- Estimasi biaya transaksi.

Output:

- Jumlah lot maksimum.
- Nilai posisi.
- Maksimum kerugian jika stop tersentuh.
- Persentase modal yang digunakan.
- Peringatan jika position size terlalu terkonsentrasi.

### 10.7 Watchlist and Alerts — P0

Pengguna dapat:

- Menambahkan ticker ke watchlist.
- Menyimpan alasan dan trigger.
- Menerima alert hanya ketika status berubah.
- Mengatur quiet hours.
- Memilih alert Telegram dan web.

Alert harus idempotent, deduplicated, dan menyebut snapshot timestamp.

### 10.8 Portfolio Context — P0 Terbatas

MVP mendukung input manual:

- Ticker.
- Average price.
- Jumlah lot.
- Tanggal entry.
- Plan atau alasan entry.

Integrasi broker bukan bagian MVP.

### 10.9 Decision Audit — P0

Setiap keputusan menyimpan:

- Data snapshot ID.
- Formula ID dan version.
- Pass/fail/unknown gates.
- Raw theoretical levels.
- Executable levels.
- Final action.
- Penjelasan AI.
- Model/provider version.
- User context yang relevan.
- Timestamp dan market phase.

### 10.10 Trading Journal — P1

Pengguna dapat mencatat:

- Entry dan exit aktual.
- Alasan transaksi.
- Screenshot atau catatan.
- Kepatuhan terhadap plan.
- Emosi sebelum/sesudah transaksi.
- Hasil dalam rupiah dan R-multiple.

### 10.11 Weekly Review — P1

Hermes merangkum:

- Transaksi yang mengikuti plan.
- Entry yang mengejar harga.
- Stop yang dilanggar.
- Strategi dengan hasil terbaik.
- MFE, MAE, win rate, expectancy, dan sample size.
- Saran perbaikan perilaku, bukan janji performa.

### 10.12 Trend Ride Mode — P1

Untuk posisi dengan horizon lebih panjang:

- Entry awal tetap terukur.
- Tidak menggunakan klaim target pasti.
- Menggunakan trailing-stop state machine.
- Menyimpan high-water mark dan perubahan stop.

### 10.13 Corporate Action and News Risk — P1

Sistem menampilkan:

- Corporate action terverifikasi.
- Suspensi, UMA, atau notasi jika sumber resmi tersedia.
- Material news dengan sumber dan timestamp.
- Dampak potensial, bukan fakta yang tidak terverifikasi.

Berita tidak boleh langsung mengubah status formula tanpa aturan yang telah didefinisikan.

---

## 11. Kontrak Formula Deterministik

Setiap formula memiliki:

```text
formula_id
formula_version
strategy_type
required_inputs
required_gates
optional_context
hard_rejects
status_transition_rules
risk_policy_version
price_tick_policy_version
effective_from
effective_to
```

### 11.1 Gate State

Setiap gate hanya boleh berstatus:

```text
PASS
FAIL
UNKNOWN
NOT_APPLICABLE
```

`UNKNOWN` tidak boleh dianggap `PASS`.

### 11.2 Final Action Invariant

`ENTER` hanya boleh terjadi jika:

- Semua required gates `PASS`.
- Tidak ada hard reject.
- Data masih fresh.
- Market session sesuai strategi.
- Harga executable.
- Stop lebih rendah dari entry untuk posisi long.
- Target lebih tinggi dari entry.
- R:R aktual memenuhi minimum.
- Risk profile tersedia.
- Position sizing valid.

### 11.3 Formula Change Management

- Setiap perubahan threshold menaikkan formula version.
- Hasil lama tetap menunjuk versi lama.
- Formula baru melalui unit test, replay, dan shadow run.
- Tidak boleh mengubah formula production tanpa audit trail dan approval.

---

## 12. Peran AI

### 12.1 Yang Boleh Dilakukan AI

- Menjelaskan hasil formula.
- Menyederhanakan istilah teknikal.
- Membandingkan kandidat berdasarkan structured data.
- Mengingat preferensi dan portfolio context pengguna.
- Menjawab pertanyaan lanjutan berdasarkan snapshot.
- Menjelaskan skenario bullish, neutral, dan bearish.
- Menyusun checklist eksekusi dan review.

### 12.2 Yang Tidak Boleh Dilakukan AI

- Mengubah keputusan formula.
- Membuat ticker, angka, harga, berita, indikator, atau corporate action.
- Memberi `ENTER` ketika data stale atau required gate gagal.
- Menampilkan TP/SL seolah teknikal jika hanya berupa persentase generik.
- Menjanjikan return.
- Menutupi kegagalan data provider.
- Menggunakan data pengguna lain.
- Melakukan order broker tanpa produk dan persetujuan terpisah.

### 12.3 Output Contract

LLM menerima structured context seperti:

```json
{
  "finalAction": "WATCH",
  "formulaVersion": "pullback-v1.0",
  "snapshotAt": "ISO-8601",
  "marketPhase": "SESSION_2",
  "dataFreshnessSeconds": 20,
  "passedGates": [],
  "failedGates": [],
  "unknownGates": [],
  "riskPlan": null,
  "allowedClaims": [],
  "forbiddenClaims": []
}
```

Server memvalidasi respons AI sebelum ditampilkan.

---

## 13. Data dan Freshness

### 13.1 Sumber Data

MVP internal dapat menggunakan sumber riset/prototipe. Sebelum peluncuran komersial, produk wajib menggunakan data yang memiliki hak penggunaan dan redistribusi yang memadai.

TradingView/unofficial scanner tidak boleh dianggap sebagai production SLA atau sumber resmi IDX.

### 13.2 Market Phase

Sistem mengenali minimal:

```text
PRE_OPEN
SESSION_1
LUNCH_BREAK
SESSION_2
POST_CLOSE
WEEKEND
HOLIDAY
SOURCE_DELAYED
SOURCE_UNAVAILABLE
```

Market calendar tidak boleh hanya mengandalkan Senin–Jumat; hari libur dan penghentian sesi harus dapat dikonfigurasi.

### 13.3 Freshness Contract

Setiap response menampilkan:

- Source.
- Exchange timestamp jika tersedia.
- Ingested timestamp.
- Calculated timestamp.
- Data age.
- Live/Delayed/Last Close status.

Jika melewati freshness threshold:

- Jangan menyebut data real-time.
- Jangan menerbitkan `ENTER` baru.
- Tampilkan `WAIT — DATA STALE`.

### 13.4 Data Quality

Worker harus mendeteksi:

- Missing symbols.
- Duplicate rows.
- Zero/negative prices.
- Invalid OHLC.
- Volume/value anomaly.
- Outlier RVOL.
- Stale timestamps.
- Corporate-action discontinuity.
- Harga yang tidak sesuai tick policy.

---

## 14. Contoh Respons Produk

```text
📡 HERMES SAHAM — DAILY SETUP

Market: SESSION 1 • Data 18 detik lalu
Profil: Swing konservatif • Risk/trade: Rp500.000

1. ABCD — WATCH
Harga: Rp1.000 • Fraksi: Rp5
Alasan: trend masih naik, pullback dekat SMA20, volume koreksi mengecil.
Belum ENTER karena harga belum reclaim VWAP.

Trigger valid : minimal Rp1.005 dan bertahan di atas VWAP
Entry plan    : Rp1.005–1.015
Stop          : < Rp980
Target 1      : Rp1.065
R:R acuan     : 1:2,4 dari entry Rp1.005
Ukuran maks.  : 20 lot

Invalid jika close < Rp980 atau data menjadi stale.
Formula: pullback-v1.0 • Snapshot: 09:34:20 WIB

⚠️ Ini alat bantu keputusan, bukan jaminan keuntungan.
```

Jika tidak ada kandidat:

```text
Hari ini belum ada setup yang memenuhi semua syarat.
Saya tidak menyarankan mengejar kandidat yang sudah extended.
Saya akan memberi alert jika status watchlist berubah.
```

---

## 15. Functional Requirements

### FR-001 — User Risk Profile

Sistem menyimpan profil risiko per pengguna dan memerlukan parameter minimum sebelum membuat position sizing.

### FR-002 — Natural Language Screening

Sistem menerjemahkan permintaan pengguna menjadi filter terstruktur dan memperlihatkan filter tersebut.

### FR-003 — Formula Evaluation

Setiap kandidat dievaluasi oleh formula version yang eksplisit.

### FR-004 — Unknown Data Handling

Required input yang hilang menghasilkan `UNKNOWN` dan harus fail-closed untuk `ENTER`.

### FR-005 — Executable Price Validation

Semua entry, trigger, stop, target, dan trailing stop harus lolos IDX tick validation berdasarkan Harga Previous.

### FR-006 — Actual Risk/Reward

R:R dihitung dari executable entry, stop, dan target; tidak boleh hardcoded.

Jika entry berupa rentang, sistem harus menampilkan entry reference atau rentang R:R terbaik–terburuk.

### FR-007 — Snapshot Ownership

Server membangun dan menyimpan final decision snapshot. Client dan formatter tidak boleh menghitung ulang level.

### FR-008 — AI Grounding

AI hanya menerima data yang berasal dari snapshot terverifikasi dan tidak boleh membuat level baru.

### FR-009 — Market Session Awareness

Setiap rekomendasi mempertimbangkan market phase dan freshness.

### FR-010 — Portfolio Awareness

Analisis membedakan pengguna yang belum memiliki saham dan pengguna yang sedang memegang posisi.

### FR-011 — Alert State Transition

Alert dikirim saat perubahan status yang relevan, bukan pada setiap refresh harga.

### FR-012 — Alert Deduplication

Satu event tidak boleh menghasilkan notifikasi ganda pada channel yang sama.

### FR-013 — Invalidation

Setiap actionable setup memiliki kondisi invalidation dan expiry.

### FR-014 — Audit Trail

Admin dapat melacak input, formula, keputusan, AI explanation, dan delivery event tanpa melihat data privat pengguna yang tidak diperlukan.

### FR-015 — No-Candidate Response

Sistem harus menghasilkan respons yang berguna ketika tidak ada kandidat lolos.

### FR-016 — Outcome Tracking

Setiap signal snapshot dapat dievaluasi terhadap harga sesudahnya tanpa mengubah nilai historisnya.

### FR-017 — User Feedback

Pengguna dapat menandai rekomendasi sebagai relevan, tidak relevan, sudah dimiliki, atau diabaikan.

### FR-018 — Kill Switch

Admin dapat menonaktifkan AI, alert, strategi tertentu, provider, atau seluruh actionable recommendation tanpa deployment.

---

## 16. Data Model Konseptual

### Users and Preferences

```text
users
risk_profiles
strategy_preferences
notification_preferences
user_consents
```

### Market Data

```text
instruments
market_sessions
candles_daily
candles_intraday
market_snapshots
source_snapshots
ingestion_runs
corporate_actions
```

### Decision Intelligence

```text
formula_versions
formula_runs
formula_gate_results
signal_snapshots
risk_plans
signal_state_transitions
signal_outcomes
```

### User Context

```text
watchlists
watchlist_items
portfolios
positions
trade_plans
trade_journals
```

### Operations

```text
alert_events
alert_deliveries
ai_requests
ai_usage_reservations
admin_audit_logs
feature_flags
```

### Immutability

Market snapshot, formula run, dan signal snapshot bersifat immutable. Koreksi dibuat sebagai snapshot/version baru, bukan menimpa sejarah.

---

## 17. Arsitektur Teknis Rekomendasi

```text
Telegram / Web App
        │
        ▼
Conversation & API Gateway
        │
        ├── Auth, entitlement, rate limit
        ├── User/portfolio context
        └── Request orchestration
        │
        ▼
Decision Service
        ├── Formula Engine
        ├── Risk Engine
        ├── IDX Tick Engine
        └── Policy/Compliance Guard
        │
        ├──────────► PostgreSQL
        ├──────────► Redis / Queue
        │
        ▼
Grounded AI Explanation
        │
        ▼
Response Validator → Telegram/Web

Licensed Data Feed
        │
        ▼
Independent Market Worker
        ├── Raw snapshot persistence
        ├── Data quality checks
        ├── Indicator computation
        ├── Formula runs
        └── Alert transition events
```

### 17.1 Recommended Boundary

- Web/API tidak menjalankan full-market scan di request lifecycle.
- Worker menangani ingestion dan formula batch.
- Formula dan risk engine merupakan package deterministik terpisah.
- AI provider tidak memiliki akses langsung ke database.
- Semua tool call AI melewati server policy.

### 17.2 Initial Stack

Rekomendasi pragmatis:

```text
Web/API       : Next.js + TypeScript
Worker        : Node.js/TypeScript + queue
Database      : PostgreSQL
Cache/Queue   : Redis
Formula       : Shared TypeScript package
Analytics     : SQL terlebih dahulu; Python bila backtest skala besar diperlukan
Messaging     : Telegram Bot API
Observability : structured logs, metrics, tracing, error tracking
```

---

## 18. API Contracts Awal

### 18.1 Screener Request

```http
POST /api/v1/screen
```

Input:

```json
{
  "strategy": "SAFE_PULLBACK",
  "userContextId": "...",
  "filters": {},
  "maxCandidates": 5
}
```

Output harus menyertakan:

```text
marketPhase
snapshotAt
dataFreshness
sourceProvenance
formulaVersion
universeCount
stageCounts
candidates[]
rejectedSummary
```

### 18.2 Ticker Analysis

```http
GET /api/v1/stocks/{symbol}/decision
```

Output:

```text
finalAction
gates
riskPlan
pricePolicy
freshness
source
formulaVersion
snapshotId
```

### 18.3 Conversation

```http
POST /api/v1/chat
```

Chat harus mereferensikan `snapshotId`. Jawaban actionable tanpa snapshot valid ditolak server.

### 18.4 Alert Events

```text
WATCH_CREATED
WATCH_TO_ENTER
ENTER_TO_INVALIDATED
STOP_APPROACHING
TARGET_REACHED
DATA_STALE
SOURCE_RECOVERED
```

---

## 19. Security, Privacy, and Abuse Prevention

### 19.1 Security Requirements

- Authentication wajib untuk fitur personal.
- Authorization diperiksa server-side.
- Secrets tidak pernah dikirim ke browser atau prompt AI.
- Data antarpengguna terisolasi.
- Input ticker dan parameter divalidasi.
- Rate limit per user dan per endpoint.
- Concurrency cap per user dan global.
- AI token/cost reservation bersifat atomic dan fail-closed.
- Global AI budget dan kill switch tersedia.
- Prompt injection dari berita, website, atau dokumen dianggap data tidak tepercaya.
- Webhook Telegram dan pembayaran diverifikasi serta idempotent.
- Admin action memiliki audit log.
- Backup terenkripsi dan restore diuji berkala.

### 19.2 Privacy Requirements

- Simpan hanya data pengguna yang diperlukan.
- Jelaskan penggunaan portfolio dan conversation history.
- Sediakan delete/export account data.
- Jangan menggunakan portfolio pengguna untuk training tanpa consent eksplisit.
- Redact credential dan identifier sensitif dari logs.

### 19.3 Abuse Prevention

- Cegah scraping massal lewat chat.
- Batasi broadcast dan alert storm.
- Larang koordinasi manipulasi pasar.
- Jangan membantu pengguna membuat informasi palsu atau pump campaign.

---

## 20. Safety and Compliance

1. Semua output menyatakan bahwa produk adalah alat bantu keputusan dan tidak menjamin keuntungan.
2. Bahasa marketing tidak boleh memakai “pasti profit”, “anti rugi”, atau klaim akurasi tanpa metodologi dan sampel.
3. Personalized recommendation, paid signal, broker integration, dan auto-execution harus melewati review legal/compliance sebelum diluncurkan.
4. Hak penggunaan dan redistribusi data harus dikonfirmasi sebelum produk komersial.
5. Performance reporting wajib memperhitungkan biaya, pajak, spread, slippage, sample size, dan survivorship bias.
6. Backtest tidak boleh dipasarkan sebagai hasil aktual.
7. Conflict of interest dan kepemilikan saham internal harus memiliki kebijakan disclosure.
8. Jika sumber resmi tidak tersedia, klaim corporate action atau status khusus harus ditandai belum terverifikasi.

---

## 21. Non-Functional Requirements

### Reliability

- Sistem harus fail-closed ketika data atau formula tidak tersedia.
- Worker mendukung retry terbatas, checkpoint, dan dead-letter handling.
- Alert tidak boleh hilang atau terkirim ganda tanpa status yang dapat direkonsiliasi.

### Performance

- Chat untuk snapshot yang sudah tersedia harus terasa responsif.
- Full-universe scan berjalan asynchronous.
- Slow provider tidak boleh menahan seluruh request tanpa timeout.

### Availability

- Read-only analysis dapat tetap tersedia ketika AI provider gagal.
- Jika AI gagal, tampilkan structured deterministic result tanpa narasi AI.
- Jika market data gagal, jangan gunakan static fake data sebagai live fallback.

### Scalability

- Mendukung horizontal worker scaling.
- Job memiliki idempotency key.
- Per-symbol dan per-snapshot caching tidak boleh melanggar freshness.

### Accessibility and Localization

- Bahasa utama Indonesia.
- Istilah teknis memiliki tooltip/penjelasan.
- Format Rupiah, lot, persen, dan waktu WIB konsisten.
- Web companion responsif untuk mobile.

---

## 22. Observability

Dashboard operasional minimal menampilkan:

- Last successful ingestion.
- Data age per source.
- Symbols expected vs received.
- Formula run success/failure.
- Distribution WATCH/ENTER/WAIT/INVALIDATED.
- Invalid price-level count.
- Alert queued/sent/failed/deduplicated.
- AI requests, latency, tokens, cost, fallback, timeout.
- Rate-limit and budget rejection.
- Source outage dan recovery.
- User-visible error rate.

Setiap response memiliki correlation ID tanpa mengekspos data sensitif.

---

## 23. Quality and Testing Strategy

### Unit Tests

- Formula gates.
- Unknown input behavior.
- Risk calculation.
- Position sizing.
- IDX tick boundaries.
- Directional rounding.
- State transition rules.
- Session and holiday logic.

### Contract Tests

- Worker snapshot ke API.
- API ke Telegram/web formatter.
- Formula result ke AI context.
- AI output validator.

### Integration Tests

- Alert deduplication.
- Telegram webhook retry.
- AI quota concurrency.
- Source outage.
- Stale-data rejection.
- Portfolio isolation.

### Replay and Backtest

- Jalankan formula baru terhadap historical snapshots.
- Gunakan point-in-time data.
- Masukkan fees, tax, spread, dan slippage.
- Pisahkan in-sample dan out-of-sample.
- Laporkan sample size dan market regime.

### Production Verification

- Shadow mode sebelum rekomendasi publik.
- Canary cohort pengguna internal.
- Monitor invalid-level count harus nol.
- Bandingkan snapshot DB, API, web, dan Telegram.

---

## 24. MVP Scope

### In Scope — P0

- Telegram bot dan web companion sederhana.
- Login/account linking.
- Risk-profile onboarding.
- Conversational ticker analysis.
- Natural-language screener dengan filter terstruktur.
- Safe Pullback dan Momentum mode.
- WATCH, ENTER, WAIT, INVALIDATED.
- Data freshness dan market-phase label.
- Deterministic formula engine.
- IDX executable price engine.
- Entry, stop, target, R:R, dan position sizing.
- Manual watchlist dan portfolio.
- State-transition alerts.
- Decision audit trail.
- Admin kill switches dan operational dashboard.

### Out of Scope — MVP

- Auto-order ke broker.
- Broker account synchronization.
- Social/copy trading.
- Public leaderboards.
- AI price prediction.
- Options, derivatives, crypto, forex, dan commodity.
- Broker-flow berbayar tanpa licensed data.
- Fully autonomous portfolio management.

---

## 25. Prioritas Delivery

### Phase 0 — Feasibility and Compliance Gate

- Validasi target persona melalui wawancara.
- Tentukan sumber data dan hak komersial.
- Legal/compliance review.
- Definisikan formula version awal.
- Definisikan risk policy.
- Uji data completeness dan latency.

### Phase 1 — Internal Copilot

- Digunakan founder/internal users.
- Telegram ticker analysis.
- Deterministic screening.
- Risk plan dan fraksi IDX.
- Snapshot audit.
- Tidak ada paid launch.

### Phase 2 — Closed Beta

- Onboarding pengguna.
- Watchlist, portfolio manual, dan alert.
- Usage/cost controls.
- Outcome tracking.
- Feedback loop.

### Phase 3 — Paid MVP

Hanya setelah:

- Data rights jelas.
- Reliability memenuhi target.
- Formula outcomes telah diamati.
- Safety controls aktif.
- Legal/compliance sign-off tersedia.

### Phase 4 — Post-MVP

- Journal dan weekly review.
- Trend Ride.
- Corporate-action risk.
- Broker integration setelah review terpisah.
- Multi-channel seperti WhatsApp setelah model biaya dan policy tervalidasi.

---

## 26. Monetization Hypothesis

### Free

- Analisis ticker terbatas.
- Delayed/last-close insights.
- Edukasi dan contoh formula.
- Watchlist terbatas.

### Pro

- Intraday screening.
- Personalized risk plan.
- Lebih banyak watchlist.
- State-transition alerts.
- Portfolio context dan journal.

### Advanced

- Multiple strategies.
- Outcome analytics.
- Weekly coaching review.
- Team/community tools jika compliance memungkinkan.

Harga final tidak ditetapkan dalam PRD ini. Validasi willingness-to-pay dilakukan setelah closed beta menunjukkan recurring value.

---

## 27. Success Metrics

### North Star Metric

**Weekly users who make or avoid a decision using a complete, compliant plan.**

Complete plan berarti memiliki:

- Fresh data.
- Formula version.
- Status keputusan.
- Trigger/invalidation.
- Executable levels jika actionable.
- Risk amount dan position size.

### Product Metrics

- Onboarding completion.
- Weekly active users.
- Retention minggu ke-4.
- Watchlist-to-plan conversion.
- Alert usefulness feedback.
- Journal completion.
- Persentase pengguna yang membaca invalidation sebelum entry.

### Quality Metrics

- Invalid executable price level: target `0`.
- Hardcoded/fabricated R:R: target `0`.
- ENTER dengan required gate FAIL/UNKNOWN: target `0`.
- ENTER dari stale data: target `0`.
- Cross-surface snapshot mismatch: target `0`.
- Duplicate alert rate.
- Source completeness dan freshness SLA.

### Safety Metrics

- Persentase rekomendasi dengan risk plan lengkap.
- Persentase extended setup yang ditolak.
- Pengguna yang melebihi risk policy.
- Unverified claim incidence.
- Kill-switch activation and recovery time.

### Outcome Metrics

Outcome bukan satu-satunya ukuran kualitas. Pantau:

- MFE dan MAE.
- Stop/target hit sequence.
- R-multiple.
- Expectancy per strategy dan market regime.
- Sample size.
- Kepatuhan pengguna terhadap plan.

---

## 28. MVP Acceptance Criteria

MVP dapat masuk closed beta jika seluruh kondisi berikut terpenuhi:

1. Pengguna dapat menyelesaikan risk onboarding.
2. Pengguna dapat meminta analisis ticker melalui Telegram.
3. Sistem selalu menampilkan source, snapshot time, freshness, market phase, dan formula version.
4. Formula deterministik menghasilkan WATCH/ENTER/WAIT/INVALIDATED.
5. Required gate yang `UNKNOWN` tidak dapat menghasilkan ENTER.
6. Seluruh entry, breakout, stop, target, dan trailing stop sesuai fraksi IDX.
7. R:R dihitung ulang dari harga executable dan tidak hardcoded.
8. Position sizing menggunakan modal dan risk policy pengguna.
9. AI tidak dapat mengubah final action atau menciptakan level baru.
10. Jika AI provider gagal, deterministic result tetap tersedia.
11. Jika data stale, actionable recommendation diblokir.
12. Telegram dan web menampilkan snapshot yang sama.
13. Alert status transition tidak terkirim ganda.
14. User A tidak dapat mengakses portfolio atau chat User B.
15. Rate limit, concurrency cap, global budget, dan kill switch teruji.
16. Audit log dapat merekonstruksi input sampai output.
17. Data source dan commercial-use rights telah direview sebelum paid launch.
18. Legal/compliance review telah memberikan keputusan untuk scope closed beta dan paid launch.

---

## 29. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Data stale atau provider gagal | Keputusan salah konteks | Freshness gate, fail-closed, multi-source strategy |
| Data tanpa hak komersial | Legal dan bisnis | Data licensing gate sebelum paid launch |
| AI hallucination | Kepercayaan dan kerugian pengguna | Structured context, output validator, deterministic final action |
| Formula overfitting | Hasil historis menyesatkan | Walk-forward, out-of-sample, regime analysis |
| FOMO akibat notifikasi | Perilaku pengguna memburuk | Anti-chase, quiet hours, alert hanya pada state transition |
| RR atau harga tidak executable | Plan tidak bisa dijalankan | Central tick engine, parity tests, production sampler |
| Pengguna salah memasukkan portfolio | Saran manajemen posisi salah | Confirmation summary dan editable context |
| Alert ganda | Overtrading dan spam | Idempotency key dan delivery ledger |
| Biaya AI tidak terkendali | Margin negatif | Reservation atomic, quota, cache, model routing, kill switch |
| Klaim pemasaran berlebihan | Risiko reputasi/compliance | Copy approval dan evidence requirement |
| Formula terlalu kompleks | Sulit dipahami/dipelihara | Versioned, modular, explainable gates |
| Ketergantungan satu channel | Risiko platform | Web companion dan channel abstraction |

---

## 30. Keputusan Produk yang Direkomendasikan

1. Gunakan **Hermes Saham** sebagai working title; lakukan pengecekan brand/trademark sebelum publik.
2. Mulai dari **Telegram-first**, karena perilaku konsultasi saham bersifat conversational dan alert-driven.
3. Gunakan web untuk audit detail, chart, portfolio, journal, dan pengaturan.
4. Jadikan **Safe Pullback** sebagai mode default bagi pengguna baru.
5. Pisahkan formula deterministic dari AI sejak awal.
6. Prioritaskan sumber data dan legal/compliance sebelum visual premium atau paid launch.
7. Jangan membangun broker integration pada MVP.
8. Ukur disiplin dan kualitas keputusan, bukan hanya jumlah sinyal atau win rate.

---

## 31. Open Questions

1. Apakah Hermes Saham produk mandiri atau menjadi AI layer di atas NadiBursa?
2. Apakah target pertama pengguna pemula, swing trader, atau existing NadiBursa subscribers?
3. Sumber market data berlisensi mana yang akan dipakai?
4. Apakah data intraday penuh dibutuhkan pada MVP atau cukup snapshot berkala?
5. Formula strategi mana yang menjadi default resmi?
6. Berapa risk profile minimum dan maksimum yang diperbolehkan?
7. Apakah paid plan menjual analytics, coaching workflow, atau alerts?
8. Apakah conversation history disimpan permanen atau memiliki retention period?
9. Apakah corporate action dan news tersedia pada MVP atau P1?
10. Siapa yang memberikan legal/compliance sign-off sebelum closed beta dan paid launch?
11. Apakah nama “Hermes” tersedia dan aman untuk penggunaan komersial?
12. Bagaimana mekanisme pengguna mengoreksi portfolio context yang salah?

---

## 32. Definition of Done untuk Produk Awal

Hermes Saham dianggap berhasil sebagai produk awal ketika pengguna dapat:

1. Menjelaskan profil dan tujuan tradingnya.
2. Meminta shortlist saham dengan bahasa natural.
3. Mendapatkan kandidat yang diprioritaskan secara deterministik.
4. Memahami alasan lolos/gagal setiap saham.
5. Mendapatkan status WATCH, ENTER, WAIT, atau INVALIDATED yang konsisten.
6. Mendapatkan level harga yang benar-benar dapat dipasang di IDX.
7. Mengetahui risiko rupiah dan jumlah lot sebelum entry.
8. Mendapatkan alert hanya ketika kondisi berubah.
9. Berdiskusi lanjutan dengan AI tanpa kehilangan konteks posisi.
10. Mengevaluasi apakah keputusan mereka mengikuti plan.

Dan sistem dapat membuktikan:

- Data apa yang digunakan.
- Kapan data diambil.
- Formula versi berapa yang berjalan.
- Mengapa keputusan diberikan.
- Level dan R:R mana yang dihitung.
- Pesan mana yang dikirim.
- Outcome apa yang terjadi setelahnya.

---

## 33. Ringkasan Satu Kalimat

> **Hermes Saham adalah asisten saham personal yang menyaring peluang secara deterministik, menjelaskan keputusan dengan AI, dan membantu pengguna mengeksekusi serta mengelola risiko secara disiplin—tanpa menjanjikan keuntungan.**
