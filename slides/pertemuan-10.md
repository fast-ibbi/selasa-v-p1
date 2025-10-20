---
title: Middleware dan Authentication Dasar
version: 1.0.0
header: Middleware dan Authentication Dasar
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Middleware dan Authentication Dasar

---

## Tujuan Pembelajaran

- Memahami konsep middleware dan perannya dalam request lifecycle
- Mampu membuat custom middleware
- Memahami perbedaan authentication dan authorization
- Mengimplementasikan sistem login-logout sederhana
- Melindungi route dengan middleware auth

---

## Relevansi Middleware & Authentication

**Mengapa Penting?**

- Keamanan aplikasi: mencegah akses tidak sah
- Kontrol akses berbasis peran (admin, user, guest)
- Proteksi data sensitif
- Audit trail dan logging aktivitas user

**Contoh Real-World:**

- Dashboard admin hanya untuk admin
- Halaman profile hanya untuk user yang login
- API endpoint dengan token validation

---

## Roadmap Pembelajaran

**Alur Materi Hari Ini:**

1. Konsep middleware dan jenisnya
2. Membuat custom middleware
3. Authentication dasar (login/logout)
4. Protecting routes dengan middleware
5. Studi kasus implementasi lengkap

**Ke Depan:**

- Authorization & Gates (Pertemuan lanjutan)
- Role-based access control
- API authentication dengan Sanctum

---

## Apa itu Middleware?

**Definisi:**
Middleware adalah lapisan filter yang memproses HTTP request sebelum mencapai controller atau setelah response dibuat.

**Fungsi Utama:**

- Memfilter request masuk
- Memodifikasi request/response
- Validasi akses user
- Logging dan monitoring
- Rate limiting

**Posisi dalam Request Lifecycle:**
Request → Middleware → Route → Controller → Response

---

## Analogi Middleware

**Security Checkpoint di Bandara:**

- Penumpang (Request) masuk terminal
- Melewati security check (Middleware)
- Jika valid: lanjut ke gate (Controller)
- Jika tidak valid: ditolak/redirect

**Dalam Laravel:**

- User mengakses `/dashboard`
- Middleware cek: apakah user sudah login?
- Jika ya: tampilkan dashboard
- Jika tidak: redirect ke `/login`

---

## Middleware dalam Laravel Request Lifecycle

**Alur Request di Laravel:**

```
Browser Request
    ↓
Web Server (Apache/Nginx)
    ↓
public/index.php
    ↓
Kernel.php (Global Middleware)
    ↓
Route Middleware
    ↓
Controller
    ↓
Response (melalui middleware lagi)
    ↓
Browser
```

**3 Tahap Middleware:**

1. Before Middleware (sebelum controller)
2. Controller eksekusi
3. After Middleware (setelah controller)

---

## Jenis-Jenis Middleware

**1. Global Middleware**

- Dijalankan pada setiap request
- Didaftarkan di `app/Http/Kernel.php` → `$middleware`
- Contoh: `TrustProxies`, `CheckForMaintenanceMode`

**2. Route Middleware**

- Dijalankan pada route tertentu
- Didaftarkan di `$middlewareAliases`
- Contoh: `auth`, `guest`, `throttle`

**3. Middleware Group**

- Kumpulan middleware untuk grup route
- Contoh: `web`, `api`

---

## Struktur File Middleware

**Lokasi Middleware:**

```
app/
└── Http/
    └── Middleware/
        ├── Authenticate.php
        ├── RedirectIfAuthenticated.php
        ├── TrustProxies.php
        └── CustomMiddleware.php (buatan sendiri)
```

**Registrasi di Kernel.php:**

```php
protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
];
```

---

## Middleware Bawaan Laravel

**Middleware Penting:**

- `auth`: Memastikan user sudah login
- `guest`: Hanya untuk user yang belum login
- `throttle`: Rate limiting request
- `verified`: Email sudah diverifikasi
- `can`: Authorization dengan gates/policies

**Contoh Penggunaan di Route:**

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');
```

---

## Studi Kasus Penggunaan Middleware

**Kasus 1: Halaman Admin**

- Hanya user dengan role admin yang boleh akses
- Middleware: `CheckAdmin`

**Kasus 2: API Rate Limiting**

- Batasi request API maksimal 60/menit
- Middleware: `throttle:60,1`

**Kasus 3: Log Aktivitas User**

- Catat setiap akses ke halaman tertentu
- Middleware: `LogActivity`

**Kasus 4: Maintenance Mode**

- Redirect semua request kecuali IP tertentu
- Middleware: `CheckForMaintenanceMode`

---

## Membuat Custom Middleware

**Command Artisan:**

```bash
php artisan make:middleware CheckAge
```

**Output:**
File baru di `app/Http/Middleware/CheckAge.php`

**Struktur Dasar:**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAge
{
    public function handle(Request $request, Closure $next)
    {
        // Logic middleware di sini
        return $next($request);
    }
}
```

