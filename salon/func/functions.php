<?php
function checkPassword ($email, $password, $conn) {
  $password = md5($password);
  $sql = "SELECT * FROM korisnici WHERE email = '$email' AND lozinka ='$password'";
  $res = $conn->query($sql);
  return $res->num_rows > 0;
}

function getIdByEmail ($email, $conn) {
  $sql = "SELECT id FROM korisnici WHERE email ='$email'";
  $res = $conn->query($sql);
  if($res->num_rows > 0) {
    return $res->fetch_assoc()['id'];
  }
}

function checkAdminPassword($user, $password, $conn) {
  $password = md5($password);
  $sql = "SELECT id FROM administratori 
          WHERE korisnicko_ime = '$user' AND lozinka = '$password'"; 
  
  $res = $conn->query($sql);
  return $res->num_rows > 0;
}

?>