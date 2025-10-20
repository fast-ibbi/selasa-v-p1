---
title: Routing, Controller, dan View (Blade Template)
version: 1.0.0
header: Routing, Controller, dan View (Blade Template)
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Routing, Controller, dan View (Blade Template)

---

## Tujuan Pembelajaran

- Mahasiswa mampu membuat route untuk menghubungkan URL dengan aplikasi
- Mahasiswa dapat menghubungkan route dengan controller
- Mahasiswa mampu menampilkan data menggunakan Blade Template
- Mahasiswa dapat membuat layout master dengan template inheritance

---

## Alur Request-Response di Laravel

**Bagaimana Laravel memproses request?**

1. User mengakses URL (misal: `/products`)
2. Route menangkap request dan mengarahkan ke Controller
3. Controller memproses logika dan mengambil data
4. Controller mengirim data ke View
5. View (Blade) merender HTML
6. Response dikirim kembali ke browser user

**Pola: Route → Controller → View → Response**

---

## Konsep Routing

**Apa itu Routing?**

- Routing adalah proses menghubungkan URL dengan fungsi/method tertentu
- Route mendefinisikan endpoint yang bisa diakses user
- Setiap route terikat dengan HTTP method (GET, POST, PUT, DELETE)

**Kegunaan:**

- Membuat URL yang clean dan SEO-friendly
- Mengatur akses ke berbagai fitur aplikasi
- Memisahkan logika berdasarkan URL pattern

---

## File Routing di Laravel

**Lokasi file routing:**

- `routes/web.php` - untuk aplikasi web (dengan session, CSRF protection)
- `routes/api.php` - untuk REST API (stateless, token-based)

**Struktur dasar `web.php`:**

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
```

---

## Routing Dasar

**Method HTTP yang umum digunakan:**

```php
// GET - mengambil data
Route::get('/home', function () {
    return 'Halaman Home';
});

// POST - mengirim data
Route::post('/submit', function () {
    return 'Data diterima';
});
```

---

```php


// PUT - update data
Route::put('/update/{id}', function ($id) {
    return 'Update data ' . $id;
});

// DELETE - hapus data
Route::delete('/delete/{id}', function ($id) {
    return 'Hapus data ' . $id;
});
```

---

## Route dengan Parameter

**Dynamic Routing - URL dengan parameter:**

```php
// Parameter wajib
Route::get('/user/{id}', function ($id) {
    return 'User ID: ' . $id;
});

// Parameter opsional
Route::get('/user/{name?}', function ($name = 'Guest') {
    return 'Hello, ' . $name;
});

```

---

```php

// Multiple parameters
Route::get('/post/{category}/{id}', function ($category, $id) {
    return "Category: $category, Post ID: $id";
});
```

**Akses:** `/user/5`, `/post/technology/123`

---

## Named Routes

**Memberi nama pada route:**

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/profile', function () {
    return view('profile');
})->name('user.profile');
```

---

**Kegunaan dalam view/controller:**

```php
// Generate URL
$url = route('dashboard');

// Redirect
return redirect()->route('user.profile');
```

**Keuntungan:** Jika URL berubah, tidak perlu mengubah semua referensi

---

## Route Groups dan Prefix

**Mengelompokkan route dengan karakteristik sama:**

```php
// Group dengan prefix
Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        return 'Admin Users';
    });
    Route::get('/posts', function () {
        return 'Admin Posts';
    });
});

// Akses: /admin/users, /admin/posts
```

---

```php
// Group dengan name prefix
Route::name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        //
    })->name('dashboard'); // route name: admin.dashboard
});
```

---

## Apa itu Controller?

**Controller dalam MVC:**

- Controller adalah class yang menangani logika aplikasi
- Memisahkan logika dari routing (separation of concerns)
- Mengatur alur data antara Model dan View
- Membuat kode lebih terstruktur dan mudah di-maintain

**Lokasi:** `app/Http/Controllers/`

**Prinsip:** Satu controller untuk satu resource/fitur

---

## Membuat Controller dengan Artisan

**Command untuk membuat controller:**

```bash
# Controller biasa
php artisan make:controller PageController

# Resource Controller (dengan method CRUD lengkap)
php artisan make:controller ProductController --resource

# Controller dengan model
php artisan make:controller PostController --model=Post
```

