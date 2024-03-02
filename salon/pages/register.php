<?php
  session_start();

  if(isset($_SESSION["user"]) || isset($_SESSION["admin"])) {
    header("Location: ./../index.php");
    exit();
  }

  include("./../db/db.php");
  include('./../func/functions.php');

  $errors = false;
  $done = false;
  $errorMsg = '';

  $email  = "";
  $password = "";
  $name = "";
  $phone = "";

  if(isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $name = $_POST["name"];
    $phone = $_POST["phone"];

    if(!$name) {
      $errors = true;
      $errorMsg .= "<li>Niste uneli ime!</li>";
    }

    if(!$email) {
      $errors = true;
      $errorMsg .= "<li>Niste uneli e-mail!</li>";
    }

    if(!$password) {
      $errors = true;
      $errorMsg .= "<li>Niste uneli lozinku!</li>";
    }


    if(!$phone) {
      $errors = true;
      $errorMsg .= "<li>Niste uneli broj telefona!</li>";
    }

    if(!$errors) {
      if(getIdByEmail($email, $conn)) {
        $errors = true;
        $errorMsg .= "<li>E-mail već postoji!</li>";
      } else {
        $password = md5($password);
        $sql = "INSERT INTO korisnici(email, lozinka, ime, broj_telefona)
                VALUES ('$email', '$password', '$name', '$phone')";

        $conn->query($sql);
        $done = true;
        header('Refresh:2; URL=./login.php');
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
  <title>Korisnička registracija</title>
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
            <a class="nav-link active" aria-current="page" href="#">Početna</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">O nama</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Kontakt</a>
          </li>
        </ul>
        <div class="d-flex mx-lg-5">
          <a class="dropdown-item" href="./login.php">Prijava</a>  
        </div>
      </div>
    </div>
  </nav>

  <div class="container-fluid middle_div">
    <div class="row d-flex justify-content-center bg-white">
      <div class="col-lg-8 col-sm-12 col-md-10">
        <div class="row shadow">
          <div class="col-sm-12 col-md-7 bg-white">
            <div class="row d-flex justify-content-center">
              <div class="col-sm-12 col-md-9 mt-5">
                <p class="fw-bold">SALON LEPOTE</p>
                <p class="fw-bold">Napravite Vaš nalog</p>
                <form method="POST">
                  <div class="mb-3">
                    <input type="text" name="name" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Ime i prezime" value="<?php echo $name; ?>">
                  </div>
                  <div class="mb-3">
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="E-mail adresa" <?php echo $email; ?>>
                  </div>
                  <div class="mb-3">
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Lozinka">
                  </div>
                  <div class="mb-3">
                    <input type="tel" name="phone" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Broj telefona" <?php echo $phone; ?>>
                  </div>
                  <input type="submit" name="submit" class="btn btn-primary w-100 btn_black" value="Registrujte se!" />
                </form>
                <?php if($errors): ?>
                  <ul class="error mt-3 rounded">
                    <?php echo $errorMsg; ?>
                  </ul>
                <?php endif; ?>
                <?php if($done): ?>
                  <span class="d-block success mt-3 text-center">Uspešna registracija!</span>
                <?php endif; ?>
                <span class="mt-3 d-block">Imate nalog? <a href="./login.php">Prijavite se!</a></span>
                <span class="my-2 d-block">Admin ste? <a href="./admin/login.php">Prijavite se!</a></span>
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-12 login_display">
            <img class="w-100 h-100 login_image login_display" src="./../assets/image1.jpg" alt="Login slika" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="./../scripts/bootstrap.bundle.min.js"></script>
</body>
</html>