### ✅ **Pertemuan 9 – Form Handling, Validation, Request Handling**

1. Jelaskan alur lengkap proses pengiriman data dari form HTML hingga data tersebut diproses di controller Laravel.
2. Apa perbedaan antara `$request->input()`, `$request->all()`, dan `$request->only()` dalam Laravel? Berikan contoh penggunaannya.
3. Jelaskan tujuan validasi form dalam aplikasi web dan bagaimana Laravel menangani validasi secara default.
4. Buatlah contoh aturan validasi Laravel untuk form registrasi yang memiliki field: `name`, `email`, `password`, dan `password_confirmation`.
5. Jelaskan bagaimana cara menampilkan pesan error validasi pada form Blade.

---

### ✅ **Pertemuan 10 – Middleware dan Authentication Dasar**

6. Jelaskan konsep middleware dalam Laravel dan perannya dalam siklus request-response.
7. Bagaimana cara membuat middleware custom dan mendaftarkannya agar bisa digunakan pada route tertentu?
8. Jelaskan perbedaan middleware `auth` dan `guest` dalam Laravel serta contoh penggunaannya.
9. Uraikan langkah-langkah membuat sistem login dan logout sederhana menggunakan Laravel authentication.
10. Mengapa middleware sangat penting dalam pengamanan aplikasi web? Berikan contoh kasus penggunaannya.

---

### ✅ **Pertemuan 11 – Relationship Database (Eloquent Relationships)**

11. Jelaskan perbedaan relasi **One to One**, **One to Many**, dan **Many to Many** dalam Laravel beserta contoh kasusnya.
12. Bagaimana cara mendefinisikan relasi One to Many antara model `Post` dan `Comment` di Laravel?
13. Jelaskan fungsi tabel pivot dalam relasi Many to Many dan bagaimana Laravel mengelolanya.
14. Apa manfaat penggunaan eager loading (`with()`) dalam relasi database? Jelaskan dengan contoh.
15. Bagaimana cara mengambil data relasi beserta atribut tambahan dari tabel pivot?

---

### ✅ **Pertemuan 12 – File Upload dan Storage Management**

16. Jelaskan langkah-langkah mengimplementasikan fitur upload file di Laravel mulai dari form hingga penyimpanan file.
17. Apa perbedaan antara disk `public`, `local`, dan `s3` dalam Laravel filesystem configuration?
18. Jelaskan cara melakukan validasi file upload (tipe file dan ukuran) di Laravel.
19. Bagaimana cara menampilkan kembali file yang telah diupload agar dapat diakses melalui browser?
20. Jelaskan bagaimana Laravel menangani manajemen file storage agar aman dan terstruktur.

---

## ✅ **Pertemuan 13 – Mail & Notification di Laravel**

1. Jelaskan perbedaan antara fitur **Mail** dan **Notification** dalam Laravel serta kapan masing-masing digunakan.
2. Uraikan langkah-langkah membuat dan mengirim email menggunakan Laravel Mailables.
3. Jelaskan fungsi file `.env` dalam konfigurasi email Laravel dan parameter apa saja yang wajib diatur.
4. Bagaimana cara mengirim email berbasis template Blade di Laravel? Jelaskan alurnya.
5. Jelaskan bagaimana sistem **Notification Channel** bekerja di Laravel (email, database, broadcast, dll).

---

## ✅ **Pertemuan 14 – Optimisasi Aplikasi & Best Practices (Security, Cache, Performance)**

1. Jelaskan mengapa aspek keamanan sangat penting dalam pengembangan aplikasi Laravel. Sebutkan minimal tiga risiko keamanan yang umum terjadi.
2. Uraikan cara Laravel melindungi aplikasi dari serangan **CSRF**, **XSS**, dan **SQL Injection**.
3. Jelaskan konsep caching dalam Laravel dan manfaatnya terhadap performa aplikasi.
4. Sebutkan dan jelaskan minimal tiga jenis cache driver yang tersedia di Laravel.
5. Bagaimana cara mengimplementasikan caching query database menggunakan Laravel?

