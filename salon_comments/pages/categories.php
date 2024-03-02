<?php 
  // --- STRANICA GDE KORISNIK ZAKAZUJE TERMINE ---
  // ova stranica funkcionise koriscenjem GET requesta po kome se i zna koja kategorija je odabrana
  // GET request koristi promenljive u samom URI-u, npr. http://localhost/salon/categories?category=1 
  // promenljiva je "category" i ima vrednost 1, da bismo pristupili njoj u PHP-u koristi se $_GET['nazivPromenljive']
  
  // pokrecemo sesiju
  session_start();

  // ukoliko onaj ko pristupa stranici nije korisnik bice vracen na login
  // isto se desava i sa administratorima (koji ce potom biti prebaceni na procetnu)
  if(!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit();
  }

  // ukljucujemo $conn promenljivu
  include("./../db/db.php");

  // ukoliko postoji promenljiva "category" u URI-u
  if(isset($_GET["category"])) {
    // pravimo asocijativni niz $subcategories u kome cemo smestiti sve potkategorije
    // koje su izabrane
    $subcategories = array(
      "id" => array(),
      "naziv" => array(),
      "cena" => array()
    );

    // $categoryId se uzima iz URI-a, kao i promenljiva $image, moguce je ovo i po koracima odraditi
    // ali ako prenesmo sliku u linku smanjujemo duzinu koda (u suprotnom morali bismo da pronadjemo red u tabeli gde su kategorije)
    $categoryId = $_GET["category"];
    if(isset($_GET["image"])) {
      $image = $_GET["image"];
    }

    // IZABERI SVE IZ potkategorije GDE JE kategorije_id = $categoryId
    $sql = "SELECT * FROM potkategorije WHERE kategorije_id = '$categoryId'";
    
    // rezultat upita smestamo u $result
    $result = $conn->query($sql);

    // dok god imamo redove potkategorija semstamo ih u $subcategories asoc. niz
    while($row = $result->fetch_assoc()) {
      array_push($subcategories["id"], $row["id"]);
      array_push($subcategories["naziv"], $row["naziv"]);
      array_push($subcategories["cena"], $row["cena"]);
    }
  } else {
    // ukoliko se category parametar ne nalazi u URI-u, korisnik ce biti vracen na pocetnu#usluge
    header('Location: ./../index.php#usluge');
    exit();
  }


  // pre nego sto korisnik izabere uslugu ukupna suma je 0
  // $toPrint promenljiva sluzi za ispisivanje svih izabranih potkategorija
  $total = 0;
  $toPrint = '';

  // ukoliko je set-ovana selected promenljiva u URI-u (koja je niz)
  if(isset($_GET["selected"])) {
    // $i = 0 -> duzina niza "selected
    for($i = 0; $i < sizeof($_GET["selected"]); $i++) {
      // iz niza $subcategories["id"] trazimo vrednost indeksa $_GET['selected'][$i] npr:
      // $...selected = [1, 2]; $i = 0; $subcategories["id"] = [1, 2, 3, 4] 
      // => array_search(...) = 0 jer je selected[0] = 1, a index jedinice u nizu $subcategories["id"] je nula
      // tj prvi je element
      $index = array_search(intval($_GET["selected"][$i]), $subcategories["id"]);
      // dodajemo u $total cenu
      $total += $subcategories["cena"][$index];
      // a u toPrint naziv i cenu
      $toPrint .= "<li>" . $subcategories["naziv"][$index] . " (". $subcategories["cena"][$index] .")</li>";
    }
  } else {
    // ukoliko selected nije set-ovano $total je jednako nuli
    $total = 0;
  }

  // ove promenljive se odnose na potvrdjivanje termina
  $done = false;
  $errors = false;
  $errorMsg = '';

  // ukoliko je kliknuto dugme za potvrdjivanje
  if(isset($_POST["submit"])) {
    // uzimamo datum i vreme
    $date = $_POST["date"];
    $time = $_POST["time"];

    // uzimamo danasnji datum i vreme u formatu 2024-29-02 15:30
    $currentTime = date_format(date_create(), 'Y-m-d H:i:s'); // danasnji datum
    // spajamo uneti datum i vreme (iz forme)
    $terminDatum = $date . ' ' . $time;
    // default vrednost <option></option> elementa je postavljena kao 0
    // ako je $time == 0 znaci da nije izabran nijedan termin
    if($time == '0') {
      $errors = true;
      $errorMsg = 'Morate izabrati termin!';
    } 
    else if ($terminDatum <= $currentTime) {
      // ovde uporedjujemo danasnji datum od izabranog, 
      // ako je "veci" danasnji to znaci da je izabrani termin u proslosti
      $errors = true;
      $errorMsg = "Termin ne sme biti u prošlosti!";
    } 
    else {
      // ukoliko je prosla provera ovih uslova
      // IZABERI SVE IZ transakcije GDE JE termin = $terminDatum
      $sql = "SELECT * FROM transakcije WHERE termin = '$terminDatum'";
      $result = $conn->query($sql);

      // ukoliko ne postoji termin u bazi sa ovim datumom
      if($result->num_rows == 0) {
        // definisemo ID korisnika iz sesije
        $korisnikID = $_SESSION["user"];

        // definisemo ID kategorije iz URI-a
        $categoryID = $_GET["category"];

        // UBACI U transakcije(...) VREDNOSTI(...)
        $sql = "INSERT INTO transakcije(korisnik_id, kategorije_id, termin, ukupno)
                VALUES ('$korisnikID', '$categoryID', '$terminDatum', '$total')";
        $conn->query($sql);

        // insert_id je ID prethodno unete vrednosti, npr ako smo u bazi imali ID-eve 1 i 2
        // a onda uneli jos jedan (koji sada ima 3) $transakcijeID promenljiva imace vrednost 3
        $transakcijeID= $conn->insert_id;

        // vrsimo iteraciju kroz ceo niz selected i ubacujemo u bazu svaku potkategoriju sa ID-em transakcije
        for($i = 0; $i < sizeof($_GET["selected"]); $i++) {
          $indexCategory = $_GET["selected"][$i];
          // UBACI U transakcije_potkategorije(...) VREDNOSTI (...)
          $sql = "INSERT INTO transakcije_potkategorije(transakcije_id, potkategorije_id)
                  VALUES ('$transakcijeID', '$indexCategory')";
          $conn->query($sql);
          // definisemo da je $done true i vracamo korisnika na pocetnu
          $done = true;
          header("Refresh: 2; URL=./../index.php");
        }        
      } else {
        // ukoliko je odabran termin zauzet
        $errors = true;
        $errorMsg = "Termin je zauzet, pokušajte drugi!";
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./../style/bootstrap.min.css">
  <link rel="stylesheet" href="./../style/index.css">
  <title>Usluge</title>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand mx-lg-5 fw-bold text-uppercase" href="./../index.php">Salon lepote</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="./../index.php">Početna</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./../index.php#o_nama">O nama</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./../index.php#usluge">Usluge</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./../index.php#kontakt">Kontakt</a>
          </li>
        </ul>
        <div class="d-flex mx-lg-5">
          <a class="dropdown-item" href="./../logout.php">Odjava</a>  
        </div>
      </div>
    </div>
  </nav>

  <!-- 
    Ukoliko je set-ovana $_GET["category"], a nije $_GET["selected"] prikazace se HTML ispod,
    ovo znaci da ce ovo korisnik videti prilikom biranja potkategorije
  -->
  <?php if(isset($_GET["category"]) && !isset($_GET["selected"])): ?>
    <div class="row d-flex justify-content-center mt-5">
      <h1 class="text-center">Usluga</h1>
      <div class="col-lg-6 col-md-8 col-sm-11 border">
        <div class="w-100 d-flex justify-content-center">
          <img class="category_img" src="./../assets/categories/<?php echo $image;?>" alt="...">
        </div>
        <div>
          <ul class="list-group list-group-flush">
          <form method="GET">
            <input name="category" value=<?php echo $_GET["category"]; ?> class="d-none" >
            <input name="image" value=<?php echo $_GET["image"]; ?> class="d-none" >
            <!-- izbacujemo sve potkategorije -->
            <?php for($i = 0; $i < sizeof($subcategories["id"]); $i++): ?>
              <div class="list-group-item">
                <input type="checkbox" name="selected[]" value="<?php echo $subcategories["id"][$i] ?>" />
                <?php echo $subcategories["naziv"][$i]; ?> (<?php echo $subcategories["cena"][$i]; ?>)
              </div>
            <?php endfor; ?>  
            <div class="d-flex justify-content-end">
              <button class="my-3 btn btn-primary btn_black" type="submit" name="calc" value="true">Izaberi termin &rarr;</button>
            </div>
          </form>
          </ul>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- 
    Ukoliko je set-ovan selected treba izbaciti formu za odabir termina 
   -->
  <?php if(isset($_GET["selected"])): ?>
    <div class="row d-flex justify-content-center mt-5">
    <h1 class="text-center">Termin</h1>
      <div class="col-lg-6 col-md-8 col-sm-11 border p-5">
      <p>Izabrali ste:</p>
      <ol>
        <?php echo $toPrint; ?>
      </ol>
      <hr />
      <p class="text-end">=<span class="fw-bold"><?php echo $total; ?></span></p>
      <form method="POST">
        <label>Izaberi datum</label>
        <div >
          <input type="date" name="date" class="form-control" />
        </div>
        <select name="time" class="form-control my-2">
          <option value="0">Izaberi termin</option>
          <option value="09:00">09:00 - 11:00</option>
          <option value="12:00">12:00 - 14:00</option>
          <option value="16:00">16:00 - 18:00</option>
        </select>  
        <input class="btn btn-primary btn_black w-100 my-3" type="submit" name="submit" value="Zakaži termin!" />
      </form>
      <!-- ovde ispisujemo rezultat (uspesno ili ne) -->
      <?php if($errors): ?>
        <p class="error text-center"><?php echo $errorMsg; ?></p>
      <?php endif; ?>
      <?php if($done): ?>
        <p class="success text-center">Uspešno zakazan termin!</p>
      <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <script src="./../scripts/bootstrap.bundle.min.js"></script>

</body>
</html>