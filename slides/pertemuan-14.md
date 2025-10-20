---
title: Optimisasi Aplikasi & Best Practices (Security, Cache, Performance)

version: 1.0.0
header: Optimisasi Aplikasi & Best Practices (Security, Cache, Performance)

footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Optimisasi Aplikasi & Best Practices (Security, Cache, Performance)

---

## Tujuan Pembelajaran

**OBE Outcome:**

- Mahasiswa mengetahui praktik terbaik dalam menjaga keamanan aplikasi Laravel
- Mahasiswa mampu mengimplementasikan caching untuk meningkatkan performa
- Mahasiswa dapat mengoptimalkan query dan resource aplikasi
- Mahasiswa memahami checklist untuk production-ready application

---

## Pentingnya Optimisasi

**Mengapa Optimisasi Penting?**

- Aplikasi yang lambat = pengalaman pengguna buruk
- Keamanan lemah = risiko data breach dan serangan
- Resource tidak efisien = biaya server tinggi
- Best practices = kode maintainable dan scalable

**Dampak Nyata:**

- Loading time > 3 detik = 40% pengguna meninggalkan website
- Serangan keamanan dapat merusak reputasi bisnis

---

## Roadmap Topik Hari Ini

**Tiga Pilar Utama:**

1. Security Best Practices (Keamanan)
2. Caching Strategies (Performa Cache)
3. Performance Optimization (Optimasi Query & Resource)

**Bonus:** Production Readiness Checklist

---

## Prinsip Keamanan Dasar

**CIA Triad dalam Web Security:**

- **Confidentiality**: Data hanya diakses oleh yang berhak
- **Integrity**: Data tidak diubah tanpa otorisasi
- **Availability**: Sistem selalu tersedia untuk pengguna sah

**OWASP Top 10 (Ancaman Umum):**

- SQL Injection
- Cross-Site Scripting (XSS)
- Cross-Site Request Forgery (CSRF)
- Broken Authentication
- Sensitive Data Exposure

---

## CSRF Protection

**Cross-Site Request Forgery:**
Serangan yang memaksa pengguna melakukan aksi tanpa sepengetahuan mereka.

**Proteksi di Laravel:**

```php
// Blade Template - Form dengan CSRF token
<form method="POST" action="/profile">
    @csrf
    <input type="text" name="name">
    <button type="submit">Update</button>
</form>
```

**Middleware:**
Laravel otomatis memverifikasi token CSRF di setiap POST request melalui `VerifyCsrfToken` middleware.

---

## XSS Prevention

**Cross-Site Scripting:**
Injeksi script berbahaya ke halaman web.

**Proteksi Otomatis Blade:**

```php
// Blade otomatis escape output
{{ $user->name }} // AMAN - di-escape

// Raw output (BERBAHAYA jika tidak dipercaya)
{!! $htmlContent !!} // Hanya untuk konten terpercaya
```

**Sanitasi Input:**

```php
$clean = strip_tags($request->input('content'));
$clean = htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

---

## SQL Injection Prevention

**SQL Injection:**
Manipulasi query database melalui input pengguna.

**BURUK (Raw Query Vulnerable):**

```php
DB::select("SELECT * FROM users WHERE email = '$email'");
```

**BAIK (Prepared Statement):**

```php
// Eloquent (otomatis aman)
User::where('email', $email)->first();

// Query Builder dengan binding
DB::select('SELECT * FROM users WHERE email = ?', [$email]);
```

**Eloquent selalu gunakan prepared statements!**

---

## Mass Assignment Protection

**Mass Assignment Attack:**
Pengguna mengirim field yang tidak seharusnya (misal: `is_admin`).

**Proteksi dengan Fillable:**

```php
// Model User
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    // Hanya field ini yang boleh di-assign massal
}
```

**Atau dengan Guarded:**

```php
protected $guarded = ['id', 'is_admin', 'created_at'];
// Field ini TIDAK boleh di-assign massal
```

---

## Authentication & Authorization

**Best Practices:**

**Gunakan Hash untuk Password:**

```php
// JANGAN simpan plain text password
$user->password = bcrypt($request->password);
// atau
$user->password = Hash::make($request->password);
```

**Gunakan Gates & Policies:**

```php
// Gate
Gate::define('update-post', function ($user, $post) {
    return $user->id === $post->user_id;
});

