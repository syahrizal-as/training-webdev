# Panduan Deployment Aplikasi Go (Golang) di VPS Linux

Dokumen ini berisi panduan langkah-demi-langkah untuk mendeploy aplikasi Go Anda di VPS Linux setelah melakukan git pull dari GitHub, melakukan kompilasi (build) langsung di server, hingga mengatur Nginx sebagai Reverse Proxy dan mengamankannya dengan SSL.

---

## 1. Persiapan Server (Satu Kali Setup)

Sebelum melakukan proses deployment, pastikan server Anda telah dilengkapi dengan kakas (tools) yang dibutuhkan.

### A. Install Go Compiler di VPS

Karena proses kompilasi dilakukan langsung di server, Anda perlu menginstal Go runtime.

```bash
# Update package list
sudo apt update

# Install Go compiler
sudo apt install golang-go -y

# Pastikan instalasi berhasil dengan memeriksa versi Go
go version
```

### B. Siapkan Folder Aplikasi dan Hak Akses

Direkomendasikan untuk menaruh aplikasi di direktori `/var/www/` dan tidak menggunakan user `root` secara langsung untuk menjalankan aplikasi demi alasan keamanan.

```bash
# Buat direktori aplikasi
sudo mkdir -p /var/www/my-go-app

# Ubah kepemilikan folder ke user non-root Anda (misal: ubuntu atau debian)
sudo chown -R $USER:$USER /var/www/my-go-app
```

### C. Clone Repository Pertama Kali

```bash
# Masuk ke direktori
cd /var/www/

# Clone repository Anda (gunakan SSH atau HTTPS dengan Token)
git clone git@github.com:username/repo-anda.git my-go-app
```

---

## 2. Alur Rutin: Git Pull & Build Aplikasi

Langkah-langkah di bawah ini adalah prosedur yang Anda jalankan **setiap kali ada pembaruan kode** dari GitHub.

### Langkah 1: Masuk ke folder aplikasi

```bash
cd /var/www/my-go-app
```

### Langkah 2: Ambil kode terbaru dari GitHub

```bash
git pull origin main
```

### Langkah 3: Ambil dependensi & compile binary

Kompilasi kode Go Anda menjadi satu file binary executable tunggal bernama `app-production`:

```bash
# Unduh/sinkronisasi library dependency
go mod tidy

# Build binary Go dengan optimasi (tanpa debug info untuk memperkecil ukuran file)
go build -ldflags="-s -w" -o app-production ./cmd/api
```

---

## 3. Konfigurasi Background Service (Systemd)

Agar aplikasi Go tetap berjalan di latar belakang saat terminal ditutup dan otomatis menyala ketika VPS melakukan *reboot*, kita perlu mendaftarkannya sebagai **Systemd Service**.

### Langkah 1: Buat file konfigurasi service

```bash
sudo nano /etc/systemd/system/my-go-app.service
```

### Langkah 2: Paste konfigurasi berikut

Sesuaikan variabel environment (`GOOGLE_CLOUD_PROJECT` dan `GOOGLE_CLOUD_LOCATION`) sesuai konfigurasi Google Cloud Agent Platform Anda.

```ini
[Unit]
Description=Aplikasi Go Gemini ADK Agent
After=network.target

[Service]
Type=simple
User=ubuntu
WorkingDirectory=/var/www/my-go-app
ExecStart=/var/www/my-go-app/app-production
Restart=always
RestartSec=5

# Environment variables untuk Google Cloud Agent Platform
Environment=GOOGLE_CLOUD_PROJECT=id-project-google-cloud-kamu
Environment=GOOGLE_CLOUD_LOCATION=us-central1
Environment=GOOGLE_GENAI_USE_ENTERPRISE=True

# Environment tambahan aplikasi Anda jika ada (misal: Port internal)
Environment=PORT=8080

[Install]
WantedBy=multi-user.target
```

> *Catatan: Pastikan nilai `User` disesuaikan dengan username non-root VPS Anda (misal: `ubuntu`, `debian`, atau `vpsuser`).*

### Langkah 3: Aktifkan dan Jalankan Service

```bash
# Reload daemon untuk mendeteksi service baru
sudo systemctl daemon-reload

# Aktifkan agar otomatis berjalan saat VPS booting
sudo systemctl enable my-go-app

# Jalankan service sekarang
sudo systemctl start my-go-app

# Periksa status aplikasi untuk memastikan sudah berjalan lancar
sudo systemctl status my-go-app
```

---

## 4. Konfigurasi Nginx sebagai Reverse Proxy

Nginx bertugas menerima trafik dari internet (Port 80/443) lalu meneruskannya ke aplikasi Go Anda yang berjalan secara internal di Port 8080.

### Langkah 1: Install Nginx

```bash
sudo apt install nginx -y
```

### Langkah 2: Buat konfigurasi server block baru

```bash
sudo nano /etc/nginx/sites-available/my-go-app
```

### Langkah 3: Paste konfigurasi berikut

> *Ganti `domainanda.com` dengan domain atau subdomain asli yang Anda miliki.*

```nginx
server {
    listen 80;
    server_name domainanda.com www.domainanda.com;

    location / {
        # Meneruskan trafik ke port aplikasi Go
        proxy_pass http://127.0.0.1:8080;

        # Header standar untuk reverse proxy
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;

        # Atur timeout jika aplikasi membutuhkan pemrosesan AI/Gemini yang lama
        proxy_connect_timeout 300s;
        proxy_send_timeout 300s;
        proxy_read_timeout 300s;
    }
}
```

### Langkah 4: Aktifkan konfigurasi dan restart Nginx

```bash
# Hubungkan file konfigurasi ke folder sites-enabled agar aktif
sudo ln -s /etc/nginx/sites-available/my-go-app /etc/nginx/sites-enabled/

# Uji konfigurasi Nginx untuk memastikan tidak ada salah ketik (syntax error)
sudo nginx -t

# Restart Nginx untuk menerapkan perubahan
sudo systemctl restart nginx
```

---

## 5. Mengamankan Aplikasi dengan SSL (HTTPS) Gratis

Gunakan **Certbot (Let's Encrypt)** untuk memasang SSL secara otomatis dan gratis.

### Langkah 1: Install Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
```

### Langkah 2: Ambil dan Terapkan SSL Certificate

Jalankan perintah berikut, lalu ikuti instruksi di layar (masukkan email dan setujui syarat penggunaan). Certbot akan mendeteksi server block Nginx Anda dan mengonfigurasinya secara otomatis menjadi HTTPS.

```bash
sudo certbot --nginx -d domainanda.com -d www.domainanda.com
```

### Langkah 3: Verifikasi Auto-Renewal SSL

Sertifikat Let's Encrypt berlaku selama 90 hari. Sistem akan melakukan perpanjangan otomatis secara berkala. Anda bisa mengetes proses simulasi perpanjangan dengan perintah:

```bash
sudo certbot renew --dry-run
```

---

## 6. Cheat Sheet Pemeliharaan Rutin

Setiap kali Anda selesai melakukan `git push` dari komputer lokal, Anda hanya perlu mengetikkan baris perintah ringkas ini di terminal VPS Anda:

```bash
cd /var/www/my-go-app
git pull origin main
go build -ldflags="-s -w" -o app-production ./cmd/api
sudo systemctl restart my-go-app
```

Untuk memantau log aplikasi Anda (misal melihat error kodingan atau hasil cetak `fmt.Println` secara real-time):

```bash
# Memantau log aplikasi secara real-time
journalctl -u my-go-app.service -f -n 100
```
