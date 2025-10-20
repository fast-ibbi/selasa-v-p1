# Praktikum Pertemuan 5 - Routing, Controller, dan View (Blade Template)

## Soal 1: Membuat Route Sederhana

**Tujuan:** Memahami cara membuat route dasar di Laravel

**Instruksi:**

1. Buka file `routes/web.php`
2. Tambahkan route berikut:
   - Route GET `/welcome` yang menampilkan teks "Selamat Datang di Laravel"
   - Route GET `/about` yang menampilkan teks "Halaman About"
   - Route GET `/contact` yang menampilkan teks "Halaman Kontak"
3. Jalankan development server
4. Test setiap route di browser
5. Screenshot hasil dari ketiga route

**Expected Output:**

- `/welcome` → menampilkan "Selamat Datang di Laravel"
- `/about` → menampilkan "Halaman About"
- `/contact` → menampilkan "Halaman Kontak"

**Perintah:**

```bash
php artisan serve
```

---

## Soal 2: Route dengan Parameter

**Tujuan:** Membuat route dengan dynamic parameter

**Instruksi:**

1. Buka file `routes/web.php`
2. Tambahkan route berikut:

   ```php
   Route::get('/user/{id}', function ($id) {
       return "User ID: " . $id;
   });

   Route::get('/product/{category}/{id}', function ($category, $id) {
       return "Category: $category, Product ID: $id";
   });

   Route::get('/greeting/{name?}', function ($name = 'Guest') {
       return "Hello, " . $name;
   });
   ```

3. Test route dengan berbagai parameter:
   - `/user/123`
   - `/product/electronics/456`
   - `/greeting/John`
   - `/greeting` (tanpa parameter)
4. Screenshot hasil dari setiap test

**Yang harus dipahami:**

- Parameter wajib vs opsional
- Multiple parameters
- Default value

---

## Soal 3: Membuat Controller Pertama

**Tujuan:** Membuat dan menggunakan controller

**Instruksi:**

1. Generate controller baru dengan nama `PageController`:
   ```bash
   php artisan make:controller PageController
   ```
2. Buka file `app/Http/Controllers/PageController.php`
3. Tambahkan method berikut:

   ```php
   public function home()
   {
       return "Ini adalah halaman Home dari Controller";
   }

   public function about()
   {
       return "Ini adalah halaman About dari Controller";
   }

   public function contact()
   {
       return "Ini adalah halaman Contact dari Controller";
   }
   ```

4. Update `routes/web.php` untuk menggunakan controller:

   ```php
   use App\Http\Controllers\PageController;

   Route::get('/home', [PageController::class, 'home']);
   Route::get('/about', [PageController::class, 'about']);
   Route::get('/contact', [PageController::class, 'contact']);
   ```

5. Test semua route di browser
6. Screenshot controller file dan hasil di browser

---

## Soal 4: Membuat View Blade Sederhana

**Tujuan:** Membuat dan menampilkan view Blade

**Instruksi:**

1. Buat file view baru `resources/views/home.blade.php`:
   ```html
   <!DOCTYPE html>
   <html>
     <head>
       <title>Home Page</title>
     </head>
     <body>
       <h1>Selamat Datang di Halaman Home</h1>
       <p>Ini adalah halaman home menggunakan Blade Template</p>
     </body>
   </html>
   ```
2. Update method `home()` di `PageController`:
   ```php
   public function home()
   {
       return view('home');
   }
   ```
3. Buat file view `resources/views/about.blade.php` dan `resources/views/contact.blade.php` dengan konten serupa
4. Update method `about()` dan `contact()` di controller untuk menggunakan view
5. Test semua halaman di browser
6. Screenshot semua file view dan hasil di browser

---

## Soal 5: Passing Data ke View

**Tujuan:** Mengirim data dari controller ke view

**Instruksi:**

1. Buat controller baru `StudentController`:
   ```bash
   php artisan make:controller StudentController
   ```
