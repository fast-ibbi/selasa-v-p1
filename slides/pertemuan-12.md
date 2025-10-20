---
title: File Upload dan Storage Management
version: 1.0.0
header: File Upload dan Storage Management
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# File Upload dan Storage Management

---

## Tujuan Pembelajaran (OBE Outcome)

Setelah pertemuan ini, mahasiswa diharapkan mampu:

- Memahami konsep file upload dalam aplikasi web
- Mengimplementasikan file upload dengan form Laravel
- Mengelola file menggunakan Laravel Storage
- Melakukan validasi dan manipulasi file yang di-upload
- Menyimpan informasi file ke database

---

## Pentingnya File Management

**Mengapa File Upload Penting?**

- Upload foto profil pengguna
- Upload dokumen (CV, KTP, sertifikat)
- Upload produk di e-commerce
- Upload konten media (video, audio)
- Backup dan export data

**Tantangan:**

- Keamanan (virus, file berbahaya)
- Storage management (kapasitas, organisasi)
- Performance (ukuran file besar)

---

## Review Materi Sebelumnya

**Relasi Database (Pertemuan 11)**

- One To One, One To Many, Many To Many
- Eloquent relationships

**Koneksi ke Materi Hari Ini:**

- File yang di-upload perlu disimpan informasinya di database
- Relasi antara user dan file (satu user punya banyak foto)

---

## Roadmap Materi

1. Konsep dasar file upload
2. Form upload di Laravel
3. Laravel Storage System
4. Menyimpan file
5. Validasi dan manipulasi
6. Menampilkan dan menghapus file
7. Studi kasus praktis

---

## Bagaimana File Upload Bekerja

**Proses HTTP File Upload:**

- Browser mengirim file via HTTP POST
- Content-Type: `multipart/form-data`
- File dikirim dalam chunks (bagian-bagian kecil)
- Server menerima dan menyimpan file

**Flow:**

```
User pilih file → Form submit → Server terima → Validasi → Simpan → Response
```

---

## Tipe File yang Umum Di-upload

**Kategori File:**

**Gambar:** JPG, PNG, GIF, WebP

- Avatar/profil user
- Foto produk
- Banner/slider

**Dokumen:** PDF, DOC, DOCX, XLS

- CV/resume
- Invoice
- Laporan

**Media:** MP4, MP3, AVI

- Video pembelajaran
- Audio podcast

---

## Keamanan File Upload

**Risiko Keamanan:**

- Executable files (.exe, .sh, .php)
- File dengan virus/malware
- File terlalu besar (DoS attack)
- Path traversal (../../etc/passwd)

**Solusi:**

- Validasi tipe file (whitelist)
- Validasi ukuran maksimal
- Rename file otomatis
- Simpan di luar document root

---

## Best Practices File Upload

**Aturan Umum:**

- Batasi ukuran file (contoh: max 2MB untuk gambar)
- Whitelist format file yang diizinkan
- Generate nama file unik (hindari duplikasi)
- Compress/resize gambar otomatis
- Scan virus sebelum menyimpan
- Gunakan storage terpisah (bukan di server aplikasi)

---

## Potensi Masalah

**Masalah yang Sering Terjadi:**

- Storage penuh (disk space habis)
- Upload timeout (file terlalu besar)
- Permission error (folder tidak writable)
- Memory limit exceeded
- File corrupt setelah upload

**Tips Debugging:**

- Check `php.ini`: `upload_max_filesize`, `post_max_size`
- Check folder permissions (755 atau 775)
- Monitor disk space

---

## Form Upload dengan Blade

**HTML Form untuk Upload File:**

```html
<form
  action="{{ route('upload.store') }}"
  method="POST"
  enctype="multipart/form-data"
>
  @csrf
  <div>
    <label>Pilih Foto:</label>
    <input type="file" name="photo" required />
  </div>
  <button type="submit">Upload</button>
</form>
```

**Penting:** `enctype="multipart/form-data"` wajib ada!

---

## Input File: Single dan Multiple

**Single Upload:**

```html
<input type="file" name="avatar" />
```

**Multiple Upload:**

```html
<input type="file" name="photos[]" multiple />
```

**Accept Attribute (filter di browser):**

```html
<input type="file" name="image" accept="image/*" />
<input type="file" name="document" accept=".pdf,.doc,.docx" />
```

---

## Menangani Request di Controller

**Controller Method:**

```php
public function store(Request $request)
{
    // Cek apakah ada file
    if ($request->hasFile('photo')) {
        $file = $request->file('photo');

        // Proses file
        // ...
    }
}
```

---

## Mengakses File dari Request

