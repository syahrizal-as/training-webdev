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


## Struktur MVC pada Laravel

**Model:**  
Berfungsi untuk mengelola data dan logika bisnis, biasanya berhubungan dengan database.

**View:**  
Berfungsi untuk menampilkan data ke user, menggunakan Blade template engine.

**Controller:**  
Menghubungkan model dan view, mengatur logika request dan response.

![Struktur MVC](/assets/mvc.png)

---

## Manfaat Integrasi SB Admin 2 dengan Laravel

- **Mempercepat pembuatan dashboard admin**
- **Tampilan profesional dan responsif**
- **Kode lebih rapi dan terstruktur dengan arsitektur MVC**
- **Mudah dikembangkan dan scalable untuk proyek besar**


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

#  Laravel 12 & SB Admin 2

## 1. Persiapan Project

Install Laravel 12:
```bash
composer create-project laravel/laravel:^12.0 laravel-sbadmin
```
Integrasikan SB Admin 2 seperti pada materi sebelumnya.

## 2. Konfigurasi File `.env` di Laravel

Setelah instalasi Laravel selesai, lakukan konfigurasi pada file `.env` yang ada di root folder project. File ini digunakan untuk menyimpan konfigurasi lingkungan aplikasi seperti koneksi database, mail, dan lain-lain.

Contoh konfigurasi database di file `.env`:
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:xxxxxxxxxxxxxx
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

## 3. Membuat Model dan Migration User

Jika belum ada, buat model User dan migration:
```bash
php artisan make:model User -m
```
Edit migration di `database/migrations/xxxx_xx_xx_create_users_table.php`:
```php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->timestamps();
    });
}
```
Migrasi database:
```bash
php artisan migrate
```

## 4. Membuat Controller CRUD

Buat controller dengan resource:
```bash
php artisan make:controller UserController --resource
```

## 5. Membuat Form Request Validasi

Buat form request untuk validasi:
```bash
php artisan make:request UserRequest
```
Edit `app/Http/Requests/UserRequest.php`:
```php

public function authorize(): bool
{
    return true;
}

public function rules()
{
    return [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|unique:users,email,' . $this->id,
        'password' => $this->isMethod('post') ? 'required|min:6' : 'nullable|min:6',
    ];
}
```

## 6. Implementasi CRUD di Controller

Edit `app/Http/Controllers/UserController.php`:
```php
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

public function index()
{
    $users = User::paginate(10);
    return view('users.index', compact('users'));
}

public function create()
{
    return view('users.create');
}

public function store(UserRequest $request)
{
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    return to_route('users.index')->with('success', 'User berhasil ditambahkan!');
}

public function edit(User $user)
{
    return view('users.edit', compact('user'));
}

public function update(UserRequest $request, User $user)
{
    $data = $request->only(['name','email']);
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }
    $user->update($data);
    return to_route('users.index')->with('success', 'User berhasil diupdate!');
}

public function destroy(User $user)
{
    $user->delete();
    return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
}
```

## 7. Routing

Edit `routes/web.php`:
```php
Route::resource('users', UserController::class);
```

## 8. Blade SB Admin 2 - Tabel Users

Contoh tampilan index users (`resources/views/users/index.blade.php`):
```blade
@extends('layouts.sbadmin')
@section('content')
<div class="container">
    <h1>Daftar Users</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary mb-2">Tambah User</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
```

## 9. Blade SB Admin 2 - Create & Edit User

`resources/views/users/create.blade.php` dan `edit.blade.php` (mirip):
```blade
@extends('layouts.sbadmin')
@section('content')
<div class="container">
    <h1>Tambah User</h1>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
```
Untuk edit, ganti action dan value:
```blade
<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
        @error('name') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
        @error('email') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="mb-3">
        <label>Password (isi jika ingin mengganti)</label>
        <input type="password" name="password" class="form-control">
        @error('password') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <button class="btn btn-success">Update</button>
</form>
```

## 10. Layout SB Admin 2

Pastikan `resources/views/layouts/sbadmin.blade.php` sudah menampilkan konten dengan `@yield('content')`, dan asset sudah terintegrasi.

---

## 11. Validasi Otomatis

Jika validasi gagal, Laravel otomatis redirect ke form dan menampilkan error di setiap field, seperti yang dicontohkan di atas.


**Tugas 2:**
- Buatkan satu sidebar baru (Book, Courses, Product, dsb) upload foto ke storage Laravel
- Buat se-kreatifitas mungkin dengan memanfaatkan styling dari Bootstrap
- Di menu tersebut terdapat:
    - Penjelasan terkait fungsi dari menu tersebut
    - Action Button untuk Add Data dan List Data baik sebelum atau sesudah ada datanya
    - Action Button Edit dan fungsi Editnya berjalan dengan baik
    - Action Button Delete dan fungsi Deletenya berjalan dengan baik
    - Dokumentasi berupa screenshot dari web yang sudah dibuat dalam bentuk .pdf

**Link Pengumpulan Tugas:**  
Menyusul