<?php
// Funkcija koja proverava da li se podudaraju korisnicko ime i lozinka
function checkPassword ($email, $password, $conn) {
  // promenljiva $password se hashuje md5 algoritmom
  $password = md5($password);

  // "IZABERI SVE IZ korisnici GDE JE email = '$email' I lozinka = '$password'"
  $sql = "SELECT * FROM korisnici WHERE email = '$email' AND lozinka ='$password'";
  
  // rezultat upita stavljamo u $res (skraceno od result, rezultat)
  $res = $conn->query($sql);
  
  // vracamo da li u promenljivoj $res postoji vise od jednog reda
  // ako postoji, znaci da je pronadjen korisnik sa unetim podacima
  return $res->num_rows > 0;
}

// Isto kao funckija iznad samo sto se selektuje iz administratora i koristi se
// korisnicko ime umesto email-a
function checkAdminPassword($user, $password, $conn) {
  $password = md5($password);
  $sql = "SELECT id FROM administratori 
          WHERE korisnicko_ime = '$user' AND lozinka = '$password'"; 
  
  $res = $conn->query($sql);
  return $res->num_rows > 0;
}

// Funkcija koja uzima ID korisnika iz baze na osnovu unetog email-a
function getIdByEmail ($email, $conn) {
  // IZABERI id IZ korisnici GDE JE email = $email
  $sql = "SELECT id FROM korisnici WHERE email ='$email'";
  $res = $conn->query($sql);
  // ukoliko postoji, vrati uzeti id
  if($res->num_rows > 0) {
    return $res->fetch_assoc()['id'];
  }
}


?>