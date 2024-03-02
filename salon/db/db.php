<?php 
  $server = 'localhost';
  $user = 'root';
  $pw = '';
  $db = 'salon_lepote';
  $conn = new mysqli($server, $user, $pw, $db);

  if($conn->connect_error) { 
    echo $conn->connect_error;
    die();
  }
?>