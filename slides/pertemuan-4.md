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

## Tujuan Pembelajaran

- Mahasiswa memahami struktur proyek Laravel secara menyeluruh
- Mahasiswa mampu menggunakan Composer untuk mengelola dependencies
- Mahasiswa dapat mengkonfigurasi environment file (.env) dengan benar
- Mahasiswa memahami file-file konfigurasi dasar Laravel

---

## Apa itu Composer?

**Composer = Dependency Manager untuk PHP**

- Tool untuk mengelola library/package yang dibutuhkan project
- Mirip dengan npm (Node.js) atau pip (Python)
- Mengelola versi package secara otomatis
- Autoloading class PHP modern (PSR-4)

---

## Peran Composer dalam Ekosistem PHP

**Mengapa Composer Penting?**

- Menghindari copy-paste library manual
- Memastikan compatibility antar package
- Update package dengan mudah
- Standard industri untuk project PHP modern

---

**Laravel menggunakan Composer untuk:**

- Install framework itu sendiri
- Mengelola dependencies (puluhan package)
- Autoloading semua class

---

## Instalasi Composer

- Download dari getcomposer.org
- Jalankan Composer-Setup.exe
- Restart terminal setelah instalasi
- composer --version

---

## File composer.json

**Struktur composer.json:**

```json
{
  "name": "laravel/laravel",
  "type": "project",
  "require": {
    "php": "^8.1",
    "laravel/framework": "^10.0"
  },
  "autoload": {
    "psr-4": {
      "App\\": "app/"
    }
  }
}
```

---

**Bagian Penting:**

- `require`: dependencies production
- `require-dev`: dependencies development only
- `autoload`: mapping namespace ke folder

---

## Perintah Dasar Composer

**Command yang Sering Digunakan:**

```bash
# Install semua dependencies dari composer.json
composer install

# Menambah package baru
composer require guzzlehttp/guzzle

# Update semua dependencies
composer update
```

---

```bash
# Update autoload files
composer dump-autoload

# Hapus package
composer remove package-name
```

---

## Overview Struktur Folder Laravel

**Root Directory Laravel:**

```
laravel-project/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── vendor/
├── .env
├── artisan
└── composer.json
```

---

## Folder app/

**Isi Folder app/:**

```
app/
├── Console/          # Custom Artisan commands
├── Exceptions/       # Exception handling
├── Http/
│   ├── Controllers/  # Application controllers
│   ├── Middleware/   # HTTP middleware
│   └── Requests/     # Form requests
├── Models/           # Eloquent models
└── Providers/        # Service providers
```

**Ini adalah jantung aplikasi** - tempat logic bisnis berada

---

## Folder config/

**File Konfigurasi Aplikasi:**

```
config/
├── app.php          # Konfigurasi umum (timezone, locale)
├── database.php     # Koneksi database
├── mail.php         # Konfigurasi email
├── cache.php        # Cache drivers
├── session.php      # Session management
└── ...
```

**Semua setting dari .env akan dibaca di sini**

---

## Folder database/

**Struktur Database:**

```
database/
├── factories/       # Model factories untuk testing
├── migrations/      # Database schema migrations
└── seeders/         # Data seeding
```

**Migrations:** Version control untuk database schema
**Seeders:** Populate database dengan data dummy

---

## Folder public/

**Entry Point Aplikasi:**

```
public/
├── index.php        # Front controller (entry point)
├── css/             # Compiled CSS
├── js/              # Compiled JavaScript
└── images/          # Public images
```

**Penting:**

- Hanya folder ini yang accessible dari web
- Document root web server harus point ke sini
- Semua request masuk melalui `index.php`

---

## Folder resources/

**Asset & View Files:**

```
resources/
├── css/             # Source CSS files
├── js/              # Source JavaScript files
└── views/           # Blade template files
    ├── layouts/
    ├── components/
    └── pages/
```

**Blade templates:** File dengan ekstensi `.blade.php`

---

## Folder routes/

**Definisi Routing:**

```
routes/
├── web.php          # Web routes (dengan session, CSRF)
├── api.php          # API routes (stateless)
├── console.php      # Artisan commands
└── channels.php     # Broadcasting channels
```

**Contoh web.php:**

```php
Route::get('/', function () {
    return view('welcome');
});
```

---

## Folder storage/ dan bootstrap/cache/

**storage/:**

```
storage/
├── app/             # File uploads
├── framework/       # Cache, sessions, views
└── logs/            # Application logs
```

**bootstrap/cache/:**

- Cache untuk config, routes, services
- Meningkatkan performance

**Penting:** Folder ini harus writable oleh web server

---

## Apa itu File .env?

**Environment Configuration File**

- File yang menyimpan konfigurasi environment-specific
- Berbeda untuk setiap environment (local, staging, production)
- Tidak di-commit ke Git (ada di .gitignore)

---

**Contoh .env:**

```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## Struktur File .env

**Kategori Konfigurasi:**

**Application:**

```env
APP_NAME=MyApp
APP_ENV=local           # local, production, staging
APP_DEBUG=true          # false di production
APP_URL=http://localhost
```

---

**Database:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=secret
```

