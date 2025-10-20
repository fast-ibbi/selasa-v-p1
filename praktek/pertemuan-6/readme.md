# Praktikum Pertemuan 6 - Artisan CLI dan Pembuatan Komponen

## Soal 1: Eksplorasi Perintah Artisan

**Tujuan:** Mengenal dan memahami perintah-perintah Artisan yang tersedia

**Instruksi:**

1. Buka terminal di root project Laravel
2. Jalankan perintah untuk melihat semua command Artisan:
   ```bash
   php artisan list
   ```
3. Catat minimal 10 perintah Artisan yang berbeda dari kategori berbeda (make, migrate, cache, dll)
4. Untuk setiap perintah, jalankan help untuk melihat detail:
   ```bash
   php artisan help make:controller
   php artisan help migrate
   # dst...
   ```
5. Buat tabel dokumentasi dengan format:

| Kategori | Perintah        | Fungsi             | Options yang tersedia |
| -------- | --------------- | ------------------ | --------------------- |
| make     | make:controller | Membuat controller | --resource, --model   |
| migrate  | migrate         | ...                | ...                   |

6. Screenshot terminal dan tabel dokumentasi

**Deliverable:**

- Screenshot `php artisan list`
- Tabel dokumentasi 10 perintah
- Screenshot help dari minimal 3 perintah

---

## Soal 2: Membuat Controller Basic dan Resource

**Tujuan:** Memahami perbedaan controller biasa dan resource controller

**Instruksi:**

1. Buat controller biasa dengan nama `HomeController`:

   ```bash
   php artisan make:controller HomeController
   ```

2. Buat resource controller dengan nama `BookController`:

   ```bash
   php artisan make:controller BookController --resource
   ```

3. Buka kedua file dan bandingkan:

   - `app/Http/Controllers/HomeController.php`
   - `app/Http/Controllers/BookController.php`

4. Tambahkan method di `HomeController`:

   ```php
   public function index()
   {
       return view('home', [
           'title' => 'Halaman Home',
           'description' => 'Selamat datang di aplikasi Laravel'
       ]);
   }
   ```

5. Buat dokumentasi perbedaan keduanya dalam bentuk tabel:

| Aspek           | Basic Controller | Resource Controller |
| --------------- | ---------------- | ------------------- |
| Method otomatis | Tidak ada        | 7 method RESTful    |
| Penggunaan      | Custom method    | CRUD standar        |

6. Screenshot kedua file controller

**Deliverable:**

- Screenshot kedua file controller
- Dokumentasi perbedaan
- Penjelasan kapan menggunakan masing-masing jenis controller

---

## Soal 3: Membuat Model dengan Berbagai Opsi

**Tujuan:** Memahami opsi-opsi saat membuat model

**Instruksi:**

1. Buat model `Category` dengan migration:

   ```bash
   php artisan make:model Category -m
   ```

2. Buat model `Author` dengan migration dan controller:

   ```bash
   php artisan make:model Author -mc
   ```

3. Buat model `Book` dengan migration, controller resource, dan factory:

   ```bash
   php artisan make:model Book -mcrf
   ```

4. Verifikasi file yang terbuat:

   - Cek folder `app/Models/`
   - Cek folder `app/Http/Controllers/`
   - Cek folder `database/migrations/`
   - Cek folder `database/factories/`

5. Buat tabel yang menunjukkan file apa saja yang dibuat untuk setiap model:

| Model    | Files yang terbuat                             |
| -------- | ---------------------------------------------- |
| Category | Category.php, xxxx_create_categories_table.php |
| Author   | ...                                            |
| Book     | ...                                            |

6. Screenshot struktur folder dan isi file

**Deliverable:**

- Screenshot struktur folder
- Tabel file yang terbuat
- Screenshot minimal 3 file yang di-generate

---

## Soal 4: Konfigurasi Model dengan Properties

**Tujuan:** Memahami properties penting dalam Model

**Instruksi:**

1. Edit model `Book` (`app/Models/Book.php`) dan tambahkan properties:

   ```php
   <?php

   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;

   class Book extends Model
   {
       use HasFactory;

       // Nama tabel (opsional jika mengikuti konvensi)
       protected $table = 'books';

       // Primary key
       protected $primaryKey = 'id';

       // Kolom yang bisa di-mass assignment
       protected $fillable = [
           'title',
           'author_id',
           'category_id',
           'isbn',
           'price',
           'stock',
           'description',
           'published_date'
       ];

       // Kolom yang disembunyikan saat serialize (JSON)
       protected $hidden = [
           'created_at',
           'updated_at'
       ];

       // Cast tipe data
       protected $casts = [
           'price' => 'decimal:2',
           'published_date' => 'date',
           'stock' => 'integer'
       ];

       // Timestamps otomatis
       public $timestamps = true;
   }
   ```