// Di Controller
if (Gate::allows('update-post', $post)) {
    // Update post
}
```

---

## Environment Variables Security

**Jangan Hardcode Sensitive Data:**

**BURUK:**

```php
$apiKey = 'sk_live_abc123xyz';
```

**BAIK:**

```php
$apiKey = env('STRIPE_API_KEY');
```

**File .env:**

```
APP_KEY=base64:...
DB_PASSWORD=secret123
STRIPE_API_KEY=sk_live_abc123xyz
```

**PENTING:**

- Jangan commit `.env` ke Git
- Gunakan `.env.example` sebagai template
- Rotate keys secara berkala

---

## Konsep Caching

**Apa itu Caching?**
Menyimpan hasil operasi mahal (query, computation) untuk digunakan kembali.

**Manfaat:**

- Response time lebih cepat
- Beban database berkurang
- Efisiensi resource server

**Analogi:**
Seperti menyimpan fotokopi dokumen penting daripada harus ke kantor setiap kali butuh.

---

## Jenis Cache di Laravel

**Application Cache:**

- Config Cache
- Route Cache
- View Cache
- Query/Data Cache

**Cache Drivers:**

- File (default)
- Database
- Redis (production recommended)
- Memcached

---

## File vs Database vs Redis

**Perbandingan Cache Drivers:**

| Driver    | Kecepatan | Use Case                    |
| --------- | --------- | --------------------------- |
| File      | Sedang    | Development, shared hosting |
| Database  | Lambat    | Ketika Redis tidak tersedia |
| Redis     | Cepat     | Production, high traffic    |
| Memcached | Cepat     | Alternative Redis           |

**Konfigurasi di .env:**

```
CACHE_DRIVER=redis
```

---

## Config Cache

**Optimasi Loading Konfigurasi:**

**Cache semua config:**

```bash
php artisan config:cache
```

**Clear cache config:**

```bash
php artisan config:clear
```

**PENTING:**
Setelah `config:cache`, fungsi `env()` tidak akan bekerja di luar file config. Selalu akses via `config()`.

**Contoh:**

```php
// BURUK setelah config cache
$debug = env('APP_DEBUG');

// BAIK
$debug = config('app.debug');
```

---

## Route Cache

**Optimasi Loading Routes:**

**Cache routes:**

```bash
php artisan route:cache
```

**Clear cache:**

```bash
php artisan route:clear
```

**CATATAN:**

- Route cache tidak support closure routes
- Hanya gunakan untuk production
- Harus re-cache setiap kali ada perubahan routes

---

## Cache Facade

**Menyimpan Data di Cache:**

```php
use Illuminate\Support\Facades\Cache;

// Simpan data (default forever)
Cache::put('key', 'value', $seconds);

// Simpan dengan expiration
Cache::put('users', $users, now()->addMinutes(10));

// Ambil data
$value = Cache::get('key');

// Ambil atau default
$value = Cache::get('key', 'default');

// Cek keberadaan
if (Cache::has('key')) {
    //
}

// Hapus
Cache::forget('key');
```

---

## Caching Query Database

**Problem: Query yang Sama Berulang Kali**

**Tanpa Cache:**

```php
public function index()
{
    $products = Product::all(); // Query database setiap request
    return view('products.index', compact('products'));
}
```

**Dengan Cache (remember):**

```php
public function index()
{
    $products = Cache::remember('products', 3600, function () {
        return Product::all();
    });
    return view('products.index', compact('products'));
}
```

**Cache di-refresh otomatis setelah 3600 detik (1 jam).**

---

## N+1 Query Problem

**Problem yang Sangat Umum:**

```php
// N+1 Problem (BURUK)
$posts = Post::all(); // 1 query

foreach ($posts as $post) {
    echo $post->user->name; // N queries (1 per post)
}
// Total: 1 + N queries
```

**Solution: Eager Loading**

```php
// Eager Loading (BAIK)
$posts = Post::with('user')->get(); // 2 queries total

