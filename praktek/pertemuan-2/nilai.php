<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <?php
            $nilai = 65;

            $grade = "";
            if($nilai >= 90){
                $grade = "A";
            }elseif($nilai >= 80){
                $grade = "B";
            }elseif($nilai >= 70){
                $grade = "C";
            }elseif($nilai >= 60){
                $grade = "D";
            }else{
                $grade = "E";
            }

            $status = "";
            switch($grade){
                case "A":
                case "B":
                case "C":
                    $status = "LULUS";
                    break;
                case "D":
                case "E":
                    $status = "TIDAK LULUS";
                    break;
            }

            // if($grade == "A" || $grade == "B" || $grade == "C" ){
            //     $status = "LULUS";
            // }else {
            //     $status = "TIDAK LULUS";
            // }

            echo "Nilai  : $nilai <br>";
            echo "Grade  : $grade <br>";
            echo "Status : $status <br>";
        ?>
    </div>
</body>
</html>