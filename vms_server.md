# Panduan Deployment Aplikasi Go (Golang) di VPS Linux

Dokumen ini berisi panduan langkah-demi-langkah untuk mendeploy aplikasi **aigo** (Go Backend Agent) di VPS Linux.

---

## 1. Persiapan Server (Satu Kali Setup)

### A. Install Go Compiler di VPS

```bash
sudo apt update
sudo apt install golang-go -y
go version
```

> Minimal **Go 1.21**. Kalau < 1.21, upgrade manual (§7.2).

### B. Siapkan Folder Aplikasi

```bash
sudo mkdir -p /var/www/aigo
sudo chown -R $USER:$USER /var/www/aigo
```

### C. Clone Repository

```bash
cd /var/www/
git clone https://github.com/syahrizal-as/aigo.git aigo
```

### D. Setup .env

```bash
cd /var/www/aigo
cp .env.example .env
nano .env   # isi DB credentials, GEMINI_API_KEY, API_KEY
```

---

## 2. Alur Rutin: Git Pull & Build

```bash
cd /var/www/aigo
git pull origin main
go mod tidy
go build -ldflags="-s -w" -o api-server ./cmd/api
```

---

## 3. Konfigurasi Systemd Service

### Buat file service

```bash
sudo nano /etc/systemd/system/aigo.service
```

### Isi konfigurasi

```ini
[Unit]
Description=Aigo — VMS AI Backend Agent
After=network.target

[Service]
Type=simple
User=root
WorkingDirectory=/var/www/aigo
ExecStart=/var/www/aigo/api-server
Restart=always
RestartSec=5

Environment=PORT=8080

[Install]
WantedBy=multi-user.target
```

### Aktifkan & Jalankan

```bash
sudo systemctl daemon-reload
sudo systemctl enable aigo
sudo systemctl start aigo
sudo systemctl status aigo
```

---

## 4. Konfigurasi Nginx Reverse Proxy

```bash
sudo apt install nginx -y
sudo nano /etc/nginx/sites-available/aigo
```

```nginx
server {
    listen 80;
    server_name aigo-api.yourdomain.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;

        # Timeout untuk AI/Gemini processing
        proxy_connect_timeout 300s;
        proxy_send_timeout 300s;
        proxy_read_timeout 300s;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/aigo /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 5. SSL dengan Certbot (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d aigo-api.yourdomain.com
sudo certbot renew --dry-run
```

---

## 6. Cheat Sheet Pemeliharaan

Setiap `git push` dari lokal, jalankan di server:

```bash
cd /var/www/aigo
git pull origin main
go build -ldflags="-s -w" -o api-server ./cmd/api
sudo systemctl restart aigo
```

Pantau log:

```bash
journalctl -u aigo.service -f -n 100
```

---

## 7. Troubleshooting

### 7.1 `go: errors parsing go.mod: invalid go version`
Server punya Go < 1.21. Upgrade Go (§7.2).

### 7.2 Upgrade Go di Server
```bash
wget -q https://go.dev/dl/go1.23.0.linux-amd64.tar.gz -O /tmp/go.tar.gz
sudo rm -rf /usr/local/go && sudo tar -C /usr/local -xzf /tmp/go.tar.gz && rm /tmp/go.tar.gz
echo 'export PATH=/usr/local/go/bin:$PATH' >> ~/.bashrc && source ~/.bashrc
go version  # harus: go1.23.0
```

### 7.3 `status=203/EXEC` — Binary tidak bisa dieksekusi
```bash
cd /var/www/aigo
go build -ldflags="-s -w" -o api-server ./cmd/api
chmod +x api-server
sudo systemctl restart aigo
```

### 7.4 `status=1/FAILURE` — Aplikasi crash
Cek log:
```bash
journalctl -u aigo.service -n 30 --no-pager
cd /var/www/aigo && ./api-server   # jalanin manual
```

| Error | Solusi |
|---|---|
| `Gagal menghubungkan database MySQL` | Cek `DB_*` di `.env` |
| `GEMINI_API_KEY belum disetel` | `.env`: `AI_MODE=gemini` + `GEMINI_API_KEY=...` |
| `failed to find default credentials` | Ganti ke `AI_MODE=gemini` atau setup ADC |
| `bind: address already in use` | `sudo fuser -k 8080/tcp && sudo systemctl restart aigo` |

### 7.5 Git pull conflict di `go.mod`
```bash
git checkout -- go.mod && git pull
```

### 7.6 `no required module provides package main.go`
```bash
go build -o api-server ./cmd/api    # bukan main.go!
```
