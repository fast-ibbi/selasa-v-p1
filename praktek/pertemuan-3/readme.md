# Soal Praktek Pertemuan 3 - OOP pada PHP

## Petunjuk Umum

- Kerjakan setiap soal dalam file PHP terpisah
- Gunakan nama file sesuai dengan nomor soal (contoh: `soal_1.php`, `soal_2.php`, dst)
- Pastikan setiap file dapat dijalankan secara independen
- Gunakan konsep OOP yang telah dipelajari

---

## Soal 1: Class dan Object Sederhana

Buatlah sebuah class `Buku` dengan properti:

- `judul` (public)
- `penulis` (public)
- `tahunTerbit` (public)

Tambahkan method `infoBuku()` yang mengembalikan informasi lengkap buku dalam format string.

Buat 2 object dari class tersebut dan tampilkan informasinya.

**Contoh Output:**

```
Judul: Laskar Pelangi, Penulis: Andrea Hirata, Tahun: 2005
Judul: Bumi Manusia, Penulis: Pramoedya Ananta Toer, Tahun: 1980
```

---

## Soal 2: Constructor dan Property

Buatlah class `Laptop` dengan properti private:

- `merk`
- `processor`
- `ram` (dalam GB)
- `harga`

Gunakan constructor untuk menginisialisasi semua properti saat object dibuat.

Tambahkan method `tampilkanSpesifikasi()` yang menampilkan semua informasi laptop.

Buat minimal 2 object laptop dengan spesifikasi berbeda.

**Contoh Output:**

```
Laptop: Asus ROG
Processor: Intel Core i7
RAM: 16 GB
Harga: Rp 15.000.000
```

---

## Soal 3: Getter dan Setter dengan Validasi

Buatlah class `Mahasiswa` dengan properti private:

- `nama`
- `nim`
- `ipk`

Buat getter dan setter untuk setiap properti dengan ketentuan:

- `setIpk()` hanya menerima nilai antara 0.0 sampai 4.0
- Jika nilai di luar range, tampilkan pesan error dan jangan ubah nilai

Tambahkan method `getPredikat()` yang mengembalikan predikat berdasarkan IPK:

- IPK >= 3.5: "Cumlaude"
- IPK >= 3.0: "Sangat Memuaskan"
- IPK >= 2.5: "Memuaskan"
- IPK < 2.5: "Cukup"

**Contoh Output:**

```
Nama: Budi Santoso
NIM: 123456
IPK: 3.75
Predikat: Cumlaude
```

---

## Soal 4: Inheritance (Pewarisan)

Buatlah class `Kendaraan` sebagai parent class dengan properti:

- `merk` (protected)
- `warna` (protected)
- `tahunPembuatan` (protected)

Method:

- `infoKendaraan()` yang menampilkan informasi dasar

Buat 2 class turunan:

1. `Motor` dengan properti tambahan `jenisMotor` (matic/manual)
2. `Mobil` dengan properti tambahan `jumlahPintu`

Setiap class turunan harus meng-override method `infoKendaraan()` untuk menampilkan informasi lengkap sesuai jenisnya.

**Contoh Output:**

```
Motor: Honda Beat, Warna: Merah, Tahun: 2023, Jenis: Matic
Mobil: Toyota Avanza, Warna: Silver, Tahun: 2022, Pintu: 5
```

---

## Soal 5: Encapsulation dan Private Property

Buatlah class `RekeningBank` dengan properti private:

- `nomorRekening`
- `namaPemilik`
- `saldo`

Method public:

- `__construct($nomorRekening, $namaPemilik, $saldoAwal)` - untuk inisialisasi
- `setor($jumlah)` - menambah saldo
- `tarik($jumlah)` - mengurangi saldo (cek saldo cukup atau tidak)
- `getSaldo()` - menampilkan saldo saat ini
- `getInfoRekening()` - menampilkan info lengkap rekening

Buat contoh transaksi: buat rekening, setor uang, tarik uang, tampilkan saldo.

**Contoh Output:**

```
Rekening: 1234567890
Pemilik: Ahmad
Saldo Awal: Rp 1.000.000
Setor: Rp 500.000 - Berhasil
Tarik: Rp 300.000 - Berhasil
Saldo Akhir: Rp 1.200.000
```

---

## Soal 6: Method Overriding dan Polymorphism

Buatlah class abstract `Hewan` dengan method abstract:

- `bersuara()` - mengembalikan suara hewan
- `bergerak()` - mengembalikan cara bergerak

Buat 3 class turunan:

1. `Kucing` - suara "Meong", bergerak "Berjalan dengan empat kaki"
2. `Burung` - suara "Cuit cuit", bergerak "Terbang dengan sayap"
3. `Ikan` - suara "..." (tidak bersuara), bergerak "Berenang dengan sirip"

Buat array berisi object dari ketiga hewan tersebut, lalu tampilkan suara dan cara bergerak masing-masing menggunakan loop.

**Contoh Output:**

```
Kucing: Meong - Berjalan dengan empat kaki
Burung: Cuit cuit - Terbang dengan sayap
Ikan: ... - Berenang dengan sirip
```

---

## Soal 7: Interface Implementation