2. Tambahkan method di `StudentController`:
   ```php
   public function index()
   {
       $students = [
           ['nama' => 'Budi', 'nim' => '12345', 'jurusan' => 'Informatika'],
           ['nama' => 'Ani', 'nim' => '12346', 'jurusan' => 'Sistem Informasi'],
           ['nama' => 'Citra', 'nim' => '12347', 'jurusan' => 'Teknik Komputer']
       ];

       return view('students.index', compact('students'));
   }
   ```
3. Buat folder `resources/views/students/`
4. Buat file `resources/views/students/index.blade.php`:
   ```html
   <!DOCTYPE html>
   <html>
     <head>
       <title>Daftar Mahasiswa</title>
     </head>
     <body>
       <h1>Daftar Mahasiswa</h1>
       <table border="1">
         <thead>
           <tr>
             <th>Nama</th>
             <th>NIM</th>
             <th>Jurusan</th>
           </tr>
         </thead>
         <tbody>
           @foreach($students as $student)
           <tr>
             <td>{{ $student['nama'] }}</td>
             <td>{{ $student['nim'] }}</td>
             <td>{{ $student['jurusan'] }}</td>
           </tr>
           @endforeach
         </tbody>
       </table>
     </body>
   </html>
   ```
5. Tambahkan route di `routes/web.php`
6. Test halaman di browser
7. Screenshot code dan hasil

---

## Soal 6: Blade Directives - Conditional

**Tujuan:** Menggunakan directive kondisi di Blade

**Instruksi:**

1. Buat method baru di `StudentController`:
   ```php
   public function show($nim)
   {
       $students = [
           '12345' => ['nama' => 'Budi', 'nim' => '12345', 'nilai' => 85],
           '12346' => ['nama' => 'Ani', 'nim' => '12346', 'nilai' => 92],
           '12347' => ['nama' => 'Citra', 'nim' => '12347', 'nilai' => 78]
       ];

       $student = $students[$nim] ?? null;
       return view('students.show', compact('student'));
   }
   ```
2. Buat file `resources/views/students/show.blade.php`:
   ```html
   <!DOCTYPE html>
   <html>
     <head>
       <title>Detail Mahasiswa</title>
     </head>
     <body>
       <h1>Detail Mahasiswa</h1>

       @if($student)
       <p>Nama: {{ $student['nama'] }}</p>
       <p>NIM: {{ $student['nim'] }}</p>
       <p>Nilai: {{ $student['nilai'] }}</p>

       @if($student['nilai'] >= 90)
       <p>Grade: A (Excellent!)</p>
       @elseif($student['nilai'] >= 80)
       <p>Grade: B (Good!)</p>
       @elseif($student['nilai'] >= 70)
       <p>Grade: C (Average)</p>
       @else
       <p>Grade: D (Need Improvement)</p>
       @endif @else
       <p>Mahasiswa tidak ditemukan</p>
       @endif
     </body>
   </html>
   ```
3. Tambahkan route dengan parameter
4. Test dengan NIM yang ada dan tidak ada
5. Screenshot hasil berbagai kondisi

---

## Soal 7: Membuat Master Layout

**Tujuan:** Membuat layout master dengan template inheritance

**Instruksi:**

