# Praktek Pertemuan 14: Optimisasi Aplikasi & Best Practices

## Soal 1: Implementasi CSRF Protection

Buat form yang aman dengan CSRF protection:

- Buat form untuk update profile user (nama, email, bio)
- Implementasikan CSRF token dengan benar
- Test dengan dan tanpa CSRF token untuk melihat perbedaannya
- Buat error handling jika CSRF token tidak valid
- Tampilkan pesan error yang user-friendly

**Hint:** Gunakan `@csrf` directive di Blade template

**Testing:**

- Submit form dengan CSRF token valid (seharusnya berhasil)
- Hapus CSRF token dari HTML (menggunakan inspect element)
- Submit form lagi (seharusnya error 419 - Page Expired)

**Expected Protection:**

```blade
<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    <!-- form fields -->
</form>
```

---

## Soal 2: Prevent SQL Injection

Buat fitur search yang aman dari SQL Injection:

- Buat search form untuk mencari user berdasarkan name atau email
- Implementasikan dengan Query Builder atau Eloquent (prepared statements)
- Test dengan input yang mencoba SQL injection
- Bandingkan dengan raw query yang vulnerable (untuk pembelajaran)
- Tampilkan hasil search dengan pagination

**Hint:** Gunakan Eloquent atau Query Builder dengan parameter binding

**Test Cases:**

```
Normal input: "John"
SQL Injection attempt: "'; DROP TABLE users; --"
SQL Injection attempt: "1' OR '1'='1"
```

**Good Implementation:**

```php
// AMAN - menggunakan binding
User::where('name', 'LIKE', "%{$search}%")->get();

// BAHAYA - raw query tanpa binding (hanya untuk demo, jangan digunakan!)
DB::select("SELECT * FROM users WHERE name LIKE '%{$search}%'");
```

---

## Soal 3: Mass Assignment Protection

Implementasikan mass assignment protection:

- Buat model `Product` dengan field: name, price, stock, is_featured
- Set `$fillable` atau `$guarded` dengan benar
- Buat form untuk create product (tanpa field is_featured)
- Test mencoba inject `is_featured` via form input
- Pastikan `is_featured` tidak bisa di-set melalui mass assignment

**Hint:** Gunakan `$fillable` untuk whitelist atau `$guarded` untuk blacklist

**Model Implementation:**

```php
class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock'];
    // is_featured tidak bisa di-mass assign
}
```

**Testing:**
Coba kirim request dengan field tambahan:

```
POST /products
{
    "name": "Product 1",
    "price": 100000,
    "stock": 50,
    "is_featured": 1  // Ini seharusnya diabaikan
}
```

---

## Soal 4: Password Hashing dan Validation

Buat sistem authentication yang aman:

- Buat form register dengan password dan password confirmation
- Hash password menggunakan `bcrypt()` atau `Hash::make()`
- Implementasi password validation (min 8 karakter, huruf + angka)
- Jangan simpan password dalam plain text
- Test login dengan password verification

**Hint:** Gunakan `Hash::make()` untuk hash dan `Hash::check()` untuk verify

**Validation Rules:**

```php
$request->validate([
    'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
]);
```

**Storage:**

```php
User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password), // Hash!
]);
```

---

## Soal 5: Implementasi Caching

Implementasikan caching untuk data yang sering diakses:

- Cache daftar kategori produk (yang jarang berubah)
- Set cache expiration 1 jam
- Buat method untuk clear cache saat kategori berubah
- Tampilkan indicator apakah data dari cache atau database
- Ukur perbedaan waktu loading dengan dan tanpa cache

**Hint:** Gunakan `Cache::remember()` untuk automatic caching

**Implementation:**

```php
public function index()
{
    $categories = Cache::remember('categories', 3600, function () {
        return Category::all();
    });

    return view('categories.index', compact('categories'));
}

public function store(Request $request)
{
    $category = Category::create($request->all());

    // Clear cache setelah data berubah
    Cache::forget('categories');

    return redirect()->route('categories.index');
}
```

**Testing:**

- Load halaman pertama kali (query database)
- Load halaman kedua kali (dari cache, lebih cepat)
- Tambah kategori baru (cache di-clear)
- Load halaman lagi (query database lagi)

---

## Soal 6: Optimize N+1 Query

Identifikasi dan fix N+1 Query Problem:

- Buat halaman yang menampilkan posts dengan author name
- Implementasikan tanpa eager loading (akan ada N+1 problem)
- Install Laravel Debugbar untuk melihat jumlah query
- Fix dengan eager loading menggunakan `with()`
- Bandingkan jumlah query sebelum dan sesudah optimization

**Hint:** Gunakan `Post::with('user')->get()` untuk eager loading

