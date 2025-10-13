---
title: Struktur Dasar Laravel & Project Setup (Composer, Direktori, .env, Konfigurasi Dasar)
version: 1.0.0
header: Struktur Dasar Laravel & Project Setup (Composer, Direktori, .env, Konfigurasi Dasar)
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Struktur Dasar Laravel & Project Setup (Composer, Direktori, .env, Konfigurasi Dasar)

---

# Judul & Tujuan Pertemuan

- Memahami struktur folder Laravel
- Mengerti fungsi Composer dalam Laravel
- Mengatur konfigurasi dasar, khususnya file `.env`

---

# Alur Pengembangan Laravel

1. Gunakan Composer untuk instalasi & dependency
2. Struktur folder terorganisir: `app`, `routes`, `config`, dll.
3. Artisan CLI untuk bantu pembangunan server side code
4. File `.env` menentukan konfigurasi environment

---

# Composer - Apa dan Kenapa?

- Dependency manager untuk PHP
- Mengatur paket eksternal yang dibutuhkan Laravel
- Memudahkan update dan autoload class

Contoh perintah pemasangan Laravel:

```bash
composer create-project --prefer-dist laravel/laravel nama-project
```

---

# Perintah Dasar Composer

- `composer install` : Install paket dari `composer.json`
- `composer update` : Update paket ke versi terbaru
- `composer require vendor/package` : Menambah paket baru

---

# Struktur Folder Laravel Overview

Beberapa folder utama:

- `app/` — Logika bisnis dan model
- `bootstrap/` — Inisialisasi framework
- `config/` — File konfigurasi
- `database/` — Migration & seed
- `public/` — Root website
- `resources/` — View blade & assets
- `routes/` — Mendefinisikan rute aplikasi
- `storage/` — Cache, logs, upload
- `vendor/` — Package Composer

---

# Folder `app/`

`app/` adalah pusat logika aplikasi:

- Controller
- Model
- Middleware
- Request

---

# Folder `bootstrap/`

- Berisi file `app.php`
- Inisialisasi framework dan melakukan bootstrap aplikasi

---

# Folder `config/`

File konfigurasi utama aplikasi seperti:

- `app.php` — konfigurasi umum
- `database.php` — koneksi database

---

# Folder `database/`

- Berisi migration dan seeders
- File migration digunakan untuk membentuk struktur database

---

# Folder `public/`

- Folder yang dapat diakses publik
- Menyimpan asset seperti JS, CSS, gambar
- File `index.php` berada di sini, pintu masuk aplikasi web

---

# Folder `resources/`

- Tempat template blade dan asset front-end

---

# Folder `routes/`

- Mendefinisikan route URL aplikasi
- Contoh file:
  - `web.php` untuk route web biasa
  - `api.php` untuk route API

---

# Folder `storage/`

- Menyimpan cache, logs, dan file upload user

---

# Folder `tests/`

- Unit testing dan feature testing aplikasi

---

# Folder `vendor/`

- Menyimpan semua package dari Composer
- Jangan diubah manual

---

# Rangkuman Struktur Folder

Memahami fungsi umum folder memudahkan navigasi kode dan pengembangan

---

# Struktur internal `app/` (bagian pertama)

- `Http/Controllers` — Controller aplikasi
- `Http/Middleware` — Filter request HTTP

---

# Struktur internal `app/` (bagian kedua)

- `Models` — Representasi tabel database
- `Providers` — Provider layanan, bootstrap aplikasi

---

# Praktik Membuat Controller dengan Artisan

Perintah artisan:

```bash
php artisan make:controller NamaController
```

Contoh membuat controller bernama UserController:

```bash
php artisan make:controller UserController
```

---

# Instalasi Laravel dengan Composer

Perintah instalasi project baru:

```bash
composer create-project --prefer-dist laravel/laravel nama_project
```

Setelah selesai, masuk folder:

```bash
cd nama_project
```

---

# Struktur Project Setelah Instalasi

Folder yang muncul sesuai struktur Laravel standar yang sudah dijelaskan sebelumnya.

---

# Konfigurasi Database di `.env`

Contoh isi `.env` bagian database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database
```

Ubah sesuai konfigurasi lokal

---

# Fungsi File `.env`

- Menyimpan variabel konfigurasi aplikasi
- Membedakan environment (local, production, testing)
- Tidak dipush ke repository git biasa (tertera di `.gitignore`)

---

# Cara Laravel Menggunakan `.env`

- File `.env` dibaca saat bootstrap aplikasi
- Variabelnya bisa diakses via fungsi `env('VAR_NAME')`
- Mendukung konfigurasi dinamis dan aman

---

# Praktik Mengubah Konfigurasi di `.env`

Langkah:

1. Buka file `.env`
2. Edit informasi DB sesuai server lokal
3. Simpan dan jalankan aplikasi:

```bash
php artisan serve
```

4. Cek koneksi database lewat migrasi

---

# Keamanan File `.env`

- Jangan commit file `.env` ke repositori
- Gunakan `.env.example` sebagai template
- Saat deployment buat file `.env` baru di server

---

# Konfigurasi File `config/app.php`

Contoh isi bagian app name:

```php
return [
    'name' => env('APP_NAME', 'Laravel'),
    // konfigurasi lain...
];
```

Variabel `APP_NAME` diambil dari `.env`

---

# Mengubah Environment `APP_ENV` dan Debug

- Di `.env`

```env
APP_ENV=local
APP_DEBUG=true
```

- `APP_DEBUG` aktifkan untuk menampilkan error saat development