1. Buat folder `resources/views/layouts/`
2. Buat file `resources/views/layouts/app.blade.php`:
   ```html
   <!DOCTYPE html>
   <html lang="id">
     <head>
       <meta charset="UTF-8" />
       <meta name="viewport" content="width=device-width, initial-scale=1.0" />
       <title>@yield('title', 'My Laravel App')</title>
       <style>
         body {
           font-family: Arial, sans-serif;
           margin: 0;
           padding: 0;
         }
         header {
           background-color: #333;
           color: white;
           padding: 1rem;
         }
         nav {
           background-color: #555;
           padding: 0.5rem;
         }
         nav a {
           color: white;
           text-decoration: none;
           margin: 0 1rem;
         }
         main {
           padding: 2rem;
         }
         footer {
           background-color: #333;
           color: white;
           text-align: center;
           padding: 1rem;
           position: fixed;
           bottom: 0;
           width: 100%;
         }
       </style>
       @yield('styles')
     </head>
     <body>
       <header>
         <h1>My Laravel Application</h1>
       </header>

       <nav>
         <a href="/home">Home</a>
         <a href="/about">About</a>
         <a href="/contact">Contact</a>
         <a href="/students">Students</a>
       </nav>

       <main>@yield('content')</main>

       <footer>
         <p>&copy; 2025 My Laravel App. All rights reserved.</p>
       </footer>

       @yield('scripts')
     </body>
   </html>
   ```
3. Update semua view yang sudah dibuat untuk menggunakan layout ini
4. Contoh update `home.blade.php`:

   ```php
   @extends('layouts.app')

   @section('title', 'Home Page')

   @section('content')
       <h2>Selamat Datang di Halaman Home</h2>
       <p>Ini adalah halaman home menggunakan Blade Template dengan Layout Master</p>
   @endsection
   ```

5. Update semua view lainnya
6. Screenshot semua halaman dengan layout baru

---

## Soal 8: Resource Controller

**Tujuan:** Membuat dan menggunakan resource controller

**Instruksi:**

1. Generate resource controller:
   ```bash
   php artisan make:controller ProductController --resource
   ```