**Hasil:** File controller baru di `app/Http/Controllers/`

---

## Struktur Dasar Controller

**Contoh PageController.php:**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }
```

---

```php
    public function about()
    {
        $data = [
            'title' => 'Tentang Kami',
            'description' => 'Selamat datang di website kami'
        ];
        return view('about', $data);
    }

    public function contact()
    {
        return view('contact');
    }
}
```

---

## Menghubungkan Route ke Controller

**Dari closure ke controller method:**

**Sebelum (dengan closure):**

```php
Route::get('/home', function () {
    return view('home');
});
```

---

**Sesudah (dengan controller):**

```php
use App\Http\Controllers\PageController;

Route::get('/home', [PageController::class, 'home']);
Route::get('/about', [PageController::class, 'about']);
Route::get('/contact', [PageController::class, 'contact']);
```

**Lebih clean:** Route hanya mapping URL ke controller method

---

## Single Action vs Resource Controller

**Single Action Controller:**

```php
// Controller dengan satu method __invoke()
class ShowProfile extends Controller
{
    public function __invoke()
    {
        return view('profile');
    }
}

// Route
Route::get('/profile', ShowProfile::class);
```

---

**Resource Controller:**

```php
// Route dengan method CRUD lengkap
Route::resource('products', ProductController::class);

// Menghasilkan 7 route otomatis:
// GET /products - index()
// GET /products/create - create()
// POST /products - store()
// GET /products/{id} - show()
// GET /products/{id}/edit - edit()
// PUT /products/{id} - update()
// DELETE /products/{id} - destroy()
```

---

## Passing Data ke View

**3 cara mengirim data dari controller ke view:**

```php
// Cara 1: Array
public function index()
{
    $data = ['name' => 'John', 'age' => 25];
    return view('profile', $data);
}

```

---

```php
// Cara 2: with()
public function index()
{
    return view('profile')
        ->with('name', 'John')
        ->with('age', 25);
}

// Cara 3: compact()
public function index()
{
    $name = 'John';
    $age = 25;
    return view('profile', compact('name', 'age'));
}
```

---

## Studi Kasus Controller

**PageController untuk halaman statis:**

```php
class PageController extends Controller
{
    public function home()
    {
        $featured = ['Laravel', 'Vue.js', 'React'];
        return view('home', compact('featured'));
    }

    public function about()
    {
        $company = [
            'name' => 'PT Web Dev',
            'year' => 2020,
            'employees' => 50
        ];
        return view('about', compact('company'));
    }
```

---

```php
    public function products()
    {
        $products = [
            ['id' => 1, 'name' => 'Laptop'],
            ['id' => 2, 'name' => 'Mouse'],
            ['id' => 3, 'name' => 'Keyboard']
        ];
        return view('products', compact('products'));
    }
}
```

---

## Pengenalan Blade Template

**Apa itu Blade?**

- Template engine bawaan Laravel
- Sintaks yang simple dan powerful
- Support inheritance dan components
- Compile menjadi PHP murni (cepat)

---

**Keunggulan:**

- Sintaks lebih bersih dari PHP murni
- Template inheritance untuk reusability
- Automatic escaping untuk keamanan XSS
- Control structures yang mudah dibaca

---

## Direktori View

**Lokasi file view:**

- Semua view disimpan di `resources/views/`
- Ekstensi file: `.blade.php`
- Penamaan menggunakan dot notation

---

**Contoh struktur:**

```
resources/views/
├── home.blade.php
├── about.blade.php
├── layouts/
│   └── app.blade.php
└── products/
    ├── index.blade.php
    └── show.blade.php
```

**Pemanggilan:** `view('home')`, `view('products.index')`

---

## Sintaks Dasar Blade

**Echo data:**

```php
{{-- Escaped (aman dari XSS) --}}
{{ $name }}
{{ $user->email }}

{{-- Unescaped (hati-hati!) --}}
{!! $htmlContent !!}

{{-- PHP expression --}}
{{ strtoupper($name) }}
{{ $price * 1.1 }}
```

---

**PHP code block:**

```php
@php
    $discount = 0.1;
    $finalPrice = $price * (1 - $discount);
@endphp
```

**Comments:**

```php
{{-- Komentar Blade (tidak muncul di HTML) --}}
```

---

## Blade Directives - Kondisi

**If statement:**

```php
@if ($score >= 80)
    <p>Nilai A</p>
