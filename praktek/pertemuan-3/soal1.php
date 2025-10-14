<?php

class Buku {
    public $judul;
    public $penulis;
    public $tahunTerbit;

    public function __construct(
        $judul ="", $penulis ="", $tahunTerbit =""
    )
    {
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->tahunTerbit = $tahunTerbit;
    }

    public function infoBuku(){
        echo "Judul: $this->judul, 
            Penulis: $this->penulis, 
            Tahun: $this->tahunTerbit\n";
    }
}

// Judul: Laskar Pelangi, Penulis: Andrea Hirata, Tahun: 2005
$buku1 = new Buku();
$buku1->judul = "Laskar Pelangi";
$buku1->penulis = "Andrea Hirata";
$buku1->tahunTerbit = 2005;
$buku1->infoBuku();

// Judul: Bumi Manusia, 
// Penulis: Pramoedya Ananta Toer, 
// Tahun: 1980

$buku2 = new Buku("Bumi Manusia"
                    ,"Pramoedya Anata Toer"
                    , 1980);
// $buku2->judul = "Bumi Manusia";
// $buku2->penulis = "Pramoedya Ananta Toer";
// $buku2->tahunTerbit = 1980;
$buku2->infoBuku();
