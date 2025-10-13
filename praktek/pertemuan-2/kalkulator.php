<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <form action="/kalkulator.php" method="POST">
            <div class="mb-3">
                <label for="angka1">Angka 1</label>
                <input type="text" name="angka1" id="angka1" class="form-control">
            </div>
            <div class="mb-3">
                <label for="angka2">Angka 2</label>
                <input type="text" name="angka2" id="angka2" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Hitung</button>
        </form>
        
        <?php
            // cek apakah variabel angka1 dan angka2 terdeklarasi
            if(!isset($_POST["angka1"]) && !isset($_POST["angka2"])) exit;
            // ambil nilai angka1 dan angka2 dari form
            $angka1 = $_POST["angka1"];
            $angka2 = $_POST["angka2"];

            // echo $angka1." + ".$angka2."=".$angka1 + $angka2."<br>";
            echo "$angka1 + $angka2 = ".$angka1 + $angka2." <br>";
            echo $angka1." - ".$angka2."=".$angka1 - $angka2."<br>";
            echo $angka1." * ".$angka2."=".$angka1 * $angka2."<br>";
            echo $angka1." / ".$angka2."=".$angka1 / $angka2."<br>";
            echo $angka1." % ".$angka2."=".$angka1 % $angka2."<br>";
        ?>
    </div>
</body>
</html>