Buatlah interface `HitungBangunDatar` dengan method:

- `hitungLuas()`
- `hitungKeliling()`

Implementasikan interface tersebut pada 3 class:

1. `PersegiPanjang` - dengan properti `panjang` dan `lebar`
2. `Segitiga` - dengan properti `alas`, `tinggi`, dan `sisiMiring`
3. `Lingkaran` - dengan properti `jariJari`

Setiap class harus mengimplementasikan kedua method sesuai rumus matematika yang benar.

Buat object dari masing-masing class dan tampilkan luas serta kelilingnya.

**Contoh Output:**

```
Persegi Panjang (5 x 3):
Luas: 15
Keliling: 16

Lingkaran (jari-jari 7):
Luas: 153.86
Keliling: 43.96
```

---

## Soal 8: Trait untuk Code Reusability

Buatlah trait `Timestamp` yang memiliki method:

- `setCreatedAt()` - set waktu dibuat
- `getCreatedAt()` - get waktu dibuat
- `setUpdatedAt()` - set waktu diupdate
- `getUpdatedAt()` - get waktu diupdate

Gunakan trait ini pada 2 class berbeda:

1. `Artikel` - dengan properti `judul` dan `konten`
2. `Produk` - dengan properti `nama` dan `harga`

Tunjukkan penggunaan trait dengan membuat object dan mengatur timestamp-nya.

**Contoh Output:**

```
Artikel: Tutorial PHP OOP
Dibuat: 2025-10-13 10:30:00
Diupdate: 2025-10-13 15:45:00

Produk: Laptop Gaming
Dibuat: 2025-10-13 09:00:00
Diupdate: 2025-10-13 14:20:00
```

---

## Soal 9: Kombinasi OOP Concepts

Buatlah sistem manajemen perpustakaan sederhana dengan struktur:

**Class `Item` (abstract):**

- Properties: `kode`, `judul`, `status` (tersedia/dipinjam)
- Method abstract: `getInfoLengkap()`
- Method: `pinjam()`, `kembalikan()`

**Class turunan:**

1. `BukuFisik` extends `Item` - tambah properti `penulis`, `halaman`
2. `DVD` extends `Item` - tambah properti `durasi`, `genre`

**Interface `Penilaian`:**

- Method: `beriRating($nilai)`, `getRating()`

Class `BukuFisik` dan `DVD` harus implement interface `Penilaian`.

Buat contoh peminjaman dan pemberian rating.

**Contoh Output:**

```
Buku: Laskar Pelangi (BK001)
Status: Tersedia
--- Dipinjam ---
Status: Dipinjam
--- Dikembalikan ---
Status: Tersedia
Rating: 4.5/5
```

---

## Soal 10: Studi Kasus - Mini E-Commerce

Buatlah sistem mini e-commerce dengan requirement berikut:

**Class `User` (abstract):**

- Properties: `nama`, `email`, `password`
- Method abstract: `getRole()`

**Class turunan:**

1. `Pembeli` extends `User` - tambah properti `alamat`, `keranjang` (array)
2. `Penjual` extends `User` - tambah properti `namaToko`, `produkDijual` (array)

**Class `Produk`:**

- Properties: `idProduk`, `namaProduk`, `harga`, `stok`
- Methods: `tambahStok($jumlah)`, `kurangiStok($jumlah)`, `getInfo()`

**Class `Transaksi`:**

- Properties: `idTransaksi`, `pembeli`, `produk`, `jumlah`, `total`
- Method: `prosesPembelian()` - kurangi stok produk jika tersedia

Buat skenario:

1. Buat 1 penjual dengan 2 produk
2. Buat 1 pembeli
3. Pembeli membeli 2 produk berbeda
4. Tampilkan detail transaksi dan sisa stok produk

**Contoh Output:**

```
Penjual: Toko Elektronik (penjual@email.com)
Produk Dijual:
- Laptop: Rp 10.000.000 (Stok: 5)
- Mouse: Rp 150.000 (Stok: 20)

Pembeli: Budi (budi@email.com)
Alamat: Jl. Merdeka No. 123

Transaksi #TRX001:
- Laptop x 1 = Rp 10.000.000
- Mouse x 2 = Rp 300.000
Total: Rp 10.300.000
Status: Berhasil

Stok Terbaru:
- Laptop: 4
- Mouse: 18
```

---

## Kriteria Penilaian

1. **Struktur Class yang Benar (20%)** - Properti dan method sesuai requirement
2. **Implementasi OOP (30%)** - Penggunaan inheritance, encapsulation, polymorphism dengan benar
3. **Functionality (30%)** - Program berjalan sesuai yang diminta
4. **Code Quality (10%)** - Kode rapi, terstruktur, dan mudah dibaca
5. **Output (10%)** - Output sesuai dengan contoh atau logika yang benar

---

## Tips Pengerjaan

- Mulai dari soal yang paling mudah (1-3)
- Pahami konsep sebelum mulai coding
- Test setiap class dan method yang dibuat
- Gunakan `var_dump()` atau `print_r()` untuk debugging jika diperlukan
- Baca kembali materi pertemuan 3 jika ada yang kurang jelas

**Selamat Mengerjakan! 🚀**
