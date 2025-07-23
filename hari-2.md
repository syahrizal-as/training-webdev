# Integrasi Laravel 12 dengan SB Admin 2

## SB Admin 2
![SB ADMIN 2](/assets/sbadmin-2.png)
SB Admin 2 adalah sebuah _free Bootstrap admin HTML template_ yang populer untuk membangun dashboard dan panel admin. Template ini memiliki desain modern, responsif, dan mudah diintegrasikan dengan berbagai framework web, termasuk Laravel.

Beberapa fitur utama SB Admin 2:
- Desain bersih dan profesional
- Responsif untuk desktop dan mobile
- Tersedia berbagai komponen UI seperti tabel, grafik, chart, form, dan lain-lain
- Mudah untuk dikustomisasi

Sumber:  
https://startbootstrap.com/theme/sb-admin-2  
Download:  
https://github.com/StartBootstrap/startbootstrap-sb-admin-2

---

## Fitur Utama SB Admin 2

**Layout & Responsivitas**  
Tampilan otomatis menyesuaikan di desktop, tablet, dan smartphone.

**Komponen UI**  
Menyediakan banyak elemen siap pakai seperti sidebar, navbar, chart, tabel, form login, halaman profil, dan lain-lain.

**Plugin Kustom**  
Dapat menambahkan plugin tambahan untuk kebutuhan dashboard seperti grafik, kalender, dan widget lainnya.

**Integrasi Mudah**  
Struktur file rapi dan mudah untuk diintegrasikan dengan framework seperti Laravel.

---

## Apa Itu Laravel?

Laravel adalah framework PHP modern yang menerapkan arsitektur MVC (Model-View-Controller). Framework ini digunakan untuk membangun aplikasi web secara efisien, aman, dan scalable.

**Keunggulan Laravel:**
- Routing dan middleware yang powerful
- ORM Eloquent untuk database
- Blade Template Engine untuk tampilan
- Otentikasi dan manajemen user
- Ekosistem paket yang luas

---

## Integrasi SB Admin 2 dengan Laravel 12

### Langkah-langkah Integrasi

1. **Install Laravel 12**
   ```bash
   composer create-project laravel/laravel laravel-sbadmin
   ```

2. **Download SB Admin 2**
   Unduh template SB Admin 2 dari Github atau website resmi.

3. **Copy Asset SB Admin 2**
   - Copy folder `assets` (CSS, JS, fonts) ke folder `public/` Laravel.
   - Copy file HTML utama ke folder `resources/views/`.

4. **Ubah File Blade**
   - Rename file HTML menjadi `.blade.php`
   - Integrasikan dengan Blade Template (`@yield`, `@section`, dst)

5. **Sesuaikan Routing dan Controller**
   - Buat controller dan route untuk mengakses dashboard SB Admin 2.

Contoh route:
```php
Route::get('/dashboard', [DashboardController::class, 'index']);
```

---

## Struktur MVC pada Laravel

**Model:**  
Berfungsi untuk mengelola data dan logika bisnis, biasanya berhubungan dengan database.

**View:**  
Berfungsi untuk menampilkan data ke user, menggunakan Blade template engine.

**Controller:**  
Menghubungkan model dan view, mengatur logika request dan response.

![Struktur MVC](image2)

---

## Manfaat Integrasi SB Admin 2 dengan Laravel

- **Mempercepat pembuatan dashboard admin**
- **Tampilan profesional dan responsif**
- **Kode lebih rapi dan terstruktur dengan arsitektur MVC**
- **Mudah dikembangkan dan scalable untuk proyek besar**

![Manfaat MVC](image3)

---

## Kenapa MVC dan Template Admin Penting?

**Modular:**  
Pisahkan tugas, mudah di-maintain dan dikembangkan.

**Reusable:**  
Logic dan tampilan bisa dipakai ulang.

**Lebih Terstruktur:**  
Alur kode jelas, mudah debug.

**Aman dan Scalable:**  
Cocok untuk aplikasi besar dan tim development.

![Kenapa MVC penting](image4)
