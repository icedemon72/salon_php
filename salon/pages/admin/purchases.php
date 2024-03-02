<?php
  session_start();

  if(!isset($_SESSION["admin"])) {
    header("Location: ./login.php");
    exit();
  }

  include("./../../db/db.php");
  include("./../../func/functions.php");


  if(isset($_POST["submit"])) {
    if(isset($_POST["date"])) {
      $selectedMonth = $_POST["date"];
      $yearly = 0;
      $monthly = 0;

      $sql = "SELECT ukupno FROM transakcije WHERE DATE_FORMAT(termin, '%Y-%m') = '$selectedMonth' AND aktivno = 0";
      $result = $conn->query($sql);

      while($row = $result->fetch_assoc()) {
        $monthly += $row["ukupno"];
      }

      $selectedYear = date_format(date_create($selectedMonth.'-01'), 'Y');
      $sql = "SELECT ukupno FROM transakcije WHERE YEAR(termin) = '$selectedYear' AND aktivno = 0";

      $result = $conn->query($sql);
      while($row = $result->fetch_assoc()) {
        $yearly += $row["ukupno"];
      }

      $transactions = array(
        "id" => array(),
        "korisnik" => array(),
        "kategorijaID" => array(),
        "kategorijaNaziv" => array(),
        "termin" => array(),
        "slika" => array(),
        "ukupno" => array(),
        "aktivno" => array()
      );

      $sql = "SELECT 
        transakcije.id AS id,
        transakcije.termin AS termin,
        transakcije.ukupno AS ukupno,
        kategorije.id AS kategorijaID,
        kategorije.naziv AS naziv, 
        kategorije.slika AS slika,
        korisnici.ime AS korisnik
        FROM kategorije 
        INNER JOIN transakcije 
        ON kategorije.id = transakcije.kategorije_id
        INNER JOIN korisnici
        ON transakcije.korisnik_id = korisnici.id
        WHERE transakcije.aktivno = 0 AND DATE_FORMAT(termin, '%Y-%m') = '$selectedMonth'
        ORDER BY termin ASC";

      $result = $conn->query($sql);

      while($row = $result->fetch_assoc()) {
        array_push($transactions["id"], $row["id"]);
        array_push($transactions["korisnik"], $row["korisnik"]);
        array_push($transactions["ukupno"], $row["ukupno"]);
        array_push($transactions["kategorijaID"], $row["kategorijaID"]);
        array_push($transactions["kategorijaNaziv"], $row["naziv"]);
        array_push($transactions["slika"], $row["slika"]);
        array_push($transactions["termin"], date_format(date_create($row["termin"]), 'd.m.Y. H:i'));
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./../../style/bootstrap.min.css">
  <link rel="stylesheet" href="./../../style/index.css">
  <title>Document</title>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand mx-lg-5 fw-bold text-uppercase" href="./../../index.php">Salon lepote</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="./../../index.php">Početna</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./../../index.php#o_nama">O nama</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./../../index.php#usluge">Usluge</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./../../index.php#kontakt">Kontakt</a>
          </li>
        </ul>
        <div class="d-lg-flex d-xs-block mx-lg-5 gap-3 nav-item">
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Admin
            </a>
            <ul class="dropdown-menu dropdown_menu" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="./schedule.php">Zakazane usluge</a></li>
              <li><a class="dropdown-item" href="./purchases.php">Statistika</a></li>
            </ul>
          </div>
          <hr class="d-xs-block d-lg-none"/>
          <a class="nav-link" href="./../../logout.php">Odjavi se</a>  
        </div>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <h2 class="text-center">Pregled prethodnih transakcija</h2>
    <div class="row d-flex justify-content-center mt-3">
      <div class="col-lg-4 col-md-6 col-sm-12 border p-4">
        <form method="POST">
          <div class="row">
            <div class="col-md-8 col-sm-12">
              <input class="form-control" type="month" name="date" placeholder="Format: 2024-01 za januar" required />
            </div>
            <div class="col-md-4 col-sm-12 mt-md-0 mt-xs-4">
              <input class="btn btn-primary btn_black w-100"type="submit" name="submit" value="Unesi!"/>
            </div>
          </div>
        </form>  
      </div>
    </div>
  </div>
  <?php if(isset($_POST["submit"])): ?>
    <div class="container">
      <div class="row d-flex justify-content-center mt-3">
        <div class="col-lg-4 col-md-6 col-sm-12 border p-4">
          <h4>Rezultati za odabrani datum</h4>
          <hr/>
          <p>Mesečno: <?php echo $monthly; ?></p>
          <p>Godišnje: <?php echo $yearly; ?></p>
        </div>
      </div>
    </div>

    <?php if(sizeof($transactions['id']) != 0): ?>
      <div class="container mt-5">
        <div class="row d-flex justify-content-center">
          <h2 class="text-center">Uneti termini</h2>
            <div class="col-lg-6 col-md-8 col-sm-12">
              <?php for($i = 0; $i < sizeof($transactions['id']); $i++): ?>
                <div class="row w-100 border mb-3 p-4">
                  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-9">
                    <p>Klijent: <?php echo $transactions["korisnik"][$i]; ?></p>
                    <p>Datum i vreme: <?php echo $transactions["termin"][$i]; ?></p>
                    <p>Usluga: <?php echo $transactions["kategorijaNaziv"][$i]; ?></p>
                    <p>Ukupno: <?php echo $transactions["ukupno"][$i] ?></p>
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3">
                    <img class="w-100" src="./../../assets/categories/<?php echo $transactions["slika"][$i]; ?>" style="filter: brightness(0);"/>
                  </div>
                  </hr >
                  <p class="text-end">#<?php echo $i + 1; ?></p>
                </div>
              <?php endfor; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

  <?php endif; ?>
  <script src="./../../scripts/bootstrap.bundle.min.js"></script>
</body>
</html>