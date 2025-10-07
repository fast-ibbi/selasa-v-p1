## Soal Latihan Praktek

---

### Latihan 1: Kalkulator Sederhana

Buat program PHP yang melakukan operasi aritmatika dasar:

```php
<?php
$angka1 = 15;
$angka2 = 4;

// TODO: Hitung dan tampilkan hasil:
// - Penjumlahan
// - Pengurangan
// - Perkalian
// - Pembagian
// - Modulus (sisa bagi)
?>
```

**Output yang diharapkan:**

```
15 + 4 = 19
15 - 4 = 11
15 * 4 = 60
15 / 4 = 3.75
15 % 4 = 3
```

---

### Latihan 2: Sistem Penilaian

Buat program yang menentukan grade berdasarkan nilai:

```php
<?php
$nilai = 85;

// TODO: Buat kondisi untuk menentukan grade:
// A: 90-100
// B: 80-89
// C: 70-79
// D: 60-69
// E: < 60
?>
```

**Output yang diharapkan:**

```
Nilai: 85
Grade: B
Status: Lulus
```

---

### Latihan 3: Tabel Perkalian

Buat program yang menampilkan tabel perkalian:

```php
<?php
$angka = 7;

// TODO: Buat loop untuk menampilkan tabel perkalian
// dari 1 sampai 10
?>
```

**Output yang diharapkan:**

```
Tabel Perkalian 7:
7 x 1 = 7
7 x 2 = 14
7 x 3 = 21
...
7 x 10 = 70
```

---

### Latihan 4: Data Mahasiswa

Buat program untuk mengelola data mahasiswa:

```php
<?php
$mahasiswa = [
    ['nama' => 'Andi', 'nim' => '2021001', 'nilai' => 85],
    ['nama' => 'Budi', 'nim' => '2021002', 'nilai' => 78],
    ['nama' => 'Citra', 'nim' => '2021003', 'nilai' => 92],
    ['nama' => 'Dina', 'nim' => '2021004', 'nilai' => 67]
];

// TODO:
// 1. Tampilkan semua data mahasiswa
// 2. Hitung rata-rata nilai
// 3. Temukan mahasiswa dengan nilai tertinggi
?>
```

**Output yang diharapkan:**

```
Data Mahasiswa:
Andi (2021001) - Nilai: 85
Budi (2021002) - Nilai: 78
Citra (2021003) - Nilai: 92
Dina (2021004) - Nilai: 67

Rata-rata nilai: 80.5
Nilai tertinggi: Citra (92)
```

---

### Latihan 5: Validasi Input

Buat program validasi untuk form pendaftaran:

```php
<?php
$nama = "John Doe";
$email = "john@example.com";
$umur = 17;
$password = "12345";

// TODO: Buat validasi untuk:
// 1. Nama minimal 3 karakter
// 2. Email harus mengandung "@"
// 3. Umur minimal 18 tahun
// 4. Password minimal 6 karakter
//
// Tampilkan pesan error jika tidak valid
// Tampilkan "Registrasi berhasil" jika semua valid
?>
```

**Output yang diharapkan:**

```
Validasi Form Pendaftaran:
✓ Nama valid
✓ Email valid
✗ Umur harus minimal 18 tahun
✗ Password minimal 6 karakter

Status: Registrasi gagal - Ada 2 kesalahan
```

---