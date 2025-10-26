---
title: Artisan CLI dan Pembuatan Komponen (Controller, Model, Migration)
version: 1.0.0
header: Artisan CLI dan Pembuatan Komponen (Controller, Model, Migration)
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Artisan CLI dan Pembuatan Komponen (Controller, Model, Migration)

---

## Tujuan Pembelajaran

Setelah pertemuan ini, mahasiswa diharapkan mampu:

- Memahami fungsi dan peran Artisan CLI dalam Laravel
- Menggunakan Artisan untuk membuat controller, model, dan migration
- Menerapkan best practices dalam pembuatan komponen Laravel
- Mengintegrasikan komponen yang dibuat dengan Artisan ke dalam aplikasi

---

## Mengapa Artisan CLI Penting?

**Masalah tanpa Artisan:**

- Membuat file manual → rawan typo dan struktur salah
- Lupa namespace atau use statement
- Tidak konsisten dengan konvensi Laravel
- Membuang waktu untuk hal repetitif

---

**Solusi dengan Artisan:**

- Generate file otomatis dengan struktur benar
- Namespace dan boilerplate code sudah tersedia
- Konsisten dengan Laravel convention
- Hemat waktu, fokus pada logika bisnis

---

## Apa itu Artisan CLI?

**Artisan** adalah command-line interface (CLI) bawaan Laravel yang menyediakan berbagai perintah untuk mempermudah development.

**Karakteristik Artisan:**

- Interface berbasis terminal/command prompt
- Perintah dimulai dengan `php artisan`
- Menyediakan scaffolding (generate code otomatis)
- Dapat di-extend dengan custom command

**Lokasi file:** `artisan` di root folder project Laravel

---

## Filosofi "Convention over Configuration"

Laravel menganut prinsip **Convention over Configuration**:

- Ikuti konvensi → kode bekerja otomatis
- Penamaan file dan class mengikuti standard
- Artisan memastikan konvensi terjaga

**Contoh konvensi:**

- Model: `Product` (singular, PascalCase)
- Tabel: `products` (plural, snake_case)
- Controller: `ProductController`
- Migration: `create_products_table`

---

## Struktur Perintah Artisan

**Format umum:**

```bash
php artisan [command] [arguments] [options]
```

**Komponen:**

- `php artisan`: memanggil Artisan CLI
- `[command]`: perintah yang ingin dijalankan
- `[arguments]`: parameter wajib (nama file, dll)
- `[options]`: parameter opsional (flag dengan `--`)

**Contoh:**

```bash
php artisan make:controller ProductController --resource
```

---

## Menampilkan Daftar Perintah

**Perintah untuk melihat semua command:**

```bash
php artisan list
```

**Output akan menampilkan:**

- Daftar lengkap perintah tersedia
- Pengelompokan berdasarkan kategori (make, migrate, db, cache, dll)
- Deskripsi singkat setiap perintah

---

**Kategori penting:**

- `make:*` - generate file/komponen
- `migrate:*` - kelola database migration
- `db:*` - operasi database
- `cache:*` - kelola cache

---

## Melihat Bantuan Perintah

**Untuk detail suatu perintah:**

```bash
php artisan help [command]
```

**Contoh:**

```bash
php artisan help make:controller
```

---

**Informasi yang ditampilkan:**

- Deskripsi perintah
- Cara penggunaan (usage)
- Arguments yang tersedia
- Options/flags yang bisa digunakan
- Contoh penggunaan

---

## Demo Live - Eksplorasi Artisan

**Praktik di terminal:**

```bash
# Lihat semua perintah
php artisan list

# Lihat bantuan make:controller
php artisan help make:controller

# Cek versi Laravel
php artisan --version

# Lihat environment saat ini
php artisan env
```

**Catatan:** Pastikan terminal/cmd berada di folder root project Laravel!

---

## Membuat Controller dengan Artisan

**Perintah dasar:**

