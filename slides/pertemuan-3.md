---
title: OOP pada PHP (Class, Object, Inheritance, Polymorphism)
version: 1.0.0
header: OOP pada PHP (Class, Object, Inheritance, Polymorphism)
footer: https://github.com/ibbi/selasa-v-p1
paginate: true
marp: true
---

<!--
_class: lead
_paginate: skip
-->

# OOP pada PHP (Class, Object, Inheritance, Polymorphism)

---

## Tujuan Pembelajaran

- Mahasiswa mampu menjelaskan konsep dasar OOP (Object-Oriented Programming).
- Mahasiswa mampu membuat dan menggunakan `class` dan `object` dalam PHP.
- Mahasiswa memahami dan dapat mengimplementasikan `inheritance`, `encapsulation`, dan `polymorphism`.
- Mahasiswa memahami relevansi OOP dalam pengembangan aplikasi dengan framework Laravel.

---

## Hubungan Antara OOP dan Laravel

Laravel adalah framework yang dibangun sepenuhnya menggunakan prinsip OOP. Semua komponen utamanya—seperti `Model`, `View`, `Controller`, `Middleware`, dan `Request`—adalah `class`. Memahami OOP adalah kunci untuk bisa memodifikasi, memperluas, dan menggunakan Laravel secara efektif. Tanpa pemahaman OOP, developer hanya akan menjadi pengguna tanpa bisa berinovasi.

---

## Prinsip Dasar OOP

- **Encapsulation**: "Membungkus" data (properti) dan metode (fungsi) yang beroperasi pada data tersebut ke dalam satu unit (objek). Ini menyembunyikan detail internal dan melindungi data dari manipulasi luar yang tidak sah.
- **Inheritance**: "Mewariskan" properti dan metode dari sebuah `class` (induk) ke `class` lain (anak). Ini mempromosikan penggunaan ulang kode (_code reusability_).
- **Polymorphism**: "Banyak bentuk." Kemampuan objek yang berbeda untuk merespons metode dengan nama yang sama dengan cara yang berbeda. Ini memungkinkan fleksibilitas dalam desain.
- **Abstraction**: Menyembunyikan kompleksitas dan hanya menampilkan fitur-fitur yang esensial. `Abstract class` dan `interface` adalah contoh implementasinya.

---

## Perbedaan Prosedural vs. OOP

- **Pemrograman Prosedural**: Fokus pada urutan eksekusi fungsi atau prosedur. Data dan fungsi seringkali terpisah. Sulit dikelola untuk proyek besar karena perubahan di satu tempat bisa memengaruhi banyak bagian lain.
- **Pemrograman OOP**: Fokus pada objek yang memiliki data (properti) dan perilaku (metode). Kode lebih terorganisir, modular, mudah dikelola, dan dapat digunakan kembali.

---

## Komponen Utama OOP

- **Class**: Sebuah _blueprint_ atau cetak biru untuk membuat objek. Mendefinisikan properti dan metode yang akan dimiliki oleh objek.
- **Object**: Instansiasi atau perwujudan nyata dari sebuah `class`. Jika `class` adalah cetak biru rumah, `object` adalah rumah yang sebenarnya.
- **Property**: Variabel yang ada di dalam `class`. Disebut juga atribut atau _fields_.
- **Method**: Fungsi yang ada di dalam `class`. Mendefinisikan perilaku atau aksi yang bisa dilakukan oleh objek.

---

## Konsep Class dan Cara Mendefinisikannya

`Class` didefinisikan dengan kata kunci `class` diikuti dengan nama `class` dan kurung kurawal. Di dalamnya, kita bisa mendefinisikan properti dan metode.

- **Contoh Kode**:

```php
<?php
class Mahasiswa {
    // Property
    public $nama;
    public $nim;

    // Method
    public function sapa() {
        return "Halo, nama saya " . $this->nama;
    }
}
?>
```

---

## Membuat Object dari Class

Objek dibuat dari `class` menggunakan kata kunci `new`. Setelah objek dibuat, kita bisa mengakses properti dan metodenya menggunakan operator `->`.

---

**Contoh Kode**:

```php
<?php
// Include file class Mahasiswa
require_once 'Mahasiswa.php';

// Membuat object baru dari class Mahasiswa
$mahasiswa1 = new Mahasiswa();

// Mengisi nilai property
$mahasiswa1->nama = "Budi";
$mahasiswa1->nim = "12345";

// Memanggil method
echo $mahasiswa1->sapa(); // Output: Halo, nama saya Budi
?>
```

---

## Property dan Method: Cara Akses ($this)

Di dalam sebuah `class`, untuk mengakses properti atau metode dari `class` itu sendiri, kita menggunakan variabel khusus `$this`. `$this` merujuk pada objek saat ini (objek yang memanggil metode tersebut).

---

## Contoh Lengkap Class 'Mahasiswa'

Menggabungkan pendefinisian `class` dan pembuatan `object` dalam satu alur yang mudah dipahami.

---

**Contoh Kode**:

```php
<?php
class Mahasiswa {
    public $nama;

    public function setNama($nama) {
        $this->nama = $nama;
    }

    public function getNama() {
        return $this->nama;
    }
}

$mahasiswa_baru = new Mahasiswa();
$mahasiswa_baru->setNama("Andi");
echo "Nama mahasiswa: " . $mahasiswa_baru->getNama(); // Output: Nama mahasiswa: Andi
?>
```

---

## Apa Itu Constructor?

_Constructor_ adalah metode khusus dalam `class` yang secara otomatis dipanggil ketika sebuah objek dibuat (`new`). Fungsinya adalah untuk melakukan inisialisasi awal, seperti memberikan nilai default pada properti. Di PHP, _constructor_ didefinisikan dengan nama `__construct()`.

---

## Implementasi Constructor dan Destructor

`__destruct()` adalah kebalikan dari `__construct()`. Metode ini otomatis dipanggil tepat sebelum objek dihapus dari memori. Berguna untuk membersihkan _resource_ (misalnya, menutup koneksi database).

---

**Contoh Kode**:

```php
<?php
class Mobil {
    public $merk;

    public function __construct($merk) {
        $this->merk = $merk;
        echo "Objek mobil dengan merk {$this->merk} telah dibuat.<br>";
    }

    public function __destruct() {
        echo "Objek mobil dengan merk {$this->merk} telah dihancurkan.";
    }
}

$mobil_toyota = new Mobil("Toyota");
// Output: Objek mobil dengan merk Toyota telah dibuat.
// Ketika skrip selesai, output destructor akan muncul.
?>
```

---

## Studi Kasus Constructor

Dengan _constructor_, kita bisa "memaksa" user untuk menyediakan data yang dibutuhkan saat membuat objek, sehingga objek selalu dalam keadaan valid.

---

## Konsep Pewarisan (Inheritance)

_Inheritance_ memungkinkan sebuah `class` (anak/turunan) untuk mewarisi properti dan metode dari `class` lain (induk). Ini menciptakan hierarki "is-a" (misalnya, `Mobil` adalah sebuah `Kendaraan`).

---

## Sintaks Inheritance (extends)

Kata kunci `extends` digunakan untuk membuat `class` turunan.

**Contoh Kode**:

```php
<?php
class Kendaraan { // Class Induk
    public function bergerak() {
        echo "Kendaraan bergerak...";
    }
}

class Mobil extends Kendaraan { // Class Anak
    // Class Mobil kini memiliki method bergerak()
}

$avanza = new Mobil();
$avanza->bergerak(); // Output: Kendaraan bergerak...
?>
```

---

## Contoh Class Induk dan Turunan

`Class` anak bisa memiliki properti dan metodenya sendiri, selain yang diwarisi dari `class` induk.

---

## Overriding Method

_Overriding_ adalah ketika `class` anak mendefinisikan ulang metode yang sudah ada di `class` induk dengan nama yang sama. Ini memungkinkan `class` anak untuk memiliki implementasi yang lebih spesifik.

---

**Contoh Kode**:

```php
<?php
class Kendaraan {
    public function bergerak() {
        echo "Kendaraan bergerak...";
    }
}

class Mobil extends Kendaraan {
    // Method overriding
    public function bergerak() {
        echo "Mobil melaju di jalan raya...";
    }
}

$avanza = new Mobil();
$avanza->bergerak(); // Output: Mobil melaju di jalan raya...
?>
```

---

## Konsep Enkapsulasi dan Visibilitas

Enkapsulasi melindungi data dengan mengatur hak akses (_visibility_) properti dan metode.

- **`public`**: Dapat diakses dari mana saja (di dalam `class`, `class` turunan, dan dari luar `class`).
- **`protected`**: Hanya dapat diakses di dalam `class` itu sendiri dan di `class` turunannya.
- **`private`**: Hanya dapat diakses di dalam `class` itu sendiri.

---

## Contoh Implementasi (Getter & Setter)

Karena properti `private` tidak bisa diakses dari luar, kita menggunakan metode `public` yang disebut _getter_ (untuk mengambil nilai) dan _setter_ (untuk mengubah nilai) sebagai perantara.

---

**Contoh Kode**:

```php
<?php
class Produk {
    private $harga;

    public function setHarga($harga) {
        if ($harga > 0) {
            $this->harga = $harga;
        } else {
            echo "Harga tidak valid!";
        }
    }

    public function getHarga() {
        return "Rp " . number_format($this->harga, 2, ',', '.');
    }
}

$buku = new Produk();
$buku->setHarga(50000);
echo $buku->getHarga(); // Output: Rp 50.000,00
// $buku->harga = -100; // Ini akan menyebabkan error karena private
?>
```

---

## Apa Itu Polymorphism?

Kemampuan untuk memproses objek yang berbeda melalui satu antarmuka (metode) yang sama. Contoh paling umum adalah melalui _method overriding_ atau implementasi `interface`.

---

## Abstract Class dan Interface

