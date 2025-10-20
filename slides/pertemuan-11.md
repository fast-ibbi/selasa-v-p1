---
title: Relationship Database (One To One, One To Many, Many To Many)
version: 1.0.0
header: Relationship Database (One To One, One To Many, Many To Many)
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Relationship Database (One To One, One To Many, Many To Many)

---

## Tujuan Pembelajaran

- Memahami konsep relasi database dalam aplikasi web
- Mampu mengimplementasikan One To One relationship
- Mampu mengimplementasikan One To Many relationship
- Mampu mengimplementasikan Many To Many relationship
- Menguasai Eloquent ORM untuk mengelola relasi antar tabel

---

## Mengapa Relasi Database Penting?

**Manfaat Relasi Database:**

- Menghindari duplikasi data (data redundancy)
- Memudahkan maintenance dan update data
- Menjaga integritas data dengan foreign key
- Membuat query lebih efisien dan terstruktur
- Mendukung aplikasi yang kompleks dan scalable

**Contoh tanpa relasi vs dengan relasi:**

- Tanpa relasi: data user tersimpan di setiap post
- Dengan relasi: post hanya menyimpan user_id

---

## Roadmap Topik Hari Ini

**Tiga Jenis Relasi:**

1. One To One (1:1) - User ↔ Profile
2. One To Many (1:N) - User ↔ Posts
3. Many To Many (M:N) - Posts ↔ Tags

**Bonus:**

- Eager Loading untuk optimasi query
- Menghindari N+1 Query Problem

---

---

## Pengenalan Relasi Antar Tabel

**Relasi Database:**

- Hubungan antara dua atau lebih tabel
- Dihubungkan melalui key (primary key & foreign key)
- Memungkinkan data tersebar di beberapa tabel yang saling terkait

**Visualisasi:**

```
Table: users          Table: posts
+----+-------+        +----+----------+---------+
| id | name  |        | id | title    | user_id |
+----+-------+        +----+----------+---------+
| 1  | John  | <----> | 1  | Post 1   | 1       |
| 2  | Jane  |        | 2  | Post 2   | 1       |
+----+-------+        | 3  | Post 3   | 2       |
                      +----+----------+---------+
```

---

## Primary Key dan Foreign Key

**Primary Key (PK):**

- Unique identifier untuk setiap record
- Biasanya kolom `id`
- Tidak boleh NULL dan harus unik

**Foreign Key (FK):**

- Kolom yang merujuk ke primary key tabel lain
- Menjaga referential integrity
- Contoh: `user_id` di tabel posts merujuk ke `id` di tabel users

```php
// Migration dengan foreign key
$table->foreignId('user_id')->constrained()->onDelete('cascade');
```

---

## Tiga Jenis Relasi Utama

**1. One To One (1:1)**

- Satu record di tabel A berhubungan dengan satu record di tabel B
- Contoh: User memiliki satu Profile

**2. One To Many (1:N)**

- Satu record di tabel A berhubungan dengan banyak record di tabel B
- Contoh: User memiliki banyak Post

**3. Many To Many (M:N)**

- Banyak record di tabel A berhubungan dengan banyak record di tabel B
- Contoh: Post memiliki banyak Tag, Tag dimiliki banyak Post
- Membutuhkan pivot table

---

## Normalisasi Database

**Apa itu Normalisasi?**

- Proses mengorganisir data untuk mengurangi redundansi
- Memecah data ke tabel-tabel terpisah yang saling berelasi

**Manfaat:**

- Data lebih konsisten
- Update lebih mudah (hanya di satu tempat)
- Storage lebih efisien
- Integritas data terjaga

---

## Studi Kasus - Sistem Blog

**Entitas dalam sistem blog:**

- **Users**: Penulis artikel
- **Posts**: Artikel yang ditulis
- **Comments**: Komentar pada artikel
- **Tags**: Label/kategori artikel
- **Profiles**: Detail profil user

**Relasi:**

- User → Profile (1:1)
- User → Posts (1:N)
- Post → Comments (1:N)
- Post ↔ Tags (M:N)

---

---

## Konsep One To One

**Karakteristik:**

- Relasi paling sederhana
- Satu record berhubungan dengan tepat satu record
- Biasanya untuk memisahkan data yang jarang diakses

**Kapan digunakan?**

- Memisahkan informasi sensitif (password, dll)
- Data yang jarang diakses (settings, preferences)
- Membagi tabel besar menjadi lebih kecil

---

