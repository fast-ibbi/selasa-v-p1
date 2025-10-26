---
title: Database Connection, Migration, dan Eloquent ORM (CRUD Dasar)
version: 1.0.0
header: Database Connection, Migration, dan Eloquent ORM (CRUD Dasar)
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Database Connection, Migration, dan Eloquent ORM (CRUD Dasar)

---

## Tujuan Pembelajaran

- Mahasiswa dapat melakukan konfigurasi koneksi database di Laravel
- Mahasiswa mampu membuat dan menjalankan migration untuk mengelola skema database
- Mahasiswa dapat melakukan operasi CRUD menggunakan Eloquent ORM
- Mahasiswa memahami perbedaan ORM, Query Builder, dan Raw SQL

---

## Pengenalan Database dalam Aplikasi Web

**Peran Database:**

- Menyimpan data aplikasi secara permanen
- Mengelola data terstruktur (tabel, relasi)
- Mendukung operasi pencarian dan filtering data
- Menjaga integritas dan konsistensi data

**Kenapa Perlu Database?**

- Data tidak hilang saat aplikasi restart
- Dapat menangani data dalam jumlah besar
- Mendukung akses concurrent dari banyak user

---

## Konfigurasi Database di Laravel

**File .env:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=
```

**Parameter Penting:**

- `DB_CONNECTION`: Jenis database yang digunakan
- `DB_HOST`: Alamat server database
- `DB_DATABASE`: Nama database
- `DB_USERNAME` & `DB_PASSWORD`: Kredensial akses

---

## Jenis Database yang Didukung Laravel

**Database yang Didukung:**

- MySQL/MariaDB (paling umum)
- PostgreSQL
- SQLite (untuk development kecil)
- SQL Server

**Rekomendasi:**

- Development: MySQL atau SQLite
- Production: MySQL atau PostgreSQL
- Semua konfigurasi dilakukan di file `.env`

---

## Struktur File config/database.php

**File Konfigurasi Database:**

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
],
```

---

**Catatan:**

- File ini membaca dari `.env`
- Jangan hardcode kredensial di sini
- Gunakan `env()` helper untuk fleksibilitas

---

## Testing Koneksi Database

**Cara Test Koneksi:**

```bash
php artisan migrate:status
```

**Atau buat route test sederhana:**

```php
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "Database connection successful!";
    } catch (\Exception $e) {
        return "Could not connect to database: " . $e->getMessage();
    }
});
```

---

**Troubleshooting:**

- Pastikan MySQL/database service berjalan
- Cek kredensial di `.env`
- Pastikan database sudah dibuat

---

## Apa itu Migration?

**Definisi Migration:**

- System version control untuk database schema
- File PHP yang mendefinisikan struktur tabel
- Dapat di-rollback jika terjadi kesalahan

**Keuntungan:**

- Tim dapat sync struktur database dengan mudah
- History perubahan schema tercatat
- Mudah untuk rollback perubahan
- Tidak perlu export/import SQL manual

---

## Migration vs Manual SQL

**Manual SQL:**

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    price DECIMAL(10,2)
);
```

**Laravel Migration:**

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2);
    $table->timestamps();
});
```

---

**Keunggulan Migration:**

- Database agnostic (tidak tergantung jenis database)
- Dapat di-version control (Git)
- Terintegrasi dengan Laravel ecosystem

---

## Membuat Migration dengan Artisan

**Perintah Dasar:**

```bash
php artisan make:migration create_products_table
```

**Dengan opsi create table:**

```bash
php artisan make:migration create_products_table --create=products
```

**Untuk modify table:**

```bash
php artisan make:migration add_category_to_products_table --table=products
```

---

**Lokasi File:**

- Migration disimpan di `database/migrations/`
- Nama file: `YYYY_MM_DD_HHMMSS_create_products_table.php`

---

## Struktur File Migration

**Contoh Migration File:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

**Method Penting:**

