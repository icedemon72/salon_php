<?php
  session_start();

  if(isset($_SESSION["user"]) || isset($_SESSION["admin"])) {
    header("Location: ./../../index.php");
    exit();
  }

  include("./../../db/db.php");
  include("./../../func/functions.php");

  $done = false;
  $errors = false;

  if(isset($_POST["submit"])) {
    $user = $_POST["user"];
    $password = $_POST["password"];

    if(checkAdminPassword($user, $password, $conn)) {
      $_SESSION["admin"] = $user;
      $done = true;
      header("Refresh:1; URL=./../../index.php");
    } else {
      $errors = true;
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
  <title>Admin prijava</title>
</head>
<body class="centered_body">
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
        <div class="d-flex mx-lg-5">
          <a class="dropdown-item" href="./../login.php">Korisnička prijava</a>  
        </div>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div class="row h-100 d-flex justify-content-center mt-5">
      <div class="col-sm-12 col-md-6 col-lg-4 shadow p-5">
        <h2 class="text-center mb-3">Admin prijava</h2>
        <form class="row" method="POST">
          <input class="mb-3" type="text" name="user" placeholder="Korisničko ime" />
          <input class="mb-3" type="password" name="password" placeholder="Lozinka" />
          <input class="btn btn-primary w-100 btn_black" type="submit" name="submit" value="Prijavi se!" />
        </form>
        <?php if($errors): ?>
          <span class="d-block error text-center mt-3">Uneti su netačni podaci!</span>
        <?php endif; ?>
        <?php if($done): ?>
          <span class="d-block success text-center mt-3">Uspešno prijavljivanje!</span>
        <?php endif; ?>
        <span class="mt-3 d-block">Niste admin? <a href="./../login.php">Prijavite se kao korisnik!</a></span>      </div>
    </div>
  </div>

  <script src="./../../scripts/bootstrap.bundle.min.js"></script>
  
</body>
</html>