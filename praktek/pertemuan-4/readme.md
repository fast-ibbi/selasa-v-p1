# Praktikum Pertemuan 4 - Struktur Dasar Laravel & Project Setup

## Soal 1: Instalasi Composer

**Tujuan:** Memastikan Composer terinstal dengan benar

**Instruksi:**

1. Buka terminal/command prompt
2. Jalankan perintah untuk mengecek versi Composer
3. Screenshot hasil output yang menampilkan versi Composer
4. Jika belum terinstal, download dan install dari getcomposer.org

**Output yang diharapkan:**

```
Composer version x.x.x
```

---

## Soal 2: Membuat Project Laravel Baru

**Tujuan:** Membuat project Laravel menggunakan Composer

**Instruksi:**

1. Buka terminal dan navigasi ke folder kerja Anda
2. Jalankan perintah Composer untuk membuat project Laravel baru dengan nama `latihan_laravel`
3. Tunggu hingga proses instalasi selesai
4. Screenshot struktur folder yang terbentuk

**Perintah yang digunakan:**

```bash
composer create-project laravel/laravel latihan_laravel
```

---

## Soal 3: Menjalankan Development Server

**Tujuan:** Menjalankan aplikasi Laravel di local server

**Instruksi:**

1. Buka terminal di root project Laravel
2. Jalankan perintah artisan untuk start development server
3. Buka browser dan akses `http://localhost:8000`
4. Pastikan halaman welcome Laravel muncul
5. Screenshot halaman browser yang menampilkan welcome page Laravel
6. Coba akses dengan port berbeda (misalnya 8080)

**Perintah:**

```bash
# Default port 8000
php artisan serve

# Custom port
php artisan serve --port=8080
```

**Output yang diharapkan:**

- Server berjalan tanpa error
- Halaman welcome Laravel muncul di browser

---

## Soal 4: Konfigurasi File .env

**Tujuan:** Mengkonfigurasi environment variables

**Instruksi:**

1. Buka file `.env` di root project Laravel
2. Ubah konfigurasi berikut:
   - `APP_NAME` menjadi nama Anda
   - `APP_URL` sesuai dengan localhost Anda
   - `APP_TIMEZONE` menjadi `Asia/Jakarta`
3. Simpan perubahan
4. Screenshot file `.env` yang telah diubah

**Contoh:**

```env
APP_NAME="Latihan Laravel - [Nama Anda]"
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
```

---

## Soal 5: Generate Application Key

**Tujuan:** Membuat APP_KEY untuk keamanan aplikasi

**Instruksi:**

1. Buka terminal di root project Laravel
2. Jalankan perintah artisan untuk generate key
3. Periksa file `.env`, pastikan `APP_KEY` sudah terisi
4. Screenshot terminal dan file `.env` yang menunjukkan APP_KEY telah ter-generate

**Perintah:**

```bash
php artisan key:generate
```

---

## Soal 6: Setup Database

**Tujuan:** Mengkonfigurasi koneksi database

**Instruksi:**

1. Buka phpMyAdmin atau MySQL client
2. Buat database baru dengan nama `latihan_laravel_db`
3. Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=latihan_laravel_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Test koneksi dengan menjalankan migration
5. Screenshot hasil migration yang berhasil

**Perintah test koneksi:**

```bash
php artisan migrate
```

---

## Soal 7: Update dan Dump Autoload

**Tujuan:** Memahami perintah Composer untuk maintenance

**Instruksi:**

1. Jalankan perintah `composer dump-autoload` untuk regenerate autoload files
2. Jalankan perintah `composer update` (opsional, jika diperlukan)
3. Periksa folder `vendor/` dan pastikan folder `composer/` ada di dalamnya
4. Screenshot terminal yang menunjukkan proses dump-autoload berhasil
5. Catat perbedaan antara `composer install`, `composer update`, dan `composer dump-autoload`

**Perintah:**

```bash
# Regenerate autoload files
composer dump-autoload

# Update dependencies (hati-hati di production!)
composer update

# Install dependencies tanpa update
composer install
```

**Yang harus dijawab:**

- Apa fungsi `composer dump-autoload`?
- Kapan harus menggunakan `composer update`?
- Apa bedanya `composer install` dengan `composer update`?

---

## Soal 8: Menambah Package dengan Composer

**Tujuan:** Menggunakan Composer untuk menambah dependencies

**Instruksi:**

1. Install package `guzzlehttp/guzzle` menggunakan Composer
2. Periksa file `composer.json`, pastikan package sudah masuk ke daftar `require`
3. Periksa folder `vendor/`, pastikan folder `guzzlehttp/` sudah ada
4. Screenshot terminal dan file `composer.json`

**Perintah:**

```bash
composer require guzzlehttp/guzzle
```

---

## Soal 9: Konfigurasi Timezone dan Locale

**Tujuan:** Mengubah konfigurasi aplikasi

**Instruksi:**

1. Buka file `config/app.php`
2. Ubah konfigurasi berikut:
   - `timezone` menjadi `Asia/Jakarta`
   - `locale` menjadi `id`
   - `faker_locale` menjadi `id_ID`
3. Simpan perubahan
4. Jalankan server development dan akses aplikasi
5. Screenshot konfigurasi yang telah diubah

**Perintah menjalankan server:**

```bash
php artisan serve
```

---

## Soal 10: Membuat Route dan View Sederhana

**Tujuan:** Praktik dasar routing dan view

**Instruksi:**

1. Buka file `routes/web.php`
2. Tambahkan route baru:
   ```php
   Route::get('/profile', function () {
       return view('profile');
   });
   ```
3. Buat file view baru `resources/views/profile.blade.php` dengan konten:
   ```html
   <!DOCTYPE html>
   <html>
     <head>
       <title>Profile</title>
     </head>
     <body>
       <h1>Halaman Profile</h1>
       <p>Nama: [Nama Anda]</p>
       <p>NIM: [NIM Anda]</p>
       <p>Kelas: [Kelas Anda]</p>
     </body>
   </html>
   ```
4. Jalankan server development
5. Akses `http://localhost:8000/profile` di browser
6. Screenshot halaman yang muncul

**Perintah:**

```bash
php artisan serve
```

---

## Catatan Pengerjaan:

1. **Dokumentasi:** Setiap soal harus didokumentasikan dengan screenshot atau file hasil
2. **Penamaan File:** Kumpulkan semua screenshot dalam folder dengan nama `Pertemuan4_[NIM]_[Nama]`
3. **Format Laporan:** Buat laporan dalam format Word/PDF yang berisi:
   - Nomor soal
   - Screenshot/bukti pengerjaan
   - Penjelasan singkat (jika diperlukan)
   - Kendala yang dihadapi (jika ada)

## Kriteria Penilaian:

- **Kelengkapan (40%):** Semua soal dikerjakan
- **Ketepatan (30%):** Output sesuai dengan yang diharapkan
- **Dokumentasi (20%):** Screenshot dan penjelasan lengkap
- **Kreativitas (10%):** Penambahan atau eksplorasi tambahan

---

**Selamat Mengerjakan!** 🚀