```bash
php artisan make:controller NamaController
```

**Contoh:**

```bash
php artisan make:controller ProductController
```

**Hasil:**

- File dibuat di `app/Http/Controllers/ProductController.php`
- Sudah include namespace dan class definition
- Siap ditambahkan method

---

## Controller Kosong vs Resource Controller

**Controller kosong (basic):**

```bash
php artisan make:controller ProductController
```

Hasil: class kosong tanpa method

**Resource controller:**

```bash
php artisan make:controller ProductController --resource
```

Hasil: class dengan 7 method RESTful (index, create, store, show, edit, update, destroy)

---

**Kapan pakai resource?**

- Jika butuh CRUD lengkap
- Mengikuti REST convention

---

## Perbedaan Controller Biasa dan Resource

**Controller biasa:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Kosong - tambahkan method sendiri
}
```

---

**Resource controller:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() { }
    public function create() { }
    public function store(Request $request) { }
    public function show($id) { }
    public function edit($id) { }
    public function update(Request $request, $id) { }
    public function destroy($id) { }
}
```

---

## Contoh Membuat ProductController

**Perintah:**

```bash
php artisan make:controller ProductController --resource
```

**File hasil:** `app/Http/Controllers/ProductController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Menampilkan daftar produk
    public function index()
    {
        //
    }

    // Menampilkan form tambah produk
    public function create()
    {
        //
    }

    // Menyimpan produk baru
    public function store(Request $request)
    {
        //
    }
}
```

---

## Struktur File Controller

**Anatomi controller yang dihasilkan:**

```php
<?php

namespace App\Http\Controllers;  // 1. Namespace

use Illuminate\Http\Request;     // 2. Import class

class ProductController extends Controller  // 3. Class definition
{
    public function index()       // 4. Method
    {
        // Logic here
    }
}
```

---

**Penjelasan:**

1. Namespace mengikuti struktur folder
2. Import dependencies yang dibutuhkan
3. Extends Controller base class
4. Method berisi logika bisnis

---

## Membuat Model dengan Artisan

**Perintah dasar:**

```bash
php artisan make:model NamaModel
```

**Contoh:**

```bash
php artisan make:model Product
```

**Hasil:**

- File dibuat di `app/Models/Product.php`
- Nama model singular (Product, bukan Products)
- Otomatis extends Model dari Eloquent

---

## Konvensi Penamaan Model vs Tabel

**Aturan Laravel:**

| Model     | Tabel Database |
| --------- | -------------- |
| Product   | products       |
| User      | users          |
| Category  | categories     |
| OrderItem | order_items    |

---

**Konvensi:**

- Model: Singular, PascalCase
- Tabel: Plural, snake_case
- Laravel konversi otomatis (jika ikuti aturan)

**Override jika perlu:**

```php
protected $table = 'my_products';
```

---

## Opsi Tambahan Saat Membuat Model

**Kombinasi dengan komponen lain:**

```bash
# Model + Migration
php artisan make:model Product -m

# Model + Controller
php artisan make:model Product -c

# Model + Migration + Controller
php artisan make:model Product -mc

# Model + Migration + Controller + Resource
php artisan make:model Product -mcr
```

**Rekomendasi:** Gunakan `-mcr` untuk CRUD lengkap sekaligus!

---

## Contoh Membuat Model Product

**Perintah:**

```bash
php artisan make:model Product -m
```

**File hasil:** `app/Models/Product.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
```

---

```php
class Product extends Model
{
    use HasFactory;
}
```

**Bonus:** Migration juga dibuat di `database/migrations/xxxx_create_products_table.php`

---

## Struktur File Model dan Property Default

**Struktur dasar:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Kustomisasi jika perlu
    protected $table = 'products';      // nama tabel
    protected $primaryKey = 'id';       // primary key
    public $timestamps = true;          // created_at, updated_at