**Before (N+1 Problem):**

```php
// Controller
$posts = Post::all(); // 1 query

// View
@foreach($posts as $post)
    {{ $post->user->name }} // N queries (1 per post)
@endforeach

// Total queries: 1 + N (jika ada 100 posts = 101 queries!)
```

**After (Optimized):**

```php
// Controller
$posts = Post::with('user')->get(); // 2 queries total

// View
@foreach($posts as $post)
    {{ $post->user->name }} // Tidak ada query tambahan
@endforeach

// Total queries: 2 (1 untuk posts, 1 untuk all users)
```

---

## Soal 7: Database Indexing

Implementasikan database indexing untuk query optimization:

- Buat tabel `orders` dengan banyak data (min 10,000 records)
- Query untuk mencari orders berdasarkan `user_id` dan `status`
- Ukur waktu query tanpa index (gunakan `DB::listen()` atau Debugbar)
- Tambahkan index pada kolom `user_id` dan `status`
- Ukur waktu query setelah indexing dan bandingkan

**Hint:** Gunakan `$table->index()` di migration

**Migration:**

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('status');
    $table->decimal('total', 10, 2);
    $table->timestamps();

    // Tambah indexes
    $table->index('user_id');
    $table->index('status');
    $table->index(['user_id', 'status']); // Composite index
});
```

**Seeder untuk test data:**

```php
Order::factory()->count(10000)->create();
```

**Query untuk test:**

```php
// Query yang akan di-optimize
Order::where('user_id', 1)
     ->where('status', 'completed')
     ->get();
```

---

## Soal 8: Config dan Route Caching

Implementasikan caching untuk production:

- Setup aplikasi dengan beberapa routes dan config
- Jalankan `php artisan config:cache` dan test aplikasi
- Jalankan `php artisan route:cache` dan test routing
- Ukur perbedaan loading time sebelum dan sesudah cache
- Pastikan aplikasi tetap berjalan normal setelah caching

**Commands:**

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Clear all cache (jika perlu)
php artisan optimize:clear
```

**Testing Checklist:**

- [ ] Config dapat diakses via `config()` helper
- [ ] Semua routes masih accessible
- [ ] Views ter-render dengan benar
- [ ] Loading time lebih cepat

**Important Notes:**

- Setelah `config:cache`, `env()` tidak bekerja di luar config files
- Route cache tidak support closure routes
- Harus clear cache setelah perubahan config/routes

---

## Soal 9: XSS Prevention

Buat sistem komentar yang aman dari XSS attack:

- Buat form komentar untuk blog post
- Implementasikan output escaping dengan benar
- Test dengan input yang mengandung script tag
- Tampilkan perbedaan `{{ }}` vs `{!! !!}`
- Implementasi sanitasi input jika perlu allow HTML tertentu

**Hint:** Blade `{{ }}` otomatis escape HTML, `{!! !!}` tidak

**Test Cases:**

```html
<!-- Input berbahaya untuk test -->
<script>
  alert("XSS Attack!");
</script>
<img src="x" onerror="alert('XSS')" />
<a href="javascript:alert('XSS')">Click me</a>
```

**Safe Implementation:**

```blade
<!-- AMAN - otomatis escape HTML -->
<p>{{ $comment->content }}</p>

<!-- Output: &lt;script&gt;alert('XSS')&lt;/script&gt; -->
```

**Unsafe (hanya untuk trusted content):**

```blade
<!-- BAHAYA - tidak escape, hanya untuk admin/trusted content -->
<div>{!! $article->content !!}</div>
```

**Sanitasi jika perlu allow beberapa HTML tags:**

```php
use Illuminate\Support\Str;

$clean = strip_tags($request->content, '<p><br><strong><em>');
// Hanya allow <p>, <br>, <strong>, <em>
```

---

## Soal 10: Production Optimization Project

Buat mini project lengkap dengan semua optimization:

- Buat aplikasi blog sederhana (posts, categories, comments)
- Implementasikan semua best practices yang telah dipelajari
- Optimize untuk production deployment
- Buat documentation tentang optimasi yang dilakukan
- Deploy ke shared hosting atau VPS (bonus)

**Security Checklist:**

- [ ] CSRF protection pada semua forms
- [ ] XSS prevention dengan proper escaping
- [ ] SQL Injection prevention dengan Eloquent/Query Builder
- [ ] Mass assignment protection
- [ ] Password hashing
- [ ] Input validation
- [ ] `.env` tidak ter-commit ke Git

**Performance Checklist:**

- [ ] Eager loading untuk prevent N+1
- [ ] Database indexing pada foreign keys
- [ ] Caching untuk data yang jarang berubah
- [ ] Config cache (`php artisan config:cache`)
- [ ] Route cache (`php artisan route:cache`)
- [ ] View cache (`php artisan view:cache`)
- [ ] Asset minification
- [ ] Image optimization
- [ ] Lazy loading untuk images

