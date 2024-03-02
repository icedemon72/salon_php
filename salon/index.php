<?php
  session_start();

  include ("./db/db.php");

  $sql = "SELECT * FROM kategorije";
  $result = $conn->query($sql);

  $categories = array(
    'id' => array(),
    'naziv' => array(),
    'slika' => array()
  );

  while($row = $result->fetch_assoc()) {
    array_push($categories["id"], $row["id"]);
    array_push($categories["naziv"], $row["naziv"]);
    array_push($categories["slika"], $row["slika"]);
  };

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./style/bootstrap.min.css">
  <link rel="stylesheet" href="./style/index.css">
  <title>Salon lepote</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <a class="navbar-brand mx-lg-5 fw-bold text-uppercase" href="#">Salon lepote</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="#">Početna</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#o_nama">O nama</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#usluge">Usluge</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#kontakt">Kontakt</a>
          </li>
        </ul>
        <div class="d-lg-flex d-xs-block mx-lg-5 gap-3 nav-item">
          <?php if(!isset($_SESSION["user"]) && !isset($_SESSION["admin"])): ?>
            <a class="nav-link" href="./pages/login.php">Prijava</a>
          <?php else: ?>
            <?php if(isset($_SESSION["admin"])): ?>
              <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Admin
                </a>
                <ul class="dropdown-menu dropdown_menu" aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item" href="./pages/admin/schedule.php">Zakazane usluge</a></li>
                  <li><a class="dropdown-item" href="./pages/admin/purchases.php">Statistika</a></li>
                </ul>
              </div>
              <hr class="d-xs-block d-lg-none"/>
            <?php endif; ?>
            <a class="nav-link" href="./logout.php">Odjavi se</a>  
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <section id="front" class="d-flex align-items-center justify-content-center">
    <div class="container position-relative">
      <h1 class="text-center text-light">Dobrodošli u salon lepote</h1>
      <h2 class="text-center text-light">Učlanite se već danas!</h2>
      <div class="w-100 d-flex justify-content-center">
        <?php if(!isset($_SESSION['user']) && !isset($_SESSION["admin"])): ?>
          <a href="./pages/login.php" class="btn btn-primary btn_black text-center">Učlani se!</a>
        <?php else: ?>
          <a href="#usluge" class="btn btn-primary btn_black text-center">Pogledaj usluge!</a>
        <?php endif; ?>  
      </div>
    </div>
  </section>

  <section id="o_nama" class="mt-5 p-5">
    <div class="container">
      <div class="row">
        <h1 class="text-black">O nama</h1>
        <div class="col-lg-6">
          <img src="./assets/salon1.jpg" class="img-fluid" alt="">
        </div>
        <div class="col-lg-6 pt-4 pt-lg-0">
          <h3>Voluptatem dignissimos provident quasi</h3>
          <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Duis aute irure dolor in reprehenderit
          </p>
          <div class="row">
            <div class="col-md-6">
              <i class="bx bx-receipt"></i>
              <h4>Corporis voluptates sit</h4>
              <p>Consequuntur sunt aut quasi enim aliquam quae harum pariatur laboris nisi ut aliquip</p>
            </div>
            <div class="col-md-6">
              <i class="bx bx-cube-alt"></i>
              <h4>Ullamco laboris nisi</h4>
              <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="usluge" class="mt-5 p-5">
    <div class="container">
      <h1>Usluge</h1>
      <div class="row">
        <?php for($i = 0; $i < sizeof($categories['id']); $i++): ?>
          <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 d-flex justify-content-center">
            <a class="text-decoration-none text-reset" href="./pages/categories.php?category=<?php echo $categories["id"][$i] ?>&image=<?php echo $categories["slika"][$i] ?>">
            <div class="category_item">
              <img src="./assets/categories/<?php echo $categories["slika"][$i] ?>" style="filter: brightness(0%);" />
              <p class="text-center fw-bold"><?php echo $categories["naziv"][$i] ?></p>  
            </div>
            </a>
          </div>
          <?php endfor; ?>
      </div>
    </div>
  </section>

  <section id="kontakt" class="mt-5 p-5 text-white">
    <div class="container">

      <div class="section-title">
        <h2>Kontakt</h2>
        <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
      </div>

      <div class="row mt-5">

        <div class="col-lg-6">

          <div class="row">
            <div class="col-md-12">
              <div class="info-box">
                <h3>Naša adresa</h3>
                <p>Ul. Lole Ribara, K. Mitrovica, RS 38220</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-box mt-4">
                <h3>Pošaljite nam e-mail</h3>
                <p>salonlepotekm@gmail.com<br>admin@salonlepote.com</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-box mt-4">
                <h3>Pozovite nas</h3>
                <p>+381 61 234 5678<br>+381 28 123 456</p>
              </div>
            </div>
          </div>

        </div>

        <div class="col-lg-6">
          <div>
            <div class="row">
              <div class="col-md-6 form-group">
                <input type="text" name="name" class="form-control" id="name" placeholder="Vaše ime" >
              </div>
              <div class="col-md-6 form-group mt-3 mt-md-0">
                <input type="email" class="form-control" name="email" id="email" placeholder="Vaš e-mail">
              </div>
            </div>
            <div class="form-group mt-3">
              <input type="text" class="form-control" name="subject" id="subject" placeholder="Naslov">
            </div>
            <div class="form-group mt-3">
              <textarea class="form-control" name="message" rows="5" placeholder="Poruka"></textarea>
            </div>
            <div class="text-center"><button class="btn btn-primary btn_black text-center mt-3" type="submit">Pošaljite poruku!</button></div>
          </div>
        </div>

      </div>

    </div>
  </section>

  

  <script src="./scripts/bootstrap.bundle.min.js"></script>
</body>

</html>