## Contoh Kasus - User dan Profile

**Skenario:**

- Tabel `users`: data login (email, password)
- Tabel `profiles`: data detail (address, phone, bio)
- Satu user hanya punya satu profile

**Struktur:**

```
users                    profiles
+----+-------+          +----+---------+----------+
| id | name  |          | id | user_id | address  |
+----+-------+          +----+---------+----------+
| 1  | John  | <------> | 1  | 1       | Jakarta  |
| 2  | Jane  |          | 2  | 2       | Bandung  |
+----+-------+          +----+---------+----------+
```

---

## Migration One To One

**Langkah 1: Buat migration untuk profiles**

```php
php artisan make:migration create_profiles_table
```

**Langkah 2: Definisikan struktur tabel**

```php
public function up()
{
    Schema::create('profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('address')->nullable();
        $table->string('phone')->nullable();
        $table->text('bio')->nullable();
        $table->timestamps();
    });
}
```

**Penjelasan:**

- `foreignId('user_id')`: membuat kolom foreign key
- `constrained()`: otomatis merujuk ke tabel users
- `onDelete('cascade')`: hapus profile jika user dihapus

---

## Model One To One

**Model User (hasOne):**

```php
class User extends Model
{
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
```

**Model Profile (belongsTo):**

```php
class Profile extends Model
{
    protected $fillable = ['user_id', 'address', 'phone', 'bio'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**Catatan:**

- `hasOne`: dari sisi yang "memiliki"
- `belongsTo`: dari sisi yang "dimiliki"

---

## Praktik One To One

**Membuat data:**

```php
$user = User::find(1);
$user->profile()->create([
    'address' => 'Jakarta',
    'phone' => '08123456789',
    'bio' => 'Web Developer'
]);
```

**Mengakses data:**

```php
// Dari User ke Profile
$user = User::find(1);
echo $user->profile->address; // Jakarta

// Dari Profile ke User
$profile = Profile::find(1);
echo $profile->user->name; // John
```

**Update data:**

```php
$user->profile->update(['address' => 'Surabaya']);
```

---

---

## Konsep One To Many

**Karakteristik:**

- Relasi paling umum digunakan
- Satu record "parent" memiliki banyak record "child"
- Foreign key ada di tabel "child"

**Kapan digunakan?**

- User memiliki banyak Post
- Category memiliki banyak Product
- Customer memiliki banyak Order

---

## Contoh Kasus - User dan Posts

**Skenario:**

- Satu user bisa menulis banyak post
- Setiap post ditulis oleh satu user

**Struktur:**

```
users                    posts
+----+-------+          +----+----------+---------+
| id | name  |          | id | title    | user_id |
+----+-------+          +----+----------+---------+
| 1  | John  | <------- | 1  | Laravel  | 1       |
|    |       |    |---- | 2  | PHP OOP  | 1       |
|    |       |    |---- | 3  | React    | 1       |
+----+-------+          +----+----------+---------+
| 2  | Jane  | <------- | 4  | Vue.js   | 2       |
+----+-------+          +----+----------+---------+
```

---

## Migration One To Many

**Buat migration untuk posts:**

```php
php artisan make:migration create_posts_table
```

**Definisi struktur:**

```php
public function up()
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->text('content');
        $table->boolean('published')->default(false);
        $table->timestamps();
    });
}
```

---

## Model One To Many

**Model User (hasMany):**

```php
class User extends Model
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

**Model Post (belongsTo):**

```php
class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'content', 'published'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**Perbedaan dengan One To One:**

- `hasMany` bukan `hasOne`
- Mengembalikan collection, bukan single object

---

## Praktik One To Many - Create

**Membuat post untuk user:**

```php
// Cara 1: Melalui relationship
$user = User::find(1);
$user->posts()->create([
    'title' => 'Belajar Laravel',
    'content' => 'Laravel adalah framework PHP...',
    'published' => true
]);

// Cara 2: Langsung dengan user_id
Post::create([
    'user_id' => 1,
    'title' => 'Belajar Laravel',
    'content' => 'Laravel adalah framework PHP...',
    'published' => true
]);
```

---

## Praktik One To Many - Read

**Mengakses semua post dari user:**

```php
$user = User::find(1);

// Mengambil semua posts
foreach($user->posts as $post) {
    echo $post->title . "\n";
}

// Filter posts
$publishedPosts = $user->posts()->where('published', true)->get();