**Method untuk Mengakses File:**

```php
// Ambil file
$file = $request->file('photo');

// Informasi file
$originalName = $file->getClientOriginalName();
$extension = $file->getClientOriginalExtension();
$size = $file->getSize(); // dalam bytes
$mimeType = $file->getMimeType();

// Cek validitas
$isValid = $file->isValid();
```

---

## Demo Form Upload Sederhana

**Route:**

```php
Route::get('/upload', [UploadController::class, 'create']);
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
```

**View (upload.blade.php):**

```html
<form
  action="{{ route('upload.store') }}"
  method="POST"
  enctype="multipart/form-data"
>
  @csrf
  <input type="file" name="photo" />
  <button type="submit">Upload</button>
</form>
```

---

## Pengenalan Laravel Storage

**Laravel Storage = Filesystem Abstraction**

Laravel menyediakan API yang sama untuk berbagai storage:

- Local disk
- Public disk
- Amazon S3
- FTP
- SFTP

**Keuntungan:**

- Mudah switch antar storage
- API konsisten
- Tidak perlu ubah kode aplikasi

---

## Konfigurasi Storage

**File: `config/filesystems.php`**

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],

    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

---

## Storage Disk

**Jenis Disk:**

**local:** `storage/app/`

- Private files
- Tidak bisa diakses langsung dari web

**public:** `storage/app/public/`

- Public files (gambar, dokumen)
- Bisa diakses via URL

**s3:** Amazon S3

- Cloud storage
- Scalable

---

## Symbolic Link untuk Public Storage

**Masalah:**

- Folder `storage/app/public` tidak bisa diakses dari web
- Web hanya bisa akses folder `public/`

**Solusi: Symbolic Link**

```bash
php artisan storage:link
```

**Hasil:**

- Membuat link dari `public/storage` → `storage/app/public`
- File di `storage/app/public` bisa diakses via `/storage/namafile.jpg`

---

## Perbedaan storage/app vs storage/app/public

**storage/app (local disk):**

- File private
- Tidak bisa diakses langsung
- Untuk: dokumen rahasia, file temporary

**storage/app/public (public disk):**

- File public
- Bisa diakses via URL
- Untuk: foto profil, gambar produk, avatar

---

## Studi Kasus: Kapan Menggunakan Disk Berbeda

**Local Disk:**

- File temporary processing
- Export CSV/Excel yang akan di-download
- Backup database

**Public Disk:**

- Avatar user
- Foto produk
- Banner website

**S3/Cloud:**

- Aplikasi dengan traffic tinggi
- Butuh CDN
- File sharing antar server

---

## Menyimpan File dengan store()

**Method `store()`:**

```php
public function store(Request $request)
{
    if ($request->hasFile('photo')) {
        // Simpan ke storage/app/public/photos dengan nama random
        $path = $request->file('photo')->store('photos', 'public');

        // $path = "photos/abc123def456.jpg"

        return response()->json(['path' => $path]);
    }
}
```

**Parameter:**

- Parameter 1: folder tujuan
- Parameter 2: disk (default: 'local')

---

## Menyimpan File dengan storeAs()

**Custom Filename:**

```php
public function store(Request $request)
{
    if ($request->hasFile('photo')) {
        $fileName = time() . '_' . $request->file('photo')->getClientOriginalName();

        $path = $request->file('photo')->storeAs(
            'photos',
            $fileName,
            'public'
        );

        // $path = "photos/1698765432_myphoto.jpg"
    }
}
```

---

## Mengatur Nama File dan Path Custom

**Generate Unique Filename:**

```php
use Illuminate\Support\Str;

$extension = $request->file('photo')->getClientOriginalExtension();
$fileName = Str::uuid() . '.' . $extension;

$path = $request->file('photo')->storeAs('avatars', $fileName, 'public');

// avatars/9b3f5e8a-7c2d-4f1e-9a6b-3d8e7f2c1a5b.jpg
```

---

## Menyimpan ke Disk Berbeda

**Local Disk (private):**

```php
$path = $request->file('document')->store('documents');
// Tersimpan di storage/app/documents
```

**Public Disk:**

```php
$path = $request->file('photo')->store('photos', 'public');
// Tersimpan di storage/app/public/photos
```

**S3 (jika sudah konfigurasi):**

```php
$path = $request->file('photo')->store('photos', 's3');
```

---

## Menyimpan Informasi File ke Database

**Migration:**

```php
Schema::create('user_photos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('file_name');
    $table->string('file_path');
    $table->integer('file_size');
    $table->string('mime_type');
    $table->timestamps();
});
```

**Controller:**