```

---

```php
    protected $fillable = [             // kolom yang bisa mass assignment
        'name', 'price', 'description'
    ];

    protected $hidden = [                // kolom yang disembunyikan saat serialize
        'password', 'api_token'
    ];
}
```

---

## Membuat Migration dengan Artisan

**Perintah:**

```bash
php artisan make:migration nama_migration
```

**Konvensi penamaan:**

```bash
# Membuat tabel baru
php artisan make:migration create_products_table

# Menambah kolom
php artisan make:migration add_stock_to_products_table

# Mengubah kolom
php artisan make:migration modify_price_in_products_table
```

**Hasil:** File di `database/migrations/` dengan timestamp prefix

---

## Struktur Nama Migration

**Format:** `timestamp_descriptive_name.php`

**Contoh:**

```
2025_10_20_151234_create_products_table.php
```

**Komponen:**

- `2025_10_20_151234`: Timestamp (tahun_bulan_tanggal_jam_menit_detik)
- `create_products_table`: Deskripsi aksi
- `.php`: Extension file

**Fungsi timestamp:** Menentukan urutan eksekusi migration

---

## File Migration - Method up() dan down()

**Struktur migration:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Dijalankan saat migrate
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    // Dijalankan saat rollback
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

---

## Contoh Membuat Tabel Products

**Migration lengkap untuk tabel products:**

```php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->integer('stock')->default(0);
        $table->string('category');
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('products');
}
```

**Tipe data umum:** string, text, integer, decimal, boolean, date, datetime

---

## Menjalankan Migration

**Perintah untuk eksekusi migration:**

```bash
# Jalankan semua migration yang belum dijalankan
php artisan migrate

# Rollback migration terakhir
php artisan migrate:rollback

# Rollback semua dan jalankan ulang
php artisan migrate:refresh

# Reset semua migration
php artisan migrate:reset

# Cek status migration
php artisan migrate:status
```

**Penting:** Pastikan database sudah dikonfigurasi di `.env` sebelum migrate!

---

## Shortcut - Membuat Komponen Sekaligus

**Perintah paling efisien:**

```bash
php artisan make:model Product -mcr
```

**Hasil (3 file sekaligus):**

1. `app/Models/Product.php` - Model
2. `database/migrations/xxxx_create_products_table.php` - Migration
3. `app/Http/Controllers/ProductController.php` - Resource Controller

**Hemat waktu dan konsisten!**

---

**Variasi lain:**

```bash
# Model + Migration + Factory + Seeder
php artisan make:model Product -mfs
```

---

## Best Practices Artisan CLI

**Tips menggunakan Artisan:**

1. **Selalu gunakan Artisan untuk generate file**

   - Jangan buat file manual
   - Konsistensi terjaga

2. **Ikuti konvensi penamaan Laravel**

   - Model: Singular, PascalCase (Product)
   - Controller: NamaController (ProductController)
   - Migration: descriptive dengan action (create_products_table)

---

3. **Gunakan shortcut kombinasi**

   - `-mcr` untuk modul CRUD lengkap
   - Hemat waktu dan effort

4. **Cek bantuan sebelum eksekusi**
   - `php artisan help [command]`
   - Pahami options yang tersedia

---

## Studi Kasus - Membuat Modul Blog

**Skenario:** Buat modul blog lengkap dengan Post dan Category

**Langkah:**

```bash
# 1. Buat model Post dengan migration, controller resource
php artisan make:model Post -mcr

# 2. Buat model Category dengan migration, controller resource
php artisan make:model Category -mcr

# 3. Edit migration untuk definisi tabel
# 4. Jalankan migration
php artisan migrate