2. Edit model `Category` dan `Author` dengan properties yang sesuai

3. Buat dokumentasi penjelasan setiap property:

   - Apa fungsi `$fillable`?
   - Apa fungsi `$hidden`?
   - Apa fungsi `$casts`?
   - Apa fungsi `$timestamps`?

4. Screenshot semua model yang sudah dikonfigurasi

**Deliverable:**

- Screenshot 3 model dengan konfigurasi lengkap
- Dokumentasi penjelasan setiap property
- Contoh use case kapan menggunakan masing-masing property

---

## Soal 5: Membuat Migration dengan Schema Builder

**Tujuan:** Membuat struktur tabel database menggunakan migration

**Instruksi:**

1. Edit migration `create_categories_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('categories', function (Blueprint $table) {
           $table->id();
           $table->string('name', 100);
           $table->string('slug', 100)->unique();
           $table->text('description')->nullable();
           $table->boolean('is_active')->default(true);
           $table->timestamps();
       });
   }

   public function down(): void
   {
       Schema::dropIfExists('categories');
   }
   ```

2. Edit migration `create_authors_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('authors', function (Blueprint $table) {
           $table->id();
           $table->string('name', 150);
           $table->string('email', 100)->unique();
           $table->text('bio')->nullable();
           $table->string('photo')->nullable();
           $table->date('birth_date')->nullable();
           $table->timestamps();
       });
   }
   ```

3. Edit migration `create_books_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('books', function (Blueprint $table) {
           $table->id();
           $table->string('title', 200);
           $table->foreignId('author_id')->constrained()->onDelete('cascade');
           $table->foreignId('category_id')->constrained()->onDelete('cascade');
           $table->string('isbn', 20)->unique();
           $table->decimal('price', 10, 2);
           $table->integer('stock')->default(0);
           $table->text('description')->nullable();
           $table->date('published_date')->nullable();
           $table->string('cover_image')->nullable();
           $table->timestamps();
       });
   }
   ```

4. Buat dokumentasi tipe data yang digunakan:

| Tipe Data | Contoh Penggunaan           | Keterangan                      |
| --------- | --------------------------- | ------------------------------- |
| id()      | $table->id()                | Primary key auto increment      |
| string()  | $table->string('name', 100) | VARCHAR dengan panjang tertentu |
| text()    | $table->text('description') | TEXT untuk data panjang         |
| ...       | ...                         | ...                             |

5. Screenshot semua migration file

**Deliverable:**

- Screenshot 3 file migration lengkap
- Dokumentasi tipe data
- Penjelasan foreign key constraint

---

## Soal 6: Menjalankan dan Mengelola Migration

**Tujuan:** Memahami perintah-perintah migration

**Instruksi:**

1. Cek status migration:

   ```bash
   php artisan migrate:status
   ```

   Screenshot hasilnya (sebelum migrate)

2. Jalankan migration:

   ```bash
   php artisan migrate
   ```

   Screenshot proses migration

3. Cek database menggunakan phpMyAdmin atau MySQL client:

   - Verifikasi tabel `categories`, `authors`, `books` sudah terbuat
   - Screenshot struktur tabel
   - Verifikasi foreign key sudah terbuat

4. Cek status migration lagi:

   ```bash
   php artisan migrate:status
   ```

   Screenshot hasilnya (setelah migrate)

5. Lakukan rollback migration terakhir:

   ```bash
   php artisan migrate:rollback
   ```

   Screenshot proses rollback

6. Jalankan migration lagi:

   ```bash
   php artisan migrate
   ```

7. Buat dokumentasi perintah migration:

| Perintah         | Fungsi                  | Kapan digunakan                |
| ---------------- | ----------------------- | ------------------------------ |
| migrate          | Jalankan migration      | Deploy atau update schema      |
| migrate:rollback | Rollback batch terakhir | Membatalkan perubahan terakhir |
| ...              | ...                     | ...                            |

**Deliverable:**

- Screenshot semua proses migration
- Screenshot struktur tabel di database
- Dokumentasi perintah migration
- Penjelasan konsep "batch" dalam migration

---

## Soal 7: Membuat Migration untuk Modifikasi Tabel

**Tujuan:** Menambah atau mengubah kolom pada tabel yang sudah ada

**Instruksi:**

1. Buat migration untuk menambah kolom di tabel books:

   ```bash
   php artisan make:migration add_rating_to_books_table
   ```