2. Buka `ProductController.php` dan implementasikan method `index()` dan `show()`:

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

   public function show($id)
   {
       $products = [
           1 => ['id' => 1, 'name' => 'Laptop Asus', 'price' => 7500000, 'stock' => 10, 'description' => 'Laptop gaming high performance'],
           2 => ['id' => 2, 'name' => 'Mouse Logitech', 'price' => 250000, 'stock' => 50, 'description' => 'Mouse wireless ergonomic'],
           3 => ['id' => 3, 'name' => 'Keyboard Mechanical', 'price' => 850000, 'stock' => 25, 'description' => 'Mechanical keyboard RGB']
       ];

       $product = $products[$id] ?? null;
       return view('products.show', compact('product'));
   }
   ```

3. Tambahkan resource route di `routes/web.php`:
   ```php
   Route::resource('products', ProductController::class);
   ```
4. Buat view `products/index.blade.php` dan `products/show.blade.php`
5. Jalankan `php artisan route:list` untuk melihat semua route yang ter-generate
6. Screenshot route list dan hasil view

---

## Soal 9: Named Routes dan Redirect

**Tujuan:** Menggunakan named routes dan redirect

**Instruksi:**

1. Update routes di `routes/web.php` dengan menambahkan name:
   ```php
   Route::get('/home', [PageController::class, 'home'])->name('home');
   Route::get('/about', [PageController::class, 'about'])->name('about');
   Route::get('/contact', [PageController::class, 'contact'])->name('contact');
   Route::get('/students', [StudentController::class, 'index'])->name('students.index');
   ```
2. Buat controller baru `RedirectController`:

   ```php
   <?php

   namespace App\Http\Controllers;

   use Illuminate\Http\Request;

   class RedirectController extends Controller
   {
       public function toHome()
       {
           return redirect()->route('home');
       }

       public function toStudents()
       {
           return redirect()->route('students.index');
       }
   }
   ```

3. Tambahkan route untuk redirect:
   ```php
   Route::get('/go-home', [RedirectController::class, 'toHome']);
   Route::get('/go-students', [RedirectController::class, 'toStudents']);
   ```
4. Update link di navigation (layout) menggunakan named routes:
   ```html
   <nav>
     <a href="{{ route('home') }}">Home</a>
     <a href="{{ route('about') }}">About</a>
     <a href="{{ route('contact') }}">Contact</a>
     <a href="{{ route('students.index') }}">Students</a>
   </nav>
   ```
5. Test redirect dan navigation
6. Screenshot code dan hasil

---

## Soal 10: Loop Variables dan Components

**Tujuan:** Menggunakan loop variables dan membuat component sederhana

**Instruksi:**

1. Buat component alert dengan artisan:
   ```bash
   php artisan make:component Alert
   ```
2. Edit `app/View/Components/Alert.php`:

   ```php
   <?php

   namespace App\View\Components;

   use Illuminate\View\Component;

   class Alert extends Component
   {
       public $type;
       public $message;

       public function __construct($type = 'info', $message = '')
       {
           $this->type = $type;
           $this->message = $message;
       }

       public function render()
       {
           return view('components.alert');
       }
   }
   ```

3. Edit `resources/views/components/alert.blade.php`:
   ```html
   <div
     style="padding: 1rem; margin: 1rem 0; border-radius: 5px; 
       @if($type == 'success') background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;
       @elseif($type == 'danger') background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;
       @elseif($type == 'warning') background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba;
       @else background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;
       @endif"
   >
     <strong>{{ ucfirst($type) }}:</strong> {{ $message }} {{ $slot }}
   </div>
   ```
4. Update `students/index.blade.php` dengan loop variables dan component:

   ```html
   @extends('layouts.app') @section('title', 'Daftar Mahasiswa')
   @section('content')
   <h2>Daftar Mahasiswa</h2>

   <x-alert type="info" message="Menampilkan semua data mahasiswa" />

   <table border="1" style="width: 100%; border-collapse: collapse;">
     <thead>
       <tr>
         <th>No</th>
         <th>Nama</th>
         <th>NIM</th>
         <th>Jurusan</th>
         <th>Status</th>
       </tr>
     </thead>
     <tbody>
       @forelse($students as $student)
       <tr>
         <td>{{ $loop->iteration }}</td>
         <td>{{ $student['nama'] }}</td>
         <td>{{ $student['nim'] }}</td>
         <td>{{ $student['jurusan'] }}</td>
         <td>
           @if($loop->first)
           <span style="color: green;">★ Pertama</span>
           @elseif($loop->last)
           <span style="color: red;">★ Terakhir</span>
           @else - @endif
         </td>
       </tr>
       @empty
       <tr>
         <td colspan="5">Tidak ada data mahasiswa</td>
       </tr>
       @endforelse
     </tbody>
   </table>

   <p>Total mahasiswa: {{ count($students) }}</p>
   @endsection
   ```

5. Test halaman dengan berbagai kondisi
6. Screenshot component dan hasil dengan loop variables

---

## Catatan Pengerjaan:

1. **Dokumentasi:** Setiap soal harus didokumentasikan dengan screenshot
2. **File Lengkap:** Kumpulkan semua file yang dibuat (controllers, views, routes)
3. **Format Laporan:** Buat laporan dalam format Word/PDF yang berisi:
   - Nomor soal
   - Code yang dibuat (controller, route, view)
   - Screenshot hasil di browser
   - Penjelasan singkat
   - Kendala yang dihadapi (jika ada)

## Kriteria Penilaian:

- **Fungsionalitas (40%):** Aplikasi berjalan sesuai instruksi
- **Code Quality (25%):** Code rapi, mengikuti konvensi Laravel
- **Tampilan (15%):** Tampilan menarik dan profesional
- **Dokumentasi (20%):** Screenshot dan penjelasan lengkap

## Tips Pengerjaan:

1. Kerjakan secara berurutan dari soal 1-10
2. Pastikan setiap soal berjalan sebelum lanjut ke soal berikutnya
3. Gunakan `php artisan route:list` untuk mengecek route yang sudah dibuat
4. Gunakan `php artisan serve` untuk menjalankan development server
5. Jangan lupa clear cache jika ada perubahan: `php artisan cache:clear`

---

**Selamat Mengerjakan!** 🚀

**Estimasi Waktu:** 3-4 jam untuk semua soal
