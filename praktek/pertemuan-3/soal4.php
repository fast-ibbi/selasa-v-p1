<?php

class Kendaraan {
    protected $merk;
    protected $warna;
    protected $tahunPembuatan;

    public function __construct($merk = "", $warna = "", $tahunPembuatan = 1990)
    {
        $this->merk = $merk;
        $this->warna = $warna;
        $this->tahunPembuatan = $tahunPembuatan;
    }

    public function infoKendaraan(){
        echo "Motor: $this->merk
                , Warna: $this->warna
                , Tahun: $this->tahunPembuatan";
    }
}

class Motor extends Kendaraan {
    private $jenisMotor;

    public function __construct($merk = "", $warna = "", $tahunPembuatan = 1990, $jenisMotor = "")
    {
        parent::__construct($merk, $warna, $tahunPembuatan);
        $this->jenisMotor = $jenisMotor;
    }

    public function infoKendaraan()
    {
        echo "Motor: $this->merk
                , Warna: $this->warna
                , Tahun: $this->tahunPembuatan
                , Jenis: $this->jenisMotor <br>";
    }
}

class Mobil extends Kendaraan {
    private $jumlahPintu;

    public function __construct($merk = "", $warna = "", $tahunPembuatan = 1990, $jumlahPintu = 4)
    {
        parent::__construct($merk, $warna, $tahunPembuatan);
        $this->jumlahPintu = $jumlahPintu;
    }

    public function infoKendaraan()
    {
        echo "Mobil: $this->merk
                , Warna: $this->warna
                , Tahun: $this->tahunPembuatan 
                , Jumlah Pintu : $this->jumlahPintu <br>";
    }
}


$motor = new Motor("Yamaha", "Merah", 2020, "Sport");
$motor->infoKendaraan();

$mobil = new Mobil("Toyota", "Hitam", 2018, 4);
$mobil->infoKendaraan();