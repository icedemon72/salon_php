<?php
  // --- STRANICA NA KOJU SE KORISNICI PREUSMERAVAJU PRILIKOM ODJAVLJIVANJA ---
  // pokrecemo sesiju
  session_start();

  // proveravamo da li je korisnik ulogovan
  if(isset($_SESSION['user'])) {
    // ukoliko jeste, unisti sesijsku promenljivu
    unset($_SESSION['user']);
    // prebaci ga na ./pages/login.php
    header('Location: ./pages/login.php');
  }

  // proveravamo da li je admin ulogovan
  if(isset($_SESSION['admin'])) {
    // ukoliko jeste, unisti sesijsku promenljivu
    unset($_SESSION['admin']);
    // prebaci ga na ./pages/admin/login.php
    header('Location: ./pages/admin/login.php');
  }  
?>