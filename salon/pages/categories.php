<?php 
  session_start();

  if(!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit();
  }

  include("./../db/db.php");

  if(isset($_GET["category"])) {
    $subcategories = array(
      "id" => array(),
      "naziv" => array(),
      "cena" => array()
    );

    $categoryId = $_GET["category"];
    if(isset($_GET["image"])) {
      $image = $_GET["image"];
    }

    $sql = "SELECT * FROM potkategorije WHERE kategorije_id = '$categoryId'";
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()) {
      array_push($subcategories["id"], $row["id"]);
      array_push($subcategories["naziv"], $row["naziv"]);
      array_push($subcategories["cena"], $row["cena"]);
    }
  } else {
    header('Location: ./../index.php#usluge');
    exit();
  }

  $total = 0;
  $toPrint = '';

  if(isset($_GET["selected"])) {
    for($i = 0; $i < sizeof($_GET["selected"]); $i++) {
      $index = array_search(intval($_GET["selected"][$i]), $subcategories["id"]);
      $total += $subcategories["cena"][$index];
      $toPrint .= "<li>" . $subcategories["naziv"][$index] . " (". $subcategories["cena"][$index] .")</li>";
    }
  } else {
    $total = 0;
  }

  $done = false;
  $errors = false;
  $errorMsg = '';

  if(isset($_POST["submit"])) {
    $date = $_POST["date"];
    $time = $_POST["time"];

    $currentTime = date_format(date_create(), 'Y-m-d H:i:s'); // danasnji datum
    $terminDatum = $date . ' ' . $time;
    if($time == '0') {
      $errors = true;
      $errorMsg = 'Morate izabrati termin!';
    } 
    else if ($terminDatum <= $currentTime) {
      $errors = true;
      $errorMsg = "Termin ne sme biti u prošlosti!";
    } 
    else {
      $sql = "SELECT * FROM transakcije WHERE termin = '$terminDatum'";
      $result = $conn->query($sql);

      if($result->num_rows == 0) {
        $korisnikID = $_SESSION["user"];
        $categoryID = $_GET["category"];
        $sql = "INSERT INTO transakcije(korisnik_id, kategorije_id, termin, ukupno)
                VALUES ('$korisnikID', '$categoryID', '$terminDatum', '$total')";
        $conn->query($sql);

        $transakcijeID= $conn->insert_id;

        for($i = 0; $i < sizeof($_GET["selected"]); $i++) {
          $indexCategory = $_GET["selected"][$i];
          $sql = "INSERT INTO transakcije_potkategorije(transakcije_id, potkategorije_id)
                  VALUES ('$transakcijeID', '$indexCategory')";
          $conn->query($sql);
          $done = true;
          header("Refresh: 2; URL=./../index.php");
        }        
      } else {
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