<?php
  // --- STRANICA GDE ADMINISTRATOR POTVRDJUJE TERMINE ---

  // pokrecemo sesiju
  session_start();

  // proveravamo da li stranici pristupa admin
  if(!isset($_SESSION["admin"])) {
    header("Location: ./login.php");
    exit();
  }

  // ukljucujemo $conn promenljivu
  include("./../../db/db.php");
  include("./../../func/functions.php");

  // pravimo $transactions asoc. niz
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

  // pravimo $subcategories asoc. niz u kojem smestamo informacije o potkategorijama
  // izabranih transakcija
  $subcategories = array(
    "naziv" => array(),
    "cena" => array()
  );

  // SQL upit kojim se uzimaju aktivne transakcije (one koje administrator nije jos potvrdio)
  // uz njih, uzimaju se i kategorija koja je izabrana i korisnik koji je podneo zahtev za uslugu
  // a sortirane su po terminu od manjeg ka vecem (proslost -> sadasnjost -> buducnost)
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
    WHERE transakcije.aktivno = 1
    ORDER BY termin ASC";

  // trenutno vreme
  $currentTime = date_format(date_create(), 'Y-m-d H:i:s');
  // rezultat $sql upita na 39 liniji
  $result = $conn->query($sql);

  // ubacujemo u asoc. niz $transactions vrednosti koje smo dobili u rezultatu
  while($row = $result->fetch_assoc()) {
    array_push($transactions["id"], $row["id"]);
    array_push($transactions["korisnik"], $row["korisnik"]);
    array_push($transactions["ukupno"], $row["ukupno"]);
    array_push($transactions["kategorijaID"], $row["kategorijaID"]);
    array_push($transactions["kategorijaNaziv"], $row["naziv"]);
    array_push($transactions["slika"], $row["slika"]);
    array_push($transactions["termin"], date_format(date_create($row["termin"]), 'd.m.Y. H:i'));
    // ovde proveravamo da li je termin u proslosti, ako jeste znaci da administratoru trebamo pruziti priliku
    // da taj termin i oznaci kao gotov, to  ovde radimo
    if(strtotime($row["termin"]) < strtotime($currentTime)) {
      // ako je u proslosti (moze da se oznaci)
      array_push($transactions["aktivno"], true);
    } else {
      // ako nije u proslosti (sluzi kao kalendar termina)
      array_push($transactions["aktivno"], false);
    }
  }
  
  // ovde vrsimo iteraciju kroz ceo $transactions['id'] niz gde uzimamo sve
  // ID-eve rezultata upita na 39. liniji
  for($i = 0; $i < sizeof($transactions["id"]); $i++) {
    // $tempNaziv oznacava privremeni naziv gde smestamo nazive potkategorija
    $tempNaziv = array();
    // $tempCena oznacava privremene cene gde smestamo cene potkategorija
    $tempCena = array();
    
    // ovo je ID iz niza $transactions["id"] na $i-tom indeksu
    $transactionID = $transactions["id"][$i];
    
    // ovde uzimamo sve potkategorije koje "zdruzujemo" sa transakcije_potkategorije i transakcijama
    // gde na osnovu ID-a transakcije mi mozemo uzeti sve informacije o izabranim potkategorijama te transakcije
    // naravno, uzimamo samo aktivne, tj. one koje administrator nije oznacio kao neaktivne
    $sql = "SELECT * FROM potkategorije 
      INNER JOIN transakcije_potkategorije
      ON potkategorije.id = transakcije_potkategorije.potkategorije_id
      INNER JOIN transakcije
      ON transakcije_potkategorije.transakcije_id = transakcije.id
      WHERE transakcije_potkategorije.transakcije_id = '$transactionID' AND aktivno = 1";
    
    // rezultat smestamo u $result i ubacujemo nase vrednosti u $tempNaziv i $tempCena
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
      array_push($tempNaziv, $row["naziv"]);
      array_push($tempCena, $row["cena"]);
    }
    
    // sada mi pravimo dvodimenzionalni niz, koji ce da izgleda ovako
    // $sbct["naziv"] = [ ['Potkategorija 1', 'Potkategorija 2'], ['Potkategorija 1', 'Potkategorija 3'] ]
    // isti izgled imace i $subcategories["cena"], $subcategories["naziv"][0] oznacava sve potkategorije
    // transakcije $transactions["id"][0], a $subcategories["naziv"][0] je niz, dok je $transactions["id"][0] int
    array_push($subcategories["naziv"], $tempNaziv);
    array_push($subcategories["cena"], $tempCena);
  }
  
  // ukoliko je kliknuto dugme za submit
  if(isset($_POST["submit"])) {
    // uzimamo ID koji je namesten u HTML-u
    $clickedID =  key($_POST["submit"]);
    
    // AZURIRAJ transakcije POSTAVI DA JE aktivno = 0 GDE JE id = $clickedID
    $sql = "UPDATE transakcije SET aktivno = 0 WHERE id = '$clickedID'";
    $conn->query($sql);
    // kada pritisnemo dugme i kada se ovo izvrsi, stranica ce se momentalno osveziti
    header("Refresh: 0;");
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./../../style/bootstrap.min.css">
  <link rel="stylesheet" href="./../../style/index.css">
  <title>Zakazane usluge</title>
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
    <div class="row d-flex justify-content-center">
      <h1 class="text-center">Predstojeći termini</h1>
      <div class="col-lg-6 col-md-8 col-sm-12">
        <?php for($i = 0; $i < sizeof($transactions["id"]); $i++): ?>
          <div class="row w-100 border mb-3 p-4">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-9">
              <p>Klijent: <?php echo $transactions["korisnik"][$i]; ?></p>
              <p>Datum i vreme: <?php echo $transactions["termin"][$i]; ?></p>
              <p>Usluga: <?php echo $transactions["kategorijaNaziv"][$i]; ?></p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3">
              <img class="w-100" src="./../../assets/categories/<?php echo $transactions["slika"][$i]; ?>" style="filter: brightness(0);"/>
            </div>
            
            <p>Potkategorije:</p>    
            
            <ol>
              <?php for($j = 0; $j < sizeof($subcategories["naziv"][$i]); $j++): ?>
                <li class="mx-4"><?php echo $subcategories["naziv"][$i][$j] . " (" . $subcategories["cena"][$i][$j] . ")"; ?></li>        
              <?php endfor; ?>
            </ol>
            <hr/>
            <p class="text-end">=<?php echo $transactions["ukupno"][$i] ?></p>

            <?php if($transactions["aktivno"][$i]): ?>
              <div class="row d-flex justify-content-center">
                <div class="col-lg-6 col-md-8 col-sm-12">
                  <form method="POST">
                    <input class="btn btn-primary btn_black w-100" type="submit" name="submit[<?php echo $transactions["id"][$i]; ?>]"  value="Označi kao gotovo"/>
                  </form>
                </div>
              </div>
            <?php endif; ?>
            <span class="d-block text-end text-muted">#<?php echo $i + 1; ?></span>
          </div>

          <!-- <img src="./../../assets/categories/<?php echo $transactions["slika"][$i]; ?>"/> -->
        <?php endfor; ?>
      </div>
    </div>
  </div>
    
  <script src="./../../scripts/bootstrap.bundle.min.js"></script>
</body>
</html>