foreach ($posts as $post) {
    echo $post->user->name; // Tidak ada query tambahan
}
```

**Dramatis lebih cepat untuk data besar!**

---

## Query Optimization Techniques

**Select Specific Columns:**

```php
// BURUK - ambil semua kolom
User::all();

// BAIK - ambil yang diperlukan saja
User::select('id', 'name', 'email')->get();
```

**Chunk untuk Data Besar:**

```php
// Proses 100 records per batch
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});
```

**Cursor untuk Memory Efficiency:**

```php
foreach (User::cursor() as $user) {
    // Hanya load 1 model ke memory tiap iterasi
}
```

---

## Database Indexing

**Index Mempercepat Query:**

**Migration dengan Index:**

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->foreignId('user_id')->constrained();
    $table->timestamps();

    // Tambah index
    $table->index('title');
    $table->index('created_at');
});
```

**Tambah Index ke Tabel Existing:**

```php
Schema::table('posts', function (Blueprint $table) {
    $table->index(['user_id', 'created_at']);
});
```

**Index kolom yang sering di-query di WHERE, ORDER BY, JOIN.**

---

## Asset Optimization

**Minifikasi CSS & JavaScript:**

**Laravel Mix (webpack.mix.js):**

```javascript
mix
  .js("resources/js/app.js", "public/js")
  .sass("resources/sass/app.scss", "public/css")
  .minify("public/css/app.css")
  .minify("public/js/app.js")
  .version(); // Cache busting
```

**Run production build:**

```bash
npm run production
```

**Hasil:**

- File size lebih kecil
- Loading lebih cepat
- Bandwidth hemat

---

## Image Optimization

**Compress Images:**

**Intervention Image Package:**

```bash
composer require intervention/image
```

```php
use Intervention\Image\Facades\Image;

public function store(Request $request)
{
    $image = $request->file('photo');

    $filename = time() . '.jpg';
    $path = public_path('uploads/' . $filename);

    // Resize dan compress
    Image::make($image)
        ->resize(800, 600)
        ->save($path, 75); // Quality 75%

    return 'Image optimized!';
}
```

---

## Lazy Loading Images

**Defer Loading Gambar:**

**HTML Native Lazy Loading:**

```html
<img src="image.jpg" loading="lazy" alt="Description" />
```

**Blade Template:**

```blade
@foreach($products as $product)
    <img src="{{ $product->image_url }}"
         loading="lazy"
         alt="{{ $product->name }}">
@endforeach
```

**Benefits:** Browser hanya load gambar yang visible, hemat bandwidth.

---

## Production Readiness Checklist

**Sebelum Deploy ke Production:**

**Security:**

- [ ] Debug mode OFF (`APP_DEBUG=false`)
- [ ] Generate strong `APP_KEY`
- [ ] HTTPS enabled
- [ ] CSRF protection aktif
- [ ] Validasi semua input

**Performance:**

- [ ] Config cache (`php artisan config:cache`)
- [ ] Route cache (`php artisan route:cache`)
- [ ] View cache (`php artisan view:cache`)
- [ ] Optimize autoloader (`composer install --optimize-autoloader`)
- [ ] Redis/Memcached untuk cache

**Database:**

- [ ] Migration sudah running
- [ ] Database indexing sudah optimal
- [ ] Backup strategy sudah ada

---

## Monitoring & Logging

**Log Errors untuk Debugging:**

```php
use Illuminate\Support\Facades\Log;

Log::info('User logged in', ['user_id' => $user->id]);
Log::warning('Low disk space');
Log::error('Payment failed', ['order_id' => $order->id]);
```

**Monitoring Tools:**

- Laravel Telescope (development)
- Laravel Horizon (queue monitoring)
- New Relic / Datadog (production monitoring)
- Sentry (error tracking)

---

## Kesimpulan

**Key Takeaways:**

**Security:**

- Gunakan CSRF, XSS, SQL Injection protection
- Hash password, jangan hardcode secrets
- Mass assignment protection

**Caching:**

- Cache config, routes, views untuk production
- Cache query database yang sering diakses
- Gunakan Redis untuk production

**Performance:**