```php
public function store(Request $request)
{
    $file = $request->file('photo');
    $path = $file->store('photos', 'public');

    UserPhoto::create([
        'user_id' => auth()->id(),
        'file_name' => $file->getClientOriginalName(),
        'file_path' => $path,
        'file_size' => $file->getSize(),
        'mime_type' => $file->getMimeType(),
    ]);
}
```

---

## Validasi File Upload

**Request Validation:**

```php
public function store(Request $request)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        // max dalam kilobytes (2048 KB = 2 MB)
    ]);

    // Proses upload
}
```

**Validasi Rules:**

- `required`: wajib upload
- `image`: harus gambar
- `mimes:jpeg,png`: hanya jpeg dan png
- `max:2048`: maksimal 2MB
- `dimensions:min_width=100,min_height=100`: dimensi minimal

---

## Mengambil Informasi File

**File Information Methods:**

```php
$file = $request->file('photo');

// Nama asli
$originalName = $file->getClientOriginalName();

// Extension
$extension = $file->getClientOriginalExtension();
$extension2 = $file->extension();

// Size dalam bytes
$size = $file->getSize();
$sizeInKB = $size / 1024;
$sizeInMB = $sizeInKB / 1024;

// MIME type
$mimeType = $file->getMimeType(); // image/jpeg
```

---

## Image Manipulation dengan Intervention Image

**Install Package:**

```bash
composer require intervention/image
```

**Resize Image:**

```php
use Intervention\Image\Facades\Image;

public function store(Request $request)
{
    $file = $request->file('photo');
    $fileName = time() . '.jpg';
    $path = public_path('storage/photos/' . $fileName);

    // Resize to 300x300
    Image::make($file)
        ->resize(300, 300)
        ->save($path);
}
```

---

## Menampilkan File dari Storage

**Blade Template:**

```html
<!-- Untuk public disk -->
<img src="{{ asset('storage/' . $photo->file_path) }}" alt="Photo" />

<!-- atau dengan Storage facade -->
<img src="{{ Storage::url($photo->file_path) }}" alt="Photo" />
```

**Route untuk Private File:**

```php
Route::get('/download/{file}', function ($file) {
    return Storage::download('documents/' . $file);
});
```

---

## Menghapus File dengan Storage::delete()

**Delete Single File:**

```php
use Illuminate\Support\Facades\Storage;

Storage::disk('public')->delete('photos/abc123.jpg');

// atau shorthand jika default disk adalah public
Storage::delete('photos/abc123.jpg');
```

**Delete Multiple Files:**

```php
Storage::delete(['photos/file1.jpg', 'photos/file2.jpg']);
```

**Check File Exists:**

```php
if (Storage::exists('photos/abc123.jpg')) {
    Storage::delete('photos/abc123.jpg');
}
```

---

## Hapus File saat Record Dihapus

**Model Event (Eloquent Observer):**

```php
// App\Models\UserPhoto.php
protected static function boot()
{
    parent::boot();

    static::deleting(function ($photo) {
        // Hapus file fisik saat record dihapus
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }
    });
}
```

---

## Studi Kasus: Upload Avatar dengan Validasi dan Resize

**Complete Example:**

```php
public function uploadAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048|dimensions:min_width=100,min_height=100',
    ]);

    $file = $request->file('avatar');
    $fileName = auth()->id() . '_' . time() . '.jpg';
    $path = public_path('storage/avatars/' . $fileName);

    // Resize to 200x200
    Image::make($file)
        ->fit(200, 200)
        ->save($path);

    // Update user avatar
    auth()->user()->update([
        'avatar' => 'avatars/' . $fileName
    ]);

    return redirect()->back()->with('success', 'Avatar updated!');
}
```

---

## Security Considerations

**Virus Scan:**

- Gunakan ClamAV atau VirusTotal API
- Scan file sebelum menyimpan permanent

**File Type Spoofing:**

```php
// Jangan hanya cek extension
// Cek MIME type juga
$mimeType = $file->getMimeType();
$allowedMimes = ['image/jpeg', 'image/png'];

if (!in_array($mimeType, $allowedMimes)) {
    return back()->withErrors(['File type not allowed']);
}
```

**Path Traversal Prevention:**

- Laravel otomatis handle
- Jangan gunakan user input untuk path secara langsung

---

## Performance Tips

**Optimization Strategies:**

**Lazy Loading Images:**

```html
<img
  src="placeholder.jpg"
  data-src="{{ asset('storage/photo.jpg') }}"
  loading="lazy"
/>
```

**Use CDN:**

- Upload ke S3 + CloudFront
- Atau Cloudinary untuk image optimization

**Image Compression:**