---

## Struktur Method handle()

**Parameter Method:**

- `$request`: HTTP request object
- `$next`: Closure untuk melanjutkan request
- `...$params`: Parameter tambahan (opsional)

**Return Options:**

```php
// 1. Lanjutkan request
return $next($request);

// 2. Redirect
return redirect('login');

// 3. Response langsung
return response('Unauthorized', 401);

// 4. Modifikasi request lalu lanjutkan
$request->merge(['verified' => true]);
return $next($request);
```

---

## Contoh Kode CheckAge Middleware

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAge
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah parameter age ada dan >= 18
        if ($request->age && $request->age < 18) {
            return redirect('home')
                ->with('error', 'Anda harus berusia minimal 18 tahun');
        }

        return $next($request);
    }
}
```

**Penjelasan:**

- Cek parameter `age` dari request
- Jika kurang dari 18, redirect ke home dengan pesan error
- Jika valid, lanjutkan ke controller

---

## Registrasi Middleware di Kernel.php

**Lokasi:** `app/Http/Kernel.php`

**Tambahkan di `$middlewareAliases`:**

```php
protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'checkage' => \App\Http\Middleware\CheckAge::class,
];
```

**Atau di Global Middleware:**

```php
protected $middleware = [
    \App\Http\Middleware\CheckAge::class,
];
```

---

## Mengaplikasikan Middleware ke Route

**Cara 1: Single Route**

```php
Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware('checkage');
```

**Cara 2: Multiple Middleware**

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'checkage']);
```

**Cara 3: Route Group**

```php
Route::middleware(['auth', 'checkage'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

**Cara 4: Dengan Parameter**

```php
Route::get('/admin', function () {
    return 'Admin Panel';
})->middleware('checkage:21');
```

---

## Authentication vs Authorization

**Authentication (Autentikasi):**

- Verifikasi identitas: "Siapa Anda?"
- Login dengan username/password
- Cek apakah user terdaftar
- Contoh: Login, register, logout

**Authorization (Otorisasi):**

- Verifikasi hak akses: "Apa yang boleh Anda lakukan?"
- Permission dan roles
- Cek apakah user boleh akses resource
- Contoh: Admin bisa hapus user, user biasa tidak

**Analogi:**

- Authentication = menunjukkan KTP di gerbang
- Authorization = cek apakah tiket Anda untuk VIP atau regular

---

## Alur Kerja Sistem Login-Logout

**Alur Login:**

```
1. User buka halaman login
2. Input username & password
3. Submit form ke server
4. Server validasi kredensial di database
5. Jika valid: buat session & redirect ke dashboard
6. Jika tidak: tampilkan error
```

**Alur Logout:**

```
1. User klik tombol logout
2. Server hapus session
3. Redirect ke halaman login/home
```

**Session Storage:**

- Data disimpan di server
- Client dapat cookie dengan session ID
- Laravel gunakan `Auth` facade untuk manage session

---

## Session Management di Laravel

**Konfigurasi Session:**
File: `config/session.php`

```php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => 120, // menit
'expire_on_close' => false,
```

**Driver Options:**

- `file`: simpan di `storage/framework/sessions`
- `cookie`: simpan di browser cookie
- `database`: simpan di database
- `redis`: simpan di Redis cache

**Auth Session:**
Laravel otomatis manage session saat login/logout dengan `Auth` facade

---

## Laravel Breeze vs Laravel UI vs Manual

**Laravel Breeze (Recommended):**

- Scaffolding authentication lengkap
- Minimalis, modern (Tailwind CSS)
- Command: `composer require laravel/breeze`

**Laravel UI:**

- Bootstrap-based scaffolding
- Legacy, masih didukung
- Command: `composer require laravel/ui`

**Manual Authentication:**

- Kontrol penuh
- Cocok untuk pembelajaran
- Build from scratch

**Pilihan Hari Ini: Manual** (agar paham konsep dasar)

---

## Hash Password dengan Bcrypt

**Mengapa Hash Password?**

- Keamanan: password tidak disimpan plain text
- One-way encryption: tidak bisa di-decrypt
- Laravel default gunakan bcrypt

**Hash saat Register:**

```php
use Illuminate\Support\Facades\Hash;