2. Edit migration file:

   ```php
   public function up(): void
   {
       Schema::table('books', function (Blueprint $table) {
           $table->decimal('rating', 3, 2)->default(0.00)->after('price');
           $table->integer('review_count')->default(0)->after('rating');
           $table->boolean('is_featured')->default(false)->after('stock');
       });
   }

   public function down(): void
   {
       Schema::table('books', function (Blueprint $table) {
           $table->dropColumn(['rating', 'review_count', 'is_featured']);
       });
   }
   ```

3. Buat migration untuk menambah index:

   ```bash
   php artisan make:migration add_indexes_to_books_table
   ```

4. Edit migration:

   ```php
   public function up(): void
   {
       Schema::table('books', function (Blueprint $table) {
           $table->index('isbn');
           $table->index('title');
           $table->index(['category_id', 'author_id']);
       });
   }

   public function down(): void
   {
       Schema::table('books', function (Blueprint $table) {
           $table->dropIndex(['isbn']);
           $table->dropIndex(['title']);
           $table->dropIndex(['category_id', 'author_id']);
       });
   }
   ```

5. Jalankan migration
6. Verifikasi perubahan di database

**Deliverable:**

- Screenshot migration files
- Screenshot hasil di database
- Penjelasan perbedaan Schema::create vs Schema::table
- Penjelasan fungsi index dalam database

---

## Soal 8: Generate Semua Komponen Sekaligus

**Tujuan:** Menggunakan shortcut untuk efisiensi

**Instruksi:**

1. Buat modul `Publisher` lengkap dengan satu perintah:

   ```bash
   php artisan make:model Publisher -mcr
   ```

2. Verifikasi file yang terbuat:

   - Model: `app/Models/Publisher.php`
   - Migration: `database/migrations/xxxx_create_publishers_table.php`
   - Controller: `app/Http/Controllers/PublisherController.php`

3. Lengkapi migration `create_publishers_table`:

   ```php
   public function up(): void
   {
       Schema::create('publishers', function (Blueprint $table) {
           $table->id();
           $table->string('name', 150);
           $table->string('address')->nullable();
           $table->string('phone', 20)->nullable();
           $table->string('email', 100)->unique();
           $table->string('website')->nullable();
           $table->timestamps();
       });
   }
   ```

4. Lengkapi model `Publisher.php`:

   ```php
   protected $fillable = [
       'name',
       'address',
       'phone',
       'email',
       'website'
   ];
   ```

5. Implementasi method index() dan show() di `PublisherController`:

   ```php
   public function index()
   {
       $publishers = [
           ['id' => 1, 'name' => 'Gramedia', 'email' => 'info@gramedia.com'],
           ['id' => 2, 'name' => 'Erlangga', 'email' => 'info@erlangga.com'],
           ['id' => 3, 'name' => 'Mizan', 'email' => 'info@mizan.com']
       ];

       return view('publishers.index', compact('publishers'));
   }

   public function show($id)
   {
       $publishers = [
           1 => ['id' => 1, 'name' => 'Gramedia', 'email' => 'info@gramedia.com', 'phone' => '021-12345'],
           2 => ['id' => 2, 'name' => 'Erlangga', 'email' => 'info@erlangga.com', 'phone' => '021-23456'],
           3 => ['id' => 3, 'name' => 'Mizan', 'email' => 'info@mizan.com', 'phone' => '021-34567']
       ];

       $publisher = $publishers[$id] ?? null;
       return view('publishers.show', compact('publisher'));
   }
   ```

6. Jalankan migration
7. Buat route resource
8. Buat view sederhana untuk testing

**Deliverable:**

- Screenshot semua file yang terbuat
- Screenshot hasil migration
- Screenshot implementasi controller
- Bukti route dan view berjalan

---

## Soal 9: Membuat Sistem CRUD Lengkap dengan Artisan

**Tujuan:** Praktik membuat modul CRUD menggunakan Artisan

**Instruksi:**

1. Buat modul `Member` lengkap:

   ```bash
   php artisan make:model Member -mcr
   ```

2. Edit migration `create_members_table.php`:

   ```php
   public function up(): void
   {
       Schema::create('members', function (Blueprint $table) {
           $table->id();
           $table->string('member_code', 20)->unique();
           $table->string('name', 150);
           $table->string('email', 100)->unique();
           $table->string('phone', 20);
           $table->text('address')->nullable();
           $table->date('join_date');
           $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
           $table->timestamps();
       });
   }
   ```

3. Edit model `Member.php`:

   ```php
   protected $fillable = [
       'member_code',
       'name',
       'email',
       'phone',
       'address',
       'join_date',
       'status'
   ];

   protected $casts = [
       'join_date' => 'date'
   ];
   ```