```php
Image::make($file)
    ->resize(800, 800)
    ->encode('jpg', 75) // 75% quality
    ->save($path);
```

**WebP Format:**

```php
Image::make($file)
    ->encode('webp', 80)
    ->save($path);
```

---

## Ringkasan

**Key Points:**

- **Form Upload:** Gunakan `enctype="multipart/form-data"` pada form
- **Laravel Storage:** Abstraksi untuk berbagai storage (local, public, S3)
- **Validasi:** Validasi tipe file, ukuran, dan dimensi untuk keamanan
- **Public vs Local:** Public untuk file yang bisa diakses, local untuk private files
- **Symbolic Link:** `php artisan storage:link` untuk akses public storage
- **Database:** Simpan metadata file (path, size, mime_type) ke database
- **Security:** Whitelist file type, scan virus, batasi ukuran
- **Optimization:** Resize/compress image, gunakan CDN

**Best Practices:**

- Generate nama file unik (UUID atau timestamp)
- Hapus file lama saat update
- Gunakan disk yang sesuai dengan kebutuhan
- Monitor storage space

---

## Soal 1

**Atribut apa yang wajib ada pada form HTML untuk mengupload file?**

A. `method="multipart"`  
B. `enctype="multipart/form-data"`  
C. `type="file-upload"`  
D. `accept="*/*"`

<!-- **Jawaban: B** - Atribut `enctype="multipart/form-data"` wajib ada pada form untuk upload file -->

---

## Soal 2

**Method apa yang digunakan untuk menyimpan file dengan nama otomatis (random)?**

A. `save()`  
B. `upload()`  
C. `store()`  
D. `put()`

<!-- **Jawaban: C** - Method `store()` digunakan untuk menyimpan file dengan nama random otomatis -->

---

## Soal 3

**Di mana file disimpan jika menggunakan disk 'public' di Laravel?**

A. `public/uploads/`  
B. `storage/app/public/`  
C. `public/storage/`  
D. `storage/public/`

<!-- **Jawaban: B** - File disimpan di `storage/app/public/` saat menggunakan disk 'public' -->

---

## Soal 4

**Command apa yang digunakan untuk membuat symbolic link dari public/storage ke storage/app/public?**

A. `php artisan make:link`  
B. `php artisan link:storage`  
C. `php artisan storage:link`  
D. `php artisan create:symlink`

<!-- **Jawaban: C** - Command `php artisan storage:link` membuat symbolic link untuk public storage -->

---

## Soal 5

**Validasi rule yang tepat untuk membatasi ukuran file maksimal 5MB adalah?**

A. `max:5`  
B. `max:5000`  
C. `max:5120`  
D. `size:5MB`

<!-- **Jawaban: C** - `max:5120` karena max dalam kilobytes (5MB = 5 × 1024 = 5120 KB) -->

---

## Soal 6

**Method apa yang digunakan untuk menyimpan file dengan nama custom?**

A. `store()`  
B. `storeAs()`  
C. `saveAs()`  
D. `put()`

<!-- **Jawaban: B** - Method `storeAs()` digunakan untuk menyimpan file dengan nama yang kita tentukan sendiri -->

---

## Soal 7

**Bagaimana cara mengecek apakah request memiliki file yang di-upload?**

A. `$request->has('photo')`  
B. `$request->file('photo')`  
C. `$request->hasFile('photo')`  
D. `$request->checkFile('photo')`

<!-- **Jawaban: C** - Method `hasFile()` digunakan untuk mengecek apakah ada file yang di-upload -->

---

## Soal 8

**Apa fungsi dari validation rule `mimes:jpeg,png,jpg`?**

A. Mengatur ukuran maksimal file  
B. Membatasi tipe file yang diizinkan  
C. Mengatur dimensi gambar  
D. Mengkompress file otomatis

<!-- **Jawaban: B** - Rule `mimes` digunakan untuk membatasi tipe/format file yang boleh di-upload -->

---

## Soal 9

**Method apa yang digunakan untuk menghapus file dari storage?**

A. `Storage::remove()`  
B. `Storage::destroy()`  
C. `Storage::delete()`  
D. `Storage::unlink()`

<!-- **Jawaban: C** - Method `Storage::delete()` digunakan untuk menghapus file dari storage -->

---

## Soal 10

**Package apa yang digunakan untuk manipulasi gambar (resize, crop) di Laravel?**

A. `laravel/image`  
B. `intervention/image`  
C. `gd/image`  
D. `imagick/laravel`

<!-- **Jawaban: B** - Package `intervention/image` adalah library populer untuk manipulasi gambar di Laravel -->