**Code Quality:**

- [ ] Menggunakan Service/Repository pattern
- [ ] Validation menggunakan Form Request
- [ ] Consistent naming conventions
- [ ] Comments pada code yang kompleks
- [ ] Error handling yang baik

**Production Setup:**

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS enabled
- [ ] Database backup strategy
- [ ] Monitoring dan logging

**Bonus Features:**

- [ ] Search functionality dengan cache
- [ ] Pagination untuk large datasets
- [ ] Rate limiting untuk prevent abuse
- [ ] Sitemap generation
- [ ] RSS feed

---

## Testing & Benchmarking

### Tools untuk Testing:

**1. Laravel Debugbar:**

```bash
composer require barryvdh/laravel-debugbar --dev
```

**2. Laravel Telescope:**

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**3. Query Logging:**

```php
// Di AppServiceProvider atau route
DB::listen(function($query) {
    Log::info($query->sql);
    Log::info($query->bindings);
    Log::info($query->time);
});
```

### Benchmark Performance:

**Apache Bench:**

```bash
ab -n 1000 -c 10 http://localhost/products
# 1000 requests, 10 concurrent
```

**Browser DevTools:**

- Network tab untuk loading time
- Lighthouse untuk performance score
- Coverage untuk unused CSS/JS

---

## Tips Pengerjaan

### 1. Security Testing

**SQL Injection Test:**

- Test dengan input: `' OR '1'='1`
- Test dengan input: `'; DROP TABLE users; --`
- Pastikan aplikasi tidak vulnerable

**XSS Test:**

- Test dengan input: `<script>alert('XSS')</script>`
- Test dengan input: `<img src=x onerror="alert('XSS')">`
- Pastikan script tidak ter-execute

**CSRF Test:**

- Hapus token CSRF dari form
- Submit form (seharusnya error 419)

### 2. Performance Testing

**Before Optimization:**

- Record jumlah queries
- Record loading time
- Take screenshot dari Debugbar

**After Optimization:**

- Record improvement
- Document changes made
- Compare side-by-side

### 3. Cache Testing

**Test Cache Hit/Miss:**

```php
if (Cache::has('key')) {
    echo "Cache HIT";
} else {
    echo "Cache MISS";
}
```

**Monitor Cache:**

```php
Cache::remember('stats', 60, function() {
    Log::info('Cache MISS - Query database');
    return DB::table('stats')->get();
});
```

---

## Common Mistakes to Avoid

### Security:

- ❌ Menggunakan `DB::raw()` dengan user input tanpa sanitasi
- ❌ Menyimpan password dalam plain text
- ❌ Tidak validasi input dari user
- ❌ Hardcode credentials di code

### Performance:

- ❌ Tidak menggunakan eager loading (N+1 problem)
- ❌ Load semua data tanpa pagination
- ❌ Tidak menggunakan cache untuk data static
- ❌ Tidak ada database indexing

### Production:

- ❌ Deploy dengan `APP_DEBUG=true`
- ❌ Commit `.env` ke Git
- ❌ Tidak cache config/routes di production
- ❌ Tidak monitor error logs

---

## Resources

### Documentation:

- **Laravel Security:** https://laravel.com/docs/security
- **Laravel Performance:** https://laravel.com/docs/optimization
- **Laravel Caching:** https://laravel.com/docs/cache
- **Database Indexing:** https://laravel.com/docs/migrations#indexes

### Tools:

- **Laravel Debugbar:** https://github.com/barryvdh/laravel-debugbar
- **Laravel Telescope:** https://laravel.com/docs/telescope
- **OWASP Top 10:** https://owasp.org/www-project-top-ten/

### Security Checklist:

- **Laravel Security Checklist:** https://github.com/Lissy93/personal-security-checklist
- **PHP Security Guide:** https://phptherightway.com/#security

---

## Submission Requirements

Untuk setiap soal, submit:

**1. Source Code:**

- Controller code
- Model code
- Migration files
- View templates
- Routes

**2. Screenshots:**

- Before optimization (jumlah query, loading time)
- After optimization (improvement)
- Debugbar screenshots
- Test results

**3. Documentation:**

- Explanation of optimization done
- Benchmark results
- Challenges faced
- Lessons learned

**4. Demo Video (Optional):**

- Screen recording showing:
  - Application functionality
  - Security tests
  - Performance comparison
  - Explanation of code

---

**Selamat Mengerjakan! 🚀**

**Remember:**

- Security first, optimization second
- Measure before optimize
- Document your changes
- Test everything!