- `up()`: Menjalankan migration (create/modify table)
- `down()`: Rollback migration (drop table)

---

## Menjalankan Migration

**Perintah Migration:**

```bash
# Jalankan semua migration yang belum dijalankan
php artisan migrate

# Cek status migration
php artisan migrate:status

# Rollback migration terakhir
php artisan migrate:rollback

# Rollback semua migration
php artisan migrate:reset

# Rollback dan jalankan ulang semua migration
php artisan migrate:refresh

# Fresh migration (drop semua tabel dan migrate ulang)
php artisan migrate:fresh
```

---

## Tipe Kolom dalam Migration

**Tipe Data Umum:**

```php
$table->id();                          // BIGINT UNSIGNED AUTO_INCREMENT
$table->string('name', 100);           // VARCHAR(100)
$table->text('description');           // TEXT
$table->integer('quantity');           // INT
$table->decimal('price', 8, 2);        // DECIMAL(8,2)
$table->boolean('is_active');          // BOOLEAN/TINYINT
$table->date('birth_date');            // DATE
$table->datetime('published_at');      // DATETIME
$table->timestamps();                  // created_at & updated_at
$table->softDeletes();                 // deleted_at (soft delete)
```

---

**Modifiers:**

```php
$table->string('email')->unique();     // UNIQUE constraint
$table->string('phone')->nullable();   // Boleh NULL
$table->integer('price')->default(0);  // Default value
```

---

## Pengenalan Eloquent ORM

**Apa itu Eloquent?**

- Object-Relational Mapping (ORM) bawaan Laravel
- Setiap tabel database = satu Model class
- Manipulasi data dengan syntax PHP object-oriented

**Filosofi:**

```php
// Tanpa ORM
$result = DB::select('SELECT * FROM products WHERE id = ?', [1]);

// Dengan Eloquent
$product = Product::find(1);
```

---

**Keuntungan:**

- Syntax lebih bersih dan readable
- Type safety dan IDE autocomplete
- Built-in relationship handling
- Mudah untuk testing

---

## ORM vs Query Builder vs Raw SQL

**Raw SQL:**

```php
$users = DB::select('SELECT * FROM users WHERE active = ?', [1]);
```

**Query Builder:**

```php
$users = DB::table('users')->where('active', 1)->get();
```

**Eloquent ORM:**

```php
$users = User::where('active', 1)->get();
```

---

**Perbandingan:**

- Raw SQL: Kontrol penuh, tapi rawan SQL injection
- Query Builder: Aman, tapi tidak OOP
- Eloquent: OOP, relationship, events, tapi sedikit overhead

---

## Membuat Model dengan Artisan

**Perintah Dasar:**

```bash
php artisan make:model Product
```

**Dengan Migration sekaligus:**

```bash
php artisan make:model Product -m
```

**Dengan Controller dan Migration:**

```bash
php artisan make:model Product -mc
```

**Semua sekaligus (Model, Migration, Controller, Seeder, Factory):**

```bash
php artisan make:model Product -a
```

**Lokasi:**

- Model disimpan di `app/Models/`

---

## Konvensi Penamaan Model dan Tabel

**Konvensi Laravel:**

- Model: Singular, PascalCase (`Product`)
- Tabel: Plural, snake_case (`products`)

**Contoh:**

```php
// Model: Product
// Tabel otomatis: products

// Model: OrderItem
// Tabel otomatis: order_items
```

---

**Override nama tabel:**

```php
class Product extends Model
{
    protected $table = 'my_products'; // Custom table name
}
```

---

## Mass Assignment dan $fillable/$guarded