// Count posts
$totalPosts = $user->posts()->count();
```

**Dari Post ke User:**

```php
$post = Post::find(1);
echo $post->user->name; // John
```

---

---

## Konsep Many To Many

**Karakteristik:**

- Relasi paling kompleks
- Banyak record di kedua sisi saling berhubungan
- Membutuhkan pivot table (tabel penghubung)

**Kapan digunakan?**

- Post memiliki banyak Tag, Tag dimiliki banyak Post
- Student mengambil banyak Course, Course diambil banyak Student
- Product ada di banyak Category, Category punya banyak Product

---

## Contoh Kasus - Posts dan Tags

**Skenario:**

- Satu post bisa punya banyak tag
- Satu tag bisa ada di banyak post

**Struktur:**

```
posts                post_tag (pivot)         tags
+----+-------+      +----+---------+--------+  +----+--------+
| id | title |      | id | post_id | tag_id |  | id | name   |
+----+-------+      +----+---------+--------+  +----+--------+
| 1  | Post1 | <--> | 1  | 1       | 1      |  | 1  | Laravel|
| 2  | Post2 |      | 2  | 1       | 2      |  | 2  | PHP    |
+----+-------+      | 3  | 2       | 1      |  | 3  | Web    |
                    | 4  | 2       | 3      |  +----+--------+
                    +----+---------+--------+
```

---

## Pivot Table Explained

**Apa itu Pivot Table?**

- Tabel penghubung antara dua tabel utama
- Minimal berisi foreign key dari kedua tabel
- Nama konvensi: gabungan nama tabel (alphabetical order)
- Contoh: `post_tag` bukan `tag_post`

**Struktur minimal:**

```php
post_tag
- id (optional, tapi direkomendasikan)
- post_id (FK ke posts)
- tag_id (FK ke tags)
- timestamps (optional)
```

---

## Migration Many To Many

**Buat migration untuk tags:**

```php
php artisan make:migration create_tags_table

public function up()
{
    Schema::create('tags', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->string('slug')->unique();
        $table->timestamps();
    });
}
```

**Buat pivot table:**

```php
php artisan make:migration create_post_tag_table