$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);
```

**Verify saat Login:**

```php
if (Hash::check($request->password, $user->password)) {
    // Password benar
}
```

**Auth::attempt() otomatis handle hash check**

---

## Perlindungan CSRF dalam Form

**Apa itu CSRF?**
Cross-Site Request Forgery: serangan yang memaksa user mengirim request tidak sah

**Laravel CSRF Protection:**

```blade
<form method="POST" action="/login">
    @csrf

    <input type="email" name="email">
    <input type="password" name="password">
    <button type="submit">Login</button>
</form>
```

**Token CSRF:**

- `@csrf` generate hidden input dengan token
- Laravel validasi token setiap POST request
- Jika tidak valid: error 419

**Disable CSRF (untuk API):**
Tambahkan route ke `$except` di `VerifyCsrfToken.php`

---

## Setup Database Migration untuk Users

**Default Migration:**
Laravel sudah include migration untuk tabel `users`

**File:** `database/migrations/xxxx_create_users_table.php`

```php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });
}
```

**Jalankan Migration:**

```bash
php artisan migrate
```

**Model User:** sudah ada di `app/Models/User.php`

---

## Membuat Controller untuk Authentication

**Generate Controller:**

```bash
php artisan make:controller AuthController
```

**Method yang Dibutuhkan:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Logic login
    }

    public function logout(Request $request)
    {
        // Logic logout
    }
}
```

---

## Implementasi Method Login

```php
public function login(Request $request)
{
    // Validasi input
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    // Attempt login
    if (Auth::attempt($credentials)) {
        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        return redirect()->intended('dashboard')
            ->with('success', 'Login berhasil!');
    }

    // Login gagal
    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->onlyInput('email');
}
```

**Penjelasan:**

- `Auth::attempt()`: validasi kredensial & buat session
- `session()->regenerate()`: prevent session fixation
- `intended()`: redirect ke halaman yang dituju sebelumnya

---

## Membuat View Login Form

**File:** `resources/views/auth/login.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>
</body>
</html>
```

---

## Implementasi Method Logout

```php
public function logout(Request $request)
{
    // Logout user (hapus session)
    Auth::logout();

    // Invalidate session
    $request->session()->invalidate();

    // Regenerate CSRF token
    $request->session()->regenerateToken();

    return redirect('/')->with('success', 'Logout berhasil!');
}
```

**Penjelasan:**

- `Auth::logout()`: hapus authentication session
- `invalidate()`: hapus semua session data
- `regenerateToken()`: buat CSRF token baru untuk keamanan

**Route untuk Logout:**

```php
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

---

## Protecting Routes dengan Middleware Auth

**Setup Routes:**

```php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Public routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
```

**Redirect Otomatis:**
Jika user belum login akses `/dashboard`, otomatis redirect ke `/login`

**Cek Status Login di Blade:**

```blade
@auth
    <p>Selamat datang, {{ Auth::user()->name }}!</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endauth

@guest
    <a href="{{ route('login') }}">Login</a>
@endguest
```

---

## Helper Functions untuk Authentication

**Auth Facade Methods:**

```php
// Cek apakah user sudah login
if (Auth::check()) {
    // User sudah login
}

// Ambil user yang sedang login
$user = Auth::user();

// Ambil ID user yang sedang login
$id = Auth::id();

// Login user secara manual
Auth::login($user);

// Logout
Auth::logout();
```

**Directive Blade:**

```blade
@auth
    <!-- Tampil jika user login -->
@endauth

@guest
    <!-- Tampil jika user belum login -->
@endguest

<!-- User data -->
{{ Auth::user()->name }}
{{ Auth::user()->email }}
```

---

## Remember Me Functionality

**Tambahkan Checkbox di Form:**

```blade
<form method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" name="email" required>
    <input type="password" name="password" required>

    <label>
        <input type="checkbox" name="remember"> Remember Me
    </label>

    <button type="submit">Login</button>
</form>
```

**Update Method Login:**

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $remember = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'Credentials do not match.',
    ]);
}
```

---

## Middleware Parameter

**Passing Parameter ke Middleware:**

**Route:**

```php
Route::get('/admin', function () {
    return 'Admin Panel';
})->middleware('role:admin');
```

**Middleware:**

```php
public function handle(Request $request, Closure $next, $role)
{
    if (Auth::user()->role !== $role) {
        abort(403, 'Unauthorized');
    }

    return $next($request);
}
```

**Multiple Parameters:**

```php
->middleware('role:admin,editor')
```

```php
public function handle(Request $request, Closure $next, ...$roles)
{
    if (!in_array(Auth::user()->role, $roles)) {
        abort(403);
    }
    return $next($request);
}
```

