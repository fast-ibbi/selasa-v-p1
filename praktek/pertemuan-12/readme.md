# Praktek Pertemuan 12: File Upload dan Storage Management

## Soal 1: Form Upload Sederhana

Buat form upload file sederhana dengan ketentuan:

- Buat route `/upload-photo` (GET dan POST)
- Buat view dengan form HTML yang bisa upload 1 file foto
- Pastikan form memiliki atribut yang benar untuk upload file
- Tampilkan pesan sukses setelah file berhasil di-upload
- Simpan file ke folder `storage/app/public/photos`

**Hint:** Jangan lupa atribut `enctype` pada form!

---

## Soal 2: Validasi File Upload

Modifikasi soal 1 dengan menambahkan validasi:

- File wajib di-upload (required)
- Hanya menerima gambar (jpeg, png, jpg, gif)
- Ukuran maksimal 2MB
- Tampilkan error message jika validasi gagal
- Gunakan `@error` directive di Blade untuk menampilkan error

**Hint:** Gunakan validation rules seperti `required|image|mimes:jpeg,png,jpg,gif|max:2048`

---

## Soal 3: Multiple File Upload

Buat fitur upload multiple files:

- Form bisa menerima beberapa file sekaligus
- Validasi setiap file (max 5 file, masing-masing max 1MB)
- Simpan semua file ke storage
- Tampilkan daftar nama file yang berhasil di-upload
- Gunakan loop untuk memproses setiap file

**Hint:** Gunakan `multiple` attribute pada input file dan `foreach` untuk iterasi

---

## Soal 4: Simpan Informasi File ke Database

Buat fitur upload dengan menyimpan metadata ke database:

- Buat migration untuk tabel `uploaded_files` dengan kolom:
  - `id`, `file_name`, `file_path`, `file_size`, `mime_type`, `timestamps`
- Buat model `UploadedFile`
- Simpan informasi file ke database setiap kali upload
- Tampilkan daftar semua file yang pernah di-upload (dari database)

**Hint:** Gunakan method `getClientOriginalName()`, `getSize()`, `getMimeType()`

---

## Soal 5: Upload Avatar User

Buat fitur upload avatar untuk user:

- Buat halaman profile edit dengan form upload avatar
- Simpan path avatar di kolom `avatar` pada tabel users
- Generate nama file unik menggunakan `user_id` dan `timestamp`
- Tampilkan preview avatar di halaman profile
- Jika user sudah punya avatar, hapus avatar lama saat upload baru

**Hint:** Gunakan `Storage::delete()` untuk hapus file lama

---

## Soal 6: Download File dari Storage

Buat fitur download file:

- Buat route `/download/{id}` untuk download file berdasarkan ID di database
- File harus ter-download (bukan ditampilkan di browser)
- Gunakan nama file original saat download
- Tambahkan tombol download di daftar file

**Hint:** Gunakan `Storage::download()` atau `response()->download()`

---

## Soal 7: Resize Image Otomatis

Implementasikan resize image saat upload:

- Install package `intervention/image`
- Resize semua gambar yang di-upload menjadi maksimal 800x600 pixel
- Maintain aspect ratio (tidak distorsi)
- Simpan hasil resize ke storage
- Tampilkan dimensi gambar sebelum dan sesudah resize

**Hint:** Gunakan `Image::make()->resize()` atau `fit()`

---

## Soal 8: Upload Dokumen dengan Kategori

Buat sistem upload dokumen dengan kategori:

- Buat form upload dengan pilihan kategori (KTP, Ijazah, Sertifikat, Lainnya)
- Simpan file ke folder sesuai kategori (mis: `documents/ktp/`, `documents/ijazah/`)
- Simpan informasi kategori ke database
- Buat halaman untuk melihat dokumen berdasarkan kategori
- Tampilkan icon berbeda untuk setiap tipe dokumen

**Hint:** Gunakan parameter pertama pada `store()` untuk menentukan folder

---

## Soal 9: Hapus File dari Storage dan Database

Buat fitur hapus file:

- Tambahkan tombol "Hapus" di setiap item file
- Hapus file fisik dari storage
- Hapus record dari database
- Tampilkan konfirmasi sebelum menghapus
- Tampilkan pesan sukses/gagal setelah proses hapus

**Hint:** Gunakan JavaScript confirm dialog atau SweetAlert untuk konfirmasi

---

## Soal 10: Galeri Foto dengan Upload

Buat aplikasi galeri foto sederhana:

- Halaman upload foto dengan judul dan deskripsi
- Validasi: hanya gambar, max 5MB
- Generate thumbnail (200x200) dan full size (1200x900)
- Simpan kedua versi ke storage
- Halaman galeri yang menampilkan semua foto dalam grid
- Klik thumbnail untuk melihat gambar full size
- Fitur hapus foto dari galeri

**Bonus:**

- Tambahkan pagination (10 foto per halaman)
- Tambahkan fitur search berdasarkan judul
- Upload multiple photos sekaligus

**Hint:**

- Gunakan `Image::make()->fit()` untuk thumbnail dan full size
- Simpan path thumbnail dan full size di kolom berbeda

---

## Checklist Praktek

Pastikan semua soal sudah mencakup:

- [ ] Form dengan `enctype="multipart/form-data"`
- [ ] Validasi file (tipe, ukuran)
- [ ] Menyimpan file ke storage
- [ ] Membaca informasi file
- [ ] Menyimpan metadata ke database
- [ ] Menampilkan file dari storage
- [ ] Menghapus file dari storage
- [ ] Error handling yang baik
- [ ] User feedback (pesan sukses/error)

---

## Tips Pengerjaan

1. **Testing Upload:**

   - Siapkan file test dengan berbagai ukuran dan format
   - Test dengan file yang melebihi batas ukuran
   - Test dengan format file yang tidak diizinkan

2. **Debugging:**

   - Cek `php.ini` untuk `upload_max_filesize` dan `post_max_size`
   - Cek permission folder storage (harus writable)
   - Gunakan `dd()` atau `Log::info()` untuk debug

3. **Security:**

   - Selalu validasi tipe file dan ukuran
   - Jangan gunakan nama file original langsung (rename!)
   - Simpan file di luar document root untuk private files

4. **Performance:**
   - Resize/compress gambar untuk menghemat space
   - Gunakan lazy loading untuk galeri dengan banyak gambar
   - Consider pagination untuk list file yang banyak

---

## Referensi

- Laravel File Storage: https://laravel.com/docs/filesystem
- Laravel Validation: https://laravel.com/docs/validation
- Intervention Image: http://image.intervention.io/

---

**Selamat Mengerjakan! 🚀**