**Contoh Model:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Kolom yang boleh di-mass assign
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock'
    ];

    // ATAU kolom yang TIDAK boleh di-mass assign
    // protected $guarded = ['id'];
}
```

---

**Mass Assignment:**

```php
// Ini akan error jika tidak ada $fillable
Product::create([
    'name' => 'Laptop',
    'price' => 5000000
]);
```

**Kenapa Penting?**

- Melindungi dari mass assignment vulnerability
- User tidak bisa inject kolom berbahaya (misal: `is_admin`)

---

## CREATE - Menyimpan Data Baru

**Method 1: create()**

```php
Product::create([
    'name' => 'Laptop ASUS',
    'description' => 'Laptop gaming dengan spesifikasi tinggi',
    'price' => 15000000,
    'stock' => 10
]);
```

**Method 2: new + save()**

```php
$product = new Product();
$product->name = 'Laptop ASUS';
$product->description = 'Laptop gaming';
$product->price = 15000000;
$product->stock = 10;
$product->save();
```

---

**Perbedaan:**

- `create()`: Langsung insert ke database, butuh $fillable
- `save()`: Lebih fleksibel, bisa untuk update juga

---

## Contoh Kode Create dengan Eloquent

**Dalam Controller:**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }
}
```

---

## READ - Mengambil Data

**Method Dasar:**

```php
// Ambil semua data
$products = Product::all();

// Ambil berdasarkan ID
$product = Product::find(1);

// Ambil atau throw exception jika tidak ada
$product = Product::findOrFail(1);

// Ambil data pertama
$product = Product::first();
```

---

```php
// Ambil dengan kondisi
$products = Product::where('price', '>', 1000000)->get();

// Ambil satu data dengan kondisi
$product = Product::where('name', 'Laptop')->first();

// Count data
$count = Product::count();
```

---

## Contoh Kode Read dengan Berbagai Method

**Controller untuk Index:**

```php
public function index()
{
    // Pagination
    $products = Product::paginate(10);

    return view('products.index', compact('products'));
}

public function show($id)
{
    $product = Product::findOrFail($id);

    return view('products.show', compact('product'));
}
```

---

```php
public function search(Request $request)
{
    $products = Product::where('name', 'like', '%' . $request->keyword . '%')
                       ->orWhere('description', 'like', '%' . $request->keyword . '%')
                       ->get();

    return view('products.search', compact('products'));
}
```

---

## UPDATE - Mengubah Data

**Method 1: find() + save()**

```php
$product = Product::find(1);
$product->name = 'Laptop ASUS ROG Updated';
$product->price = 16000000;
$product->save();
```

**Method 2: update()**

```php
Product::where('id', 1)->update([
    'name' => 'Laptop ASUS ROG Updated',
    'price' => 16000000
]);
```

---

**Method 3: findOrFail() + update**

```php
$product = Product::findOrFail(1);
$product->update([
    'name' => 'Laptop ASUS ROG Updated',
    'price' => 16000000
]);
```

---

## Contoh Kode Update dengan Eloquent

**Controller Update Method:**

```php
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $product->update([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock
    ]);

    return response()->json([
        'message' => 'Product updated successfully',
        'data' => $product
    ]);
}
```

---

```php
// Atau dengan conditional update
public function updateStock($id, $quantity)
{
    Product::where('id', $id)
           ->where('stock', '>=', $quantity)
           ->decrement('stock', $quantity);
}
```

---

## DELETE - Menghapus Data

**Method 1: find() + delete()**

```php
$product = Product::find(1);
$product->delete();
```

**Method 2: destroy()**

```php
// Delete satu data
Product::destroy(1);

// Delete multiple data
Product::destroy([1, 2, 3]);
Product::destroy(1, 2, 3);
```

---

**Method 3: where() + delete()**

```php
Product::where('stock', 0)->delete();
```

**Soft Delete:**

```php
// Di Model tambahkan
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
}

// Data tidak benar-benar dihapus, hanya marked deleted_at
```

---

## Studi Kasus Lengkap - CRUD Tabel Products

**1. Buat Migration:**

```bash
php artisan make:migration create_products_table --create=products
```