- **Abstract Class**: `Class` yang tidak bisa dibuat objeknya. Berfungsi sebagai kerangka dasar bagi `class` turunannya. Mungkin berisi metode abstrak (tanpa isi) yang _harus_ diimplementasikan oleh `class` anak.
- **Interface**: Kontrak yang mendefinisikan metode-metode apa saja yang _harus_ ada pada `class` yang mengimplementasikannya, tetapi tidak peduli bagaimana implementasinya.

---

**Contoh Kode Interface**:

```php
<?php
interface Bentuk {
    public function hitungLuas();
}

class Persegi implements Bentuk {
    private $sisi;
    public function __construct($sisi) { $this->sisi = $sisi; }
    public function hitungLuas() { return $this->sisi * $this->sisi; }
}

class Lingkaran implements Bentuk {
    private $jari;
    public function __construct($jari) { $this->jari = $jari; }
    public function hitungLuas() { return 3.14 * $this->jari * $this->jari; }
}
?>
```

---

## Trait

`Trait` digunakan untuk menggunakan ulang metode di beberapa `class` yang tidak memiliki hubungan hierarki (_inheritance_). `Trait` mengatasi batasan PHP yang hanya memperbolehkan satu `class` induk.

---

## 🧩 Trait: Contoh Sederhana

Bayangkan kamu punya mainan yang bisa melakukan hal yang sama (misalnya "nyala lampu"), tapi mainan-mainan itu berbeda jenis. Trait seperti memberikan kemampuan tambahan!

---

## 🎁 Keuntungan Trait

1. **Tidak Ada Duplikasi Kode**: Tulis sekali, pakai berkali-kali
2. **Bisa Gabung Banyak Trait**: Satu class pakai banyak trait sekaligus
3. **Lebih Fleksibel dari Inheritance**: PHP cuma bisa punya 1 parent, tapi bisa banyak trait
4. **Kode Lebih Rapi**: Kemampuan dikelompokkan berdasarkan fungsi

---

## 🚀 Kesimpulan Polymorphism & Trait

- **Polymorphism** = Satu cara panggil, banyak hasil berbeda (seperti semua hewan bersuara tapi beda-beda)
- **Trait** = Kemampuan bonus yang bisa dibagikan ke banyak class (seperti skill yang bisa dipinjamkan)

Keduanya membuat kode lebih fleksibel dan mudah dikembangkan! 🎉

---

<!--
_class: lead
-->

# Quiz OOP pada PHP

---

## Quiz 1

**Apa yang dimaksud dengan Class dalam OOP?**

A. Instance dari sebuah object  
B. Blueprint atau cetak biru untuk membuat object  
C. Fungsi khusus dalam PHP  
D. Variabel global dalam program

<!-- **Jawaban: B** -->

---

## Quiz 2

**Kata kunci apa yang digunakan untuk membuat object dari sebuah class?**

A. `create`  
B. `make`  
C. `new`
D. `instance`

<!-- **Jawaban: C** -->

---

## Quiz 3

**Apa fungsi dari variabel `$this` dalam class PHP?**

A. Merujuk ke class induk  
B. Merujuk ke object saat ini  
C. Membuat object baru  
D. Menghapus object dari memori

<!-- **Jawaban: B** -->

---

## Quiz 4

**Method khusus yang otomatis dipanggil saat object dibuat adalah:**

A. `__init()`  
B. `__start()`  
C. `__construct()`  
D. `__create()`

<!-- **Jawaban: C** -->

---

## Quiz 5

**Kata kunci yang digunakan untuk inheritance (pewarisan) dalam PHP adalah:**

A. `inherits`  
B. `extends`  
C. `implements`  
D. `derives`

<!-- **Jawaban: B** -->

---

## Quiz 6

**Visibility modifier yang membuat property hanya bisa diakses dalam class itu sendiri adalah:**

A. `public`  
B. `protected`  
C. `private`  
D. `static`

<!-- **Jawaban: C** -->

---

## Quiz 7

**Apa yang dimaksud dengan method overriding?**

A. Membuat method baru di class induk  
B. Menghapus method dari class  
C. Mendefinisikan ulang method dari class induk di class anak  
D. Membuat dua method dengan nama yang sama

<!-- **Jawaban: C** -->

---

## Quiz 8

**Interface dalam PHP digunakan untuk:**

A. Membuat object langsung  
B. Mendefinisikan kontrak method yang harus diimplementasikan  
C. Menyimpan data  
D. Menghubungkan database

<!-- **Jawaban: B** -->

---

## Quiz 9

**Trait dalam PHP digunakan untuk:**

A. Membuat class abstract  
B. Menggunakan ulang method di beberapa class tanpa inheritance  
C. Membuat interface  
D. Menghapus class dari memori

<!-- **Jawaban: B** -->

---

## Quiz 10

**Manakah yang BUKAN prinsip dasar OOP?**

A. Encapsulation  
B. Inheritance  
C. Polymorphism  
D. Compilation

<!-- **Jawaban: D** -->
