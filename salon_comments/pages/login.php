<?php
// --- STRANICA ZA PRIJAVLJIVANJE KORISNIKA ---

// zapocinjemo sesiju
session_start();

// proveravamo da li vec ima neko ulogovan (ukoliko ima, vracamo ga na pocetnu)
if (isset($_SESSION["user"]) || isset($_SESSION["admin"])) {
  header("Location: ./../index.php");
  exit();
}

// ukljucujemo $conn promenljivu
include('./../db/db.php');

// i pomocne funkcije
include('./../func/functions.php');


// na pocetku, nije doslo do greske i nismo uspesno zavrsili prijavu
$done = false;
$errors = false;


// ukoliko je kliknuto dugme <input ... name="submit">
if(isset($_POST['submit'])) {
  // uzmi informacije iz <form>...</form> elementa
  $email = $_POST['email'];
  $password = $_POST['password'];

  // ukoliko su tacne informacije
  if(checkPassword($email, $password, $conn)) {
    // postavi sesijsku promenljivu 'user' da je jednaka ID-u korisnika iz baze
    $_SESSION['user'] = getIdByEmail($email, $conn);
    $done = true;
    // osvezi stranicu za 2s i posalji korisnika na pocetnu
    header("Refresh:2 ; URL=./../index.php");
  } else {
    // ukoliko nisu unete tacne informacije, postavi da je doslo do greske
    $errors = true;
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
  <title>Korisnička prijava</title>
</head>

<body class="login_bg">
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
          <a class="dropdown-item" href="./register.php">Registracija</a>  
        </div>
      </div>
    </div>
  </nav>

  <div class="container-fluid middle_div">
    <div class="row d-flex justify-content-center bg-white">
      <div class="col-lg-8 col-sm-12 col-md-10">
        <div class="row shadow">
          <div class="col-md-5 col-sm-12 login_display">
            <img class="w-100 h-100 login_image login_display" src="./../assets/image2.jpg" alt="Login slika" />
          </div>
          <div class="col-sm-12 col-md-7 bg-white">
            <div class="row d-flex justify-content-center">
              <div class="col-sm-12 col-md-9 mt-5">
                <p class="fw-bold">SALON LEPOTE</p>
                <p class="fw-bold">Prijavite se Vašim nalogom</p>
                <form method="POST">
                  <div class="mb-3">
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="E-mail adresa">
                  </div>
                  <div class="mb-3">
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Lozinka">
                  </div>
                  <input type="submit" name="submit" class="btn btn-primary w-100 btn_black" value="Prijavite se!" />
                </form>
                <?php if($errors): ?>
                  <span class="d-block error text-center mt-3">Uneti su netačni podaci!</span>
                <?php endif; ?>
                <?php if($done): ?>
                  <span class="d-block success text-center mt-3">Uspešno prijavljivanje!</span>
                <?php endif; ?>
                <span class="mt-3 d-block">Nemate nalog? <a href="./register.php">Registrujte se!</a></span>
                <span class="my-2 d-block">Admin ste? <a href="./admin/login.php">Prijavite se!</a></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="./../scripts/bootstrap.bundle.min.js"></script>
</body>

</html>