4. Implementasi semua method di `MemberController`:

   - index() - Tampilkan semua member
   - create() - Form tambah member
   - store() - Simpan member baru
   - show() - Detail member
   - edit() - Form edit member
   - update() - Update member
   - destroy() - Hapus member

5. Buat route resource di `routes/web.php`:

   ```php
   Route::resource('members', MemberController::class);
   ```

6. Jalankan `php artisan route:list` dan screenshot

7. Buat view minimal untuk index dan show

**Deliverable:**

- Screenshot migration file
- Screenshot model dengan konfigurasi
- Screenshot controller lengkap (7 method)
- Screenshot route:list yang menunjukkan route members
- Screenshot view yang berjalan

---

## Soal 10: Praktik Lengkap - Sistem Perpustakaan

**Tujuan:** Mengintegrasikan semua yang sudah dipelajari

**Instruksi:**

1. **Buat semua modul yang dibutuhkan:**

   ```bash
   # Jika belum dibuat di soal sebelumnya
   php artisan make:model Category -mcr
   php artisan make:model Author -mcr
   php artisan make:model Publisher -mcr
   php artisan make:model Book -mcr
   php artisan make:model Member -mcr
   ```

2. **Tambahkan relasi di Model Book:**

   ```php
   // Di Book.php
   public function category()
   {
       return $this->belongsTo(Category::class);
   }

   public function author()
   {
       return $this->belongsTo(Author::class);
   }

   public function publisher()
   {
       return $this->belongsTo(Publisher::class);
   }
   ```

3. **Update migration books untuk tambah publisher_id:**

   ```bash
   php artisan make:migration add_publisher_id_to_books_table
   ```

   Edit migration:

   ```php
   public function up(): void
   {
       Schema::table('books', function (Blueprint $table) {
           $table->foreignId('publisher_id')->nullable()->after('author_id')
                 ->constrained()->onDelete('set null');
       });
   }
   ```

4. **Jalankan semua migration:**

   ```bash
   php artisan migrate
   ```

5. **Buat route untuk semua resource:**

   ```php
   Route::resource('categories', CategoryController::class);
   Route::resource('authors', AuthorController::class);
   Route::resource('publishers', PublisherController::class);
   Route::resource('books', BookController::class);
   Route::resource('members', MemberController::class);
   ```

6. **Implementasi method index() untuk semua controller**

7. **Buat master layout dan view untuk minimal:**

   - Dashboard (home)
   - List categories
   - List authors
   - List publishers
   - List books
   - List members

8. **Jalankan aplikasi dan test semua halaman**

9. **Dokumentasi lengkap:**
   - ERD (Entity Relationship Diagram)
   - Struktur database
   - Route list
   - Screenshot semua halaman

**Deliverable:**

- ERD sistem perpustakaan
- Screenshot struktur database lengkap
- Screenshot semua file Model, Migration, Controller
- Screenshot `php artisan route:list`
- Screenshot aplikasi berjalan (minimal 5 halaman berbeda)
- Dokumentasi PDF lengkap

---

## Catatan Pengerjaan:

1. **Setup Project:**

   - Pastikan Laravel sudah terinstall
   - Database sudah dikonfigurasi di `.env`
   - Composer sudah terinstall

2. **Workflow:**

   - Kerjakan soal secara berurutan
   - Test setiap langkah sebelum lanjut
   - Commit ke Git setelah selesai setiap soal

3. **Dokumentasi:**

   - Screenshot setiap proses
   - Buat catatan kendala yang dihadapi
   - Buat laporan dalam format PDF

4. **Format Laporan:**
   - Cover (Judul, Nama, NIM, Kelas)
   - Daftar Isi
   - Pembahasan setiap soal
   - Screenshot dan penjelasan
   - Kesimpulan

## Kriteria Penilaian:

- **Fungsionalitas (40%):** Perintah Artisan berjalan dengan benar
- **Struktur Code (25%):** Mengikuti konvensi Laravel
- **Database Design (20%):** Schema dan relasi benar
- **Dokumentasi (15%):** Screenshot dan penjelasan lengkap

## Perintah Artisan yang Harus Dikuasai:

```bash
# Melihat bantuan
php artisan list
php artisan help [command]

# Membuat komponen
php artisan make:controller [Name]
php artisan make:model [Name]
php artisan make:migration [name]

# Shortcut
php artisan make:model [Name] -mcr
php artisan make:model [Name] -mcrfs

# Migration
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh
php artisan migrate:status

# Utility
php artisan route:list
php artisan serve
```

---

**Selamat Mengerjakan!** 🚀

**Estimasi Waktu:** 4-5 jam untuk semua soal
