---
title: Form Handling, Validation, dan Request Handling
version: 1.0.0
header: Form Handling, Validation, dan Request Handling
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# Form Handling, Validation, dan Request Handling

---

## Tujuan Pembelajaran

- Mahasiswa mampu mengelola input data dengan form
- Mahasiswa dapat melakukan validasi data dengan Laravel validation
- Mahasiswa memahami cara mengatur request dan response
- Mahasiswa dapat menampilkan error validation dan flash message

---

## Pentingnya Validasi Data

**Mengapa Validasi Penting?**

- Melindungi aplikasi dari data yang tidak valid
- Mencegah serangan seperti SQL Injection dan XSS
- Memastikan integritas data di database
- Meningkatkan user experience dengan feedback yang jelas
- Mengurangi bug dan error di aplikasi

---

## Review Materi Sebelumnya

**Koneksi dengan Controller & View:**

- Controller menerima request dari user
- View menampilkan form kepada user
- Blade Template untuk rendering HTML
- Route menghubungkan URL dengan Controller

**Flow Dasar:**
User → Form (View) → Submit → Route → Controller → Process → Response

---

## Roadmap Alur Form

**Alur Lengkap Form Processing:**

1. User mengisi form di view (Blade)
2. Form di-submit ke route tertentu
3. Controller menerima request
4. Validasi data input
5. Jika valid: proses data (simpan ke DB)
6. Jika tidak valid: kembalikan ke form dengan error
7. Redirect dengan flash message

---

## Membuat Form di Blade

**Form HTML di Blade Template:**

```html
<form action="{{ route('user.store') }}" method="POST">
  @csrf

  <label>Nama:</label>
  <input type="text" name="name" value="{{ old('name') }}" />

  <label>Email:</label>
  <input type="email" name="email" value="{{ old('email') }}" />

  <button type="submit">Submit</button>
</form>
```

**Poin Penting:**

- `action`: URL tujuan form
- `method`: POST untuk insert data
- Route helper: `route('nama.route')`

---

## CSRF Protection

**Token CSRF di Laravel:**

```html
<form action="{{ route('user.store') }}" method="POST">
  @csrf
  <!-- form fields -->
</form>
```

**Penjelasan:**

- CSRF (Cross-Site Request Forgery) protection
- `@csrf` menghasilkan hidden input dengan token
- Laravel otomatis memvalidasi token ini
- Tanpa `@csrf`, form akan ditolak (419 error)

**Token yang dihasilkan:**

```html
<input type="hidden" name="_token" value="randomtoken123" />
```

---

## Method Spoofing

**Simulasi Method PUT/PATCH/DELETE:**

```html
<form action="{{ route('user.update', $user->id) }}" method="POST">
  @csrf @method('PUT')

  <input type="text" name="name" value="{{ $user->name }}" />
  <button type="submit">Update</button>
</form>
```

**Kenapa Perlu?**

- HTML form hanya support GET dan POST
- `@method('PUT')` menghasilkan hidden input `_method`
- Laravel mendeteksi dan memperlakukannya sebagai PUT request

---

## Menangkap Data di Controller

**Menggunakan Request Object:**

```php
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Ambil satu input
        $name = $request->input('name');

        // Ambil semua input
        $allData = $request->all();

        // Ambil beberapa input tertentu
        $data = $request->only(['name', 'email']);

        // Ambil semua kecuali tertentu
        $data = $request->except(['password']);
    }
}
```

---

## Method Request yang Sering Digunakan

**Berbagai Cara Mengakses Input:**

```php
// Cara 1: input()
$name = $request->input('name');
$name = $request->input('name', 'default'); // dengan default value

// Cara 2: Short syntax
$name = $request->name;

// Cara 3: all()
$data = $request->all();

// Cara 4: only()
$data = $request->only(['name', 'email']);

// Cek apakah input ada
if ($request->has('name')) {
    // code
}
```

---

## Studi Kasus Form Registrasi

**View (register.blade.php):**

```html
<form action="{{ route('register.store') }}" method="POST">
  @csrf
  <input type="text" name="name" placeholder="Nama Lengkap" />
  <input type="email" name="email" placeholder="Email" />
  <input type="password" name="password" placeholder="Password" />
  <button type="submit">Daftar</button>
</form>
```

**Controller:**

```php
public function store(Request $request)
{
    $name = $request->input('name');
    $email = $request->input('email');
    $password = $request->input('password');

    // Process data...
}
```