**2. Edit Migration:**

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->integer('stock')->default(0);
    $table->timestamps();
});
```

---

**3. Jalankan Migration:**

```bash
php artisan migrate
```

**4. Buat Model:**

```bash
php artisan make:model Product
```

**5. Set $fillable di Model:**

```php
protected $fillable = ['name', 'description', 'price', 'stock'];
```

---

## Best Practices Eloquent ORM

**Timestamps Otomatis:**

```php
// Laravel otomatis mengisi created_at dan updated_at
// Jika tidak ingin timestamps:
public $timestamps = false;
```

**Soft Deletes:**

```php
use SoftDeletes;
// Data tidak benar-benar dihapus, bisa di-restore
```

---

**Query Optimization:**

```php
// Gunakan select() untuk kolom tertentu
Product::select('id', 'name', 'price')->get();

// Gunakan chunk() untuk data besar
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // Process
    }
});
```

**Eager Loading (untuk relationship):**

```php
// Hindari N+1 query problem
Product::with('category')->get();
```

---

<!--
_class: lead
-->

# Quiz

---

## Soal 1

**File mana yang digunakan untuk konfigurasi koneksi database di Laravel?**

A. `config/app.php`  
B. `.env`  
C. `config/services.php`  
D. `database.php`

<!-- **Jawaban: B** -->

---

## Soal 2

**Apa fungsi dari method `up()` dalam file migration?**

A. Menghapus tabel dari database  
B. Menjalankan migration (create/modify table)  
C. Rollback migration  
D. Mengecek status migration

<!-- **Jawaban: B** -->

---

## Soal 3

**Perintah mana yang digunakan untuk rollback semua migration?**

A. `php artisan migrate:rollback`  
B. `php artisan migrate:undo`  
C. `php artisan migrate:reset`  
D. `php artisan migrate:clear`

<!-- **Jawaban: C** -->

---

## Soal 4

**Sesuai konvensi Laravel, jika nama Model adalah `OrderItem`, maka nama tabel database-nya adalah?**

A. `OrderItem`  
B. `orderitems`  
C. `order_items`  
D. `orderItems`

<!-- **Jawaban: C** -->

---

## Soal 5

**Apa fungsi dari property `$fillable` dalam Model?**

A. Menentukan kolom yang tidak boleh NULL  
B. Menentukan kolom yang bisa di-mass assignment  
C. Menentukan primary key tabel  
D. Menentukan kolom yang wajib diisi

<!-- **Jawaban: B** -->

---

## Soal 6

**Method Eloquent mana yang akan throw exception jika data tidak ditemukan?**

A. `find()`  
B. `first()`  
C. `findOrFail()`  
D. `get()`

<!-- **Jawaban: C** -->

---

## Soal 7

**Perintah mana yang benar untuk membuat Model dengan Migration sekaligus?**

A. `php artisan make:model Product`  
B. `php artisan make:model Product -m`  
C. `php artisan make:model Product --migration`  
D. `php artisan make:migration Product -model`

<!-- **Jawaban: B** -->

---

## Soal 8

**Apa perbedaan antara `migrate:refresh` dan `migrate:fresh`?**

A. Tidak ada perbedaan  
B. `refresh` rollback lalu migrate, `fresh` drop semua tabel lalu migrate  
C. `refresh` untuk production, `fresh` untuk development  
D. `refresh` lebih cepat dari `fresh`

<!-- **Jawaban: B** -->

---

## Soal 9

**Method mana yang digunakan untuk menghapus multiple data sekaligus dengan Eloquent?**

A. `delete([1, 2, 3])`  
B. `destroy([1, 2, 3])`  
C. `remove([1, 2, 3])`  
D. `deleteMany([1, 2, 3])`

<!-- **Jawaban: B** -->

---

## Soal 10

**Apa keuntungan menggunakan Eloquent ORM dibanding Raw SQL?**

A. Eloquent lebih cepat daripada Raw SQL  
B. Syntax lebih bersih, OOP, dan mendukung relationship  
C. Eloquent hanya bisa untuk MySQL  
D. Raw SQL lebih aman dari SQL injection

<!-- **Jawaban: B** -->
