<?php 
  // --- FAJL KOJI SLUZI ZA POVEZIVANJE SA BAZOM PODATAKA ---
  // ukoliko se baza zove drugacije iz bilo kog razloga, ovde je to potrebno promeniti
  $server = 'localhost';
  $user = 'root';
  $pw = '';
  $db = 'salon_lepote';
  
  // pravimo $conn promenljivu
  $conn = new mysqli($server, $user, $pw, $db);

  if($conn->connect_error) { 
    echo $conn->connect_error;
    die();
  }
?>