---

## Best Practices Security

**1. Selalu Hash Password:**

```php
Hash::make($password) // GOOD
$password // BAD - never store plain text
```

**2. CSRF Protection:**

```blade
@csrf // Wajib di semua form POST
```

**3. Session Regeneration:**

```php
$request->session()->regenerate(); // Prevent session fixation
```

**4. Validation:**

```php
$request->validate([...]) // Validasi semua input
```

**5. Rate Limiting:**

```php
Route::middleware('throttle:60,1')->group(function () {
    // Max 60 request per menit
});
```

**6. HTTPS di Production:**

```php
// Force HTTPS
if (!$request->secure() && app()->environment('production')) {
    return redirect()->secure($request->path());
}
```

---

## Rangkuman

**Middleware:**

- Filter request sebelum/sesudah controller
- Global, route, atau group middleware
- Custom middleware dengan `make:middleware`
- Register di `Kernel.php`

**Authentication:**

- Login dengan `Auth::attempt()`
- Logout dengan `Auth::logout()`
- Hash password dengan `Hash::make()`
- CSRF protection dengan `@csrf`
- Protect route dengan middleware `auth`

**Best Practice:**

- Selalu validasi input
- Hash password
- Regenerate session
- Use HTTPS di production

---

<!--
_class: lead
-->

# Quiz

---

## Soal 1

**Apa fungsi utama dari middleware di Laravel?**

A. Menyimpan data ke database  
B. Memfilter dan memproses HTTP request sebelum mencapai controller  
C. Membuat tampilan HTML  
D. Mengelola routing aplikasi

<!-- **Jawaban: B** -->

---

## Soal 2

**Di mana lokasi file untuk registrasi middleware di Laravel?**

A. `config/app.php`  
B. `routes/web.php`  
C. `app/Http/Kernel.php`  
D. `app/Providers/RouteServiceProvider.php`

<!-- **Jawaban: C** -->

---

## Soal 3

**Perintah artisan mana yang digunakan untuk membuat custom middleware?**

A. `php artisan create:middleware CheckAge`  
B. `php artisan make:middleware CheckAge`  
C. `php artisan new:middleware CheckAge`  
D. `php artisan generate:middleware CheckAge`

<!-- **Jawaban: B** -->

---

## Soal 4

**Apa perbedaan antara Authentication dan Authorization?**

A. Tidak ada perbedaan, keduanya sama  
B. Authentication = siapa Anda, Authorization = apa yang boleh Anda lakukan  
C. Authentication = apa yang boleh Anda lakukan, Authorization = siapa Anda  
D. Authentication untuk admin, Authorization untuk user

<!-- **Jawaban: B** -->

---

## Soal 5

**Method mana yang digunakan untuk hash password di Laravel?**

A. `Crypt::make($password)`  
B. `Encrypt::hash($password)`  
C. `Hash::make($password)`  
D. `Password::hash($password)`

<!-- **Jawaban: C** -->

---

## Soal 6

**Apa fungsi dari `Auth::attempt($credentials)` dalam proses login?**

A. Membuat user baru di database  
B. Memvalidasi kredensial dan membuat session jika valid  
C. Menghapus session user  
D. Menampilkan form login

<!-- **Jawaban: B** -->

---

## Soal 7

**Directive Blade mana yang digunakan untuk menampilkan konten hanya untuk user yang sudah login?**

A. `@login ... @endlogin`  
B. `@authenticated ... @endauthenticated`  
C. `@auth ... @endauth`  
D. `@user ... @enduser`

<!-- **Jawaban: C** -->

---

## Soal 8

**Apa yang harus dilakukan setelah user login untuk keamanan session?**

A. Menghapus semua session  
B. Regenerate session dengan `$request->session()->regenerate()`  
C. Membuat cookie baru  
D. Restart server

<!-- **Jawaban: B** -->

---

## Soal 9

**Bagaimana cara melindungi route agar hanya bisa diakses user yang sudah login?**

A. `Route::get('/dashboard')->protected()`  
B. `Route::get('/dashboard')->secure()`  
C. `Route::get('/dashboard')->middleware('auth')`  
D. `Route::get('/dashboard')->login()`

<!-- **Jawaban: C** -->

---

## Soal 10

**Method mana yang digunakan untuk logout user dan menghapus session?**

A. `Auth::logout()` saja sudah cukup  
B. `Auth::logout()` + `$request->session()->invalidate()` + `$request->session()->regenerateToken()`  
C. `Session::destroy()`  
D. `User::logout()`

<!-- **Jawaban: B** -->
