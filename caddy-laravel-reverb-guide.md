# Panduan Panduan Setup Laravel Reverb & Caddy Web Server di VPS

Panduan praktis dan efisien untuk mengonfigurasi **Laravel Reverb** (WebSocket) di belakang **Caddy Web Server** dan **Supervisor** pada VPS Linux.

---

## 🏗️ Arsitektur Singkat

```text
[ Browser / Client ] 
       │ (HTTPS / WSS via Port 443)
       ▼
[ Caddy Web Server ]
       ├── Traffic Biasa (HTTP / Inertia) ──> [ PHP-FPM / Sock ] ──> Laravel App
       └── Traffic WebSocket (/app/*) ──────> [ 127.0.0.1:8080 ] ──> Laravel Reverb
```

---

## 🌐 1. Konfigurasi Environment (`.env`)

Pastikan file `.env` di Laravel memuat variabel berikut:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=ol1vxdch4osqwgjgatj1
REVERB_APP_KEY=ol1vxdch4osqwgjgatj1
REVERB_APP_SECRET=rahasia_app_secret

REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

---

## ⚡ 2. Konfigurasi Frontend Echo (`resources/js/echo.js`)

Sesuaikan `echo.js` agar klien browser otomatis mengarahkan koneksi WebSocket melalui port **443 (HTTPS/WSS)** di domain utama:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: window.location.hostname === 'localhost' ? 8080 : 443,
    wssPort: window.location.hostname === 'localhost' ? 8080 : 443,
    forceTLS: window.location.hostname !== 'localhost',
    enabledTransports: ['ws', 'wss'],
});
```

---

## ⚙️ 3. Konfigurasi Supervisor (Layanan Background)

Agar daemon Reverb tetap berjalan otomatis di background dan hidup kembali jika server restart.

### Buat File: `/etc/supervisor/conf.d/skripsi-reverb.conf`

```ini
[program:skripsi-reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/skripsi/artisan reverb:start --host=127.0.0.1 --port=8080
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/skripsi/storage/logs/reverb.log
```

### Jalankan perintah reload Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status skripsi-reverb:skripsi-reverb_00
```

---

## 🛡️ 4. Konfigurasi Caddy Web Server (`/etc/caddy/Caddyfile`)

Edit file `/etc/caddy/Caddyfile` pada blok domain aplikasi Anda. Tambahkan matcher dan rule `reverse_proxy` ke port **8080**.

```caddy
ai.izaldev.my.id {
    root * /var/www/skripsi/public
    encode zstd gzip
    tls internal

    # Matcher & Proxy khusus untuk WebSocket Reverb
    @reverb {
        header Connection *Upgrade*
        header Upgrade websocket
    }
    reverse_proxy @reverb 127.0.0.1:8080
    reverse_proxy /app/* 127.0.0.1:8080

    # Handler PHP-FPM standar untuk halaman web biasa
    php_fastcgi unix//run/php/php8.4-fpm.sock
    file_server
}
```

### Uji & Reload Caddy:

```bash
# Validasi sintaks Caddyfile
sudo caddy validate --config /etc/caddy/Caddyfile

# Reload Caddy tanpa downtime
sudo systemctl reload caddy
```

---

## 🔍 5. Pengujian & Verifikasi (Troubleshooting)

### A. Tes Reverb Lokal (VPS)
```bash
curl -i -N -H "Connection: Upgrade" -H "Upgrade: websocket" \
  -H "Host: ai.izaldev.my.id" \
  -H "Origin: https://ai.izaldev.my.id" \
  "http://127.0.0.1:8080/app/YOUR_REVERB_APP_KEY?protocol=7&client=js&version=8.5.0&flash=false"
```
*Ekspektasi Output:* `HTTP/1.1 101 Switching Protocols`

### B. Tes Reverb Publik (HTTPS / WSS via Caddy)
```bash
curl -i -N -H "Connection: Upgrade" -H "Upgrade: websocket" \
  -H "Host: ai.izaldev.my.id" \
  -H "Origin: https://ai.izaldev.my.id" \
  "https://ai.izaldev.my.id/app/YOUR_REVERB_APP_KEY?protocol=7&client=js&version=8.5.0&flash=false"
```
*Ekspektasi Output:* `HTTP/1.1 101 Switching Protocols`

---

## 📌 Kesimpulan Ringkas
1. **Reverb** berjalan secara privat di `127.0.0.1:8080` dikelola oleh **Supervisor**.
2. **Caddy** menangkap request jalur `/app/*` atau Header Upgrade WebSocket lalu meneruskannya ke port `8080`.
3. Frontend **Echo** terhubung cukup lewat port standar `443` (`wss://domain.com/app/...`), tanpa perlu membuka port `8080` ke publik.
