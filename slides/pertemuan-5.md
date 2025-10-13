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

Mahasiswa mampu membuat route, menghubungkannya dengan controller, dan menampilkan data menggunakan Blade Template.

---

## Apa itu Routing?

- Routing adalah pemetaan URL ke logika aplikasi.
- Menghubungkan request user dengan kode yang akan dijalankan.

---

## File Routes di Laravel

- File utama: `routes/web.php` untuk routing web.
- `routes/api.php` untuk API routes.

---

## Sintaks Dasar Route

- Contoh route GET:

```php
Route::get('/welcome', function () {
    return 'Hello, World!';
});
```

---

## Route dengan Parameter

- Membuat route dengan parameter:

```php
Route::get('/user/{id}', function ($id) {
    return 'User ID: ' . $id;
});
```

---

## Route dengan Parameter Opsional

- Parameter bisa opsional dengan default:

```php
Route::get('/post/{id?}', function ($id = null) {
    return $id ? 'Post ID: ' . $id : 'All Posts';
});
```

---

## Named Routes

- Memberi nama route untuk kemudahan:

```php
Route::get('/profile', function () {
    // ...
})->name('profile');
```

- Memanggil URL:

```php
$url = route('profile');
```

---

## Route Groups

- Mengelompokkan route dengan prefix:

```php
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return 'Admin Dashboard';
    });
});
```

---

## Middleware pada Route Groups

- Menambahkan middleware pada grup route:

```php
Route::middleware(['auth'])->group(function () {
    // routes yang harus login
});
```

---

## Apa itu Controller?

- Controller mengatur alur logika sesuai request.
- Memisahkan kode dari route ke class khusus.

---

## Membuat Controller dengan Artisan

- Contoh perintah:

```
php artisan make:controller ProductController
```

---

## Struktur Controller

- Controller adalah class PHP di folder `app/Http/Controllers`.
- Contoh method:

```php
public function index() {
    return view('products.index');
}
```

---

## Menghubungkan Route ke Controller

- Sintaks route ke controller:

```php
use App\Http\Controllers\ProductController;
Route::get('/products', [ProductController::class, 'index']);
```

---

## Resource Controller

- Otomatis menyediakan 7 fungsi CRUD.
- Contoh membuat resource controller:

```
php artisan make:controller ProductController --resource
```

- Daftarkan dengan:

```php
Route::resource('products', ProductController::class);
```

---

## Single Action Controller

- Controller dengan satu method `__invoke`.
- Cocok untuk aksi tunggal.

```php
class ShowProfileController extends Controller {
    public function __invoke() {
        // logic
    }
}
```

---

## Apa itu View?

- View adalah tampilan hasil yang dilihat user.
- Menggunakan Blade Template di Laravel.

---

## Blade Template Engine

- Blade memudahkan syntax templating PHP.
- Keunggulan: mudah, bersih, cache efisien.

---

## Lokasi File Blade

- Folder `resources/views/`.
- File berekstensi `.blade.php`, contoh: `welcome.blade.php`.

---

## Menampilkan Data di Blade

- Sintaks escape data:

```blade
{{ $name }}
```

- Raw HTML (tidak di-escape):

```blade
{!! $htmlContent !!}
```

---

## Blade Directives

- Kondisi:

```blade
@if ($user)
    Hello, {{ $user }}
@endif
```

- Looping:

```blade
@foreach ($items as $item)
    {{ $item }}
@endforeach
```

---

## Layout dengan `@extends` dan `@section`

- Membuat layout utama:

```blade
<!-- layouts/app.blade.php -->
<html>
<head><title>@yield('title')</title></head>
<body>@yield('content')</body>
</html>
```

- Template child:

```blade
@extends('layouts.app')
@section('title', 'Home Page')
@section('content')
<p>Welcome!</p>
@endsection
```

---

## Include & Components

- `@include('partials.header')`
- Membuat komponen sederhana:

```blade
<x-alert type="error" message="Terjadi kesalahan!" />
```

---

## Passing Data dari Controller ke View

- Di Controller:

```php
public function index() {
    $products = Product::all();
    return view('products.index', ['products' => $products]);
}
```

- Di Blade:

```blade
@foreach ($products as $product)
    {{ $product->name }}
@endforeach
```