@elseif ($score >= 70)
    <p>Nilai B</p>
@else
    <p>Nilai C</p>
@endif
```

---

**Unless (kebalikan if):**

```php
@unless ($user->isAdmin())
    <p>Anda bukan admin</p>
@endunless
```

---

**Isset & Empty:**

```php
@isset($name)
    <p>Nama: {{ $name }}</p>
@endisset

@empty($users)
    <p>Tidak ada user</p>
@endempty
```

---

## Blade Directives - Perulangan

**Foreach:**

```php
@foreach ($products as $product)
    <li>{{ $product->name }} - Rp {{ $product->price }}</li>
@endforeach
```

---

**For loop:**

```php
@for ($i = 0; $i < 10; $i++)
    <p>Nomor {{ $i }}</p>
@endfor
```

---

**Loop variable (tersedia di dalam loop):**

```php
@foreach ($users as $user)
    <p>{{ $loop->iteration }}. {{ $user->name }}</p>
    @if ($loop->first)
        <p>User pertama</p>
    @endif
    @if ($loop->last)
        <p>User terakhir</p>
    @endif
@endforeach
```

---

## Template Inheritance

**Master Layout (`layouts/app.blade.php`):**

```php
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Website Saya')</title>
</head>
<body>
    <header>
        <nav>Menu navigasi</nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2025 Website Saya</p>
    </footer>
</body>
</html>
```

---

**Child view yang extends:**

```php
@extends('layouts.app')

@section('title', 'Halaman Home')

@section('content')
    <h1>Selamat Datang</h1>
    <p>Ini adalah halaman home</p>
@endsection
```

---

## Section dan Yield

**@yield - mendefinisikan placeholder:**

```php
<head>
    @yield('styles')
</head>
<body>
    @yield('content')
    @yield('scripts')
</body>
```

---

**@section - mengisi placeholder:**

```php
@section('styles')
    <link rel="stylesheet" href="custom.css">
@endsection

@section('content')
    <h1>Content di sini</h1>
@endsection

@section('scripts')
    <script src="app.js"></script>
@endsection
```

---

**@parent - menambahkan ke parent section:**

```php
@section('sidebar')
    @parent
    <p>Konten tambahan di sidebar</p>
@endsection
```

---

## Blade Components dan Slots

**Component (`components/alert.blade.php`):**

```php
<div class="alert alert-{{ $type }}">
    <strong>{{ $title }}</strong>
    {{ $slot }}
</div>
```

---

**Penggunaan component:**

```php
<x-alert type="success" title="Berhasil!">
    Data berhasil disimpan ke database.
</x-alert>
```

---

**Named slots:**

```php
{{-- Component definition --}}
<div class="card">
    <div class="card-header">{{ $header }}</div>
    <div class="card-body">{{ $slot }}</div>
</div>

{{-- Usage --}}
<x-card>
    <x-slot name="header">Judul Card</x-slot>
    Isi konten card di sini.
</x-card>
```

---

## Alur Lengkap Route-Controller-View

**Request → Route → Controller → View → Response**

**1. Route (`routes/web.php`):**

```php
Route::get('/products', [ProductController::class, 'index']);
```

---

**2. Controller (`ProductController.php`):**

```php
public function index()
{
    $products = [
        ['name' => 'Laptop', 'price' => 5000000],
        ['name' => 'Mouse', 'price' => 150000]
    ];
    return view('products.index', compact('products'));
}
```

---

**3. View (`products/index.blade.php`):**

```php
@extends('layouts.app')

@section('content')
    <h1>Daftar Produk</h1>
    @foreach ($products as $product)
        <p>{{ $product['name'] }} - Rp {{ $product['price'] }}</p>
    @endforeach