**Mail, Cache, Queue, dll** (akan dibahas di pertemuan lanjutan)

---

## Environment Variables vs Hard-coded

**❌ Hard-coded (Buruk):**

```php
// Di dalam code
$connection = mysqli_connect(
    '127.0.0.1',
    'root',
    'password123',
    'mydb'
);
```

---

**✅ Environment Variables (Baik):**

```php
// Menggunakan .env
$host = env('DB_HOST');
$user = env('DB_USERNAME');
$pass = env('DB_PASSWORD');
```

**Keuntungan:** Mudah ganti setting tanpa ubah code

---

## Praktik Setup Database di .env

**Langkah-langkah:**

1. Buat database di MySQL/phpMyAdmin

```sql
CREATE DATABASE laravel_app;
```

2. Edit file .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=root
DB_PASSWORD=
```

---

3. Test koneksi

```bash
php artisan migrate
```

---

## Keamanan File .env

**⚠️ JANGAN:**

- Commit .env ke Git/GitHub
- Share .env di public
- Gunakan password lemah di .env

---

**✅ LAKUKAN:**

- Gunakan .env.example sebagai template

```env
# .env.example
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

- Setiap developer copy ke .env dan isi sendiri
- Gunakan password kuat di production

---

## File config/app.php

**Konfigurasi Umum Aplikasi:**

```php
return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'providers' => [
        // Service providers list
    ],
];
```

**Ubah timezone ke Asia/Jakarta untuk Indonesia**

---

## File config/database.php

**Setup Multiple Database Connections:**

```php
'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'forge'),
        'username' => env('DB_USERNAME', 'forge'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],

    'sqlite' => [
        'driver' => 'sqlite',
        'database' => database_path('database.sqlite'),
    ],
],
```

---

## Cache Konfigurasi

**Optimasi dengan Config Cache:**

```bash
# Cache semua config untuk production
php artisan config:cache

# Clear config cache
php artisan config:clear
```

**Kapan menggunakan:**

- Production: SELALU cache config
- Development: JANGAN cache (sulit debug)

---

**Efek:**

- Aplikasi lebih cepat
- File .env tidak dibaca lagi (gunakan cache)

---

## Debug Mode dan APP_KEY

**APP_DEBUG:**

```env
APP_DEBUG=true   # Development: tampilkan error detail
APP_DEBUG=false  # Production: sembunyikan error detail
```

**APP_KEY:**

```env
APP_KEY=base64:random32characterstring...
```

- Digunakan untuk encryption
- Generate dengan: `php artisan key:generate`
- **WAJIB** di-generate setelah clone project

---

<!--
_class: lead
-->

# Quiz

---

## Soal 1

**Apa fungsi utama Composer dalam ekosistem PHP?**

A. Compiler untuk PHP  
B. Dependency manager untuk PHP  
C. Web server untuk PHP  
D. Database manager untuk PHP

<!-- **Jawaban: B** -->

---

## Soal 2

**Folder mana yang berisi logic bisnis utama aplikasi Laravel?**

A. `public/`  
B. `resources/`  
C. `app/`  
D. `vendor/`

<!-- **Jawaban: C** -->

---

## Soal 3

**File apa yang menjadi entry point dari aplikasi Laravel?**

A. `app/index.php`  
B. `public/index.php`  
C. `routes/web.php`  
D. `bootstrap/app.php`

<!-- **Jawaban: B** -->

---

## Soal 4

**Perintah Composer mana yang digunakan untuk menambah package baru?**

A. `composer add package-name`  
B. `composer install package-name`  
C. `composer require package-name`  
D. `composer new package-name`

<!-- **Jawaban: C** -->

---

## Soal 5

**Mengapa file .env tidak boleh di-commit ke Git?**

A. File terlalu besar  
B. Berisi konfigurasi sensitif seperti password  
C. File tidak diperlukan di repository  
D. Laravel melarang upload file .env

<!-- **Jawaban: B** -->

---

## Soal 6

**Folder mana yang harus writable oleh web server?**

A. `app/`  
B. `config/`  
C. `storage/`  
D. `routes/`

<!-- **Jawaban: C** -->

---

## Soal 7

**Perintah artisan apa yang digunakan untuk generate APP_KEY?**

A. `php artisan make:key`  
B. `php artisan key:generate`  
C. `php artisan generate:key`  
D. `php artisan create:key`

<!-- **Jawaban: B** -->

---

## Soal 8

**Nilai mana yang sebaiknya diset untuk APP_DEBUG di production?**

A. `true`  
B. `false`  
C. `1`  
D. `debug`

<!-- **Jawaban: B** -->

---

## Soal 9

**Folder mana yang berisi file Blade template?**

A. `app/Views/`  
B. `public/views/`  
C. `resources/views/`  
D. `storage/views/`

<!-- **Jawaban: C** -->

---

## Soal 10

**Apa fungsi dari file `composer.json`?**

A. Menyimpan password database  
B. Konfigurasi web server  
C. Mendefinisikan dependencies dan autoloading  
D. Menyimpan routing aplikasi

<!-- **Jawaban: C** -->