# 5. Isi controller dengan logic CRUD
# 6. Buat route resource di routes/web.php
```

---

## Perintah Artisan Lainnya yang Berguna

**Cache dan optimization:**

```bash
php artisan cache:clear       # Clear application cache
php artisan config:clear      # Clear config cache
php artisan route:clear       # Clear route cache
php artisan view:clear        # Clear compiled views
php artisan optimize          # Optimize framework
```

**Development:**

```bash
php artisan serve             # Start development server
php artisan route:list        # List all routes
php artisan tinker            # Interactive REPL
```

---

## Rangkuman Perintah Penting

| Perintah                      | Fungsi                          |
| ----------------------------- | ------------------------------- |
| `php artisan list`            | Lihat semua command             |
| `php artisan help [command]`  | Bantuan command tertentu        |
| `php artisan make:controller` | Buat controller                 |
| `php artisan make:model`      | Buat model                      |
| `php artisan make:migration`  | Buat migration                  |
| `php artisan make:model -mcr` | Buat model+migration+controller |

---

| Perintah                       | Fungsi             |
| ------------------------------ | ------------------ |
| `php artisan migrate`          | Jalankan migration |
| `php artisan migrate:rollback` | Rollback migration |
| `php artisan serve`            | Start dev server   |
| `php artisan route:list`       | List semua route   |

---

<!--
_class: lead
-->

# Quiz

---

## Soal 1

**Apa fungsi utama dari Artisan CLI di Laravel?**

A. Menjalankan aplikasi Laravel  
B. Menyediakan perintah untuk generate code dan scaffolding  
C. Mengelola database secara manual  
D. Membuat web server untuk Laravel

<!-- **Jawaban: B** -->

---

## Soal 2

**Perintah mana yang benar untuk membuat resource controller?**

A. `php artisan make:controller ProductController`  
B. `php artisan make:controller ProductController --resource`  
C. `php artisan create:controller ProductController --resource`  
D. `php artisan make:resource ProductController`

<!-- **Jawaban: B** -->

---

## Soal 3

**Berapa jumlah method yang otomatis dibuat saat membuat resource controller?**

A. 5 method  
B. 6 method  
C. 7 method  
D. 8 method

<!-- **Jawaban: C** -->

---

## Soal 4

**Sesuai konvensi Laravel, jika nama model adalah `Product`, maka nama tabel database-nya adalah?**

A. `Product`  
B. `product`  
C. `products`  
D. `PRODUCTS`

<!-- **Jawaban: C** -->

---

## Soal 5

**Perintah mana yang membuat Model, Migration, dan Resource Controller sekaligus?**

A. `php artisan make:model Product -m -c -r`  
B. `php artisan make:model Product -mcr`  
C. `php artisan make:model Product --all`  
D. `php artisan make:all Product`

<!-- **Jawaban: B** -->

---

## Soal 6

**Method mana yang dijalankan saat menjalankan perintah `php artisan migrate`?**

A. Method `down()`  
B. Method `up()`  
C. Method `run()`  
D. Method `execute()`

<!-- **Jawaban: B** -->

---

## Soal 7

**Perintah mana yang digunakan untuk rollback migration terakhir?**

A. `php artisan migrate:reset`  
B. `php artisan migrate:undo`  
C. `php artisan migrate:rollback`  
D. `php artisan migrate:back`

<!-- **Jawaban: C** -->

---

## Soal 8

**Di folder mana file Model disimpan setelah dibuat dengan Artisan?**

A. `app/Models/`  
B. `app/Http/Models/`  
C. `database/Models/`  
D. `resources/Models/`

<!-- **Jawaban: A** -->

---

## Soal 9

**Apa fungsi dari property `$fillable` dalam Model?**

A. Menentukan kolom yang wajib diisi  
B. Menentukan kolom yang bisa di-mass assignment  
C. Menentukan kolom yang tidak boleh null  
D. Menentukan kolom yang auto increment

<!-- **Jawaban: B** -->

---

## Soal 10

**Perintah mana yang digunakan untuk melihat daftar semua route yang terdaftar?**

A. `php artisan show:routes`  
B. `php artisan list:routes`  
C. `php artisan routes`  
D. `php artisan route:list`

<!-- **Jawaban: D** -->