---

## Konsep Validation

**Mengapa Validasi Penting?**

**Skenario Tanpa Validasi:**

- User bisa submit form kosong
- Email format salah masuk database
- Password terlalu pendek
- Data duplikat tidak terdeteksi

**Dengan Validasi:**

- Data dijamin sesuai format
- User mendapat feedback jelas
- Database tetap konsisten
- Aplikasi lebih aman

---

## Validasi Dasar dengan validate()

**Syntax Dasar:**

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|min:3|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);

    // Jika sampai sini, berarti data valid
    User::create($validated);

    return redirect()->route('home');
}
```

**Cara Kerja:**

- Jika validasi gagal, Laravel otomatis redirect kembali
- Error messages otomatis tersimpan di session
- Old input juga disimpan untuk repopulate form

---

## Validation Rules Umum

**Rules yang Sering Digunakan:**

```php
$request->validate([
    'name' => 'required',              // wajib diisi
    'email' => 'required|email',       // wajib + format email
    'age' => 'required|numeric|min:17', // angka minimal 17
    'username' => 'required|unique:users', // unique di tabel users
    'password' => 'required|min:8|confirmed', // min 8 char + konfirmasi
    'avatar' => 'image|mimes:jpg,png|max:2048', // gambar max 2MB
    'bio' => 'nullable|max:500',       // opsional max 500 char
    'status' => 'required|in:active,inactive', // harus salah satu
]);
```

---

## Multiple Rules dengan Array

**Format Array untuk Rules Kompleks:**

```php
$request->validate([
    'email' => ['required', 'email', 'unique:users,email'],
    'password' => ['required', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
    'phone' => ['required', 'regex:/^08[0-9]{9,11}$/'],
]);
```

**Keuntungan Format Array:**

- Lebih mudah dibaca untuk rules panjang
- Memudahkan conditional validation
- Bisa gunakan Rule objects

---

## Menampilkan Error di Blade

**Menggunakan @error Directive:**

```html
<form action="{{ route('user.store') }}" method="POST">
  @csrf

  <input type="text" name="name" value="{{ old('name') }}" />
  @error('name')
  <span class="text-red-500">{{ $message }}</span>
  @enderror

  <input type="email" name="email" value="{{ old('email') }}" />
  @error('email')
  <span class="text-red-500">{{ $message }}</span>
  @enderror

  <button type="submit">Submit</button>
</form>
```

**Penjelasan:**

- `@error('field_name')` mengecek apakah ada error untuk field tersebut
- `$message` berisi pesan error otomatis dari Laravel

---

## Menampilkan Semua Error

**Tampilkan Error List:**

```html
@if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<form action="{{ route('user.store') }}" method="POST">
  @csrf
  <!-- form fields -->
</form>
```

**Kapan Digunakan:**

- Untuk menampilkan semua error sekaligus di atas form
- User langsung tahu semua field yang bermasalah

---

## Old Input untuk Repopulate

**Mempertahankan Input User:**

```html
<form action="{{ route('user.store') }}" method="POST">
    @csrf

    <input type="text" name="name" value="{{ old('name') }}">

    <input type="email" name="email" value="{{ old('email') }}">

    <textarea name="bio">{{ old('bio') }}</textarea>

    <select name="gender">
        <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Laki-laki</option>
        <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Perempuan</option>
    </select>

    <button type="submit">Submit</button>
</form>
```

**Fungsi old():**

- Mengambil nilai input sebelumnya dari session
- Memudahkan user tidak perlu isi ulang semua field

---

## Custom Validation Messages

**Pesan Error Kustom:**

```php
$request->validate([
    'name' => 'required|min:3',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8',
], [
    'name.required' => 'Nama wajib diisi',
    'name.min' => 'Nama minimal 3 karakter',
    'email.required' => 'Email wajib diisi',
    'email.email' => 'Format email tidak valid',
    'email.unique' => 'Email sudah terdaftar',
    'password.required' => 'Password wajib diisi',
    'password.min' => 'Password minimal 8 karakter',
]);
```

**Format:** `'field.rule' => 'Pesan kustom'`

---

## Form Request Class

**Membuat Form Request:**

```bash
php artisan make:request StoreUserRequest
```

**StoreUserRequest.php:**

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // atau logic authorization
    }

    public function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
        ];
    }
}
```

---

## Menggunakan Form Request di Controller

**Controller dengan Form Request:**

```php
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        // Data sudah tervalidasi otomatis
        $validated = $request->validated();

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat');
    }
}
```

**Keuntungan:**

- Pemisahan logic validasi dari controller
- Code lebih clean dan reusable
- Mudah di-maintain untuk validasi kompleks

---

## Studi Kasus Lengkap

**View (contact.blade.php):**

```html
@if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<form action="{{ route('contact.store') }}" method="POST">
  @csrf

  <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama" />
  @error('name')<span class="error">{{ $message }}</span>@enderror

  <input
    type="email"
    name="email"
    value="{{ old('email') }}"
    placeholder="Email"
  />
  @error('email')<span class="error">{{ $message }}</span>@enderror

  <textarea name="message" placeholder="Pesan">{{ old('message') }}</textarea>
  @error('message')<span class="error">{{ $message }}</span>@enderror

  <button type="submit">Kirim</button>
</form>
```

---

## Controller untuk Studi Kasus

**ContactController.php:**

```php
class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3|max:100',
            'email' => 'required|email',
            'message' => 'required|min:10|max:1000',
        ], [
            'name.required' => 'Nama wajib diisi',
            'message.min' => 'Pesan minimal 10 karakter',
        ]);

        // Simpan ke database atau kirim email
        Contact::create($validated);

        return redirect()->back()
            ->with('success', 'Pesan berhasil dikirim!');
    }
}
```

---

## Jenis-Jenis Request Method

**GET vs POST vs PUT/DELETE:**

```php
// Route
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Controller - Cek method
public function handle(Request $request)
{
    if ($request->isMethod('post')) {
        // Handle POST
    }

    if ($request->isMethod('put')) {
        // Handle PUT
    }
}
```

**Prinsip REST:**

- GET: mengambil data
- POST: membuat data baru
- PUT/PATCH: update data
- DELETE: hapus data

---

## File Upload Handling

**Form dengan File Upload:**

```html
<form
  action="{{ route('profile.update') }}"
  method="POST"
  enctype="multipart/form-data"
>
  @csrf @method('PUT')

  <input type="file" name="avatar" />
  @error('avatar')<span>{{ $message }}</span>@enderror

  <button type="submit">Upload</button>
</form>
```

**Controller:**

```php
public function update(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
        // $path berisi path file yang tersimpan
    }
}
```

**Penting:** Tambahkan `enctype="multipart/form-data"` pada form

---

## Validasi File Upload

**Rules untuk File:**

```php
$request->validate([
    'avatar' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:2048',
    'document' => 'required|file|mimes:pdf,doc,docx|max:5120',
    'video' => 'nullable|file|mimes:mp4,mov|max:51200',
]);
```

**Penjelasan Rules:**

- `file`: harus berupa file
- `image`: harus berupa gambar
- `mimes`: tipe file yang diizinkan
- `max`: ukuran maksimal dalam kilobytes (2048 = 2MB)
- `dimensions`: untuk validasi dimensi gambar

**Validasi Dimensi:**

```php
'avatar' => 'dimensions:min_width=100,min_height=100,max_width=1000'
```

---

## Redirect dengan Flash Message

**Mengirim Pesan Sukses:**

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required',
        'email' => 'required|email',
    ]);

    User::create($validated);

    // Redirect dengan flash message
    return redirect()->route('users.index')
        ->with('success', 'User berhasil ditambahkan!');
}
```

**Menampilkan di View:**

```html
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif @if (session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
```

---

## Best Practice Form Security

**Keamanan Form:**

1. **CSRF Protection:** Selalu gunakan `@csrf`
2. **XSS Prevention:** Laravel otomatis escape output dengan `{{ }}`
3. **SQL Injection:** Gunakan Eloquent atau Query Builder, jangan raw query
4. **Mass Assignment:** Gunakan `$fillable` atau `$guarded` di Model
5. **File Upload:** Validasi tipe dan ukuran file
6. **Rate Limiting:** Batasi jumlah submit form

**Contoh Fillable di Model:**

```php
class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];
    // Atau
    protected $guarded = ['id', 'is_admin'];
}
```

---

## Struktur Validasi untuk Proyek Besar

**Organisasi Code:**

```
app/
├── Http/
│   ├── Controllers/
│   │   └── UserController.php
│   ├── Requests/
│   │   ├── StoreUserRequest.php
│   │   ├── UpdateUserRequest.php
│   │   └── StorePostRequest.php
│   └── Middleware/
```

**Keuntungan Form Request Class:**

- Validasi terpisah dari controller
- Reusable untuk berbagai controller
- Mudah testing
- Authorization logic terpusat
- Custom error messages terorganisir

**Contoh Penggunaan:**

```php
public function store(StoreUserRequest $request) { }
public function update(UpdateUserRequest $request, $id) { }
```

---

## Rangkuman

**Form Handling:**

- `@csrf` untuk CSRF protection (wajib)
- `@method()` untuk method spoofing (PUT/DELETE)
- `old()` untuk repopulate form setelah error
- `enctype="multipart/form-data"` untuk file upload

**Validation:**

- `$request->validate()` untuk validasi inline
- Form Request class untuk validasi kompleks
- `@error` directive untuk tampilkan error per field
- Custom messages dengan array kedua di validate()

**Best Practice:**

- Validasi di server side (jangan hanya client side)
- Gunakan Form Request untuk logic kompleks
- Selalu gunakan `$fillable` di Model
- Flash message untuk feedback ke user

---

<!--
_class: lead
-->

# Quiz

---

## Soal 1

**Apa fungsi dari directive `@csrf` dalam form Laravel?**

A. Untuk validasi data form  
B. Untuk melindungi form dari Cross-Site Request Forgery  
C. Untuk enkripsi data form  
D. Untuk mengirim data ke database

<!-- **Jawaban: B** -->

---

## Soal 2

**Method mana yang digunakan untuk mengambil semua input dari form kecuali field tertentu?**

A. `$request->all()`  
B. `$request->only(['field'])`  
C. `$request->except(['field'])`  
D. `$request->without(['field'])`

<!-- **Jawaban: C** -->

---

## Soal 3

**Validation rule mana yang digunakan untuk memastikan email belum terdaftar di database?**

A. `email|exists:users`  
B. `email|unique:users`  
C. `email|available:users`  
D. `email|check:users`

<!-- **Jawaban: B** -->

---

## Soal 4

**Apa fungsi dari helper `old()` dalam form Blade?**

A. Menghapus data lama dari database  
B. Mengambil nilai input sebelumnya untuk repopulate form  
C. Memvalidasi data yang sudah ada  
D. Mengecek umur data di database

<!-- **Jawaban: B** -->

---

## Soal 5

**Bagaimana cara menampilkan pesan error untuk field 'email' di Blade?**

A. `@error('email') {{ $message }} @enderror`  
B. `@errors('email') {{ $message }} @enderrors`  
C. `{{ $errors->email }}`  
D. `@validate('email') {{ $error }} @endvalidate`

<!-- **Jawaban: A** -->

---

## Soal 6

**Perintah artisan mana yang digunakan untuk membuat Form Request class?**

A. `php artisan make:form StoreUserRequest`  
B. `php artisan make:request StoreUserRequest`  
C. `php artisan create:request StoreUserRequest`  
D. `php artisan make:validation StoreUserRequest`

<!-- **Jawaban: B** -->

---

## Soal 7

**Apa yang harus ditambahkan pada form agar bisa upload file?**

A. `method="FILE"`  
B. `type="multipart"`  
C. `enctype="multipart/form-data"`  
D. `accept="file/*"`

<!-- **Jawaban: C** -->

---

## Soal 8

**Validation rule untuk file gambar dengan ukuran maksimal 2MB adalah?**

A. `image|size:2048`  
B. `image|max:2048`  
C. `file|image|maxsize:2MB`  
D. `image|limit:2048`

<!-- **Jawaban: B** -->

---

## Soal 9

**Apa fungsi dari directive `@method('PUT')` dalam form?**

A. Mengubah method form menjadi GET  
B. Mensimulasikan HTTP PUT method karena HTML form hanya support GET dan POST  
C. Validasi method yang digunakan  
D. Enkripsi data dengan method PUT

<!-- **Jawaban: B** -->

---

## Soal 10

**Bagaimana cara redirect dengan flash message di Laravel?**

A. `return redirect()->route('home')->message('success', 'Berhasil')`  
B. `return redirect()->route('home')->flash('success', 'Berhasil')`  
C. `return redirect()->route('home')->with('success', 'Berhasil')`  
D. `return redirect()->route('home')->send('success', 'Berhasil')`

<!-- **Jawaban: C** -->
