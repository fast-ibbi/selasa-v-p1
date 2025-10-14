<?php

class Laptop {
    private $merk;
    private $processor;
    private $ram;
    private $harga;

    public function __construct($merk, $processor, $ram, $harga){
        $this->merk = $merk;
        $this->processor = $processor;
        $this->ram = $ram;
        $this->harga = $harga;
    }

    public function tampilkanSpesifikasi(){
        echo "Laptop: $this->merk <br>
            Processor: $this->processor <br>
            RAM: $this->ram <br>
            Harga: Rp $this->harga <br><br>";
    }
}

$laptop = new Laptop("Asus ROG","Intel Core I7","16 GB", "15.0000.000");
$laptop->tampilkanSpesifikasi();

$laptop2 = new Laptop("Lenovo Legion","Intel Core I5","8 GB", "10.0000.000");
$laptop2->tampilkanSpesifikasi();