public function up()
{
    Schema::create('post_tag', function (Blueprint $table) {
        $table->id();
        $table->foreignId('post_id')->constrained()->onDelete('cascade');
        $table->foreignId('tag_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
```

---

## Model Many To Many

**Model Post:**

```php
class Post extends Model
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
```

**Model Tag:**

```php
class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
```

**Catatan:**

- Kedua sisi menggunakan `belongsToMany`
- Laravel otomatis detect pivot table berdasarkan naming convention

---

## Praktik Many To Many

**Attach (menambah relasi):**

```php
$post = Post::find(1);

// Attach satu tag
$post->tags()->attach(1);

// Attach multiple tags
$post->tags()->attach([1, 2, 3]);

// Attach dengan data tambahan di pivot
$post->tags()->attach(1, ['created_at' => now()]);
```

**Detach (menghapus relasi):**

```php
// Detach satu tag
$post->tags()->detach(1);

// Detach semua tags
$post->tags()->detach();
```

**Sync (replace semua relasi):**

```php
// Ganti semua tags dengan yang baru
$post->tags()->sync([1, 2, 3]);
```

**Read:**

```php
$post = Post::find(1);
foreach($post->tags as $tag) {
    echo $tag->name . "\n";
}
```

---

---

## N+1 Query Problem & Eager Loading

**Problem N+1 Query:**

```php
// BAD: N+1 queries
$posts = Post::all(); // 1 query
foreach($posts as $post) {
    echo $post->user->name; // N queries (1 per post)
}
// Total: 1 + N queries
```

**Solution: Eager Loading**

```php
// GOOD: 2 queries only
$posts = Post::with('user')->get(); // 2 queries total
foreach($posts as $post) {
    echo $post->user->name; // No additional query
}
```

**Multiple relationships:**

```php
$posts = Post::with(['user', 'tags'])->get();
```

**Nested relationships:**

```php
$posts = Post::with('user.profile')->get();
```

---

## Query Kompleks dengan Relationships

**Filter melalui relasi:**

```php
// Posts yang dibuat user tertentu
$posts = Post::whereHas('user', function($query) {
    $query->where('name', 'John');
})->get();

// Posts dengan tag tertentu
$posts = Post::whereHas('tags', function($query) {
    $query->where('name', 'Laravel');
})->get();
```

**Count relasi:**

```php
// Users dengan jumlah posts
$users = User::withCount('posts')->get();
foreach($users as $user) {
    echo $user->name . ': ' . $user->posts_count . ' posts';
}
```

**Conditional loading:**

```php
$posts = Post::when($includeTags, function($query) {
    $query->with('tags');
})->get();
```

---

## Ringkasan

**One To One (1:1):**

- `hasOne` dan `belongsTo`
- Contoh: User → Profile
- Foreign key di tabel "child"

**One To Many (1:N):**

- `hasMany` dan `belongsTo`
- Contoh: User → Posts
- Relasi paling umum

**Many To Many (M:N):**

- `belongsToMany` di kedua sisi
- Contoh: Posts ↔ Tags
- Membutuhkan pivot table

**Best Practices:**

- Gunakan eager loading untuk menghindari N+1 query
- Foreign key constraint untuk data integrity
- Naming convention untuk pivot table

---

## Soal 1

**Apa yang dimaksud dengan relasi One To One dalam database?**

A. Satu record di tabel A berhubungan dengan banyak record di tabel B  
B. Satu record di tabel A berhubungan dengan satu record di tabel B  
C. Banyak record di tabel A berhubungan dengan banyak record di tabel B  
D. Record di tabel A tidak berhubungan dengan tabel B

<!-- **Jawaban: B** - One To One berarti satu record di tabel A berhubungan dengan tepat satu record di tabel B -->

---

## Soal 2

**Method Eloquent apa yang digunakan pada model "parent" untuk mendefinisikan relasi One To Many?**

A. `hasOne()`  
B. `hasMany()`  
C. `belongsTo()`  
D. `belongsToMany()`

<!-- **Jawaban: B** - Method `hasMany()` digunakan di sisi parent untuk relasi One To Many -->

---

## Soal 3

**Di mana foreign key disimpan pada relasi One To Many antara User dan Posts?**

A. Di tabel users  
B. Di tabel posts  
C. Di pivot table  
D. Di kedua tabel

<!-- **Jawaban: B** - Foreign key (`user_id`) disimpan di tabel posts (child) -->

---

## Soal 4

**Apa nama konvensi untuk pivot table pada relasi Many To Many antara Posts dan Tags?**

A. `tags_posts`  
B. `posts_tags`  
C. `post_tag`  
D. `tag_post`

<!-- **Jawaban: C** - Konvensi Laravel: nama tabel singular, alphabetical order: `post_tag` -->

---

## Soal 5

**Method apa yang digunakan untuk menambahkan relasi Many To Many tanpa menghapus yang sudah ada?**

A. `sync()`  
B. `attach()`  
C. `detach()`  
D. `create()`

<!-- **Jawaban: B** - Method `attach()` menambahkan relasi baru tanpa menghapus yang existing -->

---

## Soal 6

**Apa yang dimaksud dengan N+1 Query Problem?**

A. Database error karena query terlalu banyak  
B. Query yang dijalankan N+1 kali karena tidak menggunakan eager loading  
C. Foreign key constraint violation  
D. Syntax error pada query

<!-- **Jawaban: B** - N+1 terjadi saat 1 query untuk data utama + N query untuk relasi (inefficient) -->

---

## Soal 7

**Method apa yang digunakan untuk mengatasi N+1 Query Problem?**

A. `get()`  
B. `all()`  
C. `with()`  
D. `find()`

<!-- **Jawaban: C** - Method `with()` untuk eager loading, mengurangi jumlah query -->

---

## Soal 8

**Kode berikut menggunakan relasi apa?**

```php
class User extends Model {
    public function profile() {
        return $this->hasOne(Profile::class);
    }
}
```

A. One To Many  
B. Many To Many  
C. One To One  
D. Has Many Through

<!-- **Jawaban: C** - Method `hasOne()` menandakan relasi One To One -->

---

## Soal 9

**Apa fungsi dari `onDelete('cascade')` pada foreign key constraint?**

A. Mencegah penghapusan data  
B. Menghapus data terkait secara otomatis ketika parent dihapus  
C. Membuat soft delete  
D. Mengupdate data terkait

<!-- **Jawaban: B** - Cascade delete akan otomatis menghapus child records saat parent dihapus -->

---

## Soal 10

**Method apa yang digunakan untuk mengganti semua relasi Many To Many dengan yang baru?**

A. `attach()`  
B. `detach()`  
C. `sync()`  
D. `toggle()`

<!-- **Jawaban: C** - Method `sync()` akan replace semua relasi existing dengan array yang baru -->