- Eager loading untuk prevent N+1
- Database indexing
- Optimize assets dan images
- Use lazy loading

**Production:**

- Follow checklist sebelum deploy
- Monitor dan log aplikasi

---

## Soal 1

Apa fungsi dari directive `@csrf` pada form di Laravel?

A. Untuk validasi form input  
B. Untuk melindungi dari Cross-Site Request Forgery attack  
C. Untuk encrypt data form  
D. Untuk compress form data

<!-- **Jawaban: B** - `@csrf` directive menghasilkan token untuk melindungi aplikasi dari CSRF attack -->

---

## Soal 2

Method mana yang benar untuk hashing password di Laravel?

A. `md5($password)`  
B. `sha1($password)`  
C. `Hash::make($password)` atau `bcrypt($password)`  
D. `encrypt($password)`

<!-- **Jawaban: C** - Laravel menyediakan `Hash::make()` atau helper `bcrypt()` untuk hashing password dengan algoritma yang aman -->

---

## Soal 3

Apa yang dimaksud dengan Mass Assignment attack?

A. Mengirim banyak request sekaligus  
B. User mengirim field yang tidak seharusnya bisa diubah (misal: is_admin)  
C. Menghapus banyak data sekaligus  
D. Upload banyak file sekaligus

<!-- **Jawaban: B** - Mass Assignment adalah ketika user bisa mengubah field yang seharusnya protected (seperti is_admin) melalui form input -->

---

## Soal 4

Command apa yang digunakan untuk cache konfigurasi di Laravel?

A. `php artisan cache:config`  
B. `php artisan config:clear`  
C. `php artisan config:cache`  
D. `php artisan make:cache`

<!-- **Jawaban: C** - `php artisan config:cache` digunakan untuk cache semua file konfigurasi untuk meningkatkan performa -->

---

## Soal 5

Cache driver mana yang direkomendasikan untuk production dengan traffic tinggi?

A. File  
B. Database  
C. Redis atau Memcached  
D. Array

<!-- **Jawaban: C** - Redis atau Memcached adalah cache driver tercepat dan direkomendasikan untuk production dengan traffic tinggi -->

---

## Soal 6

Apa yang dimaksud dengan N+1 Query Problem?

A. Query yang error 1 kali  
B. Query database yang dijalankan N+1 kali padahal bisa lebih efisien  
C. Query dengan N parameter dan 1 kondisi  
D. Query yang timeout setelah N detik

<!-- **Jawaban: B** - N+1 Problem adalah ketika kita melakukan 1 query utama + N query tambahan untuk relasi, padahal bisa di-optimize dengan eager loading -->

---

## Soal 7

Method apa yang digunakan untuk mengatasi N+1 Query Problem di Eloquent?

A. `load()`  
B. `with()` (Eager Loading)  
C. `join()`  
D. `select()`

<!-- **Jawaban: B** - Method `with()` untuk eager loading akan load relasi sekaligus, mencegah N+1 query problem -->

---

## Soal 8

Di mana sebaiknya menyimpan API keys dan credentials yang sensitive?

A. Langsung di code controller  
B. Di file config  
C. Di file `.env`  
D. Di database

<!-- **Jawaban: C** - API keys dan credentials sebaiknya disimpan di file `.env` dan diakses via `env()` atau `config()` helper -->

---

## Soal 9

Apa fungsi dari database indexing?

A. Membuat database lebih aman  
B. Mempercepat query pencarian data  
C. Mengkompress data di database  
D. Backup otomatis database

<!-- **Jawaban: B** - Database indexing membuat query pencarian (WHERE, ORDER BY, JOIN) lebih cepat dengan membuat struktur data tambahan -->

---

## Soal 10

Apa yang harus dilakukan sebelum deploy aplikasi Laravel ke production?

A. Set `APP_DEBUG=true`  
B. Hapus semua cache  
C. Set `APP_DEBUG=false` dan jalankan config/route cache  
D. Install semua dependencies dengan flag `--dev`

<!-- **Jawaban: C** - Sebelum production: set `APP_DEBUG=false`, jalankan cache commands, dan pastikan keamanan sudah optimal -->
