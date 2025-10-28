---
title: PHP Dasar (Syntax, Variabel, Operator, Control Flow)
version: 1.0.0
header: PHP Dasar (Syntax, Variabel, Operator, Control Flow)
footer: https://github.com/fast-ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# PHP Dasar (Syntax, Variabel, Operator, Control Flow)

---

## Tujuan Pembelajaran

- Mahasiswa memahami sintaks dasar PHP.
- Mahasiswa mampu menggunakan variabel, tipe data, operator, dan struktur kontrol dasar.
- Mahasiswa dapat menulis program PHP sederhana.

---

## Kaitan PHP dengan Web dan Laravel

- PHP adalah bahasa backend untuk membangun aplikasi web.
- Laravel adalah framework PHP modern yang memudahkan pengembangan web.
- Menguasai dasar PHP penting sebelum belajar Laravel.

---

## Sejarah Singkat PHP

- PHP dikembangkan oleh Rasmus Lerdorf pada 1994.
- Awalnya untuk membuat halaman web dinamis.
- Terus berkembang dengan fitur OOP dan framework.

---

## Proses Eksekusi Script PHP di Server

- Server menerima request dari client.
- Server menjalankan script PHP.
- Menghasilkan HTML yang dikirim ke browser.

---

## Penulisan Script PHP: Tag Pembuka & Penutup

- Script PHP ditulis di antara tag `<?php ?>`.

```php
<?php
  echo "Hello World!";
?>
```

---

## Quiz 1

1. Tag pembuka PHP yang benar adalah?

- A) `<? php ?>`
- B) `<?php ?>`
- C) `<script php></script>`
- D) `<?= ?>`

---

## Komentar pada PHP

- Komentar satu baris: `//` atau `#`.
- Komentar multi baris: `/* ... */`.

```php

---
// Ini komentar satu baris
## Ini juga komentar satu baris
/*
Ini komentar
multi baris
*/
```

---

## Menampilkan Output: echo dan print

- `echo` dan `print` digunakan menampilkan teks atau variabel.

```php
echo "Halo Dunia!";
print "Halo Dunia!";
```

---

## Variabel: Definisi dan Aturan Penamaan

- Variabel awali dengan `$`, case-sensitive.
- Nama variabel hanya huruf, angka, dan underscore, tidak boleh diawali angka.

```php
$nama = "Andi";
$umur = 20;
```

---

## Tipe Data Dasar

- String, Integer, Float, Boolean.

```php
$nama = "Andi"; // string
$umur = 20; // integer
$tinggi = 172.5; // float
$menikah = false; // boolean
```

---

## Contoh Deklarasi dan Penugasan Variabel

- Menugaskan nilai sekaligus deklarasi.

```php
$greeting = "Selamat pagi";
echo $greeting; // Output: Selamat pagi
```

---

## Quiz 2

2. Mana penamaan variabel PHP yang valid?

- A) `$1nama`
- B) `$nama_1`
- C) `nama$`
- D) `$-nama`

---

## Konstanta pada PHP

- Nilai tetap menggunakan `define` atau `const`.

```php
define('NAMA_APP', 'Aplikasi Web');
echo NAMA_APP; // Output: Aplikasi Web
```

---

## Fungsi Built-in (contoh: strlen, var_dump)

- `strlen()` menghitung panjang string.
- `var_dump()` menampilkan tipe dan nilai.

```php
$kata = "Laravel";
echo strlen($kata); // Output: 7
var_dump($kata); // string(7) "Laravel"
```

---

## Quiz 3

3. Apa hasil dari `var_dump(5 == '5');` di PHP?

- A) `int(5)`
- B) `string(1)`
- C) `bool(true)`
- D) error

---

## Operator Aritmatika

- `+`, `-`, `*`, `/`, `%`

```php
$a = 5;
$b = 2;
echo $a + $b; // 7
echo $a % $b; // 1
```

---

## Operator Perbandingan

- `==`, `===`, `!=`, `<`, `>`, `<=`, `>=`

```php
var_dump(5 == '5'); // true
var_dump(5 === '5'); // false
```

---

## Operator Logika

- `&&` (AND), `||` (OR), `!` (NOT)

```php
$x = true;
$y = false;
echo $x && $y; // false
```

---

## Studi Kasus Mini: Operator Campuran

```php
$x = 10;
$y = 5;
if ($x > 5 && $y < 10) {
  echo "Syarat terpenuhi.";
}
```

---

## Statement if, else, elseif

```php
$nilai = 75;
if ($nilai >= 80) {
  echo "A";
} elseif ($nilai >= 60) {
  echo "B";
} else {
  echo "C";
}
```

---

## Switch Case

```php
$warna = 'merah';
switch ($warna) {
  case 'merah':
    echo "Stop";
    break;
  case 'hijau':
    echo "Jalan";
    break;
  default:
    echo "Hati-hati";
}
```

---

## Looping: Pengantar

- Memberi perulangan untuk mengulang blok kode.
- Contoh digunakan `while`, `for`, `foreach`.

---

## Looping: while dan do...while

```php
$i = 1;
while ($i <= 5) {
  echo $i;
  $i++;
}
```

---

## Looping: for dan foreach

```php
for ($i = 1; $i <= 5; $i++) {
  echo $i;
}
```

```php
$buah = ['apel', 'jeruk', 'mangga'];
foreach($buah as $item) {
  echo $item;
}
```

---

## Contoh Kasus Loop sederhana

- Cetak bilangan 1 sampai 10 dengan `for`.

```php
for ($i = 1; $i <= 10; $i++) {
  echo $i . ' ';
}
```

---

## Quiz 4

4. Pernyataan yang tepat untuk mengeksekusi blok kode berulang berdasarkan kondisi adalah?

- A) `if`
- B) `switch`
- C) `for`
- D) `include`

---

## Definisi dan Inisialisasi Array

- Array adalah tipe data koleksi.

```php
$warna = ['merah', 'kuning', 'hijau'];
```

---

## Mengakses dan Menambah Data ke Array

```php
echo $warna[0]; // merah
$warna[] = 'biru'; // tambah elemen baru
```

---

## Array Asosiatif

- Array dengan key khusus.

```php
$mahasiswa = [
  'nama' => 'Andi',
  'umur' => 20
];
echo $mahasiswa['nama']; // Andi
```

---

## Array Multidimensi

- Array di dalam array.

```php
$matkul = [
  ['Pemrograman', 'Senin'],
  ['Basis Data', 'Rabu']
];
echo $matkul[0][0]; // Pemrograman
```

---

## Contoh Penggunaan Array dalam Loop

```php
foreach ($warna as $w) {
  echo $w . ', ';
}
```

---

## Quiz 5

5. Cara yang benar menambahkan elemen di akhir array numerik `$arr` adalah?

- A) `$arr[] = 'elemen';`
- B) `$arr->push('elemen');`
- C) `$arr['elemen'] = true;`
- D) `push($arr, 'elemen');`

