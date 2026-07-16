# 🛠️ Go Backend Agent — Panduan Menambah Tool Baru

> Dokumentasi untuk pemula. Cara kerja sistem & langkah-langkah menambah `function calling` tool baru.

---

## 📦 Daftar Isi

1. [Arsitektur Sistem](#-arsitektur-sistem)
2. [Cara Kerja (Dari Request → Response)](#-cara-kerja-dari-request--response)
3. [Struktur Folder](#-struktur-folder)
4. [Langkah demi Langkah: Menambah Tool Baru](#-langkah-demi-langkah-menambah-tool-baru)
5. [Pola Kode Tool (Template)](#-pola-kode-tool-template)
6. [Helper Functions](#-helper-functions)
7. [Daftar Tool yang Sudah Ada](#-daftar-tool-yang-sudah-ada)
8. [Tips & Best Practices](#-tips--best-practices)

---

## 🏗️ Arsitektur Sistem

```
┌───────────────────────────────────────────────────────────┐
│                     main.go (Entry Point)                  │
│                                                           │
│  1. Load .env                                             │
│  2. Koneksi MySQL via GORM                                │
│  3. Inisialisasi GeminiService                            │
│  4. Inisialisasi ToolRegistry                             │
│  5. Setup Gin HTTP Server + Routes                        │
└───────────────────────────────────────────────────────────┘
                            │
            ┌───────────────┼───────────────┐
            ▼               ▼               ▼
    ┌──────────────┐ ┌────────────┐ ┌──────────────┐
    │   handlers/  │ │ services/  │ │   models/    │
    │ handlers.go  │ │ gemini.go  │ │ models.go    │
    │              │ │ tools.go   │ │ supplier.go  │
    │  REST API    │ │            │ │ item.go      │
    │  endpoints   │ │ AI Logic   │ │ transaction  │
    │              │ │ DB Tools   │ │ performance  │
    └──────────────┘ └────────────┘ │ evaluation   │
                                    └──────────────┘
```

### Peran Masing-masing File

| File | Peran |
|------|-------|
| [`cmd/api/main.go`](cmd/api/main.go) | Entry point: inisialisasi DB, Gemini, ToolRegistry, HTTP server |
| [`handlers/handlers.go`](handlers/handlers.go) | HTTP handler: endpoint REST API untuk agent, chat, message |
| [`services/gemini.go`](services/gemini.go) | Wrapper Gemini AI: `GenerateWithTools()` — kirim prompt + tools ke Gemini |
| [`services/tools.go`](services/tools.go) | **Tempat semua tools** — definisi schema & eksekusi function calling |
| [`models/models.go`](models/models.go) | Model GORM: Agent, ChatHeader, ChatHistory |
| [`models/supplier.go`](models/supplier.go) | Model GORM: Supplier (vendor) |
| [`models/item.go`](models/item.go) | Model GORM: Item (barang) |
| [`models/transaction.go`](models/transaction.go) | Model GORM: Transaction (transaksi PO) |
| [`models/performance.go`](models/performance.go) | Model GORM: SupplierPerformanceHistory |
| [`models/evaluation.go`](models/evaluation.go) | Model GORM: MasterEvaluationPeriod, MasterVendorGrade |

---

## 🔄 Cara Kerja (Dari Request → Response)

```
User kirim pesan: "Cek performa vendor PT Maju Jaya"
        │
        ▼
POST /api/v1/chats/:id/message
        │
        ▼
Handler.SendMessage()
  1. Ambil Agent config (model_name, system_instruction, temperature)
  2. Ambil riwayat chat sebelumnya
  3. Panggil Gemini.GenerateWithTools()
        │
        ▼
Gemini.GenerateWithTools()
  1. Gabungkan history + prompt baru
  2. Kirim ke Gemini API dengan Tools = toolRegistry.Declarations()
  3. Gemini memutuskan: apakah perlu panggil function?
        │
        ├── YA → Gemini return FunctionCall { name: "cek_performa_vendor", args: {...} }
        │         │
        │         ▼
        │   ToolRegistry.Execute(ctx, fnCall)
        │     → switch fnCall.Name:
        │         case "cek_performa_vendor": execCekPerformaVendor(args)
        │           → Query DB: supplier_performance_histories
        │           → Format hasil jadi teks
        │           → Return string hasil
        │         │
        │         ▼
        │   Hasil dikirim balik ke Gemini sebagai FunctionResponse
        │   Gemini menghasilkan jawaban final dengan bahasa natural
        │
        └── TIDAK → Gemini langsung beri jawaban text
        │
        ▼
Handler menyimpan pesan user & model ke DB (chat_histories)
        │
        ▼
Response JSON: { role: "model", content: "Performa vendor PT Maju Jaya..." }
```

**Maksimal 5 round-trip function calling** — setelah itu akan error.

---

## 📂 Struktur Folder

```
go-backend-agent/
├── cmd/api/main.go          ← Entry point
├── handlers/handlers.go     ← HTTP handlers
├── services/
│   ├── gemini.go            ← Gemini AI client
│   └── tools.go             ← SEMUA TOOLS (function declarations + execution)
├── models/
│   ├── models.go            ← Agent, ChatHeader, ChatHistory
│   ├── supplier.go          ← Supplier
│   ├── item.go              ← Item
│   ├── transaction.go       ← Transaction
│   ├── performance.go       ← SupplierPerformanceHistory
│   └── evaluation.go        ← MasterEvaluationPeriod, MasterVendorGrade
├── agents/                  ← Agent orchestration (ADK)
├── .env                     ← Environment variables
├── go.mod / go.sum          ← Dependencies
└── ADD_NEW_TOOL_GUIDE.md    ← File ini
```

---

## 📝 Langkah demi Langkah: Menambah Tool Baru

Ikuti **3 langkah** berikut untuk menambah tool baru. Contoh: kita akan menambah tool `cek_stok_barang`.

### Step 1: Buat Function Declaration (Schema)

Buka [`services/tools.go`](services/tools.go). Tambahkan method `fnCekStokBarang()`:

```go
// =============================================================================
// TOOL 11: Cek Stok Barang
// =============================================================================

func (tr *ToolRegistry) fnCekStokBarang() *genai.FunctionDeclaration {
    return &genai.FunctionDeclaration{
        Name:        "cek_stok_barang",                                    // ⚠️ NAMA UNIK (snake_case)
        Description: "Mengecek stok barang di gudang berdasarkan nama atau kode barang.",
        Parameters: &genai.Schema{
            Type: genai.TypeObject,
            Properties: map[string]*genai.Schema{
                "item_name": {Type: genai.TypeString, Description: "Nama barang (bisa sebagian)."},
                "item_code": {Type: genai.TypeString, Description: "Kode barang (opsional)."},
                "gudang":    {Type: genai.TypeString, Description: "Nama gudang (opsional)."},
                "limit":     {Type: genai.TypeInteger, Description: "Maks hasil. Default: 10."},
            },
            Required: []string{"item_name"},                                // ⚠️ Parameter wajib (jika ada)
        },
    }
}
```

### Step 2: Buat Execution Function

Tambahkan method `execCekStokBarang()`:

```go
func (tr *ToolRegistry) execCekStokBarang(args map[string]any) (string, error) {
    // 1. Ambil parameter dari args
    itemName := strArg(args, "item_name")
    itemCode := strArg(args, "item_code")
    gudang := strArg(args, "gudang")
    limit := intArg(args, "limit", 10)

    // 2. Query database menggunakan GORM
    query := tr.DB.Model(&models.Item{})

    if itemName != "" {
        query = query.Where("item_name LIKE ?", "%"+itemName+"%")
    }
    if itemCode != "" {
        query = query.Where("item_code = ?", itemCode)
    }

    var items []models.Item
    if err := query.Limit(limit).Find(&items).Error; err != nil {
        return "", err
    }

    // 3. Jika tidak ada hasil, return pesan yang informatif
    if len(items) == 0 {
        return fmt.Sprintf("Tidak ada barang ditemukan untuk '%s'.", itemName), nil
    }

    // 4. Format hasil menjadi teks yang mudah dibaca Gemini
    var sb strings.Builder
    sb.WriteString(fmt.Sprintf("Hasil pencarian stok barang (maks %d):\n", limit))
    for i, item := range items {
        sb.WriteString(fmt.Sprintf("%d. %s | Kode: %s | Stok: %.0f %s\n",
            i+1, item.ItemName, item.ItemCode, item.StockQty, item.StockUom))
    }

    // 5. Return string (bukan JSON — Gemini lebih baik dengan teks)
    return sb.String(), nil
}
```

### Step 3: Daftarkan Tool

Di method `Declarations()` (sekitar line 32-58), tambahkan function declaration baru:

```go
func (tr *ToolRegistry) Declarations() []*genai.Tool {
    return []*genai.Tool{
        {
            FunctionDeclarations: []*genai.FunctionDeclaration{
                // ... tools yang sudah ada ...
                tr.fnCekPerformaVendor(),
                tr.fnCekStatusVendor(),
                tr.fnDaftarKategoriVendor(),
                tr.fnGetServerTime(),
                // ⬇️ TAMBAHKAN DI SINI
                tr.fnCekStokBarang(),
            },
        },
    }
}
```

Di method `Execute()` (sekitar line 65-89), tambahkan `case` baru:

```go
func (tr *ToolRegistry) Execute(ctx context.Context, fnCall *genai.FunctionCall) (string, error) {
    switch fnCall.Name {
    // ... case yang sudah ada ...
    case "daftar_kategori_vendor":
        return tr.execDaftarKategoriVendor(fnCall.Args)
    case "get_server_time":
        return tr.execGetServerTime(fnCall.Args)
    // ⬇️ TAMBAHKAN DI SINI
    case "cek_stok_barang":
        return tr.execCekStokBarang(fnCall.Args)
    default:
        return "", fmt.Errorf("unknown function: %s", fnCall.Name)
    }
}
```

### ✅ Selesai! Tidak perlu ubah file lain.

Cukup compile ulang:

```bash
cd go-backend-agent
go build ./cmd/api/
./api
```

---

## 📋 Pola Kode Tool (Template)

Copy-paste template ini untuk memulai tool baru:

```go
// =============================================================================
// TOOL XX: Nama Tool Kamu
// =============================================================================

func (tr *ToolRegistry) fnNamaToolKamu() *genai.FunctionDeclaration {
    return &genai.FunctionDeclaration{
        Name:        "nama_tool_kamu",               // snake_case, unique
        Description: "Deskripsi jelas tentang apa yang tool ini lakukan.",
        Parameters: &genai.Schema{
            Type: genai.TypeObject,
            Properties: map[string]*genai.Schema{
                "param1": {Type: genai.TypeString, Description: "Deskripsi parameter 1."},
                "param2": {Type: genai.TypeInteger, Description: "Deskripsi parameter 2. Default: 10."},
                "param3": {Type: genai.TypeNumber, Description: "Deskripsi parameter 3 (float)."},
            },
            // Required: []string{"param1"},           // uncomment jika ada parameter wajib
        },
    }
}

func (tr *ToolRegistry) execNamaToolKamu(args map[string]any) (string, error) {
    // 1. Extract parameters
    param1 := strArg(args, "param1")
    limit := intArg(args, "param2", 10)

    // 2. Build query
    query := tr.DB.Model(&models.NamaModel{})

    if param1 != "" {
        query = query.Where("column LIKE ?", "%"+param1+"%")
    }

    // 3. Execute query
    var results []models.NamaModel
    if err := query.Limit(limit).Find(&results).Error; err != nil {
        return "", err
    }

    // 4. Handle empty results
    if len(results) == 0 {
        return "Tidak ada data ditemukan.", nil
    }

    // 5. Format output (gunakan strings.Builder)
    var sb strings.Builder
    sb.WriteString("=== JUDUL LAPORAN ===\n\n")
    for i, r := range results {
        sb.WriteString(fmt.Sprintf("%d. %s\n", i+1, r.SomeField))
    }

    return sb.String(), nil
}
```

---

## 🔧 Helper Functions

Semua tersedia di bagian bawah [`services/tools.go`](services/tools.go):

| Helper | Fungsi |
|--------|--------|
| `strArg(args, key)` | Ambil `string` dari args, return `""` jika tidak ada |
| `intArg(args, key, defaultVal)` | Ambil `int` dari args, return `defaultVal` jika tidak ada |
| `floatArg(args, key, defaultVal)` | Ambil `float64` dari args, return `defaultVal` jika tidak ada |
| `strOr(s, defaultVal)` | Return `defaultVal` jika `s` kosong |
| `or(s, defaultVal)` | Alias `strOr` |
| `uniqueStrings(slice, fn)` | Ekstrak unique string dari slice of struct |
| `parseFunctionArgs(args)` | Format args untuk logging |

### Cara pakai helper:

```go
// Ambil parameter
vendorName := strArg(args, "vendor_name")          // string → "" jika tidak ada
limit := intArg(args, "limit", 10)                  // int → 10 jika tidak ada
threshold := floatArg(args, "min_selisih", 5.0)     // float → 5.0 jika tidak ada

// Default value
displayName := strOr(supplier.Name, "Tanpa Nama")   // fallback

// Unique strings dari struct slice
vendorIDs := uniqueStrings(rows, func(r MyRow) string { return r.VendorID })
```

**⚠️ Penting**: Gemini mengirim angka sebagai `float64`, jadi `intArg` membaca dari `float64` dulu baru di-cast ke `int`. Jangan langsung cast ke `int`.

---

## 📊 Daftar Tool yang Sudah Ada

| # | Nama Function | File PHP Source | Deskripsi |
|---|---------------|-----------------|-----------|
| 1 | `analisis_supplier` | `AnalisisDataSupplierTool.php` | Analisis & filter supplier (email, rating) |
| 2 | `cari_vendor_by_kategori` | `CariVendorBerdasarkanKategoriTool.php` | Cari vendor berdasarkan kategori/grup |
| 3 | `cek_harga_pasar` | `CekHargaPasarTool.php` | Deteksi anomali harga vs rata-rata pasar |
| 4 | `cek_keluhan_vendor` | `CekKeluhanVendorTool.php` | Log reject & return barang vendor |
| 5 | `cek_kontrak_vendor` | `CekKontrakVendorTool.php` | Status kontrak/PO yang akan jatuh tempo |
| 6 | `cek_laporan_keuangan` | `CekLaporanKeuanganTool.php` | Anggaran vs realisasi per bulan |
| 7 | `cek_performa_vendor` | `CekPerformaVendorTool.php` | Skor performa vendor (kualitas, harga, etc) |
| 8 | `cek_status_vendor` | `CekStatusVendorTool.php` | Detail & status satu vendor |
| 9 | `daftar_kategori_vendor` | `DaftarKategoriVendorTool.php` | Semua kategori/grup vendor |
| 10 | `get_server_time` | `GetServerTimeTool.php` | Waktu server saat ini |

---

## 💡 Tips & Best Practices

### 1. Naming Convention
- **Function name**: `snake_case` — harus match antara `Name` di `FunctionDeclaration` dan `case` di `Execute()`
- **Go method**: `fnNamaTool()` untuk declaration, `execNamaTool()` untuk execution

### 2. Description is CRITICAL
Deskripsi tool adalah **satu-satunya cara Gemini tahu kapan harus memanggil tool kamu**. Tulis deskripsi yang:
- Jelas dan spesifik
- Sebutkan kapan tool ini harus dipakai
- Contoh baik: `"Mengecek stok barang di gudang berdasarkan nama atau kode barang."`
- Contoh buruk: `"Cek stok"`

### 3. Parameter Types
| Type | Go Equivalent | Digunakan untuk |
|------|---------------|-----------------|
| `genai.TypeString` | `string` | Nama, kode, filter teks |
| `genai.TypeInteger` | `int` (dari `float64`) | Limit, jumlah, threshold |
| `genai.TypeNumber` | `float64` | Persentase, harga, nilai |
| `genai.TypeBoolean` | `bool` | Flag on/off |
| `genai.TypeArray` | `[]any` | List nilai |

### 4. Format Output
- **Gunakan teks, bukan JSON** — Gemini lebih baik memahami teks terstruktur
- Gunakan `strings.Builder` untuk efisiensi
- Tambahkan header/judul dengan pemisah (`===`, `───`)
- Sertakan jumlah hasil: `"Hasil pencarian (maks 10):"`
- Jika tidak ada hasil, beri pesan informatif: `"Tidak ada data ditemukan untuk filter tersebut."`

### 5. Query Performance
- Selalu batasi dengan `Limit()` untuk mencegah data terlalu besar
- Gunakan `LIKE` untuk pencarian partial text
- Jika perlu resolve nama dari ID, lakukan batch query (lihat contoh di `execCekHargaPasar`)

### 6. Error Handling
- Return `error` untuk query error → akan di-log sebagai `[Tool] Error: ...`
- Return `string` informatif + `nil` untuk "tidak ada data" → ini bukan error
- Jangan panic; semua error ditangani di `Execute()`

### 7. Testing Lokal
Setelah menambah tool, test dengan:
```bash
# Restart server
go build ./cmd/api/ && ./api

# Kirim chat via curl
curl -X POST http://localhost:8080/api/v1/chats/:id/message \
  -H "Content-Type: application/json" \
  -d '{"content": "cek stok barang pipa"}'
```

Cek log terminal untuk melihat:
```
[Tool] Memanggil: cek_stok_barang({"item_name":"pipa","limit":10})
```

### 8. Jangan Lupa
- ❌ **Jangan** ubah file selain [`services/tools.go`](services/tools.go) (kecuali butuh model baru di `models/`)
- ❌ **Jangan** lupa daftarkan di `Declarations()` DAN `Execute()` — harus keduanya
- ✅ **Pastikan** nama function di `FunctionDeclaration.Name`, `case` di `Execute()`, dan prefix method (`fnXxx`/`execXxx`) semuanya konsisten

---

## 🎯 Quick Reference: Porting dari PHP Tool

Jika kamu punya tool PHP yang sudah ada (di `app/Ai/Tools/`), ikuti mapping ini:

| PHP | Go |
|-----|-----|
| `getName(): string` | `Name` field di `FunctionDeclaration` |
| `getDescription(): string` | `Description` field di `FunctionDeclaration` |
| `getParameters(): array` | `Parameters` field (`*genai.Schema`) |
| `execute(array $args): string` | `execXxx(args map[string]any) (string, error)` |
| `$this->queryBuilder()` / Model | `tr.DB.Model(&models.Xxx{})` |
| `$this->formatCurrency()` | `fmt.Sprintf("Rp%.0f", val)` |
| `collect()` | `uniqueStrings()` helper |

Contoh parameter PHP → Go:
```php
// PHP
'vendor_name' => ['type' => 'string', 'description' => 'Nama vendor']
'limit'      => ['type' => 'integer', 'description' => 'Maks hasil']

// Go
"vendor_name": {Type: genai.TypeString, Description: "Nama vendor"},
"limit":       {Type: genai.TypeInteger, Description: "Maks hasil"},
```

---

> **Dibuat**: Juli 2026
> **Untuk**: VMS Go Backend Agent — `go-backend-agent/services/tools.go`