@endsection
```

---

## Demo Praktis

**Membuat halaman produk dengan data dummy**

**Controller (`ProductController.php`):**

```php
public function index()
{
    $products = [
        ['id' => 1, 'name' => 'Laptop Asus', 'price' => 7500000, 'stock' => 10],
        ['id' => 2, 'name' => 'Mouse Logitech', 'price' => 250000, 'stock' => 50],
        ['id' => 3, 'name' => 'Keyboard Mechanical', 'price' => 850000, 'stock' => 25]
    ];
    return view('products.index', compact('products'));
}
```

---

```php
public function show($id)
{
    $products = [
        1 => ['id' => 1, 'name' => 'Laptop Asus', 'price' => 7500000, 'description' => 'Laptop gaming'],
        2 => ['id' => 2, 'name' => 'Mouse Logitech', 'price' => 250000, 'description' => 'Mouse wireless'],
    ];
    $product = $products[$id] ?? null;
    return view('products.show', compact('product'));
}
```

---

## Best Practices

**Routing:**

- Gunakan named routes untuk fleksibilitas
- Kelompokkan route yang berkaitan dengan prefix/group
- Gunakan resource route untuk CRUD standar

---

**Controller:**

- Satu controller untuk satu resource
- Pisahkan logika kompleks ke Service class
- Gunakan type hinting untuk dependency injection
- Jangan taruh query database langsung di controller (gunakan Model)

---

**View:**

- Buat master layout untuk konsistensi
- Gunakan components untuk elemen yang berulang
- Escape output untuk keamanan (gunakan `{{ }}`)
- Pisahkan logic dari presentation

---

<!--
_class: lead
-->

# Quiz

---

## Soal 1

**Apa urutan yang benar dalam alur request-response di Laravel?**

A. View → Route → Controller → Response  
B. Route → Controller → View → Response  
C. Controller → Route → View → Response  
D. Route → View → Controller → Response

<!-- **Jawaban: B** -->

---

## Soal 2

**File mana yang digunakan untuk mendefinisikan routing aplikasi web dengan session dan CSRF protection?**

A. `routes/api.php`  
B. `routes/channels.php`  
C. `routes/web.php`  
D. `routes/console.php`

<!-- **Jawaban: C** -->

---

## Soal 3

**Perintah artisan mana yang benar untuk membuat controller dengan method CRUD lengkap?**

A. `php artisan make:controller ProductController`  
B. `php artisan make:controller ProductController --resource`  
C. `php artisan make:controller ProductController --crud`  
D. `php artisan create:controller ProductController --resource`

<!-- **Jawaban: B** -->

---

## Soal 4

**Bagaimana cara yang benar untuk mengirim data ke view menggunakan compact()?**

A. `return view('profile')->compact('name', 'age');`  
B. `return view('profile', compact($name, $age));`  
C. `return view('profile', compact('name', 'age'));`  
D. `return view('profile')->with(compact('name', 'age'));`

<!-- **Jawaban: C** -->

---

## Soal 5

**Ekstensi file yang benar untuk Blade template adalah?**

A. `.blade`  
B. `.php`  
C. `.blade.php`  
D. `.template.php`

<!-- **Jawaban: C** -->

---

## Soal 6

**Directive Blade mana yang digunakan untuk membuat child view yang mewarisi layout master?**

A. `@include('layouts.app')`  
B. `@extends('layouts.app')`  
C. `@inherit('layouts.app')`  
D. `@layout('layouts.app')`

<!-- **Jawaban: B** -->

---

## Soal 7

**Apa perbedaan antara `{{ $name }}` dan `{!! $name !!}` di Blade?**

A. Tidak ada perbedaan  
B. `{{ }}` escaped (aman XSS), `{!! !!}` unescaped  
C. `{{ }}` unescaped, `{!! !!}` escaped  
D. `{{ }}` untuk variable, `{!! !!}` untuk function

<!-- **Jawaban: B** -->

---

## Soal 8

**Variable `$loop` di dalam foreach Blade berisi informasi apa?**

A. Jumlah total iterasi saja  
B. Index iterasi saja  
C. Informasi iterasi (first, last, iteration, dll)  
D. Data yang sedang di-loop

<!-- **Jawaban: C** -->

---

## Soal 9

**Berapa jumlah route yang dihasilkan secara otomatis oleh `Route::resource()`?**

A. 5 route  
B. 6 route  
C. 7 route  
D. 8 route

<!-- **Jawaban: C** -->

---

## Soal 10

**Apa kegunaan dari `@yield('content')` dalam master layout?**

A. Menampilkan content secara langsung  
B. Mendefinisikan placeholder untuk diisi child view  
C. Mengecek apakah content ada  
D. Meng-compile content menjadi HTML

<!-- **Jawaban: B** -->
